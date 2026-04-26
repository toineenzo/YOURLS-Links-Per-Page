<?php
/*
Plugin Name: Links Per Page
Plugin URI: https://github.com/toineenzo/YOURLS-Links-Per-Page
Description: Show a configurable number of links per page on the YOURLS admin link table, with a small settings page to update the value.
Version: 1.2.0
Author: Toine Rademacher (toineenzo)
Author URI: https://github.com/toineenzo
*/

declare(strict_types=1);

if (!defined('YOURLS_ABSPATH')) {
    die();
}

const LPP_OPTION_NAME    = 'links_per_page';
const LPP_DEFAULT_LINKS  = 50;
const LPP_MIN_LINKS      = 1;
const LPP_MAX_LINKS      = 999;
const LPP_PAGE_SLUG      = 'links_per_page_settings';
const LPP_NONCE_ACTION   = 'links_per_page_settings';

/**
 * Filter callback used by the admin link table to decide how many rows to
 * render. Returning an integer keeps PHP 8.4 strict mode happy when YOURLS
 * casts the filtered value back to int.
 */
function lpp_get_links_per_page(): int
{
    $value = (int) yourls_get_option(LPP_OPTION_NAME, LPP_DEFAULT_LINKS);
    if ($value < LPP_MIN_LINKS) {
        return LPP_DEFAULT_LINKS;
    }
    if ($value > LPP_MAX_LINKS) {
        return LPP_MAX_LINKS;
    }
    return $value;
}

yourls_add_filter('admin_view_per_page', 'lpp_get_links_per_page');

/**
 * Register the settings sub-page under Manage Plugins. Hooked late on
 * `plugins_loaded` so YOURLS' admin menu picks the entry up.
 */
function lpp_register_admin_page(): void
{
    yourls_register_plugin_page(LPP_PAGE_SLUG, 'Links Per Page', 'lpp_render_admin_page');
}
yourls_add_action('plugins_loaded', 'lpp_register_admin_page');

/**
 * Handle the POST and render the small settings form.
 */
function lpp_render_admin_page(): void
{
    if (!yourls_is_admin()) {
        die('Access denied');
    }

    $message       = '';
    $message_type  = 'success';

    if (isset($_POST['links_per_page'])) {
        $nonce = (string) ($_POST['nonce'] ?? '');
        if ($nonce === '' || !yourls_verify_nonce(LPP_NONCE_ACTION, $nonce)) {
            $message      = 'Invalid security token. Please reload the page and try again.';
            $message_type = 'error';
        } else {
            $submitted = filter_input(
                INPUT_POST,
                'links_per_page',
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => LPP_MIN_LINKS, 'max_range' => LPP_MAX_LINKS]]
            );

            if ($submitted === null || $submitted === false) {
                yourls_update_option(LPP_OPTION_NAME, LPP_DEFAULT_LINKS);
                $message      = sprintf(
                    'Invalid value — reset to the default of %d links per page.',
                    LPP_DEFAULT_LINKS
                );
                $message_type = 'warning';
            } else {
                $current = (int) yourls_get_option(LPP_OPTION_NAME, LPP_DEFAULT_LINKS);
                if ($current === $submitted) {
                    $message      = sprintf('No change — value is still %d links per page.', $submitted);
                    $message_type = 'info';
                } elseif (yourls_update_option(LPP_OPTION_NAME, $submitted)) {
                    $message      = sprintf('Saved — now showing %d links per page.', $submitted);
                    $message_type = 'success';
                } else {
                    $message      = 'YOURLS rejected the update. Please try again.';
                    $message_type = 'error';
                }
            }
        }
    }

    $current_value = (int) yourls_get_option(LPP_OPTION_NAME, LPP_DEFAULT_LINKS);
    $nonce_value   = yourls_create_nonce(LPP_NONCE_ACTION);
    ?>
    <h2>Links Per Page Settings</h2>

    <?php if ($message !== ''): ?>
    <div class="lpp-notice lpp-notice-<?php echo yourls_esc_attr($message_type); ?>" data-lpp-notice="<?php echo yourls_esc_attr($message_type); ?>">
        <p><?php echo yourls_esc_html($message); ?></p>
    </div>
    <?php endif; ?>

    <div class="lpp-notice lpp-notice-info">
        <p><strong>Note:</strong> This setting controls how many links are shown per page in the YOURLS admin link table.</p>
        <p>Default is <?php echo (int) LPP_DEFAULT_LINKS; ?> links per page; valid range is <?php echo (int) LPP_MIN_LINKS; ?>&ndash;<?php echo (int) LPP_MAX_LINKS; ?>.</p>
    </div>

    <form method="post" id="lpp-form">
        <input type="hidden" name="nonce" value="<?php echo yourls_esc_attr($nonce_value); ?>">

        <div class="lpp-group">
            <div class="lpp-row">
                <label for="links_per_page" class="lpp-label">Links per page</label>
                <input
                    type="number"
                    id="links_per_page"
                    name="links_per_page"
                    value="<?php echo yourls_esc_attr((string) $current_value); ?>"
                    min="<?php echo (int) LPP_MIN_LINKS; ?>"
                    max="<?php echo (int) LPP_MAX_LINKS; ?>"
                    step="1"
                    class="lpp-input"
                    required>
            </div>
            <p class="lpp-hint">Enter an integer between <?php echo (int) LPP_MIN_LINKS; ?> and <?php echo (int) LPP_MAX_LINKS; ?>.</p>
        </div>

        <p>
            <button type="submit" class="button primary lpp-save">Save settings</button>
        </p>
    </form>

    <style>
        #lpp-form { max-width: 600px; }
        .lpp-notice {
            margin: 15px 0;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .lpp-notice p { margin: 4px 0; }
        .lpp-notice-info    { background: rgba(0, 128, 255, 0.1); border-left: 4px solid #0080ff; }
        .lpp-notice-success { background: rgba(0, 128, 0, 0.1);   border-left: 4px solid #008000; }
        .lpp-notice-warning { background: rgba(255, 165, 0, 0.1); border-left: 4px solid #ffa500; }
        .lpp-notice-error   { background: rgba(255, 0, 0, 0.1);   border-left: 4px solid #ff0000; }

        .lpp-group {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid rgba(128, 128, 128, 0.2);
            border-radius: 5px;
        }
        .lpp-row {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .lpp-label { font-weight: 600; }
        .lpp-hint  { color: rgba(0, 0, 0, 0.6); font-size: 0.9em; margin-top: 8px; }

        .lpp-input {
            padding: 8px 10px;
            border: 1px solid rgba(128, 128, 128, 0.3);
            border-radius: 3px;
            background: transparent;
            color: inherit;
            max-width: 200px;
        }

        .button.primary.lpp-save {
            padding: 8px 16px;
            cursor: pointer;
        }
    </style>
    <?php
}

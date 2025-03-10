<?php
/**
 * Plugin Name: Links Per Page
 * Plugin URI: https://github.com/toineenzo/YOURLS-Links-Per-Page
 * Description: Show a custom number of displayed links per page with a configurable admin page.
 * Version: 1.1
 * Author: Toine Rademacher (toineenzo)
 * Author URI: https://github.com/toineenzo
 */

// Prevent direct access to this file
if (!defined('YOURLS_ABSPATH')) die();

class LinksPerPage
{
  private const OPTION_NAME = 'links_per_page';
  private const DEFAULT_LINKS = 50;

  public function __construct()
  {
    // Filter to change the number of links displayed per page
    yourls_add_filter('admin_view_per_page', [$this, 'getCustomLinksPerPage']);
    
    // Admin page hooks
    yourls_add_action('plugins_loaded', [$this, 'addAdminPage']);
  }

  /**
   * Get the custom number of links per page
   */
  public function getCustomLinksPerPage()
  {
    return yourls_get_option(self::OPTION_NAME, self::DEFAULT_LINKS);
  }

  /**
   * Add admin page to YOURLS menu
   */
  public function addAdminPage(): void
  {
    yourls_register_plugin_page('links_per_page_settings', 'Links Per Page', [$this, 'displayAdminPage']);
  }

  /**
   * Display admin settings page
   */
  public function displayAdminPage(): void
  {
    if (!yourls_is_admin()) die('Access denied');

    // Initialize message
    $message = '';
    $messageType = 'success';

    // Process form if submitted
    if (isset($_POST['links_per_page'])) {
      // Check nonce for security
      if (isset($_POST['nonce'])) {
        if (!yourls_verify_nonce('links_per_page_settings', $_POST['nonce'])) {
          $message = 'Error: Invalid security token. Please try again.';
          $messageType = 'error';
        } else {
          // Get and validate the links per page value
          $linksPerPage = intval($_POST['links_per_page']);
          
          // Enforce minimum value
          if ($linksPerPage < 1) {
            $linksPerPage = self::DEFAULT_LINKS;
            $message = 'Invalid value: Using default value of ' . self::DEFAULT_LINKS . ' links per page.';
            $messageType = 'warning';
          } else {
            // Get current value for comparison
            $currentValue = yourls_get_option(self::OPTION_NAME, self::DEFAULT_LINKS);
            
            // Only update if value has changed
            if ($currentValue == $linksPerPage) {
              $message = 'No changes made - value remains at ' . $linksPerPage . ' links per page.';
              $messageType = 'info';
            } else {
              // Update the option
              $updated = yourls_update_option(self::OPTION_NAME, $linksPerPage);
              
              if ($updated) {
                $message = 'Links per page updated successfully to ' . $linksPerPage . '.';
                $messageType = 'success';
              } else {
                $message = 'Error: Could not update links per page setting. Please try again.';
                $messageType = 'error';
              }
            }
          }
        }
      } else {
        $message = 'Error: Missing security token. Please try again.';
        $messageType = 'error';
      }
    }

    // Generate nonce for the form
    $nonce = yourls_create_nonce('links_per_page_settings');
    $currentValue = yourls_get_option(self::OPTION_NAME, self::DEFAULT_LINKS);
?>
    <h2>Links Per Page Settings</h2>

    <?php if (!empty($message)): ?>
    <div class="notice <?php echo $messageType; ?>">
      <p><?php echo $message; ?></p>
    </div>
    <?php endif; ?>

    <div class="notice info">
      <p><strong>Note:</strong> This setting controls how many links are displayed per page in the admin interface.</p>
      <p>The default value is <?php echo self::DEFAULT_LINKS; ?> links per page. Values less than 1 will be reset to the default.</p>
    </div>

    <form method="post">
      <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">

      <div class="settings-group">
        <div class="settings-row">
          <div class="settings-label">
            <label for="links_per_page">Links per page:</label>
          </div>
          <div class="settings-input">
            <input type="number" 
                  id="links_per_page" 
                  name="links_per_page" 
                  value="<?php echo $currentValue; ?>" 
                  min="1" 
                  max="999" 
                  step="1" 
                  class="text">
          </div>
        </div>

        <div class="settings-row">
          <div class="settings-description">
            <p>Enter a number between 1 and 999 to set how many links should be displayed per page in the admin interface.</p>
          </div>
        </div>
      </div>

      <p><input type="submit" value="Save Settings" class="button primary"></p>
    </form>

    <style>
      .notice {
        margin: 15px 0;
        padding: 10px 15px;
        border-radius: 5px;
      }

      .notice.info {
        background-color: rgba(0, 128, 255, 0.1);
        border-left: 4px solid #0080ff;
      }

      .notice.success {
        background-color: rgba(0, 128, 0, 0.1);
        border-left: 4px solid #008000;
      }

      .notice.warning {
        background-color: rgba(255, 165, 0, 0.1);
        border-left: 4px solid #ffa500;
      }

      .notice.error {
        background-color: rgba(255, 0, 0, 0.1);
        border-left: 4px solid #ff0000;
      }

      .settings-group {
        margin: 20px 0;
        padding: 15px;
        border: 1px solid rgba(128, 128, 128, 0.2);
        border-radius: 5px;
        max-width: 600px;
      }

      .settings-row {
        margin-bottom: 15px;
      }

      .settings-row:last-child {
        margin-bottom: 0;
      }

      .settings-label {
        margin-bottom: 5px;
      }

      .settings-label label {
        font-weight: bold;
      }

      .settings-input {
        margin-bottom: 5px;
      }

      input.text {
        width: 100%;
        padding: 8px;
        border: 1px solid rgba(128, 128, 128, 0.2);
        border-radius: 3px;
        background: transparent;
        color: inherit;
        max-width: 200px;
      }

      .settings-description {
        color: #666;
        font-size: 0.9em;
      }

      .button.primary {
        padding: 8px 16px;
        cursor: pointer;
      }

      /* For responsive design */
      @media (max-width: 768px) {
        .settings-group {
          padding: 10px;
        }
      }
    </style>
<?php
  }
}

// Initialize the plugin
new LinksPerPage();
?>

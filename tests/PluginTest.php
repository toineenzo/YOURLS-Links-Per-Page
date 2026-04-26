<?php
/**
 * Unit coverage for the Links Per Page plugin.
 *
 * The plugin is small enough that almost all of its surface area is the
 * lpp_get_links_per_page() filter callback plus the hooks it wires up — both
 * are pinned here.
 */

declare(strict_types=1);

class PluginTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        // Reset the option between tests so each case starts from a known state.
        yourls_delete_option(LPP_OPTION_NAME);
    }

    public function test_default_when_option_is_unset(): void
    {
        $this->assertSame(LPP_DEFAULT_LINKS, lpp_get_links_per_page());
    }

    /**
     * @dataProvider provideInRangeValues
     */
    public function test_in_range_value_passes_through(int $stored, int $expected): void
    {
        yourls_update_option(LPP_OPTION_NAME, $stored);
        $this->assertSame($expected, lpp_get_links_per_page());
    }

    public static function provideInRangeValues(): array
    {
        return [
            'minimum'      => [LPP_MIN_LINKS, LPP_MIN_LINKS],
            'small typical'=> [10, 10],
            'mid'          => [50, 50],
            'large'        => [500, 500],
            'maximum'      => [LPP_MAX_LINKS, LPP_MAX_LINKS],
        ];
    }

    public function test_below_minimum_falls_back_to_default(): void
    {
        yourls_update_option(LPP_OPTION_NAME, 0);
        $this->assertSame(LPP_DEFAULT_LINKS, lpp_get_links_per_page());

        yourls_update_option(LPP_OPTION_NAME, -42);
        $this->assertSame(LPP_DEFAULT_LINKS, lpp_get_links_per_page());
    }

    public function test_above_maximum_is_clamped(): void
    {
        yourls_update_option(LPP_OPTION_NAME, LPP_MAX_LINKS + 1);
        $this->assertSame(LPP_MAX_LINKS, lpp_get_links_per_page());

        yourls_update_option(LPP_OPTION_NAME, 99999);
        $this->assertSame(LPP_MAX_LINKS, lpp_get_links_per_page());
    }

    public function test_filter_is_registered_on_admin_view_per_page(): void
    {
        $filters = yourls_get_filters('admin_view_per_page');
        $this->assertIsArray($filters, 'admin_view_per_page filter has no callbacks attached.');

        $found = false;
        foreach ($filters as $bucket) {
            if (is_array($bucket) && array_key_exists('lpp_get_links_per_page', $bucket)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue(
            $found,
            'lpp_get_links_per_page is not bound to admin_view_per_page.'
        );
    }

    public function test_admin_page_registration_runs_on_plugins_loaded(): void
    {
        $filters = yourls_get_filters('plugins_loaded');
        $found = false;
        foreach ((array) $filters as $bucket) {
            if (is_array($bucket) && array_key_exists('lpp_register_admin_page', $bucket)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue(
            $found,
            'lpp_register_admin_page is not hooked to plugins_loaded.'
        );
    }

    public function test_full_filter_chain_returns_clamped_value(): void
    {
        // Round-trip through yourls_apply_filter(). 15 is YOURLS' compiled-in
        // default; the plugin filter overrides it with whatever we stored.
        yourls_update_option(LPP_OPTION_NAME, 75);
        $this->assertSame(75, yourls_apply_filter('admin_view_per_page', 15));
    }
}

import { test, expect } from '../utils/fixtures';

const ADMIN_PATH = '/admin/plugins.php?page=links_per_page_settings';

test.describe('Plugin basics', () => {
  test('YOURLS admin still loads with the plugin active', async ({ page, errors }) => {
    const response = await page.goto('/admin/index.php');
    expect(response?.status()).toBeLessThan(400);
    await expect(page.locator('#new_url_form')).toBeVisible();
    expect(errors.serverErrors).toEqual([]);
  });

  test('plugin appears as Active on plugins page', async ({ page }) => {
    await page.goto('/admin/plugins.php');
    await expect(
      page.locator('tr.plugin.active', { hasText: 'Links Per Page' })
    ).toBeVisible();
  });

  test('settings page renders the configuration form', async ({ page, errors }) => {
    await page.goto(ADMIN_PATH);
    await expect(page.locator('#lpp-form')).toBeVisible();
    await expect(page.locator('#links_per_page')).toBeVisible();
    await expect(page.locator('button.lpp-save')).toBeVisible();
    expect(errors.serverErrors).toEqual([]);
  });
});

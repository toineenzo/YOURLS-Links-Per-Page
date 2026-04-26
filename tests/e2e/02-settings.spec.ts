import { test, expect } from '../utils/fixtures';

const ADMIN_PATH = '/admin/plugins.php?page=links_per_page_settings';

test.describe.configure({ mode: 'serial' });

test.describe('Saving the links-per-page setting', () => {
  test('valid value saves and persists across a reload', async ({ page, errors }) => {
    await page.goto(ADMIN_PATH);

    await page.locator('#links_per_page').fill('25');
    await page.locator('button.lpp-save').click();
    await page.waitForLoadState('networkidle');

    // Success banner uses data-lpp-notice="success".
    await expect(page.locator('[data-lpp-notice="success"]')).toContainText(
      /now showing 25 links per page/i
    );

    // Reload — the input must come back populated with 25.
    await page.goto(ADMIN_PATH);
    await expect(page.locator('#links_per_page')).toHaveValue('25');

    expect(errors.serverErrors).toEqual([]);
  });

  test('out-of-range values are rejected and reset to default', async ({
    page,
    errors,
  }) => {
    await page.goto(ADMIN_PATH);

    // The input has min=1 / max=999, so the browser rejects negatives in the
    // UI. Bypass the constraints with fill() so we can verify the server-side
    // FILTER_VALIDATE_INT fallback.
    await page.locator('#links_per_page').evaluate((el: HTMLInputElement) => {
      el.removeAttribute('min');
      el.removeAttribute('max');
      el.value = '0';
    });
    await page.locator('button.lpp-save').click();
    await page.waitForLoadState('networkidle');

    await expect(page.locator('[data-lpp-notice="warning"]')).toContainText(
      /reset to the default of 50/i
    );
    await expect(page.locator('#links_per_page')).toHaveValue('50');

    expect(errors.serverErrors).toEqual([]);
  });

  test('submitting an unchanged value reports "no change"', async ({
    page,
    errors,
  }) => {
    // The previous test left the value at 50.
    await page.goto(ADMIN_PATH);
    await expect(page.locator('#links_per_page')).toHaveValue('50');

    await page.locator('button.lpp-save').click();
    await page.waitForLoadState('networkidle');

    await expect(page.locator('[data-lpp-notice="info"]')).toContainText(
      /no change.*still 50/i
    );

    expect(errors.serverErrors).toEqual([]);
  });
});

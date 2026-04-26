import { test, expect, createYourlsShortlink } from '../utils/fixtures';

const ADMIN_PATH = '/admin/plugins.php?page=links_per_page_settings';

test.describe.configure({ mode: 'serial' });

test.describe('Pagination respects the configured value', () => {
  const stamp = Date.now().toString(36);
  const TOTAL = 7;

  test.beforeAll(async ({ browser }) => {
    // Seed enough shortlinks that pagination kicks in.
    const ctx = await browser.newContext({
      baseURL: process.env.YOURLS_BASE_URL ?? 'http://127.0.0.1:8080',
      storageState: '.auth/admin.json',
    });
    const page = await ctx.newPage();
    for (let i = 0; i < TOTAL; i++) {
      await createYourlsShortlink(page, {
        url: `https://example.com/lpp-${stamp}-${i}`,
        keyword: `lpp${stamp}${i}`,
        title: `Pagination test ${i}`,
      });
    }
    await ctx.close();
  });

  test('admin link table caps at 5 rows when the setting is 5', async ({
    page,
    errors,
  }) => {
    // Save 5 via the plugin's admin page.
    await page.goto(ADMIN_PATH);
    await page.locator('#links_per_page').fill('5');
    await page.locator('button.lpp-save').click();
    await page.waitForLoadState('networkidle');
    await expect(page.locator('[data-lpp-notice="success"]')).toContainText(
      /5 links per page/i
    );

    // YOURLS' admin link table shows N rows where N is the filtered value.
    await page.goto('/admin/index.php');
    const rowCount = await page
      .locator('#main_table tbody tr')
      .count();
    expect(rowCount).toBeLessThanOrEqual(5);
    expect(rowCount).toBeGreaterThan(0);

    expect(errors.serverErrors).toEqual([]);
  });

  test('raising the setting to 100 reveals every seeded shortlink', async ({
    page,
    errors,
  }) => {
    await page.goto(ADMIN_PATH);
    await page.locator('#links_per_page').fill('100');
    await page.locator('button.lpp-save').click();
    await page.waitForLoadState('networkidle');

    await page.goto('/admin/index.php');
    const rowCount = await page.locator('#main_table tbody tr').count();
    // We seeded TOTAL links; YOURLS may have other links from earlier specs,
    // but we should at least see all of ours.
    expect(rowCount).toBeGreaterThanOrEqual(TOTAL);

    expect(errors.serverErrors).toEqual([]);
  });
});

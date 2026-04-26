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

  // Save a value via the plugin admin form, then read back the actual
  // rows-per-page that YOURLS' admin link table is rendering. The table's
  // tbody can contain non-data rows depending on theme, so we count <tr>
  // elements that carry an id="id-..." (the data rows YOURLS emits per
  // shortlink) — that selector is theme-independent.
  async function savePerPageAndCountRows(
    page: import('@playwright/test').Page,
    value: number
  ): Promise<number> {
    await page.goto(ADMIN_PATH);
    await page.locator('#links_per_page').fill(String(value));
    await page.locator('button.lpp-save').click();
    await page.waitForLoadState('networkidle');
    // Either a success or no-change notice is fine — we just care about
    // the option being persisted with the right value.
    await expect(page.locator('#links_per_page')).toHaveValue(String(value));

    await page.goto('/admin/index.php');
    return page.locator('#main_table tbody tr[id^="id-"]').count();
  }

  test('admin link table is capped at 5 when the setting is 5', async ({
    page,
    errors,
  }) => {
    const rowCount = await savePerPageAndCountRows(page, 5);
    expect(rowCount, `Got ${rowCount} rows on /admin/index.php`).toBe(5);
    expect(errors.serverErrors).toEqual([]);
  });

  test('raising the setting reveals more rows', async ({ page, errors }) => {
    const rowCountSmall = await savePerPageAndCountRows(page, 5);
    const rowCountLarge = await savePerPageAndCountRows(page, 100);
    expect(rowCountLarge).toBeGreaterThan(rowCountSmall);
    // We seeded TOTAL links plus YOURLS' install seeds 3, so we should see
    // at least TOTAL rows once the cap is well above that.
    expect(rowCountLarge).toBeGreaterThanOrEqual(TOTAL);
    expect(errors.serverErrors).toEqual([]);
  });
});

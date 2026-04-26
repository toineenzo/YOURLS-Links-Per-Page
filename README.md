# YOURLS Links Per Page

A small [YOURLS](https://yourls.org/) plugin that lets you configure how many links the admin link table shows per page. Instead of being locked to YOURLS' default of 15 (or whatever the site option says), set the value you actually want from a dedicated settings page.

## Features

- **Configurable links per page**, from a clean settings page under *Manage Plugins → Links Per Page*.
- **Range-validated input** — only accepts integers between 1 and 999. Bad values fall back to a safe default.
- **CSRF-protected** — every submit is nonce-verified via `yourls_verify_nonce`.
- **No database schema changes** — the value is stored as a single YOURLS option (`links_per_page`).
- **Tested in CI** against PHP 8.4 + YOURLS 1.10, both with and without the [Sleeky-backend](https://github.com/Flynntes/Sleeky) admin theme active.

## Requirements

- **YOURLS** ≥ 1.9 (verified against 1.10)
- **PHP** ≥ 8.1 (verified against 8.4)
- A modern browser for the admin page.

## Installation

```bash
cd /path/to/yourls/user/plugins
git clone https://github.com/toineenzo/YOURLS-Links-Per-Page.git Links-Per-Page
```

…or download the latest release ZIP from the [Releases page](https://github.com/toineenzo/YOURLS-Links-Per-Page/releases) and unzip into `user/plugins/`. The zip already contains a `Links-Per-Page/` folder, so the final path is `user/plugins/Links-Per-Page/`.

Then open the YOURLS admin at `/admin/plugins.php` and click **Activate** on *Links Per Page*. A new sub-page shows up at *Manage Plugins → Links Per Page*.

## Usage

1. Go to `/admin/plugins.php?page=links_per_page_settings` (or click *Manage Plugins → Links Per Page*).
2. Type the number of links you want to see per page (1–999).
3. Click **Save settings**. A green confirmation banner appears, the YOURLS link table on `/admin/index.php` immediately uses the new value.

The plugin hooks into the `admin_view_per_page` filter, so any other plugin or theme that respects that filter (Sleeky-backend included) will pick up the value.

## Tests

There is a Playwright + Docker e2e suite under `tests/`. The CI workflow at `.github/workflows/release-test.yml` boots a YOURLS container on every push, mounts the plugin in, and runs the suite — both with and without Sleeky-backend installed.

To run it locally:

```bash
cd tests
npm install
docker compose up -d
npx playwright test
docker compose down -v
```

## License

MIT — see [LICENSE](LICENSE).

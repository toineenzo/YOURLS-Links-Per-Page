# Links Per Page

> Pick how many shortlinks YOURLS should show per page. One setting, one save button.

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Latest release](https://img.shields.io/github/v/release/toineenzo/YOURLS-Links-Per-Page?display_name=tag)](https://github.com/toineenzo/YOURLS-Links-Per-Page/releases)

<p align="center">
  <!-- TODO: drop a screenshot of the settings page in docs/screenshots/settings-page.png -->
  <img src="docs/screenshots/settings-page.png" alt="Links Per Page settings screen" width="640">
</p>

---

## What it does

YOURLS' admin link table caps at 15 rows per page out of the box. This plugin gives you a small settings screen where you can change that number to whatever you want (1–999) — and the change applies to *every* admin theme that respects YOURLS' standard `admin_view_per_page` filter, including [Sleeky](https://github.com/Flynntes/Sleeky).

<p align="center">
  <!-- TODO: docs/screenshots/admin-link-table.png — the YOURLS admin link table, showing more rows than the default -->
  <img src="docs/screenshots/admin-link-table.png" alt="YOURLS admin link table with the configured row count" width="720">
</p>

---

## Installation

1. Download `YOURLS-Links-Per-Page-vX.Y.Z.zip` from the [Releases page](https://github.com/toineenzo/YOURLS-Links-Per-Page/releases) and unzip into your `user/plugins/` folder. The final path should be `user/plugins/Links-Per-Page/`.
2. Open the YOURLS admin → *Manage Plugins* → click **Activate** on *Links Per Page*.

…or via git:

```bash
cd /path/to/yourls/user/plugins
git clone https://github.com/toineenzo/YOURLS-Links-Per-Page.git Links-Per-Page
```

---

## Usage

1. Go to *Manage Plugins → Links Per Page*.
2. Type a number between 1 and 999.
3. Click **Save settings**. The next time you load the YOURLS admin, the link table uses the new value.

That's it. No config files to edit, no database migration.

---

## Compatibility

- **YOURLS** 1.9 or newer (verified against 1.10).
- **PHP** 8.1 or newer (8.4 recommended).
- Works with any admin theme that respects YOURLS' `admin_view_per_page` filter — verified against vanilla YOURLS and the [Sleeky](https://github.com/Flynntes/Sleeky) theme on every release.

---

## Help & feedback

Bug or idea? [Open an issue](https://github.com/toineenzo/YOURLS-Links-Per-Page/issues) and mention your YOURLS + PHP version.

---

## License

[MIT](LICENSE).

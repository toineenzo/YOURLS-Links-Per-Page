# YOURLS Links Per Page

A simple YOURLS plugin that lets you configure the number of links displayed per page from the YOURLS admin interface. Instead of being locked to the default YOURLS value (15), you can permanently update this number using a dedicated configuration page.

## Features

- **Customizable links per page:** Set a custom number of links to display on each admin page.
- **User-friendly configuration:** Update the number via an simple admin page without needing to dig inside PHP files.
- **Input validation:** Only accepts numerical values between 1-999.
- **Feedback messages:** Displays a green success message when updated or a red error message if something goes wrong.

## Requirements

- **YOURLS** (Version 1.9.x or later is recommended)
- **PHP 7.4 or later** (tested with PHP 8.3)

## Installation

  1. **Download or clone repository**
  2. **Upload to your YOURLS:** Copy the plugin folder to your YOURLS plugins directory, typically located at ```/user/plugins/```.
  3. **Activate plugin:** Log in to your YOURLS admin area, go to the Plugins page, and activate the **Links Per Page** plugin.

## Usage

  Once activated, the plugin adds a new configuration page in the YOURLS admin area. To update the number of links per page:
  1. Navigate to the plugin’s configuration page: ```<YOURLS-SITE-DOMAIN>/admin/plugins.php?page=lpp_config```
  2. Enter the desired number (only numerical values, between 1-999) in the input field.
  3. Click **Save**.
  4. A success message in green will be displayed below the form if the update was successful, or an error message in red if the update failed.

## Support

If you encounter any issues or have suggestions for improvements, [please open an issue on GitHub](https://github.com/toineenzo/YOURLS-Links-Per-Page/issues/new).

## License

This plugin is open-sourced software licensed under the MIT License.

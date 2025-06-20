=== Manga AutoScraper ===
Contributors: yourname
Tags: manga, scraper, automation, wordpress
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin for automatically scraping manga from multiple sources and integrating with Mangareader themes.

== Description ==

Manga AutoScraper is a powerful WordPress plugin designed to work with Mangareader themes by Themesia. It automatically scrapes manga content from multiple sources, bypasses Cloudflare protection, and manages manga updates efficiently.

= Features =

* Multi-source support (manga1688.com, go-manga.com, niceoppai.net)
* Cloudflare bypass using 2captcha integration
* Automated scheduling (hourly, twice daily, daily)
* FTP/SFTP support for image storage
* Compatible with Mangareader themes
* Manual and automatic scraping options
* Detailed logging system
* User-friendly admin interface

= Requirements =

* WordPress 5.0 or higher
* PHP 7.2 or higher
* Mangareader theme by Themesia
* 2captcha API key (for Cloudflare bypass)
* FTP/SFTP access for image storage

== Installation ==

1. Upload the `manga-autoscraper` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Manga Scraper' in the admin menu
4. Configure the plugin settings:
   * Enter FTP/SFTP credentials
   * Add your 2captcha API key
   * Select scraping sources
   * Set scraping schedule

== Frequently Asked Questions ==

= How do I get a 2captcha API key? =

1. Register at [2captcha.com](https://2captcha.com)
2. Add funds to your account
3. Get your API key from the dashboard

= What FTP settings should I use? =

* Host: Your FTP server address
* Port: Usually 21 for FTP, 22 for SFTP
* Username: Your FTP username
* Password: Your FTP password
* Path: Directory where manga images will be stored

= How often should I run the scraper? =

The recommended schedule depends on your needs:
* Hourly: For frequent updates
* Twice daily: For regular updates
* Daily: For less frequent updates

= Can I add more manga sources? =

Currently, the plugin supports three sources:
* manga1688.com
* go-manga.com
* niceoppai.net

Additional sources can be added by modifying the plugin code.

== Screenshots ==

1. Plugin settings page
2. Manual scraping interface
3. Log viewer
4. Source configuration

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of Manga AutoScraper 
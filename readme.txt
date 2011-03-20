=== myRepono WordPress Backup Plugin ===
Plugin URI: http://myrepono.com/wordpress-backup-plugin/
Author: myRepono (ionix Limited)
Author URI: http://myRepono.com/
Contributors: ionix Limited, myRepono
Donate link: http://myRepono.com/wordpress-backup-plugin/
Tags: wordpress backup, backup, back-up, restore, restoration, remote, offline, online, website, file, database, mysql, automated, automatic
Requires at least: 3.0.0
Tested up to: 3.1
Stable tag: trunk


Automate your WordPress, website & database backups using the myRepono remote website backup service.



== Description ==

Automate your WordPress, website & database backups using the [myRepono WordPress Backup Plugin & Service](http://myrepono.com/wordpress-backup-plugin/ "myRepono WordPress Backup Plugin & Service").

[myRepono](http://myrepono.com/wordpress-backup-plugin/ "myRepono Website &amp; Database Backup Service") is an online website backup service which enables you to securely backup your WordPress web site files and mySQL database tables using an online and web-based management system.  The myRepono online website backup service allows you to automate the process of backing up your entire WordPress web site and database, including all post, comments and user data, and your WordPress PHP, template and plugin files.  

We provide an easy-to-install WordPress plugin which automates the myRepono API set-up and configuration process, enabling you to setup automated and remote website backups in a matter of minutes.

myRepono is a commercial backup service which uses a pay-as-you-go balance system.  Users receive $5 USD free credit to help them get started, and with prices starting at 2 cents per day that's enough free credit to backup most WordPress installations for several months!



== Installation ==

1. Create a directory called `myrepono-wordpress-backup-plugin` in your `/wp-content/plugins/` directory.

2. Upload `myrepono.php` file to the `/wp-content/plugins/myrepono-wordpress-backup-plugin/` directory.

3. Upload `img` directory to the `/wp-content/plugins/myrepono-wordpress-backup-plugin/` directory.

4. Upload `api` directory to the `/wp-content/plugins/myrepono-wordpress-backup-plugin/` directory.

5. Ensure `data` directory exists in `/wp-content/plugins/myrepono-wordpress-backup-plugin/api/` directory.

6. If using a Unix/Linux web server, ensure `/wp-content/plugins/myrepono-wordpress-backup-plugin/api/data/` directory is writable (e.g. permissions/chmod to `755` or `777`).

7. Activate the myRepono WordPress Backup Plugin through the 'Plugins' menu in WordPress.

8. Go to 'myRepono Backup' section of 'Settings' menu.



== Frequently Asked Questions ==

= Where can I find plugin information and documentation? =

Plugin Information: http://myRepono.com/wordpress-backup-plugin/
FAQ & Documentation: http://myRepono.com/faq/

= Is support available for this plugin? =

Yes, we provide comprehensive online support free of charge via our online helpdesk at: https://myRepono.com/contact/

= Plugin Requirements =

In addition to the standard WordPress requirements, the myRepono WordPress Backup plugin requires that your PHP `allow_url_fopen` configuration option is set to `on`.  In addition, the PHP curl and OpenSSL extension libraries must be installed and curl must be configured with SSL support.  The myRepono WordPress Backup Plugin includes a CURL Extension Emulation Library which the plugin/API will use if alternate HTTP/HTTPS connection methods fail, therefore PHP CURL support may not be required.



== Screenshots ==

1. Automate your WordPress, website & database backups using the myRepono remote website backup service.



== Changelog ==

= 1.0.0 =
First official release of plugin.

= 1.0.1 =

Minor adjustments to API installations.

= 1.0.2 =

Minor layout and content adjustments.

= 1.0.3 =

Minor adjustments to data caching.

= 1.0.4 =

General usability improvements and interface adjustments.

= 1.0.5 =

Domain selection feature added enabling users to view backups for multiple domains (added under same myRepono.com account), with a single WordPress plugin.  Additional adjustments to API installation process in preparation for next API version, and new re-authentication system added for log-in when account password has changed.

= 1.0.6 =

CRITICAL UPDATE RESOLVING ERROR WHEN UPDATING MYREPONO WORDPRESS BACKUP PLUGIN
When updating the myRepono WordPress Backup Plugin your myRepono Backup API is removed which will in-turn cause your backups to fail, this version of the plugin will automatically re-install the plugin and will notify you if it is unable to do so.

= 1.0.7 =

Minor usability improvements and interface adjustments.

= 1.0.8 =

Resolved additional error when updating myRepono WordPress Backup Plugin.  API is not automatically installed after plugin update, 1.0.6 update addressed this when user visited plugin but not if user did not access plugin section - this update will re-install the API without requiring the user to visit the plugin section of your WordPress administration panel.  The plugin will also notify admin when the API is automatically re-installed.

= 1.0.9 =

Support for CURL Extension Emulation Library added enabling WordPress Backup Plugin and myRepono API to make outgoing HTTP/HTTPS connections when standard CURL support is not available, plugin/API will now attempt a range of connection methods if default methdos fail.  Further changes to API/account set-up process to improve usability and customer understanding.

= 1.1.0 =

Removal of WordPress is_rtl function requirement which meant WordPress v3.0+ was required.  Additional adjustments to backup listings and backup status module to improve usability.



== Upgrade Notice ==

= 1.0.0 =
First official release of plugin.

= 1.0.1 =

Minor adjustments to API installations.

= 1.0.2 =

Minor layout and content adjustments.

= 1.0.3 =

Minor adjustments to data caching.

= 1.0.4 =

General usability improvements and interface adjustments.

= 1.0.5 =

Domain selection feature added enabling users to view backups for multiple domains (added under same myRepono.com account), with a single WordPress plugin.  Additional adjustments to API installation process in preparation for next API version, and new re-authentication system added for log-in when account password has changed.

= 1.0.6 =

CRITICAL UPDATE RESOLVING ERROR WHEN UPDATING MYREPONO WORDPRESS BACKUP PLUGIN
When updating the myRepono WordPress Backup Plugin your myRepono Backup API is removed which will in-turn cause your backups to fail, this version of the plugin will automatically re-install the plugin and will notify you if it is unable to do so.

= 1.0.7 =

Minor usability improvements and interface adjustments.

= 1.0.8 =

Resolved additional error when updating myRepono WordPress Backup Plugin.  API is not automatically installed after plugin update, 1.0.6 update addressed this when user visited plugin but not if user did not access plugin section - this update will re-install the API without requiring the user to visit the plugin section of your WordPress administration panel.  The plugin will also notify admin when the API is automatically re-installed.

= 1.0.9 =

Support for CURL Extension Emulation Library added enabling WordPress Backup Plugin and myRepono API to make outgoing HTTP/HTTPS connections when standard CURL support is not available, plugin/API will now attempt a range of connection methods if default methdos fail.  Further changes to API/account set-up process to improve usability and customer understanding.

= 1.1.0 =

Removal of WordPress is_rtl function requirement which meant WordPress v3.0+ was required.  Additional adjustments to backup listings and backup status module to improve usability.



=== myRepono WordPress Backup Plugin ===
Plugin URI: http://myrepono.com/wordpress-backup-plugin/
Author: myRepono (ionix Limited)
Author URI: http://myRepono.com/
Contributors: ionix Limited, myRepono
Donate link: http://myRepono.com/wordpress-backup-plugin/
Tags: wordpress backup, backup, back-up, restore, restoration, remote, offline, online, website, file, database, mysql, automated, automatic
Requires at least: 3.0.4
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

In addition to the standard WordPress requirements, the myRepono WordPress Backup plugin requires that your PHP `allow_url_fopen` configuration option is set to `on`.  In addition, the PHP curl and OpenSSL libraries must be installed and curl must be configured with SSL support.



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



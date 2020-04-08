=== WP Security Safe ===
Contributors: sovstack, stevenayers63
Tags: wp security, security audit, disable XMLRPC, limit-login, logs
Requires at least: 3.5
Requires PHP: 5.6
Tested up to: 5.3
Stable tag: trunk

This WordPress security plugin helps you quickly audit, harden, and secure your website.

== Description ==

### WP FIREWALL
* Detects and Logs Threats
* Add Firewall Rules to Whitelist and Blacklist IP Addresses With Internal Notes
* Historical Log of Firewall Blocks With Visual Chart

### WP LOGIN SECURITY
* Disable XML-RPC.php
* Brute Force Protection
* **[Pro]** Import/Export Settings
* **[Pro]** Priority Support

### WP PRIVACY
* Hide WordPress CMS Version
* Hide Script Versions
* Make Website Anonymous During Updates
* **[Pro]** Make Theme Versions Private
* **[Pro]** Make Plugin Versions Private

### WP CORE, THEME, AND PLUGIN FILE SECURITY
* Enable Automatic Core, Plugin, and Theme Updates
* Disable Editing Theme Files
* Audit & Fix File Permission
* **[Pro]** Bulk Fix File Permissions
* **[Pro]** Automatically Fix Theme/Plugin File Permissions

### OTHER FEATURES
* 404 Error Logging
* Content Copyright Protection
* Audit Hosting Software Versions
* Various Logs and Charts
* Turn On/Off All Security Policies Easily

Every WordPress security plugin becomes more complicated and bloated as more features are added. As a plugin's code grows, it consumes more time to load, thus slowing down your website. WP Security Safe's purpose is to protect your website from the majority of threats with minimal impact on website load time. We constantly test our load performance to ensure our features to ensure it continues to run fast and lean.

> Note: [Upgrade to WP Security Safe Pro](https://checkout.freemius.com/mode/dialog/plugin/2439/plan/3762/) to unlock advanced Pro features.

Twitter: [Follow WP Security Safe](https://twitter.com/wpSecuritySafe/)
Website: [WP Security Safe](https://wpsecuritysafe.com)

### LANGUAGE SUPPORT

* English (default)
* Spanish
* [Translate this plugin in your language.](https://translate.wordpress.org/projects/wp-plugins/security-safe)

== Videos ==

https://player.vimeo.com/video/360060065

== Screenshots ==

1. File Permissions
2. Login Attempts
3. Firewall Blocks

== Installation ==

1. Install WP Security Safe automatically or by uploading the ZIP file to your plugins folder. 
2. Activate the WP Security Safe on the 'Plugins' admin page. When activated the plugin settings are initially set minimum security values.
3. Navigate to the Plugin Settings by clicking on the WP Security Safe menu located on the left side admin panel.
4. On Plugin Settings page, you will notice an icon menu at the top of the page. Navigate through all of them and review and change settings as they pertain to your site's needs.
5. Test your site thoroughly. If you notice that your site is not functioning as expected, you can turn off each type of security policy (Privacy, Files, User Access, etc.) by navigating to each page and disabling the policy type. If necessary, you can disable all policy types at once on Plugin Settings page.
6. If you are having issues, reach out for help in the forum before leaving a review. 

== Changelog ==

= 2.3.1 (High Priority) =
*Release Date - 05 January 2020*

* Bug Fix: version privacy for JS files conflicted with Google Recaptcha. Thank you [Lynn Appleget](https://applegetassoc.com/) for reporting this bug.
* Bug Fix: Plugin updates were not getting logged properly after an update.
* Bug Fix: Plugin would not initialize in a multi-site network.
* Bug Fix: Prevent caching of nonce for front-end login form
* Bug Fix: Some 404s were getting detected before a WP redirect was happening.
* Improvement: Fixed PHP Notices
* Improvement: Updated PHP version checks
* Improvement: PHP version comparison logic improved
* Improvement: Increase performance by reducing unnecessary method calls
* Improvement: Updated SDK
* Tested up to: 5.4

= 2.3.0 (Low Priority) =
*Release Date - 13 November 2019*

* Bug Fix: Administrator role was prevented from right-clicking and highlighting when these content protection features were enabled. This role should be excluded from these policies.
* Bug Fix: Fixed typo which had no affect on functionality due to fallback check.
* Improvement: Changed default settings to include "Make Website Anonymous" during updates and "Prevent WordPress version files from public access".
* Improvement: Minor performance enhancements
* Increase PHP version requirement to match WordPress core.
* Tested up to: 5.3

= 2.2.3 (High Priority) =
*Release Date - 21 October 2019*

* Bug Fix: Local Login feature would not allow logins via front-end login forms created with wp_login_form(). Thank you @alfonsoborghi for the bug report.
* Bug Fix: An admin notice was not properly counting directories with OK permissions on the Files admin page.
* Bug Fix: Stats were attempting to record during system activities and thus throwing "WordPress database error Duplicate entry"
* Bug Fix: Search and bulk delete on the Firewall Allow/Deny admin page would trigger false flag admin errors regarding IP validation.
* Bug Fix: Sort filters on the Firewall admin page would trigger false flag admin notices.
* Bug Fix: Body class was being added to every page in the admin.
* Bug Fix: Duplicate policy disabled admin notices were appearing on admin pages using wp_list_table()
* Security: Added nonce to reset and save settings
* Security: Added nonce to add / remove Firewall rules
* Improvement: Renamed nonces to prevent conflicts with other plugins
* Improvement: Performance tuning to reduce function calls
* Improvement: Changed default settings to include disabling XML-RPC and force Local Logins.
* Improvement: Fixed a PHP Warning.
* Improvement: Updated PHP version checks
* Tested up to: 5.2.4

= 2.2.2 (Medium Priority) =
*Release Date - 09 September 2019*

* Bug Fix: Cron cleanup scripts were failing.
* Improvement: Fixed two PHP errors.

= 2.2.1 (Medium Priority) = 
*Release Date - 05 September 2019*

* Updated Feature: The local login feature was improved to be more reliable.
* Bug Fix: The local login feature was causing server errors on Pantheon servers. Thanks FullSteam Labs for the bug report.
* Bug Fix: The blacklist check was not functioning properly.
* Bug Fix: The sidebar was appearing on tabs that were full width of the screen.
* Bug Fix: The charts would not load in a local development environment without an active internet connection.
* Bug Fix: Fixed minor styling anomalies when viewing admin in Spanish
* Pro Bug Fix: File corrupt error displayed if imported settings already matched the current settings.
* Improvement: Added more i18n language support.
* Improvement: The form that adds an IP to the firewall is more user-friendly
* Improvement: Added ability to make notes when manually adding IPs to the firewall
* Improvement: Fixed some minor PHP notices.
* Improvement: Added 'Status' column and filter to Firewall page.
* Improvement: Added additional information to the 'Details' column.
* Improvement: Converted the Firewall page to include all detected threats
* Improvement: Added Spanish Translations
* Improvement: Updated logo and minor styling
* Improvement: Updated PHP version checks
* Security: Added additional sanitization for logging
* Tested with ManageWP Version 4.9.1
* Tested up to: 5.2.3

= 2.1.1 (Medium Priority) = 
*Release Date - 15 July 2019*

* Bug Fix: Session handling conflicted with some admin features in oddball scenarios
* Improvement: Fixed a PHP Warning

= 2.1.0 (Medium Priority) = 
*Release Date - 15 July 2019*

* Bug Fix: WP Cron activities were not recording to activity log (Only visible in debug mode)
* Bug Fix: Charts do not display properly until an entry has been initially added to stats.
* Bug Fix: Styling issue with wp_table_list pagination
* Bug Fix: Search field not working on log tables
* Bug Fix: Admin notices would not display for policies that were disabled or if wp cron was disabled using DISABLE_WP_CRON.
* Bug Fix: The admin notices were not displaying bold properly
* Improvement: Fixed some PHP notices, thanks to Charles Suggs
* Improvement: Excluded user roles super admin, administrator, editor, and author from text highlighting and right-click content protection while logged in
* Improvement: Updated SDK
* Improvement: Implemented better session handling for increased load performance
* Improvement: Added more i18n language support.

= 2.0.2 (High Priority) = 
*Release Date - 10 June 2019*

* Improvement: In some outlying circumstances, the DB tables do not get created. A failsafe was added to create the tables if the insertion of a record failed.
* Bug Fix: The new DB tables get created if the plugin is disabled and then enabled, but not after an update process.

= 2.0.0 (Low Priority) = 
*Release Date - 10 June 2019*

* Bug Fix: Security Safe would unintentionally recommend a lower version of PHP if the user had a newer version higher than the known versions.
* Added Feature: Log 404 Errors
* Added Feature: Log Successful and Failed Logins
* Added Feature: Manage Blacklist / Whitelist IP Addresses
* Added Feature: Log Blocked Access Attempts
* Added Feature: Log Security Vulnerability Probing
* Added Feature: Statistics and Charts
* Improvement: Force Local Logins setting now records blocked attempts.
* Improvement: Cleaned up some PHP Notices in error log.
* Improvement: Updated namespacing to support future plugins
* Improvement: Updated directory structure for better scalability
* Improvement: Minor code standardization updates
* Improvement: Performance testing and optimization
* Improvement: Minor styling updates
* Improvement: Updated PHP version checks
* Security: Added additional security to prevent XSS
* Tested up to: 5.2.1

= 1.2.3 (High Priority) = 
*Release Date - 01 March 2019*

* Security: Updated SDK
* Improvement: Updated PHP version checks
* Tested up to: 5.1

> NOTE: [View full WP Security Safe changelog](https://sovstack.com/wp-security-safe/changelog/).

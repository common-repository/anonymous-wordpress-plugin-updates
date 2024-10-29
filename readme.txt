=== Anonymous WordPress Plugin Updates ===
Contributors: f00f
Tags: anonymize, plugin, updates, security, privacy, admin
Requires at least: 2.3
Tested up to: 2.3
Stable tag: 1.0

Anonymizes the data transmitted during plugin update check.

== Description ==

Anonymizes the plugin update checking system which is a new feature in WordPress 2.3. The plugin prevents WordPress from transmitting a list of active plugins, the blog url and WordPress version. Ideal for privacy-aware administrators of WordPress installations.

== Installation ==

This plugin is only for WordPress 2.3 or later. Earlier versions of WordPress did not have the plugin update notification system, therefore do not need this plugin.

The plugin has two ways of installation, because on fresh 2.3 installations the update-check will take place before you were able to activate the plugin.

1. `update.php` is a patched version of `wp-admin/includes/update.php`
	1. Do the upgrade to WordPress 2.3
	2. Make a backup of your `wp-admin/includes/update.php` file.
	3. Copy the patched version into your `wp-admin/includes/` directory.
	4. Now you can use the `plugins` admin page savely.
	5. Note: The file will get overwritten during your next WP-Update, so do also install the plugin.
	
2. `anonymous-plugin-updates.php` is the actual plugin.
	1. Unzip the ZIP file and drop the folder straight into your `wp-content/plugins/` directory.
	2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Why would I want to anonymize the plugin update system? =

Because you don't want anyone to know which plugins you have actually activated.

= Can I disable the WordPress plugin update notifications completely? =

Yes. Please see the [Disable WordPress Plugin Updates](http://wordpress.org/extend/plugins/disable-wordpress-plugin-updates/) plugin.
Please note! It's important that you keep your WordPress plugins up to date. If you don't, your blog or website could be susceptible to security vulnerabilities or performance issues.

= Can I disable the WordPress core update notifications too? =

Yes. Please see the [Disable WordPress Core Update](http://wordpress.org/extend/plugins/disable-wordpress-core-update/) plugin.
Please note! It's important that you keep your WordPress core up to date. If you don't, your blog or website could be susceptible to security vulnerabilities or performance issues.

== Based upon ==

Disable WordPress Plugin Updates by johnbillion
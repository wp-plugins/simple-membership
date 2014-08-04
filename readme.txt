=== Simple Membership ===
Contributors: smp7, wp.insider
Donate link: https://simple-membership-plugin.com/
Tags: member, members, members only, membership, memberships, register, WordPress membership plugin, content protection, paypal, restrict access, Restrict content, admin, access control, subscription
Requires at least: 3.3
Tested up to: 3.9.1
Stable tag: 1.7.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple membership plugin adds membership functionality to your site. Protect members only content using content protection easily.

== Description ==

= A flexible, well-supported, and easy-to-use WordPress membership plugin for offering free and premium content from your WordPress site =

The simple membership plugin lets you protect your posts and pages so only your members can view the protected content.

= Unlimited Membership Access Levels =
Set up unlimited membership levels (example: free, silver, gold etc) and protect your posts and pages using the membership levels you create.

= User Friendly Interface for Content Protection = 
When you are editing a post or page in the WordPress editor, you can select to protect that post or page for your members.

Non-members viewing a protected page will be prompted to login or become a member.

= Have Free and Paid Memberships =
You can configure it to have free and/or paid memberships on your site. Paid membership payment is handled securely via PayPal. 

Both one time and recurring/subscription payments are supported.

= Member Login Widget on The Sidebar =
You can easily add a member login widget on the sidebar of your site. Simply use the login form shortcode in the sidebar widget.

= Documentation =

Read the [setup documentation](https://simple-membership-plugin.com/wp-content/uploads/documentation/simple-membership/membership-basic-setup-and-configuration.pdf) after you install the plugin to get started.

= Plugin Support =

If you have any issue with this plugin, please visit the plugin site and post it on the support forum or send us a contact:
https://simple-membership-plugin.com/

You can create a free forum user account and ask your questions.

= Miscellaneous =

* Works with any WordPress theme.
* Ability to protect photo galleries.
* Comments on your protected posts will also be protected automatically.
* There is an option to enable debug logging so you can troubleshoot membership payment related issues easily (if any).
* Membership management side is handled by the plugin.
* Can be translated to any language.
* Hide the admin toolbar from the frontend of your site.
* The login and registration widgets will be responsive if you are using a responsive theme.

== Installation ==

Do the following to install the membership plugin:

1. Upload the 'simple-wp-membership.zip' file from the Plugins->Add New page in the WordPress administration panel.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

None.

== Screenshots ==

Please visit the memberhsip plugin page to view screenshots:
https://simple-membership-plugin.com/

== Changelog ==

= 1.7.5 = 
- Fixed an issue with language file loading.

= 1.7.4 =
- Added capability to use any of the shortcodes (example: Login widget) in the sidebar text widget.

= 1.7.3 =
- Added french language translation to the plugin. The translation was submitted by Zeb.
- Fixed a few language textdomain issue.
- Fixed an issue with the the registration and login page shortcode (On some sites the registration form wasn't visible.)
- Added simplified Chinese language translation to the plugin. The translation was submitted by Ben.

= 1.7.2 =
- Added a new hook after the plugin's admin menu is rendered so addons can hook into the main plugin menu.
- Fixed another PHP 5.2 code compatibility issue.
- Fixed an issue with the bulk member delete functionality.

= 1.7.1 =
- Fixed another PHP 5.2 code compatibility issue.
- Updated the plugin's language file template.

= 1.7 = 
- Tweaked code to make it compatible with PHP 5.2 (previously PHP 5.3 was the requirement).
- Added checks for checking if a WP user account already exists with the chosen username (when a member registers).
- Fixed a few translation strings.

= 1.6 =
- Added comment protection. Comments on your protected posts will also be protected automatically.
- Added a new feature to hide the admin toolbar for logged in users of the site.
- Bug fix: password reset email not sent correctly
- Bug fix: page rendering issue after the member updates the profile.

= 1.5.1 = 
- Compatibility with the after login redirection addon:
http://wordpress.org/plugins/simple-membership-after-login-redirection/

= 1.5 =
- Fixed a bug with sending member email when added via admin dashboard.
- Fixed a bug with general settings values resetting.
- Added a few action hooks to the plugin.

= 1.4 =
- Refactored some code to enhance the architecture. This will help us add some good features in the future.
- Added debug logger to help troubleshoot after membership payment tasks.
- Added a new action hook for after paypal IPN is processed.

= 1.3 =
- Fixed a bug with premium membership registration.

= 1.2 =
- First commit to WordPress repository.

== Upgrade Notice ==

None

== Arbitrary section ==

None

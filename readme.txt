=== Simple Membership ===
Contributors: smp7, wp.insider
Donate link: https://simple-membership-plugin.com/
Tags: member, members, members only, membership, memberships, register, WordPress membership plugin, content, content protection, paypal, restrict access, Restrict content, admin, access control, subscription, teaser, protection
Requires at least: 3.3
Tested up to: 4.0
Stable tag: 1.8.6
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

Read the [setup documentation](https://simple-membership-plugin.com/simple-membership-documentation/) after you install the plugin to get started.

= Plugin Support =

If you have any issue with this plugin, please visit the plugin site and post it on the support forum or send us a contact:
https://simple-membership-plugin.com/

You can create a free forum user account and ask your questions.

= Miscellaneous =

* Works with any WordPress theme.
* Ability to protect photo galleries.
* Show teaser content to convert visitors into members.
* Comments on your protected posts will also be protected automatically.
* There is an option to enable debug logging so you can troubleshoot membership payment related issues easily (if any).
* Ability to customize the content protection message that gets shown to non-members.
* Membership management side is handled by the plugin.
* Ability to import WordPress users as members.
* Can be translated to any language.
* Hide the admin toolbar from the frontend of your site.
* The login and registration widgets will be responsive if you are using a responsive theme.

= Language Translations =

The following language translations are already available:

* English
* Spanish
* German
* French
* Chinese
* Portuguese (Brazil)
* Swedish

You can translate the plugin using the language [translation documentation](https://simple-membership-plugin.com/translate-simple-membership-plugin/).

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

= 1.8.6 =
- Fixed an email validation issue with paid membership registration process.
- Added a new free addon to customize the protected content message.

= 1.8.5 =
- Added category protection feature under the membership level menu.
- Fixed a bug with paid membership paypal IPN processing code.

= 1.8.4 =
- The Password field won't use the browser's autofill option in the admin interface when editing a member info.

= 1.8.3 =
- Added Swedish language translation to the plugin. The translation was submitted by Geson Perry.
- There is now a cronjob in the plugin to expire the member profiles in the background.
- Released a new addon - https://simple-membership-plugin.com/simple-membership-registration-form-shortcode-generator/
- Added a menu called "Add-ons" for listing all the extensions of this plugin.

= 1.8.2 =
- Updated the members expiry check code at the time of login and made it more robust.

= 1.8.1 =
- MySQL database character set and collation values are read from the system when creating the tables.
- Added German language translation file to the plugin.
- Some code refactoring work.
- Added a new feature to allow admins to create a registration form for a particular membership level.

= 1.8.0 =
- Added a new feature called "more tag protection" to enable teaser content. Read the [teaser content documentation](https://simple-membership-plugin.com/creating-teaser-content-membership-site/) for more info.
- Added Portuguese (Brazil) language translation to the plugin. The translation was submitted by Rachel Oakes.
- Added cookiehash definition check (in case it is not defined already).

= 1.7.9 =
- Added Spanish language translation to the plugin. The translation was submitted by David Sanchez.
- Removed some hardcoded path from the auth class.
- WordPress 4.0 compatibility

= 1.7.8 =
- Architecture improvement for the [WP User import addon](https://simple-membership-plugin.com/import-existing-wordpress-users-simple-membership-plugin/)
- Updated the POT file with the new translation strings

= 1.7.7 =
- The plugin will now show the member account expiry date in the login widget (when a user is logged into the site).
- Added a couple of filters to the plugin.

= 1.7.6 =
- Fixed an issue with hiding the admin-bar. It will never be shown to non-members.
- Renamed the chinese language file to correct the name.
- Removed a lot of fields from the front-end registration form (after user feedback). The membership registration form is now a lot simpler with just a few fields.
- Fixed a bug with the member search option in the admin dashboard.
- Added a few new action hooks and filters.
- Fixed a bug with the media attachment protection.

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
If you are using the form builder adddon, then that addon will need to be upgraded to v1.1 also.

== Arbitrary section ==
None

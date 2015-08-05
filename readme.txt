=== Simple Membership ===
Contributors: smp7, wp.insider, amijanina
Donate link: https://simple-membership-plugin.com/
Tags: member, members, members only, membership, memberships, register, WordPress membership plugin, content, content protection, paypal, restrict, restrict access, Restrict content, admin, access control, subscription, teaser, protection, profile, login, login page,
Requires at least: 3.3
Tested up to: 4.3
Stable tag: 3.0.3
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

= Membership Payments Log = 
All the payments from your members are recorded in the plugin. You can view them anytime by visiting the payments menu from the admin dashboard.

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
* Ability to manually approve your members.
* Ability to import WordPress users as members.
* Can be translated to any language.
* Hide the admin toolbar from the frontend of your site.
* Allow your members to deleter their membership accounts.
* Send quick notification email to your members.
* Customize the password reset email for members.
* The login and registration widgets will be responsive if you are using a responsive theme.
* Front-end member registration page.
* Front-end member profiles.
* Front-end member login page.

= Language Translations =

The following language translations are already available:

* English
* Spanish
* German
* French
* Chinese
* Portuguese (Brazil)
* Portuguese (Portugal)
* Swedish
* Macedonian
* Polish
* Turkish
* Russian
* Dutch (Netherlands)
* Romanian
* Danish
* Lithuanian
* Serbian
* Japanese

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

= 3.0.3 =
- Increased the database character limit size of the user_name field.
- Refactored the 'swpm_registration_form_override' filter.
- Added integration with iDevAffiliate.
- Added integration with Affiliate Platform plugin.

= 3.0.2 =
- Added a new shortcode that can be used on your thank you page. This will allow your users to complete paid registration from the thank you page after payment.
- The last accessed from IP address of a member is shown to the admin in the member edit screen.
- The debug log (if enabled) for authentication request is written to the "log-auth.txt" file.
- Fixed a bug with the bulk member delete option from the bottom bulk action form.
- Fixed a bug with the bulk membership level delete option from the bottom bulk action form.

= 3.0.1 =
- Added a new CSS class to the registration complete message.
- Added Portuguese (Portugal) language translation file. The translation was submitted by Edgar Sprecher.
- Replaced mysql_real_escape_string() with esc_sql()
- Members list in the admin is now sorted by member_id by default.
- Added a new filter in the registration form so Google recaptcha can be added to it.

= 3.0 =
- Updated the swedish langauge translation
- Added a new option to enable opening of the PayPal buy button in a new window (using the "new_window" parameter in the shortcode).
- You can now create and configure PayPal Subscription button for membership payment from the payments menu.

= 2.2.9 =
- Added a new feature to customize the password reset email.
- Added a new feature to customize the admin notification email address.
- Improved the help text for a few of the email settings fields.
- Updated the message that gets displayed after a member updates the profile.

= 2.2.8 =
- Updated the swedish language translation file.
- Code refactoring: moved all the init hook tasks to a separate class.
- Increased the size of admin nav tab menu items so they are easy to see.
- Made all the admin menu title size consistent accross all the menus.
- Updated the admin menu dashicon icon to a nicer looking one.
- You can now create and configure PayPal buy now button for membership payment from the payments menu.

= 2.2.7 =
- Added Japanese language translation to the plugin. The translation was submitted by Mana.
- Added Serbian language translation to the plugin. The translation was submitted by Zoran Milijanovic.
- All member fields will be loaded in the edit page (instead of just two).

= 2.2.6 =
- Fixed an issue with the category protection menu after the class refactoring work.
- Fixed the unique key in the DB table

= 2.2.5 =
- Refactored all the class names to use the "swpm" slug to remove potential conflict with other plugins with similar class names.

= 2.2.4 =
- Fixed an issue with not being able to unprotect the category protection.
- Minor refactoring work with the classes.

= 2.2.3 =
- Updated the category protection interface to use the get_terms() function.
- Added a new Utility class that has some helpful functions (example: check if a member is logged into the site). 

= 2.2.2 =
- All the membership payments are now recorded in the payments table.
- Added a new menu item (Payments) to show all the membership payments and transactions.
- Added Lithuanian language translation to the plugin. The translation was submitted by Daiva Pakalne.
- Fixed an invalid argument error.

= 2.2.1 =
- Added a new table for logging the membership payments/transactions in the future.
- Made some enhancements in the installer class so it can handle both the WP Multi-site and single site setup via the same function.

= 2.2 =
- Added a new feature to allow expired members to be able to log into the system (to allow easy account renewal).
- The email address value of a member is now editable from the admin dashboard and in the profile edit form.
- Added CSS classes around some of the messages for styling purpose.
- Some translation updates.

= 2.1.9 =
- Improved the password reset functionality.
- Improved the message that gets displayed after the password reset functionality is used.
- Updated the Portuguese (Brazil) language file.
- Improved the user login handling code.

= 2.1.8 =
- Improved the after logout redirection so it uses the home_url() value.
- Fixed a bug in the member table sorting functionality.
- The members table can now be sorted using ID column.


= 2.1.7 =
- Added a new feature to automatically delete pending membership accounts that are older than 1 or 2 months.
- Fixed an issue with the send notification to admin email settings not saving.

= 2.1.6 =
- Fixed a bug with new membership level creation with a number of days or weeks duration value.

= 2.1.5 =
- Improved the attachment protection so it doesn't protect when viewing from the admin side also.
- Removed a dubug dump statement.

= 2.1.4 =
- Improved the login authentication handler logic.
- Fixed the restricted image icon URL.
- Updated the restricted attachment icon to use a better one.

= 2.1.3 =
- Added a new feature to allow the members to delete their accounts.

= 2.1.2 =
- Updated the membership subscription payment cancellation handler and made it more robust.
- Added an option in the settings to reset the debug log files.

= 2.1.1 =
- Enhanced the username exists function query.
- Updated one of the notice messages.

= 2.1 =
- Changed the PHP short tags to the standard tags
- Updated a message in the settings to make the usage instruction clear.
- Corrected a version number value.

= 2.0 =
- Improved some of the default content protection messages.
- Added Danish language translation to the plugin. The translation was submitted by Niels Boje Lund.

= 1.9.9 =
- WP Multi-site network activation error fix.

= 1.9.8 =
- Fixed an issue with the phone number not saving.
- Fixed an issue with the new fixed membership expiry date feature.

= 1.9.7 =
- Minor UI fix in the add new membership level menu.

= 1.9.6 =
- Added a new feature to allow fixed expiry date for membership levels.
- Added Russian language translation to the plugin. The translation was submitted by Vladimir Vaulin.
- Added Dutch language translation to the plugin. The translation was submitted by Henk Rostohar.
- Added Romanian language translation to the plugin. The translation was submitted by Iulian Cazangiu.
- Some minor code refactoring.

= 1.9.5 =
- Added a check to show the content of a protected post/page if the admin is previewing the post or page.
- Fixed an issue with the quick notification email feature not filtering the email shortcodes.
- Improved the login form's HTML and CSS.

= 1.9.4 =
- Added a new feature to send an email notification to a member when you edit a user's record. This will be helpful to notify members when you activate their account.
- Fixed an issue with "pending" member account getting set to active when the record is edited from admin side.

= 1.9.3 =
- Fixed an issue with the featured image not showing properly for some protected blog posts.

= 1.9.2 =
- Fixed the edit link in the member search interface.

= 1.9.1 =
- Added Turkish language translation to the plugin. The translation was submitted by Murat SEYISOGLU.
- WordPrss 4.1 compatibility.

= 1.9.0 =
- Fixed a bug in the default account setting option (the option to do manual approval for membership).
- Added Polish language translation to the plugin. The translation was submitted by Maytki.
- Added Macedonian language translation to the plugin. The translation was submitted by I. Ivanov.

= 1.8.9 =
- Added a new feature so you can set the default account status of your members. This can useful if you want to manually approve members after they signup.

= 1.8.8 =
- Fixed an issue with the account expiry when it is set to 1 year.

= 1.8.7 =
- Updated the registration form validation code to not accept apostrophe character in the username field.
- Added a new tab for showing addon settings options (some of the addons will be able to utilize this settings tab).
- Added a new action hook in the addon settings tab.
- Moved the plugin's main class initialization code outside of the plugins_loaded hook.

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

=== WP Invoices Ultimate ===
Contributors: nohalfpixels
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DVEW7VSBB8NYS
Tags: invoicing, payment, paypal, paypal ipn, money, shop, e commerce
Requires at least: 3.3
Tested up to: 3.3
Stable tag: 0.1.6

Simple to use invoicing system that can intergrate with Paypal. Very simple, very flexble.

== Description ==

The WP Invoices Ultimate plugin is not an attempt of world domination invoicing system.

The aim is to provide a system which is:

* Simple
* Fast
* Easy to configure
* Set and forget

WPIU Has very few options, which include a little bit about you business, notification email subject customisation, and your paypal information.

WPIU uses Paypal, and manual payments. Right now there is no agenda to add support for other gateways, so if you need multiple gateways please look elsewhere.

WPIU uses the Paypal IPN system and can update the "paid" amounts on each invoice when a user pays for it (part payments can be made).

The Paypal transactions are stored with each invoice and can be accessed both in the admin area, and on the invoice page (all paypal data captured).

Options foreach invoice include:

* Title
* Description
* Due Date
* Job Number
* Invoice Number (randomly generated, can be overidden)
* Paid Amount
* Client
* Send Email / Reminder
* Invoice Items (title / qty / unit cost / item total)

All invoice items are added up when the invoice is saved (not through javascript for accuracy).


Page template can be overridden by adding a `single-wpiu-invoices.php` file to the current theme.

Included page template includes and uses the twitter bootstrap css framework http://twitter.github.com/bootstrap/ for simple styling.

Plugin is still in beta right now and has a few limitations:

* Only global tax rule apply, no per invoice tax percentage - available soon


Please see my other plugins / projects / portfolio here http://no-half-pixels.com - always on the lookout for new ventures.


== Installation ==

1. Upload `wp-invoices-ultimate.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Fill out the required options on the invoice options page

== Frequently Asked Questions ==

= When will Version 1 come out =

Not sure yet, i need to use the system for a while before i decide on a final full release

== Screenshots ==

1. The invoice page seen by the client. Simple styling, options to pay invoice and to view previous transactions.
2. The list of invoices, can be filtered like normal post by date created and invoice title.
3. The invoice edit screen, containing all invoice data and options, all the same as a normal post.
4. The invoicing system options panel, kept as simple as possible.

== Changelog ==

= 0.1.6 =
* bug fix for date metabox in invoice edit screen

= 0.1.5 =
* tagged forum topics properly

= 0.1.4 =
* bug fixes

= 0.1.3 =
* fixed fatal error - required file names where wrong

= 0.1.2 =
* bug fixed which prevents invoices being viewed through email

= 0.1.1 =
* added all paypal currency codes for use
* added helper function which converts currency code to currency symbol for use in template
* added ipn log option update the paypal gateway
* added localisation for page titles (missed in first version)
* added paypal ipn log page with all transactions in table for debugging and overview of activity
* added help tab for fixing 404 errors when creating invoices
* fixed 404 for invoices on plugin activation
* newly created invoices get a more semantic invoice number (yearmonthdayhourminutesecond) - better for referencing when in use (looking for an invoice from 2011 will have an invoice number starting 2011, etc)
* fixed some php notices/bugs

= 0.1.0 =
* nothing
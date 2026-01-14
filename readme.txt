=== WP Datetime Difference Shortcode ===
Contributors: chrissimmons
Tags: shortcode, date, datetime, utility
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 7.2
Stable tag: 1.0.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Provides a simple shortcode to output the difference between two dates.

== Description ==
WP Datetime Difference Shortcode provides the following shortcode:

[DatetimeDifference startDate="01.07.1989" endDate="now" format="Y"]

It outputs the difference between two dates in:
- Years (Y)
- Total months (M)
- Total days (D)

This plugin is intended as a secure replacement for the 
Date Counter plugin.

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins menu
3. Use the shortcode in posts or pages

== Usage ==
Example:

[DatetimeDifference startDate="01.07.1989" endDate="now" format="Y"]

Formats:
- Y = full years
- M = total months
- D = total days

== Changelog ==
= 1.0.1 =
* Security hardening
* Restricted output to numeric formats only
* Improved date parsing validation

= 1.0.0 =
* Initial release
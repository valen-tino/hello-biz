=== Hello Biz (for Blossem Group) ===

Contributors: elementor, KingYes, ariel.k
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.2.7
Version: 1.2.7
Requires PHP: 7.4
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A lightweight and minimalist WordPress theme for Elementor site builder.

== Description ==

Modified by Valentino Jehaut (Blossem Group)

This theme is modified from the original Hello Biz Theme. 

= Added Functions, Shortcodes & Widgets =

Functions:
* Auto-assign Verhuurd Agent
* Property Live Filter (AJAX with multi-select & range support)
* Recently Viewed Properties (AJAX)
* European Number Formatting for Price Fields (ACF)
* Category support for Pages
* LCP Background Image Preloading (PageSpeed optimization)
* Font Display Swap for Proxima Nova
* TTF to WOFF2 Font Replacement
* Property Search Cache Invalidation
* Blossem Registration Admin Meta Box

Shortcodes:
* [secure_email] - Protected email link
* [acf_taxonomy_terms] - Display ACF taxonomy terms
* [property_status_tags] - Display availability & housing type tags
* [availability_status_tag] - Display availability status tag
* [housing_type_tag] - Display housing type tag
* [location_taxonomy] - Display location taxonomy terms
* [custom_excerpt] - Truncate content with customizable length
* [multistep_blossem_form] - Multi-step rental registration form
* [scf_tax_display] - Display SCF taxonomy terms

Elementor Widgets:
* Unit Type Showcase
* Project Floorplan Tabs
* Project Properties Table
* Property Search Form
* Property Search Results
* Property Count
* Project Showcase
* Recently Viewed Properties
* Map Previewer
* ACF Repeater Loop
* ACF Linked Post

Elementor Dynamic Tags:
* ACF Repeater Text
* ACF Repeater Image
* ACF Linked Text
* ACF Linked URL

= Performance Optimizations =
* LCP background image preloading for .main-hero containers
* Font-display: swap for improved PageSpeed scores
* TTF font detection and replacement with optimized WOFF2
* Smart caching for property search options with automatic invalidation

Report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team validates, triages, and handles vulnerabilities. [Report here](https://patchstack.com/database/wordpress/theme/hello-biz/vdp)

== Changelog ==

= 1.2.7 (2026-02-10) =
* Added: `filter_visible_projects` Elementor query filter to hide projects where `hide_on_project_page` is checked
* Added: Redirect hidden projects to `/project/` page when accessed directly via URL

= 1.2.6 (2026-02-08) =
* Fixed: Property Search Form dropdown overlapping issue (z-index fix)
* Fixed: Property Search Form location taxonomy filter not working (race condition fix)
* Fixed: Property Search Form project taxonomy filters (Parent Project) now correctly query projects first
* Improved: Property Search Form dropdown dynamically adjusts width based on content
* Improved: Property Search Form dropdown list items styling (nowrap, gap)
* Added: Reset cache after commit (Hummingbird & WP Pusher)
* Updated: Multistep form loading spinner to dual-ring style (Blue #3b82f6)

= 1.2.5 (2026-02-07) =
* Added: Rate limiting to multistep form (1 submission per 30 seconds per IP)
* Added: Export to TXT functionality for registration data
* Improved: Multistep form performance with async email sending via WP-Cron
* Improved: Added immediate Dutch processing notice on form submission
* Updated: Removed WPForms dependency and translated all form components to Dutch
* Updated: `[secure_email]` shortcode default email to info@blossemgroup.nl
* Override: Default Elementor global colors (primary, secondary, text, accent) set to black via CSS variables
* Override: Default link color set to black via global CSS rule

= 1.2.4 (2026-02-06) =
* Search Widget: Improved mobile UI (black toggle, vertical layout, tablet support up to 1024px)
* Search Widget: Replaced toggle icon with animated inline SVG
* Recently Viewed: Fixed mobile layout (auto height, correct image width)
* Recently Viewed: Updated Bathroom and Bedroom icons (custom SVGs with thicker strokes)
* Recently Viewed: Changed default background color to transparent
* Project Floorplan Tabs: Refactor the code to remove minor stuff

= 1.2.2 (2026-02-05) =
* Added: Blossem Multistep Form with dynamic dropdown support for real-time content mapping
* Refactor: De-cluttered `functions.php` by moving logic to `includes/enqueue.php`, `includes/performance.php`, and `includes/helpers.php`
* Refactor: Optimized widget asset loading using `get_script_depends` and `get_style_depends`
* Refactor: Extracted inline CSS/JS from all widgets to separate asset files
* Fixed: Duplicate HTML output in Property Results widget
* Fixed: PHP syntax error in Recently Viewed Properties widget
* Fixed: Removed visible debug comments from frontend

= 1.2.1 (2026-02-05) =
* Added: Dynamic taxonomy options for multistep form (type-of-project, furnish_status)
* Added: Registration Details meta box in WordPress admin for blossem_registration CPT
* Added: Dutch translations for registration form and admin meta box
* Fixed: "postal_code not focusable" error by adding novalidate to multistep form
* Fixed: AJAX response handling for form submission
* Fixed: interested_in field sanitization (changed from array to string)
* Improved: Number of rooms dropdown expanded to 1-10

= 1.2.0 (2026-02-05) =
* Added: LCP background image preloading for .main-hero containers
* Added: [custom_excerpt] shortcode for truncating content
* Added: [multistep_blossem_form] multi-step rental registration form
* Added: [scf_tax_display] shortcode for SCF taxonomy display
* Added: Font-display: swap for Proxima Nova font
* Added: TTF font detection and replacement functionality
* Added: Property search cache invalidation on post save
* Fixed: Image CLS optimization with width/height attributes
* Fixed: Form button styling with Proxima Nova font
* Security: Added nonce verification to AJAX handlers
* Security: Improved output escaping in widget templates

= 1.1.2 =
* Initial Blossem Group modifications
* Added custom shortcodes and Elementor widgets
* Added property search form with live filtering

== Copyright ==

This theme, like WordPress, is distributed under the terms of GPL.
Use it as your springboard to building a site with ***Elementor***.

Hello Biz bundles the following third-party resources:

Font Awesome icons for theme screenshot
License: SIL Open Font License, version 1.1.
Source: https://fontawesome.com/v4.7.0/

Image for theme screenshot, Copyright Elementor
Image for theme banner, Copyright Elementor
License: CC0 1.0 Universal (CC0 1.0)
Source: https://creativecommons.org/publicdomain/zero/1.0/deed.en

Images for starter content: [Free Stock Photos From Direct Media - StockSnap.io](https://stocksnap.io/author/directmedia)
License: [Creative Commons CC0 license](https://creativecommons.org/publicdomain/zero/1.0/)

== Frequently Asked Questions ==

= How can I report security bugs? =
>Report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team validates, triages, and handles vulnerabilities. [Report a security vulnerability](https://patchstack.com/database/wordpress/theme/hello-biz/vdp)
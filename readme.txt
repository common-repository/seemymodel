=== SeeMyModel ===
Contributors: seemymodel
Tags: 3d models, seemymodel, ar, webar
Requires at least: 5.7
Tested up to: 5.8
Stable tag: 1.0.3
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Provides editor blocks and elementor widgets to easily embed models from seemymodel.com into your page. 
Also adds support for woocommerce - enables embedding 3d model views on product pages.

== Description ==

# About

This plugin contains 3 packages:

- elementor-widgets - contains 2 elementor widgets for embedding models into your site: "SeeMyModel Viewer" and "SeeMyModel Group Viewer". Requires Elementor widget installed and acitvated.
- gutenberg-blocks - contains 2 editor blocks for embedding models into your site: "SeeMyModel Viewer" and "SeeMyModel Group Viewer"
- woocommerce - extension that adds 3D model views to product pages. Requires Woocommerce widget installed and activated.

# Installation

1. Place this plugin inside your wp-content/plugins directory or download it from wordpress store.
2. Enable `SeeMyModel` plugin from wordpress plugins page.
3. Go to `Setting->SeeMyModel settings` on your admin panel and log in with your seemymodel.com user credentials.

# Development

In order to modify gutenberg-blocks you need to compile them after each modification. Each block has its own package. In each package folder (e.g. `packages/gutenberg-blocks/seemymodel-viewer`) you need to run:

1. `cd /path/to/package` to enter into package folder (e.g. `cd packages/gutenberg-blocks/seemymodel-viewer`)
2. `npm install` to install dependencies
3. `npm run start` to run compilation in watch mode

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.0.3 =

- bugfix - checking if woocommerce is installed and active

= 1.0.2 =

- Add support for WooCommerce - embedding 3D models into product pages.
- Add polish translation

= 1.0.1 =

= 1.0.0 =

- Initial release

<?php
/**
 * Plugin Name:       SeeMyModel
 * Description:       Provides Editor Blocks and Elementor widgets to easily embed models from seemymodel.com into your page. Also adds support for WooCommerce - enables embedding 3d model views on product pages.
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           1.0.3
 * Author:            See My Model Sp. z o.o.
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html     
 * Text Domain:       see-my-model
 * Domain Path: /languages
 *
 * @package           see-my-model
 */

//Add plugin settings page
if ( is_admin() ) {
	include 'options-page.php';
	$seemymodel_options = new SeeMyModelOptions();
}
	
//Add editor blocks
include 'packages/gutenberg-blocks/seemymodel-blocks.php';
//Add elementor widgets
include 'packages/elementor-widgets/seemymodel-elementor.php';
//Add woocommerce extension
include 'packages/woocommerce/seemymodel-woocommerce.php';
//Add rest api
include 'rest-api.php';

//Add type="module" attribute to smm script tag
function seemm_add_type_attribute($tag, $handle, $src) {
	// if not smm script, do nothing and return original $tag
	if ( 'smm' !== $handle ) {
		return $tag;
	}
	// change the script tag by adding type="module" and return it.
	$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
	return $tag;
}
add_filter('script_loader_tag', 'seemm_add_type_attribute' , 10, 3);

//Load translations
add_action( 'init', function() {
	load_plugin_textdomain( 'see-my-model', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
});


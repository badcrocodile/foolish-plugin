<?php
/**
 * A fun plugin for Motley Fool
 *
 * @link              https://linktodocs.com
 * @since             1.0.0
 * @package           foolish-plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Foolish Company Data
 * Plugin URI:        https://github.com/badcrocodile/foolish-plugin
 * Description:       Leverages Financial Modeling Prep API to return data about publicly traded companies.
 * Version:           1.0.0
 * Author:            Jason Pollock
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fool
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Require the autoloader
require_once 'vendor/autoload.php';

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Load Carbon Fields directly
add_action( 'after_setup_theme', 'fool_load_carbon_fields' );
function fool_load_carbon_fields() {
	Carbon_Fields::boot();
}

add_action( 'carbon_fields_register_fields', 'fool_add_plugin_settings_page' );
function fool_add_plugin_settings_page() {
	Container::make( 'theme_options', __( 'Foolish Plugin Settings', 'fool' ) )
		 ->set_page_parent( 'options-general.php' )
		 ->add_fields( array(
			 Field::make( 'text', 'fool_api_key', 'API Key' )
				  ->set_attribute( 'maxLength', 64 ),
		 ) );
}

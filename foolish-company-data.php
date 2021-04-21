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


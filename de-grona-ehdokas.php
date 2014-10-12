<?php
/*
 * Plugin Name: De gröna Ehdokas
 * Version: 0.0.1
 * Plugin URI: http://www.hughlashbrooke.com/
 * Description: This is your starter template for your next WordPress plugin.
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: de-grona-ehdokas
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Hugh Lashbrooke
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_VERSION', '0.0.1' );

// Load plugin class files
require_once( 'includes/class-de-grona-ehdokas.php' );
require_once( 'includes/class-de-grona-ehdokas-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-de-grona-ehdokas-admin-api.php' );
require_once( 'includes/lib/class-de-grona-ehdokas-post-type.php' );
require_once( 'includes/lib/class-de-grona-ehdokas-taxonomy.php' );

/**
 * Returns the main instance of De_grona_Ehdokas to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object De_grona_Ehdokas
 */
function De_grona_Ehdokas () {
	$instance = De_grona_Ehdokas::instance( __FILE__, PLUGIN_VERSION );

	if( is_null( $instance->settings ) ) {
		$instance->settings = De_grona_Ehdokas_Settings::instance( $instance );
	}

	return $instance;
}

De_grona_Ehdokas();

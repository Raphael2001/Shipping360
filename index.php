<?php
/*
* Plugin Name: Shipping 360
* Plugin URI: https://raphaelaboohi.cf
* Description: Plugin for Shipping360.
* Version: 1.0
* Author: Raphael Aboohi
* Author URI: https://raphaelaboohi.cf
*/



/**
 * Security check
 *
 * Prevent direct access to the file.
 *
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/**
 * Include plugin files
 */
include_once ( plugin_dir_path( __FILE__ ) . 'createDelivery.php' );
include_once ( plugin_dir_path( __FILE__ ) . 'settings_tab.php' );
include_once ( plugin_dir_path( __FILE__ ) . 'scripts-styles.php' );



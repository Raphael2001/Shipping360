<?php
/**
 * Security check
 *
 * Prevent direct access to the file.
 *
 * @since 1.2
 */
if (! defined('ABSPATH')) {
    exit;
}



/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin scripts
 *
 * @since 1.2
 */
function shipping360_delivery_scripts()
{

    // Register Scripts
    wp_register_script('delivery_script_admin_360', plugins_url("js/delivery_script_admin.js", __FILE__), array( 'jquery' ), false);

    // Enqueue Scripts
    wp_enqueue_script('delivery_script_admin_360');
    wp_localize_script('delivery_script_admin_360', 'ajax_object', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
}
add_action('admin_enqueue_scripts', 'shipping360_delivery_scripts');



/**
 * Plugin Styles
 *
 * Register and Enqueues plugin styles
 *
 * @since 1.2
 */
function shipping360_delivery_style()
{

    // Register Styles
    wp_register_style('shipping360_delivery_style', plugins_url('style.css', __FILE__), false);

    // Enqueue Styles
    wp_enqueue_style('shipping360_delivery_style');
}
add_action('admin_enqueue_scripts', 'shipping360_delivery_style');

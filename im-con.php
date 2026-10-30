<?php
/**
 * Plugin Name: IM.CON Image Converter
 * Description: Convert JPG, PNG, WEBP, AVIF images with a drag‑&‑drop UI and shortcode output.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL2
 * Text Domain: im-con
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'IM_CON_VERSION', '1.0.0' );
define( 'IM_CON_PATH', plugin_dir_path( __FILE__ ) );
define( 'IM_CON_URL', plugin_dir_url( __FILE__ ) );

// Activation hook – flush rewrite rules.
function im_con_activate() {
    // Ensure custom rewrite rules are registered before flushing.
    im_con_register_custom_rewrites();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'im_con_activate' );

// Deactivation hook – flush rewrite rules.
function im_con_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'im_con_deactivate' );

// Load plugin classes.
require_once IM_CON_PATH . 'includes/class-im-con-loader.php';
require_once IM_CON_PATH . 'includes/class-im-con-admin.php';
require_once IM_CON_PATH . 'includes/class-im-con-shortcode.php';

// Initialize the loader which wires everything together.
function im_con_init() {
    $loader = new IM_Con_Loader();
    $loader->run();
}
add_action( 'plugins_loaded', 'im_con_init' );

/**
 * Register any custom rewrite rules (currently none, placeholder for future use).
 */
function im_con_register_custom_rewrites() {
    // Example: add_rewrite_rule( '^im-con/?$', 'index.php?im_con=1', 'top' );
}
?>
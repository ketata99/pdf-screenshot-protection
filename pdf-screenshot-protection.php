<?php
/**
 * Plugin Name: PDF Screenshot Protection
 * Plugin URI: https://github.com/ketata99/pdf-screenshot-protection
 * Description: A WordPress plugin to protect PDFs against screenshots and unauthorized access
 * Version: 1.0.0
 * Author: ketata99
 * Author URI: https://github.com/ketata99
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pdf-screenshot-protection
 * Domain Path: /languages
 *
 * @package PDF_Screenshot_Protection
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'PDF_SCREENSHOT_PROTECTION_VERSION', '1.0.0' );
define( 'PDF_SCREENSHOT_PROTECTION_PATH', plugin_dir_path( __FILE__ ) );
define( 'PDF_SCREENSHOT_PROTECTION_URL', plugin_dir_url( __FILE__ ) );
define( 'PDF_SCREENSHOT_PROTECTION_BASENAME', plugin_basename( __FILE__ ) );

// Include core plugin files
require_once PDF_SCREENSHOT_PROTECTION_PATH . 'includes/class-pdf-screenshot-protection.php';
require_once PDF_SCREENSHOT_PROTECTION_PATH . 'includes/class-admin-settings.php';

/**
 * Initialize the plugin
 */
function pdf_screenshot_protection_init() {
	// Create main plugin instance
	PDF_Screenshot_Protection::get_instance();

	// Initialize admin settings if in admin
	if ( is_admin() ) {
		PDF_Screenshot_Protection_Admin::get_instance();
	}
}
add_action( 'plugins_loaded', 'pdf_screenshot_protection_init' );

/**
 * Activation hook
 */
function pdf_screenshot_protection_activate() {
	// Set default options
	if ( ! get_option( 'pdf_screenshot_protection_enabled' ) ) {
		update_option( 'pdf_screenshot_protection_enabled', 1 );
	}
	if ( ! get_option( 'pdf_screenshot_protection_method' ) ) {
		update_option( 'pdf_screenshot_protection_method', 'watermark' );
	}
}
register_activation_hook( __FILE__, 'pdf_screenshot_protection_activate' );

/**
 * Deactivation hook
 */
function pdf_screenshot_protection_deactivate() {
	// Clean up if needed
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pdf_screenshot_protection_deactivate' );
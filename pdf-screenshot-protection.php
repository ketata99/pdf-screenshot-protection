<?php
/**
 * Plugin Name: PDF Screenshot Protection Pro
 * Plugin URI: https://github.com/ketata99/pdf-screenshot-protection
 * Description: Advanced screenshot protection plugin for Windows, Mac, and Mobile devices
 * Version: 2.0.0
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
define( 'PDF_SCREENSHOT_PROTECTION_VERSION', '2.0.0' );
define( 'PDF_SCREENSHOT_PROTECTION_PATH', plugin_dir_path( __FILE__ ) );
define( 'PDF_SCREENSHOT_PROTECTION_URL', plugin_dir_url( __FILE__ ) );
define( 'PDF_SCREENSHOT_PROTECTION_BASENAME', plugin_basename( __FILE__ ) );

// Include core plugin files
require_once PDF_SCREENSHOT_PROTECTION_PATH . 'includes/class-device-detector.php';
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

	// Windows Protection
	if ( ! get_option( 'pdf_screenshot_protection_windows_enabled' ) ) {
		update_option( 'pdf_screenshot_protection_windows_enabled', 1 );
	}

	// Mac Protection
	if ( ! get_option( 'pdf_screenshot_protection_mac_enabled' ) ) {
		update_option( 'pdf_screenshot_protection_mac_enabled', 1 );
	}

	// Mobile Protection
	if ( ! get_option( 'pdf_screenshot_protection_mobile_enabled' ) ) {
		update_option( 'pdf_screenshot_protection_mobile_enabled', 1 );
	}

	// Alert message
	if ( ! get_option( 'pdf_screenshot_protection_alert_message' ) ) {
		update_option( 'pdf_screenshot_protection_alert_message', __( 'Screenshot protection is enabled. This action is not allowed.', 'pdf-screenshot-protection' ) );
	}
}
register_activation_hook( __FILE__, 'pdf_screenshot_protection_activate' );

/**
 * Deactivation hook
 */
function pdf_screenshot_protection_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pdf_screenshot_protection_deactivate' );

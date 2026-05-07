<?php
/**
 * Main Plugin Class
 *
 * @package PDF_Screenshot_Protection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PDF_Screenshot_Protection Class
 */
class PDF_Screenshot_Protection {

	/**
	 * Instance
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Get Instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Load text domain
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Enqueue scripts and styles on frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

		// Disable right-click context menu
		add_action( 'wp_footer', array( $this, 'add_protection_overlay' ) );
	}

	/**
	 * Load text domain for translations
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'pdf-screenshot-protection',
			false,
			dirname( PDF_SCREENSHOT_PROTECTION_BASENAME ) . '/languages'
		);
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets() {
		// Check if protection is enabled
		if ( ! get_option( 'pdf_screenshot_protection_enabled' ) ) {
			return;
		}

		// Detect device
		$device = PDF_Screenshot_Protection_Device_Detector::detect_device();

		// Enqueue CSS
		wp_enqueue_style(
			'pdf-screenshot-protection',
			PDF_SCREENSHOT_PROTECTION_URL . 'assets/css/pdf-protection.css',
			array(),
			PDF_SCREENSHOT_PROTECTION_VERSION
		);

		// Enqueue device-specific protection scripts
		if ( 'windows' === $device && get_option( 'pdf_screenshot_protection_windows_enabled' ) ) {
			wp_enqueue_script(
				'pdf-screenshot-protection-windows',
				PDF_SCREENSHOT_PROTECTION_URL . 'assets/js/pdf-protection-windows.js',
				array( 'jquery' ),
				PDF_SCREENSHOT_PROTECTION_VERSION,
				true
			);
		}

		if ( 'mac' === $device && get_option( 'pdf_screenshot_protection_mac_enabled' ) ) {
			wp_enqueue_script(
				'pdf-screenshot-protection-mac',
				PDF_SCREENSHOT_PROTECTION_URL . 'assets/js/pdf-protection-mac.js',
				array( 'jquery' ),
				PDF_SCREENSHOT_PROTECTION_VERSION,
				true
			);
		}

		if ( in_array( $device, array( 'ios', 'android' ), true ) && get_option( 'pdf_screenshot_protection_mobile_enabled' ) ) {
			wp_enqueue_script(
				'pdf-screenshot-protection-mobile',
				PDF_SCREENSHOT_PROTECTION_URL . 'assets/js/pdf-protection-mobile.js',
				array( 'jquery' ),
				PDF_SCREENSHOT_PROTECTION_VERSION,
				true
			);
		}

		// Pass settings to JavaScript
		wp_localize_script(
			'jquery',
			'pdfProtectionSettings',
			array(
				'device'                => $device,
				'alert_message'         => get_option( 'pdf_screenshot_protection_alert_message', __( 'Screenshot protection is enabled.', 'pdf-screenshot-protection' ) ),
				'windows_enabled'       => get_option( 'pdf_screenshot_protection_windows_enabled', 1 ),
				'mac_enabled'           => get_option( 'pdf_screenshot_protection_mac_enabled', 1 ),
				'mobile_enabled'        => get_option( 'pdf_screenshot_protection_mobile_enabled', 1 ),
				'block_copy'            => get_option( 'pdf_screenshot_protection_block_copy', 1 ),
				'block_right_click'     => get_option( 'pdf_screenshot_protection_block_right_click', 1 ),
			)
		);
	}

	/**
	 * Add protection overlay
	 */
	public function add_protection_overlay() {
		if ( ! get_option( 'pdf_screenshot_protection_enabled' ) ) {
			return;
		}

		?>
		<div class="pdf-protection-overlay" id="pdf-protection-overlay"></div>
		<?php
	}
}

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

		// Add protection to PDF embeds
		add_filter( 'wp_get_attachment_url', array( $this, 'add_pdf_protection' ), 10, 2 );
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

		// Enqueue CSS
		wp_enqueue_style(
			'pdf-screenshot-protection',
			PDF_SCREENSHOT_PROTECTION_URL . 'assets/css/pdf-protection.css',
			array(),
			PDF_SCREENSHOT_PROTECTION_VERSION
		);

		// Enqueue JavaScript
		wp_enqueue_script(
			'pdf-screenshot-protection',
			PDF_SCREENSHOT_PROTECTION_URL . 'assets/js/pdf-protection.js',
			array( 'jquery' ),
			PDF_SCREENSHOT_PROTECTION_VERSION,
			true
		);

		// Pass settings to JavaScript
		wp_localize_script(
			'pdf-screenshot-protection',
			'pdfProtectionSettings',
			array(
				'method'           => get_option( 'pdf_screenshot_protection_method', 'watermark' ),
				'watermark_text'   => get_option( 'pdf_screenshot_protection_watermark', __( 'CONFIDENTIAL', 'pdf-screenshot-protection' ) ),
				'block_copy'       => get_option( 'pdf_screenshot_protection_block_copy', 1 ),
				'block_print'      => get_option( 'pdf_screenshot_protection_block_print', 1 ),
				'block_download'   => get_option( 'pdf_screenshot_protection_block_download', 0 ),
			)
		);
	}

	/**
	 * Add PDF protection
	 *
	 * @param string $url The URL.
	 * @param int    $attachment_id The attachment ID.
	 */
	public function add_pdf_protection( $url, $attachment_id ) {
		// Check if protection is enabled
		if ( ! get_option( 'pdf_screenshot_protection_enabled' ) ) {
			return $url;
		}

		// Check if it's a PDF
		if ( ! strpos( $url, '.pdf' ) ) {
			return $url;
		}

		// Add protection class to PDFs
		// This can be extended to add additional logic

		return $url;
	}
}

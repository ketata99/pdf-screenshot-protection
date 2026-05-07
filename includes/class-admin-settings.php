<?php
/**
 * Admin Settings Class
 *
 * @package PDF_Screenshot_Protection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PDF_Screenshot_Protection_Admin Class
 */
class PDF_Screenshot_Protection_Admin {

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
		// Add admin menu
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// Register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Enqueue admin assets
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'PDF Protection', 'pdf-screenshot-protection' ),
			__( 'PDF Protection', 'pdf-screenshot-protection' ),
			'manage_options',
			'pdf-screenshot-protection',
			array( $this, 'render_settings_page' ),
			'dashicons-lock',
			99
		);
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		// Enable/Disable
		register_setting(
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_enabled'
		);

		// Protection method
		register_setting(
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_method'
		);

		// Watermark text
		register_setting(
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_watermark'
		);

		// Block copy
		register_setting(
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_block_copy'
		);

		// Block print
		register_setting(
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_block_print'
		);

		// Block download
		register_setting(
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_block_download'
		);

		// Add settings sections
		add_settings_section(
			'pdf_screenshot_protection_main',
			__( 'Protection Settings', 'pdf-screenshot-protection' ),
			array( $this, 'render_section' ),
			'pdf_screenshot_protection_settings'
		);

		// Add settings fields
		add_settings_field(
			'pdf_screenshot_protection_enabled',
			__( 'Enable Protection', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_main',
			array( 'label_for' => 'pdf_screenshot_protection_enabled', 'option' => 'pdf_screenshot_protection_enabled' )
		);

		add_settings_field(
			'pdf_screenshot_protection_method',
			__( 'Protection Method', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_select' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_main',
			array( 'label_for' => 'pdf_screenshot_protection_method', 'option' => 'pdf_screenshot_protection_method' )
		);

		add_settings_field(
			'pdf_screenshot_protection_watermark',
			__( 'Watermark Text', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_text' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_main',
			array( 'label_for' => 'pdf_screenshot_protection_watermark', 'option' => 'pdf_screenshot_protection_watermark' )
		);

		add_settings_field(
			'pdf_screenshot_protection_block_copy',
			__( 'Block Copy', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_main',
			array( 'label_for' => 'pdf_screenshot_protection_block_copy', 'option' => 'pdf_screenshot_protection_block_copy' )
		);

		add_settings_field(
			'pdf_screenshot_protection_block_print',
			__( 'Block Print', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_main',
			array( 'label_for' => 'pdf_screenshot_protection_block_print', 'option' => 'pdf_screenshot_protection_block_print' )
		);

		add_settings_field(
			'pdf_screenshot_protection_block_download',
			__( 'Block Download', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_main',
			array( 'label_for' => 'pdf_screenshot_protection_block_download', 'option' => 'pdf_screenshot_protection_block_download' )
		);
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		?>
		<div class="wrap pdf-screenshot-protection-wrap">
			<h1><?php esc_html_e( 'PDF Screenshot Protection', 'pdf-screenshot-protection' ); ?></h1>
			<form action="options.php" method="post">
				<?php settings_fields( 'pdf_screenshot_protection_settings' ); ?>
				<?php do_settings_sections( 'pdf_screenshot_protection_settings' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render section
	 */
	public function render_section() {
		echo wp_kses_post( __( 'Configure your PDF protection settings below.', 'pdf-screenshot-protection' ) );
	}

	/**
	 * Render checkbox field
	 *
	 * @param array $args The field arguments.
	 */
	public function render_field_checkbox( $args ) {
		$option = $args['option'];
		$value  = get_option( $option );
		?>
		<input type="checkbox" id="<?php echo esc_attr( $option ); ?>" name="<?php echo esc_attr( $option ); ?>" value="1" <?php checked( $value, 1 ); ?> />
		<?php
	}

	/**
	 * Render select field
	 *
	 * @param array $args The field arguments.
	 */
	public function render_field_select( $args ) {
		$option = $args['option'];
		$value  = get_option( $option, 'watermark' );
		?>
		<select id="<?php echo esc_attr( $option ); ?>" name="<?php echo esc_attr( $option ); ?>">
			<option value="watermark" <?php selected( $value, 'watermark' ); ?>><?php esc_html_e( 'Watermark', 'pdf-screenshot-protection' ); ?></option>
			<option value="disable_copy" <?php selected( $value, 'disable_copy' ); ?>><?php esc_html_e( 'Disable Copy/Print', 'pdf-screenshot-protection' ); ?></option>
			<option value="combined" <?php selected( $value, 'combined' ); ?>><?php esc_html_e( 'Combined', 'pdf-screenshot-protection' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render text field
	 *
	 * @param array $args The field arguments.
	 */
	public function render_field_text( $args ) {
		$option = $args['option'];
		$value  = get_option( $option, __( 'CONFIDENTIAL', 'pdf-screenshot-protection' ) );
		?>
		<input type="text" id="<?php echo esc_attr( $option ); ?>" name="<?php echo esc_attr( $option ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php
	}

	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets() {
		// Enqueue admin CSS
		wp_enqueue_style(
			'pdf-screenshot-protection-admin',
			PDF_SCREENSHOT_PROTECTION_URL . 'assets/css/admin.css',
			array(),
			PDF_SCREENSHOT_PROTECTION_VERSION
		);
	}
}

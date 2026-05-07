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
			__( 'Screenshot Protection', 'pdf-screenshot-protection' ),
			__( 'Screenshot Protection', 'pdf-screenshot-protection' ),
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
		// General Settings
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_enabled' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_alert_message' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_block_copy' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_block_right_click' );

		// Windows Protection
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_windows_enabled' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_windows_block_printscreen' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_windows_block_alt_printscreen' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_windows_block_snip' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_windows_block_devtools' );

		// Mac Protection
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_mac_enabled' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_mac_block_cmd_shift_3' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_mac_block_cmd_shift_4' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_mac_block_cmd_shift_5' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_mac_block_devtools' );

		// Mobile Protection
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_mobile_enabled' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_mobile_block_volume_buttons' );
		register_setting( 'pdf_screenshot_protection_settings', 'pdf_screenshot_protection_mobile_show_alert' );

		// Add settings sections
		add_settings_section(
			'pdf_screenshot_protection_general',
			__( 'General Settings', 'pdf-screenshot-protection' ),
			array( $this, 'render_general_section' ),
			'pdf_screenshot_protection_settings'
		);

		add_settings_section(
			'pdf_screenshot_protection_windows_section',
			__( 'Windows Protection', 'pdf-screenshot-protection' ),
			array( $this, 'render_windows_section' ),
			'pdf_screenshot_protection_settings'
		);

		add_settings_section(
			'pdf_screenshot_protection_mac_section',
			__( 'Mac Protection', 'pdf-screenshot-protection' ),
			array( $this, 'render_mac_section' ),
			'pdf_screenshot_protection_settings'
		);

		add_settings_section(
			'pdf_screenshot_protection_mobile_section',
			__( 'Mobile Protection', 'pdf-screenshot-protection' ),
			array( $this, 'render_mobile_section' ),
			'pdf_screenshot_protection_settings'
		);

		// General Fields
		add_settings_field(
			'pdf_screenshot_protection_enabled',
			__( 'Enable Protection', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_general',
			array( 'label_for' => 'pdf_screenshot_protection_enabled', 'option' => 'pdf_screenshot_protection_enabled' )
		);

		add_settings_field(
			'pdf_screenshot_protection_alert_message',
			__( 'Alert Message', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_textarea' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_general',
			array( 'label_for' => 'pdf_screenshot_protection_alert_message', 'option' => 'pdf_screenshot_protection_alert_message' )
		);

		add_settings_field(
			'pdf_screenshot_protection_block_copy',
			__( 'Block Copy (All Devices)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_general',
			array( 'label_for' => 'pdf_screenshot_protection_block_copy', 'option' => 'pdf_screenshot_protection_block_copy' )
		);

		add_settings_field(
			'pdf_screenshot_protection_block_right_click',
			__( 'Block Right-Click (All Devices)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_general',
			array( 'label_for' => 'pdf_screenshot_protection_block_right_click', 'option' => 'pdf_screenshot_protection_block_right_click' )
		);

		// Windows Fields
		add_settings_field(
			'pdf_screenshot_protection_windows_enabled',
			__( 'Enable Windows Protection', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_windows_section',
			array( 'label_for' => 'pdf_screenshot_protection_windows_enabled', 'option' => 'pdf_screenshot_protection_windows_enabled' )
		);

		add_settings_field(
			'pdf_screenshot_protection_windows_block_printscreen',
			__( 'Block Print Screen (PrtScn)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_windows_section',
			array( 'label_for' => 'pdf_screenshot_protection_windows_block_printscreen', 'option' => 'pdf_screenshot_protection_windows_block_printscreen' )
		);

		add_settings_field(
			'pdf_screenshot_protection_windows_block_alt_printscreen',
			__( 'Block Alt + Print Screen', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_windows_section',
			array( 'label_for' => 'pdf_screenshot_protection_windows_block_alt_printscreen', 'option' => 'pdf_screenshot_protection_windows_block_alt_printscreen' )
		);

		add_settings_field(
			'pdf_screenshot_protection_windows_block_snip',
			__( 'Block Win + Shift + S (Snip Tool)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_windows_section',
			array( 'label_for' => 'pdf_screenshot_protection_windows_block_snip', 'option' => 'pdf_screenshot_protection_windows_block_snip' )
		);

		add_settings_field(
			'pdf_screenshot_protection_windows_block_devtools',
			__( 'Block Developer Tools (F12, Ctrl+Shift+I)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_windows_section',
			array( 'label_for' => 'pdf_screenshot_protection_windows_block_devtools', 'option' => 'pdf_screenshot_protection_windows_block_devtools' )
		);

		// Mac Fields
		add_settings_field(
			'pdf_screenshot_protection_mac_enabled',
			__( 'Enable Mac Protection', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_mac_section',
			array( 'label_for' => 'pdf_screenshot_protection_mac_enabled', 'option' => 'pdf_screenshot_protection_mac_enabled' )
		);

		add_settings_field(
			'pdf_screenshot_protection_mac_block_cmd_shift_3',
			__( 'Block Cmd + Shift + 3 (Full Screenshot)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_mac_section',
			array( 'label_for' => 'pdf_screenshot_protection_mac_block_cmd_shift_3', 'option' => 'pdf_screenshot_protection_mac_block_cmd_shift_3' )
		);

		add_settings_field(
			'pdf_screenshot_protection_mac_block_cmd_shift_4',
			__( 'Block Cmd + Shift + 4 (Selection Screenshot)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_mac_section',
			array( 'label_for' => 'pdf_screenshot_protection_mac_block_cmd_shift_4', 'option' => 'pdf_screenshot_protection_mac_block_cmd_shift_4' )
		);

		add_settings_field(
			'pdf_screenshot_protection_mac_block_cmd_shift_5',
			__( 'Block Cmd + Shift + 5 (Screenshot App)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_mac_section',
			array( 'label_for' => 'pdf_screenshot_protection_mac_block_cmd_shift_5', 'option' => 'pdf_screenshot_protection_mac_block_cmd_shift_5' )
		);

		add_settings_field(
			'pdf_screenshot_protection_mac_block_devtools',
			__( 'Block Developer Tools (Cmd+Option+I)', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_mac_section',
			array( 'label_for' => 'pdf_screenshot_protection_mac_block_devtools', 'option' => 'pdf_screenshot_protection_mac_block_devtools' )
		);

		// Mobile Fields
		add_settings_field(
			'pdf_screenshot_protection_mobile_enabled',
			__( 'Enable Mobile Protection', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_mobile_section',
			array( 'label_for' => 'pdf_screenshot_protection_mobile_enabled', 'option' => 'pdf_screenshot_protection_mobile_enabled' )
		);

		add_settings_field(
			'pdf_screenshot_protection_mobile_block_volume_buttons',
			__( 'Block Volume Button Screenshots', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_mobile_section',
			array( 'label_for' => 'pdf_screenshot_protection_mobile_block_volume_buttons', 'option' => 'pdf_screenshot_protection_mobile_block_volume_buttons' )
		);

		add_settings_field(
			'pdf_screenshot_protection_mobile_show_alert',
			__( 'Show Alert on Screenshot Attempt', 'pdf-screenshot-protection' ),
			array( $this, 'render_field_checkbox' ),
			'pdf_screenshot_protection_settings',
			'pdf_screenshot_protection_mobile_section',
			array( 'label_for' => 'pdf_screenshot_protection_mobile_show_alert', 'option' => 'pdf_screenshot_protection_mobile_show_alert' )
		);
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		?>
		<div class="wrap pdf-screenshot-protection-wrap">
			<h1><?php esc_html_e( 'Screenshot Protection Pro', 'pdf-screenshot-protection' ); ?></h1>
			<p><?php esc_html_e( 'Protect your content from screenshots on Windows, Mac, and Mobile devices.', 'pdf-screenshot-protection' ); ?></p>
			<form action="options.php" method="post">
				<?php settings_fields( 'pdf_screenshot_protection_settings' ); ?>
				<?php do_settings_sections( 'pdf_screenshot_protection_settings' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render general section
	 */
	public function render_general_section() {
		echo wp_kses_post( __( 'Configure general protection settings that apply to all devices.', 'pdf-screenshot-protection' ) );
	}

	/**
	 * Render Windows section
	 */
	public function render_windows_section() {
		echo wp_kses_post( __( 'Configure screenshot protection for Windows devices.', 'pdf-screenshot-protection' ) );
	}

	/**
	 * Render Mac section
	 */
	public function render_mac_section() {
		echo wp_kses_post( __( 'Configure screenshot protection for Mac devices.', 'pdf-screenshot-protection' ) );
	}

	/**
	 * Render Mobile section
	 */
	public function render_mobile_section() {
		echo wp_kses_post( __( 'Configure screenshot protection for iOS and Android devices.', 'pdf-screenshot-protection' ) );
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
	 * Render textarea field
	 *
	 * @param array $args The field arguments.
	 */
	public function render_field_textarea( $args ) {
		$option = $args['option'];
		$value  = get_option( $option, __( 'Screenshot protection is enabled. This action is not allowed.', 'pdf-screenshot-protection' ) );
		?>
		<textarea id="<?php echo esc_attr( $option ); ?>" name="<?php echo esc_attr( $option ); ?>" rows="3" cols="50"><?php echo esc_textarea( $value ); ?></textarea>
		<?php
	}

	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets() {
		wp_enqueue_style(
			'pdf-screenshot-protection-admin',
			PDF_SCREENSHOT_PROTECTION_URL . 'assets/css/admin.css',
			array(),
			PDF_SCREENSHOT_PROTECTION_VERSION
		);
	}
}

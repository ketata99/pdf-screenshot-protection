<?php
/**
 * Account & Device Binding Class
 *
 * @package PDF_Screenshot_Protection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PDF_Screenshot_Protection_Device_Binding Class
 * Implements device and account tethering
 */
class PDF_Screenshot_Protection_Device_Binding {

	/**
	 * Bind device to user account
	 *
	 * @param int    $user_id User ID.
	 * @param string $device_name Device name.
	 * @return array Binding data.
	 */
	public static function bind_device( $user_id, $device_name = '' ) {
		if ( empty( $user_id ) ) {
			return array();
		}

		if ( empty( $device_name ) ) {
			$device_name = self::get_device_name();
		}

		$device_binding = array(
			'binding_id'        => PDF_Screenshot_Protection_Encryption::generate_secure_token( 16 ),
			'user_id'           => absint( $user_id ),
			'device_name'       => sanitize_text_field( $device_name ),
			'device_hash'       => PDF_Screenshot_Protection_Encryption::get_device_fingerprint(),
			'ip_address'        => PDF_Screenshot_Protection_Encryption::get_client_ip(),
			'mac_address'       => self::get_mac_address(),
			'user_agent'        => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
			'created_at'        => current_time( 'mysql' ),
			'last_access_at'    => current_time( 'mysql' ),
			'max_devices'       => get_option( 'pdf_screenshot_protection_max_devices', 5 ),
		);

		// Check device limit
		if ( ! self::check_device_limit( $user_id ) ) {
			return array( 'error' => __( 'Device limit exceeded', 'pdf-screenshot-protection' ) );
		}

		// Store binding in database
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_device_bindings';

		self::create_binding_table();

		$wpdb->insert(
			$table,
			array(
				'binding_id'     => $device_binding['binding_id'],
				'user_id'        => $device_binding['user_id'],
				'device_name'    => $device_binding['device_name'],
				'device_hash'    => $device_binding['device_hash'],
				'ip_address'     => $device_binding['ip_address'],
				'mac_address'    => $device_binding['mac_address'],
				'user_agent'     => $device_binding['user_agent'],
				'created_at'     => $device_binding['created_at'],
				'last_access_at' => $device_binding['last_access_at'],
			),
			array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return $device_binding;
	}

	/**
	 * Verify device binding
	 *
	 * @param int    $user_id User ID.
	 * @param string $binding_id Binding ID.
	 * @return bool True if device is bound and authorized.
	 */
	public static function verify_device_binding( $user_id, $binding_id = '' ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'pdf_device_bindings';

		// Check if table exists
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return true; // First time, allow
		}

		$current_device_hash = PDF_Screenshot_Protection_Encryption::get_device_fingerprint();

		if ( ! empty( $binding_id ) ) {
			$binding = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM $table WHERE binding_id = %s AND user_id = %d",
					$binding_id,
					$user_id
				)
			);
		} else {
			$binding = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM $table WHERE device_hash = %s AND user_id = %d",
					$current_device_hash,
					$user_id
				)
			);
		}

		if ( null === $binding ) {
			return false;
		}

		// Update last access
		$wpdb->update(
			$table,
			array( 'last_access_at' => current_time( 'mysql' ) ),
			array( 'binding_id' => $binding->binding_id ),
			array( '%s' ),
			array( '%s' )
		);

		return true;
	}

	/**
	 * Check device limit for user
	 *
	 * @param int $user_id User ID.
	 * @return bool True if user hasn't exceeded device limit.
	 */
	public static function check_device_limit( $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_device_bindings';
		$max_devices = get_option( 'pdf_screenshot_protection_max_devices', 5 );

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return true;
		}

		$device_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $table WHERE user_id = %d",
				$user_id
			)
		);

		return absint( $device_count ) < absint( $max_devices );
	}

	/**
	 * Get user's bound devices
	 *
	 * @param int $user_id User ID.
	 * @return array Array of bound devices.
	 */
	public static function get_user_devices( $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_device_bindings';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return array();
		}

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table WHERE user_id = %d ORDER BY last_access_at DESC",
				$user_id
			)
		);
	}

	/**
	 * Revoke device binding
	 *
	 * @param string $binding_id Binding ID.
	 * @param int    $user_id User ID.
	 * @return bool True on success.
	 */
	public static function revoke_device_binding( $binding_id, $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_device_bindings';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return false;
		}

		return $wpdb->delete(
			$table,
			array(
				'binding_id' => $binding_id,
				'user_id'    => $user_id,
			),
			array( '%s', '%d' )
		);
	}

	/**
	 * Get device name
	 */
	private static function get_device_name() {
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : 'Unknown Device';

		if ( strpos( $user_agent, 'Windows' ) !== false ) {
			return 'Windows PC';
		} elseif ( strpos( $user_agent, 'Mac' ) !== false ) {
			return 'Mac';
		} elseif ( strpos( $user_agent, 'iPhone' ) !== false ) {
			return 'iPhone';
		} elseif ( strpos( $user_agent, 'iPad' ) !== false ) {
			return 'iPad';
		} elseif ( strpos( $user_agent, 'Android' ) !== false ) {
			return 'Android';
		}

		return $user_agent;
	}

	/**
	 * Get MAC address (simulated)
	 * Note: Can't reliably get MAC from web, so we use device hash
	 */
	private static function get_mac_address() {
		return strtoupper( hash( 'md5', PDF_Screenshot_Protection_Encryption::get_device_fingerprint() ) );
	}

	/**
	 * Create device binding table
	 */
	private static function create_binding_table() {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_device_bindings';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			binding_id varchar(255) NOT NULL UNIQUE,
			user_id bigint(20) NOT NULL,
			device_name varchar(255) DEFAULT NULL,
			device_hash varchar(255) NOT NULL,
			ip_address varchar(45) DEFAULT NULL,
			mac_address varchar(255) DEFAULT NULL,
			user_agent text DEFAULT NULL,
			created_at datetime NOT NULL,
			last_access_at datetime NOT NULL,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY device_hash (device_hash),
			KEY binding_id (binding_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

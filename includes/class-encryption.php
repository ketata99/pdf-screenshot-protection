<?php
/**
 * Encryption Class - DRM Implementation
 *
 * @package PDF_Screenshot_Protection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PDF_Screenshot_Protection_Encryption Class
 * Implements AES-256 encryption for DRM
 */
class PDF_Screenshot_Protection_Encryption {

	/**
	 * Encryption key
	 *
	 * @var string
	 */
	private static $encryption_key = null;

	/**
	 * Get or generate encryption key
	 */
	public static function get_encryption_key() {
		if ( null === self::$encryption_key ) {
			// Try to get from database
			$key = get_option( 'pdf_screenshot_protection_encryption_key' );

			if ( empty( $key ) ) {
				// Generate new key if not exists
				$key = self::generate_encryption_key();
				update_option( 'pdf_screenshot_protection_encryption_key', $key );
			}

			self::$encryption_key = $key;
		}

		return self::$encryption_key;
	}

	/**
	 * Generate a random encryption key
	 */
	private static function generate_encryption_key() {
		// Generate 32 bytes (256 bits) random key for AES-256
		return bin2hex( random_bytes( 32 ) );
	}

	/**
	 * Encrypt content using AES-256-CBC
	 *
	 * @param string $content Content to encrypt.
	 * @param string $user_id User ID for additional security.
	 * @return array Encrypted data with IV and tag.
	 */
	public static function encrypt( $content, $user_id = '' ) {
		if ( empty( $content ) ) {
			return array();
		}

		$key = hex2bin( self::get_encryption_key() );
		$iv = openssl_random_pseudo_bytes( 16 );
		$tag = '';

		// Use AES-256-GCM for authenticated encryption
		$encrypted = openssl_encrypt(
			$content,
			'AES-256-GCM',
			$key,
			OPENSSL_RAW_DATA,
			$iv,
			$tag
		);

		if ( false === $encrypted ) {
			return array();
		}

		return array(
			'encrypted' => base64_encode( $encrypted ),
			'iv'        => base64_encode( $iv ),
			'tag'       => base64_encode( $tag ),
			'user_id'   => sanitize_text_field( $user_id ),
			'timestamp' => time(),
		);
	}

	/**
	 * Decrypt content using AES-256-CBC
	 *
	 * @param array $encrypted_data Encrypted data array.
	 * @param string $user_id User ID for verification.
	 * @return string|false Decrypted content or false on failure.
	 */
	public static function decrypt( $encrypted_data, $user_id = '' ) {
		if ( ! is_array( $encrypted_data ) || empty( $encrypted_data['encrypted'] ) ) {
			return false;
		}

		// Verify user_id if provided
		if ( ! empty( $user_id ) && $user_id !== $encrypted_data['user_id'] ) {
			return false;
		}

		try {
			$key       = hex2bin( self::get_encryption_key() );
			$encrypted = base64_decode( $encrypted_data['encrypted'] );
			$iv        = base64_decode( $encrypted_data['iv'] );
			$tag       = base64_decode( $encrypted_data['tag'] );

			// Decrypt using AES-256-GCM
			$decrypted = openssl_decrypt(
				$encrypted,
				'AES-256-GCM',
				$key,
				OPENSSL_RAW_DATA,
				$iv,
				$tag
			);

			return ( false !== $decrypted ) ? $decrypted : false;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Hash password with salt
	 *
	 * @param string $password Password to hash.
	 * @return string Hashed password.
	 */
	public static function hash_password( $password ) {
		return password_hash( $password, PASSWORD_BCRYPT, array( 'cost' => 12 ) );
	}

	/**
	 * Verify password
	 *
	 * @param string $password Password to verify.
	 * @param string $hash Hashed password.
	 * @return bool True if password matches.
	 */
	public static function verify_password( $password, $hash ) {
		return password_verify( $password, $hash );
	}

	/**
	 * Generate secure token
	 *
	 * @param int $length Token length.
	 * @return string Secure random token.
	 */
	public static function generate_secure_token( $length = 32 ) {
		return bin2hex( random_bytes( $length / 2 ) );
	}

	/**
	 * Create DRM license for user
	 *
	 * @param int    $user_id User ID.
	 * @param int    $content_id Content ID.
	 * @param string $expiry Expiry date (YYYY-MM-DD HH:MM:SS).
	 * @return array License data.
	 */
	public static function create_drm_license( $user_id, $content_id, $expiry = '' ) {
		if ( empty( $expiry ) ) {
			// Default: 30 days from now
			$expiry = date( 'Y-m-d H:i:s', strtotime( '+30 days' ) );
		}

		$license = array(
			'license_id'   => self::generate_secure_token( 16 ),
			'user_id'      => absint( $user_id ),
			'content_id'   => absint( $content_id ),
			'created_at'   => current_time( 'mysql' ),
			'expiry_at'    => $expiry,
			'device_count' => 5, // Max devices
			'mac_address'  => self::get_device_fingerprint(),
			'ip_address'   => self::get_client_ip(),
		);

		// Store license in database
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_drm_licenses';

		// Create table if not exists
		self::create_license_table();

		$wpdb->insert(
			$table,
			array(
				'license_id'   => $license['license_id'],
				'user_id'      => $license['user_id'],
				'content_id'   => $license['content_id'],
				'created_at'   => $license['created_at'],
				'expiry_at'    => $license['expiry_at'],
				'device_count' => $license['device_count'],
				'ip_address'   => $license['ip_address'],
			),
			array( '%s', '%d', '%d', '%s', '%s', '%d', '%s' )
		);

		return $license;
	}

	/**
	 * Verify DRM license
	 *
	 * @param string $license_id License ID.
	 * @param int    $user_id User ID.
	 * @return bool True if license is valid.
	 */
	public static function verify_drm_license( $license_id, $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_drm_licenses';

		// Check if table exists
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return false;
		}

		$license = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table WHERE license_id = %s AND user_id = %d",
				$license_id,
				$user_id
			)
		);

		if ( null === $license ) {
			return false;
		}

		// Check expiry
		if ( strtotime( $license->expiry_at ) < current_time( 'timestamp' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get device fingerprint
	 */
	public static function get_device_fingerprint() {
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		$accept_language = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) : '';
		$accept_encoding = isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ) : '';

		return hash( 'sha256', $user_agent . $accept_language . $accept_encoding );
	}

	/**
	 * Get client IP
	 */
	public static function get_client_ip() {
		if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			// Cloudflare
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
			return trim( $ips[0] );
		} else {
			return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		}
	}

	/**
	 * Create DRM license table
	 */
	private static function create_license_table() {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_drm_licenses';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			license_id varchar(255) NOT NULL UNIQUE,
			user_id bigint(20) NOT NULL,
			content_id bigint(20) NOT NULL,
			created_at datetime NOT NULL,
			expiry_at datetime NOT NULL,
			device_count int(11) DEFAULT 5,
			ip_address varchar(45) DEFAULT NULL,
			access_count int(11) DEFAULT 0,
			last_access datetime DEFAULT NULL,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY content_id (content_id),
			KEY expiry_at (expiry_at)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

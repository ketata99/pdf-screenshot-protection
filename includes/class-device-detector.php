<?php
/**
 * Device Detector Class
 *
 * @package PDF_Screenshot_Protection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Device_Detector Class
 */
class PDF_Screenshot_Protection_Device_Detector {

	/**
	 * Detect device type
	 *
	 * @return string Device type (windows, mac, ios, android, other)
	 */
	public static function detect_device() {
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		if ( empty( $user_agent ) ) {
			return 'other';
		}

		// Detect OS
		if ( strpos( $user_agent, 'Windows' ) !== false || strpos( $user_agent, 'Win' ) !== false ) {
			return 'windows';
		}

		if ( strpos( $user_agent, 'Mac' ) !== false ) {
			if ( strpos( $user_agent, 'iPhone' ) !== false || strpos( $user_agent, 'iPad' ) !== false ) {
				return 'ios';
			}
			return 'mac';
		}

		if ( strpos( $user_agent, 'Linux' ) !== false && strpos( $user_agent, 'Android' ) === false ) {
			return 'linux';
		}

		if ( strpos( $user_agent, 'Android' ) !== false ) {
			return 'android';
		}

		if ( strpos( $user_agent, 'iPhone' ) !== false || strpos( $user_agent, 'iPad' ) !== false ) {
			return 'ios';
		}

		return 'other';
	}

	/**
	 * Check if device is mobile
	 *
	 * @return bool True if mobile, false otherwise
	 */
	public static function is_mobile() {
		$device = self::detect_device();
		return in_array( $device, array( 'ios', 'android' ), true );
	}

	/**
	 * Check if device is desktop
	 *
	 * @return bool True if desktop, false otherwise
	 */
	public static function is_desktop() {
		$device = self::detect_device();
		return in_array( $device, array( 'windows', 'mac', 'linux' ), true );
	}
}

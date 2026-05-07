<?php
/**
 * Watermarking Class - Social DRM Implementation
 *
 * @package PDF_Screenshot_Protection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PDF_Screenshot_Protection_Watermark Class
 * Implements invisible and visible watermarking
 */
class PDF_Screenshot_Protection_Watermark {

	/**
	 * Generate visible watermark
	 *
	 * @param string $user_id User ID.
	 * @param string $email User email.
	 * @param string $purchase_date Purchase date.
	 * @return string HTML watermark.
	 */
	public static function generate_visible_watermark( $user_id, $email = '', $purchase_date = '' ) {
		if ( empty( $purchase_date ) ) {
			$purchase_date = current_time( 'Y-m-d H:i:s' );
		}

		$watermark_html = sprintf(
			'<div class="pdf-visible-watermark" data-user-id="%d" data-email="%s" data-date="%s">' .
			'<p>User ID: %d</p>' .
			'<p>Email: %s</p>' .
			'<p>Date: %s</p>' .
			'<p style="color: red; font-weight: bold;">⚠️ This copy is licensed to the above user</p>' .
			'</div>',
			absint( $user_id ),
			esc_attr( $email ),
			esc_attr( $purchase_date ),
			absint( $user_id ),
			esc_html( $email ),
			esc_html( $purchase_date )
		);

		return $watermark_html;
	}

	/**
	 * Generate invisible watermark (metadata)
	 *
	 * @param int    $user_id User ID.
	 * @param string $email User email.
	 * @param string $ip_address IP address.
	 * @param string $user_agent User agent.
	 * @return string Encrypted watermark data.
	 */
	public static function generate_invisible_watermark( $user_id, $email = '', $ip_address = '', $user_agent = '' ) {
		$watermark_data = array(
			'user_id'      => absint( $user_id ),
			'email'        => sanitize_email( $email ),
			'ip_address'   => sanitize_text_field( $ip_address ),
			'user_agent'   => sanitize_text_field( $user_agent ),
			'timestamp'    => time(),
			'purchased_at' => current_time( 'mysql' ),
			'device_hash'  => PDF_Screenshot_Protection_Encryption::get_device_fingerprint(),
		);

		// Encrypt watermark data
		$encrypted = PDF_Screenshot_Protection_Encryption::encrypt(
			json_encode( $watermark_data ),
			$user_id
		);

		return wp_json_encode( $encrypted );
	}

	/**
	 * Add watermark to page
	 *
	 * @param int    $user_id User ID.
	 * @param string $email User email.
	 * @param bool   $visible Show visible watermark.
	 */
	public static function add_page_watermark( $user_id, $email = '', $visible = true ) {
		if ( empty( $user_id ) ) {
			return;
		}

		$ip = PDF_Screenshot_Protection_Encryption::get_client_ip();
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		// Generate invisible watermark
		$invisible = self::generate_invisible_watermark( $user_id, $email, $ip, $user_agent );

		?>
		<meta name="pdf-watermark-data" content="<?php echo esc_attr( $invisible ); ?>" />
		<script type="application/json" id="pdf-watermark-metadata">
			<?php echo wp_json_encode( array(
				'user_id'    => absint( $user_id ),
				'email'      => sanitize_email( $email ),
				'timestamp'  => current_time( 'timestamp' ),
				'ip_address' => $ip,
			) ); ?>
		</script>
		<?php

		if ( $visible ) {
			echo wp_kses_post( self::generate_visible_watermark( $user_id, $email ) );
		}
	}

	/**
	 * Embed watermark in PDF
	 *
	 * @param string $pdf_file PDF file path.
	 * @param int    $user_id User ID.
	 * @param string $email User email.
	 * @return bool True on success.
	 */
	public static function embed_watermark_in_pdf( $pdf_file, $user_id, $email = '' ) {
		if ( ! file_exists( $pdf_file ) ) {
			return false;
		}

		// This would require a PDF library like TCPDF or similar
		// For now, we'll store watermark metadata in database
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_watermarks';

		self::create_watermark_table();

		$watermark_data = array(
			'user_id'    => absint( $user_id ),
			'email'      => sanitize_email( $email ),
			'file_path'  => sanitize_file_name( $pdf_file ),
			'file_hash'  => hash_file( 'sha256', $pdf_file ),
			'created_at' => current_time( 'mysql' ),
		);

		return $wpdb->insert(
			$table,
			$watermark_data,
			array( '%d', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Get watermark data
	 *
	 * @param string $file_hash File hash.
	 * @return object|null Watermark data.
	 */
	public static function get_watermark_data( $file_hash ) {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_watermarks';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return null;
		}

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table WHERE file_hash = %s",
				$file_hash
			)
		);
	}

	/**
	 * Create watermark table
	 */
	private static function create_watermark_table() {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_watermarks';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			email varchar(255) DEFAULT NULL,
			file_path text NOT NULL,
			file_hash varchar(255) NOT NULL UNIQUE,
			created_at datetime NOT NULL,
			accessed_count int(11) DEFAULT 0,
			last_accessed datetime DEFAULT NULL,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY file_hash (file_hash)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Track file access
	 *
	 * @param string $file_hash File hash.
	 * @param int    $user_id User ID.
	 */
	public static function track_file_access( $file_hash, $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'pdf_watermarks';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return;
		}

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE $table SET accessed_count = accessed_count + 1, last_accessed = NOW() WHERE file_hash = %s AND user_id = %d",
				$file_hash,
				$user_id
			)
		);
	}
}

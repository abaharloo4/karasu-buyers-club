<?php
namespace KarasuBuyersClub\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کلاس ثبت لاگ خطاهای سیستم در فایل اشکال‌زدایی و لاگر ووکامرس.
 *
 * @package KarasuBuyersClub\Utils
 */
class Logger {

	/**
	 * ثبت پیام لاگ.
	 *
	 * @param string $message
	 * @param string $level info|warning|error|debug
	 * @param array $context
	 */
	public static function log( string $message, string $level = 'info', array $context = array() ): void {
		if ( class_exists( 'WC_Logger' ) ) {
			$logger  = wc_get_logger();
			$context = array_merge( array( 'source' => 'karasu-buyers-club' ), $context );
			$logger->log( $level, $message, $context );
		} else {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( sprintf( '[KarasuBuyersClub][%s] %s %s', strtoupper( $level ), $message, ! empty( $context ) ? wp_json_encode( $context ) : '' ) );
			}
		}
	}

	/**
	 * ثبت لاگ خطا.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public static function error( string $message, array $context = array() ): void {
		self::log( $message, 'error', $context );
	}
}

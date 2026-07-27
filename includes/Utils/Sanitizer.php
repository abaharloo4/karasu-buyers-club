<?php
namespace KarasuBuyersClub\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کلاس کمکی اعتبارسنجی و ایمن‌سازی داده‌های ورودی.
 *
 * @package KarasuBuyersClub\Utils
 */
class Sanitizer {

	/**
	 * ایمن‌سازی آرایه یا رشته متنی.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public static function clean( $data ) {
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$data[ $key ] = self::clean( $value );
			}
			return $data;
		}

		if ( is_string( $data ) ) {
			return sanitize_text_field( wp_unslash( $data ) );
		}

		return $data;
	}

	/**
	 * اعتبارسنجی عدد صحیح مثبت.
	 *
	 * @param mixed $val
	 * @return int
	 */
	public static function absint( $val ): int {
		return absint( $val );
	}

	/**
	 * اعتبارسنجی اعشاری مثبت.
	 *
	 * @param mixed $val
	 * @return float
	 */
	public static function floatval( $val ): float {
		return max( 0.0, floatval( $val ) );
	}
}

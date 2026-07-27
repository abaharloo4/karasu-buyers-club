<?php
namespace KarasuBuyersClub\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کلاس کمکی قالب‌بندی اعداد و تاریخ‌های شمسی.
 *
 * @package KarasuBuyersClub\Utils
 */
class Formatter {

	/**
	 * تبدیل ارقام انگلیسی به فارسی.
	 *
	 * @param string|int|float $number
	 * @return string
	 */
	public static function persian_digits( $number ): string {
		$en_digits = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
		$fa_digits = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );

		return str_replace( $en_digits, $fa_digits, (string) $number );
	}

	/**
	 * فرمت مبلغ ریال/تومان با جداساز سه رقمی.
	 *
	 * @param float|int $amount
	 * @param string $currency
	 * @return string
	 */
	public static function format_price( $amount, string $currency = 'تومان' ): string {
		$formatted = number_format_i18n( (float) $amount );
		return sprintf( '%s %s', $formatted, $currency );
	}

	/**
	 * فرمت عدد امتیاز.
	 *
	 * @param float|int $points
	 * @return string
	 */
	public static function format_points( $points ): string {
		return sprintf( '%s امتیاز', number_format_i18n( (float) $points ) );
	}
}

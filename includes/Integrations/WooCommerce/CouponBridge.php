<?php
namespace KarasuBuyersClub\Integrations\WooCommerce;

use WC_Coupon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * پل ساخت کدهای تخفیف پویا از امتیازات مصرف‌شده در ووکامرس.
 *
 * @package KarasuBuyersClub\Integrations\WooCommerce
 */
class CouponBridge {

	/**
	 * ساخت یک کد تخفیف یکبارمصرف پویا در ووکامرس.
	 *
	 * @param int    $user_id         شناسه کاربر
	 * @param float  $discount_amount مبلغ تخفیف به تومان/ریال
	 * @param string $discount_type   نوع تخفیف (fixed_cart یا percent)
	 * @return string|bool کد کوپن ساخته‌شده یا false
	 */
	public static function create_dynamic_coupon( int $user_id, float $discount_amount, string $discount_type = 'fixed_cart' ) {
		if ( ! class_exists( 'WC_Coupon' ) ) {
			return false;
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return false;
		}

		$coupon_code = strtolower( 'kbc-' . wp_generate_password( 8, false ) );

		$coupon = new WC_Coupon();
		$coupon->set_code( $coupon_code );
		$coupon->set_amount( $discount_amount );
		$coupon->set_discount_type( $discount_type );
		$coupon->set_individual_use( true );
		$coupon->set_usage_limit( 1 );
		$coupon->set_usage_limit_per_user( 1 );
		$coupon->set_email_restrictions( array( $user->user_email ) );
		$coupon->set_description( sprintf( __( 'کد تخفیف پاداش باشگاه مشتریان برای کاربر %s', 'karasu-buyers-club' ), $user->user_email ) );

		// تاریخ انقضای ۳۰ روزه برای کوپن پاداش.
		$expiry_date = strtotime( '+30 days', current_time( 'timestamp' ) );
		$coupon->set_date_expires( $expiry_date );

		$coupon_id = $coupon->save();

		return $coupon_id ? $coupon_code : false;
	}
}

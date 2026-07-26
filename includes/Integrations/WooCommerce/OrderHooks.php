<?php
namespace KarasuBuyersClub\Integrations\WooCommerce;

use KarasuBuyersClub\Services\PointsEngineService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * اتصال هوک‌های سفارشات ووکامرس به سرویس‌های Karasu Buyers Club.
 *
 * @package KarasuBuyersClub\Integrations\WooCommerce
 */
class OrderHooks {

	/**
	 * ثبت هوک‌های سفارش ووکامرس.
	 */
	public static function init(): void {
		// اعطای امتیاز هنگام تکمیل سفارش.
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'on_order_completed' ), 10, 1 );
	}

	/**
	 * اجرای سرویس اعطای امتیاز به مشتری هنگام تکمیل سفارش.
	 *
	 * @param int $order_id
	 */
	public static function on_order_completed( int $order_id ): void {
		if ( ! $order_id ) {
			return;
		}

		$service = new PointsEngineService();
		$service->award_for_purchase( $order_id );
	}
}

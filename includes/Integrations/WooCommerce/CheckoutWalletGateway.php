<?php
namespace KarasuBuyersClub\Integrations\WooCommerce;

use KarasuBuyersClub\Services\WalletService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * مدیریت اعمال تخفیف موجودی کیف‌پول در سبد خرید و چک‌اوت ووکامرس.
 *
 * @package KarasuBuyersClub\Integrations\WooCommerce
 */
class CheckoutWalletGateway {

	/**
	 * ثبت هوک‌های سبد خرید.
	 */
	public static function init(): void {
		// با اولویت بالا (۹۹) تا پس از سایر تخفیف‌ها اجرا شود (مطابق DEBUG_LOG.md نمونه).
		add_action( 'woocommerce_cart_calculate_fees', array( __CLASS__, 'apply_wallet_fee' ), 99 );
		add_action( 'woocommerce_checkout_order_processed', array( __CLASS__, 'process_wallet_deduction' ), 10, 1 );
	}

	/**
	 * محاسبه و اعمال کسر موجودی کیف‌پول به‌عنوان تخفیف (Fee منفی) روی سبد خرید.
	 */
	public static function apply_wallet_fee(): void {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();

		// بررسی اینکه آیا مشتری گزینه استفاده از کیف‌پول را فعال کرده است یا خیر.
		if ( empty( WC()->session->get( 'kbc_use_wallet' ) ) ) {
			return;
		}

		$wallet_service = new WalletService();
		$balance        = $wallet_service->get_balance( $user_id );

		if ( $balance <= 0 ) {
			return;
		}

		$cart = WC()->cart;
		if ( ! $cart ) {
			return;
		}

		$cart_subtotal = floatval( $cart->get_subtotal() );
		$discount      = min( $balance, $cart_subtotal );

		if ( $discount > 0 ) {
			$cart->add_fee(
				__( 'استفاده از موجودی کیف‌پول باشگاه', 'karasu-buyers-club' ),
				-$discount,
				false
			);
		}
	}

	/**
	 * کسر واقعی موجودی کیف‌پول پس از ثبت موفق سفارش.
	 *
	 * @param int $order_id
	 */
	public static function process_wallet_deduction( int $order_id ): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();

		if ( empty( WC()->session->get( 'kbc_use_wallet' ) ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// یافتن مبلغ کسرشده از کیف‌پول در سفارش.
		foreach ( $order->get_fees() as $fee ) {
			if ( strpos( $fee->get_name(), 'کیف‌پول' ) !== false ) {
				$amount = abs( floatval( $fee->get_total() ) );
				if ( $amount > 0 ) {
					$service = new WalletService();
					$service->debit_wallet( $user_id, $amount, 'checkout', (string) $order_id );
				}
				break;
			}
		}

		// پاک‌سازی سشن پس از ثبت سفارش.
		WC()->session->set( 'kbc_use_wallet', false );
	}
}

<?php
namespace KarasuBuyersClub\Services;

use KarasuBuyersClub\Database\Repositories\PointsRepository;
use KarasuBuyersClub\Integrations\WooCommerce\CouponBridge;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * سرویس مدیریت تبدیل امتیاز به پاداش‌ها.
 *
 * @package KarasuBuyersClub\Services
 */
class RedemptionService {

	/**
	 * مخزن امتیازات.
	 *
	 * @var PointsRepository
	 */
	private $points_repository;

	/**
	 * سرویس کیف‌پول.
	 *
	 * @var WalletService
	 */
	private $wallet_service;

	/**
	 * سازنده.
	 */
	public function __construct() {
		$this->points_repository = new PointsRepository();
		$this->wallet_service   = new WalletService();
	}

	/**
	 * تبدیل امتیاز کاربر به کد تخفیف یا شارژ کیف‌پول.
	 *
	 * @param int    $user_id
	 * @param float  $points_to_redeem
	 * @param string $reward_type (coupon, wallet, free_shipping)
	 * @return array|bool
	 */
	public function redeem_points( int $user_id, float $points_to_redeem, string $reward_type ) {
		if ( $points_to_redeem <= 0 ) {
			return array(
				'success' => false,
				'message' => __( 'تعداد امتیاز درخواستی نامعتبر است.', 'karasu-buyers-club' ),
			);
		}

		$current_balance = $this->points_repository->get_user_balance( $user_id );

		if ( $current_balance < $points_to_redeem ) {
			return array(
				'success' => false,
				'message' => __( 'موجودی امتیاز شما کافی نیست.', 'karasu-buyers-club' ),
			);
		}

		// نرخ تبدیل (پیش‌فرض: هر ۱ امتیاز = ۱,۰۰۰ تومان ارزش پاداش).
		$redemption_rate = floatval( get_option( 'kbc_redemption_rate', 1000 ) );
		$reward_value    = $points_to_redeem * $redemption_rate;

		$result_data = array();

		switch ( $reward_type ) {
			case 'wallet':
				$credited = $this->wallet_service->credit_wallet( $user_id, $reward_value, 'redemption' );
				if ( ! $credited ) {
					return array(
						'success' => false,
						'message' => __( 'خطا در شارژ کیف‌پول.', 'karasu-buyers-club' ),
					);
				}
				$result_data['wallet_credited'] = $reward_value;
				break;

			case 'coupon':
				$coupon_code = CouponBridge::create_dynamic_coupon( $user_id, $reward_value, 'fixed_cart' );
				if ( ! $coupon_code ) {
					return array(
						'success' => false,
						'message' => __( 'خطا در ساخت کد تخفیف.', 'karasu-buyers-club' ),
					);
				}
				$result_data['coupon_code'] = $coupon_code;
				break;

			default:
				return array(
					'success' => false,
					'message' => __( 'نوع پاداش انتخابی نامعتبر است.', 'karasu-buyers-club' ),
				);
		}

		// کسر امتیاز از دفتر کل امتیازات کاربر.
		$this->points_repository->add_transaction(
			$user_id,
			$points_to_redeem,
			'redeemed',
			'user_action',
			$reward_type
		);

		do_action( 'kbc_points_redeemed', $user_id, $points_to_redeem, $reward_type, $result_data );

		return array(
			'success' => true,
			'data'    => $result_data,
			'message' => __( 'امتیاز با موفقیت به پاداش تبدیل شد.', 'karasu-buyers-club' ),
		);
	}
}

<?php
namespace KarasuBuyersClub\Services;

use KarasuBuyersClub\Database\Repositories\TierRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * سرویس مدیریت سطوح مشتریان و محاسبه LTV.
 *
 * @package KarasuBuyersClub\Services
 */
class TierService {

	/**
	 * مخزن داده سطوح.
	 *
	 * @var TierRepository
	 */
	private $tier_repository;

	/**
	 * سازنده.
	 *
	 * @param TierRepository|null $repository
	 */
	public function __construct( ?TierRepository $repository = null ) {
		$this->tier_repository = $repository ?? new TierRepository();
	}

	/**
	 * محاسبه مجموع خرید کل دوره عضویت مشتری (Lifetime Value).
	 *
	 * @param int $user_id
	 * @return float
	 */
	public function calculate_user_ltv( int $user_id ): float {
		if ( ! function_exists( 'wc_get_orders' ) ) {
			return 0.0;
		}

		$orders = wc_get_orders(
			array(
				'customer_id' => absint( $user_id ),
				'status'      => array( 'wc-completed' ),
				'limit'       => -1,
				'return'      => 'ids',
			)
		);

		$total = 0.0;

		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$total += floatval( $order->get_total() );
			}
		}

		return $total;
	}

	/**
	 * محاسبه و ارتقای سطح کاربر بر اساس LTV بدون تنزل سطح.
	 *
	 * @param int $user_id
	 * @return array|null سطح جدید یا سطح فعلی
	 */
	public function recalculate_user_tier( int $user_id ): ?array {
		$ltv = $this->calculate_user_ltv( $user_id );
		$all_tiers = $this->tier_repository->get_all_tiers();

		if ( empty( $all_tiers ) ) {
			return null;
		}

		$current_tier = $this->tier_repository->get_user_current_tier( $user_id );
		$current_threshold = $current_tier ? floatval( $current_tier['threshold'] ) : -1.0;

		$new_eligible_tier = null;

		foreach ( $all_tiers as $tier ) {
			$threshold = floatval( $tier['threshold'] );
			if ( $ltv >= $threshold && $threshold > $current_threshold ) {
				$new_eligible_tier = $tier;
			}
		}

		// در صورت وجود سطح بالاتر شایسته، ارتقا ثبت می‌شود.
		if ( $new_eligible_tier ) {
			$this->tier_repository->record_tier_achievement( $user_id, absint( $new_eligible_tier['id'] ) );
			do_action( 'kbc_user_tier_upgraded', $user_id, $new_eligible_tier, $current_tier );
			return $new_eligible_tier;
		}

		return $current_tier;
	}

	/**
	 * دریافت ضریب امتیازدهی سطح کاربر.
	 *
	 * @param int $user_id
	 * @return float
	 */
	public function get_user_point_multiplier( int $user_id ): float {
		$current_tier = $this->tier_repository->get_user_current_tier( $user_id );

		if ( ! $current_tier || empty( $current_tier['perks'] ) ) {
			return 1.0;
		}

		$perks = json_decode( $current_tier['perks'], true );
		if ( is_array( $perks ) && isset( $perks['point_multiplier'] ) ) {
			return floatval( $perks['point_multiplier'] );
		}

		return 1.0;
	}
}

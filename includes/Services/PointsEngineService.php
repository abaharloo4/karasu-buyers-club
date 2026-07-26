<?php
namespace KarasuBuyersClub\Services;

use KarasuBuyersClub\Database\Repositories\PointsRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * سرویس اصلی موتور امتیازدهی Karasu Buyers Club.
 *
 * @package KarasuBuyersClub\Services
 */
class PointsEngineService {

	/**
	 * مخزن امتیازات.
	 *
	 * @var PointsRepository
	 */
	private $points_repository;

	/**
	 * سازنده.
	 *
	 * @param PointsRepository|null $repository
	 */
	public function __construct( ?PointsRepository $repository = null ) {
		$this->points_repository = $repository ?? new PointsRepository();
	}

	/**
	 * محاسبه و اعطای امتیاز خرید بر اساس سفارش تکمیل‌شده ووکامرس.
	 *
	 * @param int $order_id
	 * @return bool|int
	 */
	public function award_for_purchase( int $order_id ) {
		if ( ! function_exists( 'wc_get_order' ) ) {
			return false;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return false;
		}

		$user_id = $order->get_customer_id();

		if ( ! $user_id || $user_id <= 0 ) {
			return false;
		}

		// چک جلوگیری از اعطای تکراری امتیاز برای یک سفارش.
		$history = $this->points_repository->get_user_history( $user_id, 100 );
		foreach ( $history as $tx ) {
			if ( 'purchase' === $tx['source'] && (string) $order_id === (string) $tx['reference_id'] ) {
				return false; // قبلاً اعطا شده است.
			}
		}

		// نرخ تبدیل تومان/ریال به امتیاز (پیش‌فرض: هر ۱۰۰,۰۰۰ تومان = ۱۰ امتیاز).
		$earn_rate = floatval( get_option( 'kbc_purchase_earn_rate', 10000 ) );

		if ( $earn_rate <= 0 ) {
			return false;
		}

		$order_total = floatval( $order->get_total() );
		$points      = floor( $order_total / $earn_rate );

		if ( $points <= 0 ) {
			return false;
		}

		// محاسبه تاریخ انقضا در صورت فعال بودن.
		$expires_at = $this->calculate_expiry_date();

		$tx_id = $this->points_repository->add_transaction(
			$user_id,
			$points,
			'earned',
			'purchase',
			(string) $order_id,
			$expires_at
		);

		if ( $tx_id ) {
			/**
			 * اکشن هوک اختصاصی پس از اعطای امتیاز خرید.
			 *
			 * @param int   $user_id
			 * @param float $points
			 * @param int   $order_id
			 */
			do_action( 'kbc_points_awarded_for_purchase', $user_id, $points, $order_id );
		}

		return $tx_id;
	}

	/**
	 * اعطای امتیاز برای اکشن‌های خاص (ثبت‌نام، اولین خرید، ثبت نظر).
	 *
	 * @param int    $user_id
	 * @param string $action_key (signup, first_order, review)
	 * @param string|null $reference_id
	 * @return bool|int
	 */
	public function award_for_action( int $user_id, string $action_key, ?string $reference_id = null ) {
		$option_key = 'kbc_action_points_' . sanitize_key( $action_key );
		$points     = floatval( get_option( $option_key, $this->get_default_action_points( $action_key ) ) );

		if ( $points <= 0 ) {
			return false;
		}

		// تاریخ انقضا.
		$expires_at = $this->calculate_expiry_date();

		$tx_id = $this->points_repository->add_transaction(
			$user_id,
			$points,
			'earned',
			sanitize_key( $action_key ),
			$reference_id,
			$expires_at
		);

		if ( $tx_id ) {
			do_action( 'kbc_points_awarded_for_action', $user_id, $points, $action_key );
		}

		return $tx_id;
	}

	/**
	 * دریافت مقادیر پیش‌فرض امتیاز اکشن‌ها.
	 *
	 * @param string $action_key
	 * @return float
	 */
	private function get_default_action_points( string $action_key ): float {
		$defaults = array(
			'signup'      => 50.0,
			'first_order' => 100.0,
			'review'      => 20.0,
		);

		return $defaults[ $action_key ] ?? 0.0;
	}

	/**
	 * محاسبه تاریخ انقضای امتیاز بر اساس تنظیمات.
	 *
	 * @return string|null
	 */
	private function calculate_expiry_date(): ?string {
		$expiry_enabled = (bool) get_option( 'kbc_expiry_enabled', false );

		if ( ! $expiry_enabled ) {
			return null;
		}

		$months = absint( get_option( 'kbc_expiry_months', 6 ) );

		if ( $months <= 0 ) {
			return null;
		}

		return date( 'Y-m-d H:i:s', strtotime( "+{$months} months", current_time( 'timestamp' ) ) );
	}

	/**
	 * اجرای زمان‌بندی‌شده Cron انقضای امتیازات.
	 *
	 * @return int تعداد کدهای پردازش‌شده
	 */
	public function run_expiry_cron(): int {
		return $this->points_repository->process_expired_points();
	}
}

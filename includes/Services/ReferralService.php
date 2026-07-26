<?php
namespace KarasuBuyersClub\Services;

use KarasuBuyersClub\Database\Repositories\ReferralRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * سرویس مدیریت معرفی دوست و پاداش دوطرفه.
 *
 * @package KarasuBuyersClub\Services
 */
class ReferralService {

	/**
	 * مخزن داده‌های معرفی.
	 *
	 * @var ReferralRepository
	 */
	private $referral_repository;

	/**
	 * سرویس موتور امتیاز.
	 *
	 * @var PointsEngineService
	 */
	private $points_engine;

	/**
	 * سازنده.
	 */
	public function __construct() {
		$this->referral_repository = new ReferralRepository();
		$this->points_engine       = new PointsEngineService();
	}

	/**
	 * دریافت یا ایجاد کد معرف کاربر.
	 *
	 * @param int $user_id
	 * @return string
	 */
	public function get_user_code( int $user_id ): string {
		return $this->referral_repository->get_or_create_referral_code( $user_id );
	}

	/**
	 * پردازش ثبت‌نام کاربر جدید با کد معرف.
	 *
	 * @param int    $new_user_id
	 * @param string $referral_code
	 * @return bool
	 */
	public function process_referral_signup( int $new_user_id, string $referral_code ): bool {
		$referrer_user_id = $this->referral_repository->get_user_by_code( $referral_code );

		if ( ! $referrer_user_id || $referrer_user_id === $new_user_id ) {
			return false; // کد وجود ندارد یا کاربر سعی دارد کد خودش را وارد کند (ضد سوءاستفاده).
		}

		// ثبت رابطه معرف.
		$this->referral_repository->record_referral_link( $new_user_id, $referrer_user_id );

		// شرط اعطای امتیاز: اگر فقط ثبت‌نام شرط باشد.
		$reward_trigger = get_option( 'kbc_referral_trigger', 'signup' );

		if ( 'signup' === $reward_trigger ) {
			$this->award_dual_referral_points( $referrer_user_id, $new_user_id );
		}

		return true;
	}

	/**
	 * اعطای امتیاز دوطرفه (معرف + معرفی‌شونده).
	 *
	 * @param int $referrer_id
	 * @param int $referee_id
	 */
	public function award_dual_referral_points( int $referrer_id, int $referee_id ): void {
		$referrer_points = floatval( get_option( 'kbc_referral_referrer_points', 100 ) );
		$referee_points  = floatval( get_option( 'kbc_referral_referee_points', 50 ) );

		if ( $referrer_points > 0 ) {
			$this->points_engine->award_for_action( $referrer_id, 'referral', 'ref_' . $referee_id );
		}

		if ( $referee_points > 0 ) {
			$this->points_engine->award_for_action( $referee_id, 'referred_by', 'refby_' . $referrer_id );
		}

		do_action( 'kbc_referral_completed', $referrer_id, $referee_id );
	}
}

<?php
namespace KarasuBuyersClub\Database\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * دسترسی دیتابیس به کدهای معرف و روابط معرفی (kbc_referrals).
 *
 * @package KarasuBuyersClub\Database\Repositories
 */
class ReferralRepository {

	/**
	 * نام جدول.
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'kbc_referrals';
	}

	/**
	 * دریافت یا ساخت کد معرف اختصاصی کاربر.
	 *
	 * @param int $user_id
	 * @return string
	 */
	public function get_or_create_referral_code( int $user_id ): string {
		global $wpdb;

		$table = $this->get_table_name();

		$query = $wpdb->prepare( "SELECT referral_code FROM {$table} WHERE user_id = %d LIMIT 1", absint( $user_id ) );
		$code  = $wpdb->get_var( $query );

		if ( $code ) {
			return $code;
		}

		// ساخت کد جدید منحصربه‌فرد.
		$new_code = 'kbc' . absint( $user_id ) . strtolower( wp_generate_password( 4, false ) );

		$wpdb->insert(
			$table,
			array(
				'user_id'             => absint( $user_id ),
				'referral_code'       => $new_code,
				'referred_by_user_id' => null,
				'status'              => 'active',
				'created_at'          => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%d', '%s', '%s' )
		);

		return $new_code;
	}

	/**
	 * یافتن کاربر مالک کد معرف.
	 *
	 * @param string $referral_code
	 * @return int|null
	 */
	public function get_user_by_code( string $referral_code ): ?int {
		global $wpdb;

		$table = $this->get_table_name();
		$query = $wpdb->prepare( "SELECT user_id FROM {$table} WHERE referral_code = %s LIMIT 1", sanitize_key( $referral_code ) );

		$user_id = $wpdb->get_var( $query );

		return $user_id ? absint( $user_id ) : null;
	}

	/**
	 * ثبت رابطه معرف و معرفی‌شونده جدید.
	 *
	 * @param int $referee_user_id    کاربر جدید (معرفی‌شونده)
	 * @param int $referrer_user_id   کاربر معرف
	 * @return int|bool
	 */
	public function record_referral_link( int $referee_user_id, int $referrer_user_id ) {
		global $wpdb;

		$table = $this->get_table_name();

		$wpdb->update(
			$table,
			array( 'referred_by_user_id' => absint( $referrer_user_id ) ),
			array( 'user_id' => absint( $referee_user_id ) ),
			array( '%d' ),
			array( '%d' )
		);

		return true;
	}

	/**
	 * دریافت آمار فعالیت معرفی کاربر.
	 *
	 * @param int $user_id
	 * @return array (count, list)
	 */
	public function get_referral_stats( int $user_id ): array {
		global $wpdb;

		$table = $this->get_table_name();

		$query = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table} WHERE referred_by_user_id = %d",
			absint( $user_id )
		);

		$total_referred = $wpdb->get_var( $query );

		return array(
			'total_referred' => absint( $total_referred ),
		);
	}
}

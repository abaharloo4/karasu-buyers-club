<?php
namespace KarasuBuyersClub\Database\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * دسترسی دیتابیس به تاریخچه اعلان‌ها (kbc_notifications_log).
 *
 * @package KarasuBuyersClub\Database\Repositories
 */
class NotificationRepository {

	/**
	 * نام جدول.
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'kbc_notifications_log';
	}

	/**
	 * ثبت تراکنش ارسال اعلان.
	 *
	 * @param int    $user_id
	 * @param string $channel (sms, email, in_app)
	 * @param string $template_key (points_earned, tier_upgraded, birthday, etc.)
	 * @param string $status (sent, failed)
	 * @return int|bool
	 */
	public function add_log( int $user_id, string $channel, string $template_key, string $status ) {
		global $wpdb;

		$table = $this->get_table_name();

		$data = array(
			'user_id'      => absint( $user_id ),
			'channel'      => sanitize_key( $channel ),
			'template_key' => sanitize_key( $template_key ),
			'status'       => sanitize_key( $status ),
			'sent_at'      => current_time( 'mysql' ),
		);

		$result = $wpdb->insert( $table, $data, array( '%d', '%s', '%s', '%s', '%s' ) );

		return false !== $result ? $wpdb->insert_id : false;
	}

	/**
	 * دریافت لیست اعلان‌های داخلی کاربر.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function get_user_in_app_notifications( int $user_id, int $limit = 20 ): array {
		global $wpdb;

		$table = $this->get_table_name();

		$query = $wpdb->prepare(
			"SELECT * FROM {$table}
			WHERE user_id = %d AND channel = 'in_app'
			ORDER BY sent_at DESC
			LIMIT %d",
			absint( $user_id ),
			absint( $limit )
		);

		return $wpdb->get_results( $query, ARRAY_A ) ?: array();
	}
}

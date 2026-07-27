<?php
namespace KarasuBuyersClub\Database\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * دسترسی دیتابیس به جداول سطوح مشتریان (kbc_tiers و kbc_user_tier_history).
 *
 * @package KarasuBuyersClub\Database\Repositories
 */
class TierRepository {

	/**
	 * نام جدول سطوح.
	 *
	 * @return string
	 */
	private function get_tiers_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'kbc_tiers';
	}

	/**
	 * نام جدول تاریخچه سطح کاربران.
	 *
	 * @return string
	 */
	private function get_history_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'kbc_user_tier_history';
	}

	/**
	 * دریافت تمام سطوح تعریف‌شده (مرتب‌شده بر اساس sort_order یا threshold).
	 *
	 * @return array
	 */
	public function get_all_tiers(): array {
		global $wpdb;

		$table = $this->get_tiers_table();
		$query = "SELECT * FROM {$table} WHERE is_active = 1 ORDER BY threshold ASC";

		return $wpdb->get_results( $query, ARRAY_A ) ?: array();
	}

	/**
	 * دریافت سطح بر اساس شناسه.
	 *
	 * @param int $tier_id
	 * @return array|null
	 */
	public function get_tier_by_id( int $tier_id ): ?array {
		global $wpdb;

		$table = $this->get_tiers_table();
		$query = $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", absint( $tier_id ) );

		$row = $wpdb->get_row( $query, ARRAY_A );
		return $row ?: null;
	}

	/**
	 * دریافت سطح فعلی کاربر بر اساس آخرین ارتقا در تاریخچه.
	 *
	 * @param int $user_id
	 * @return array|null
	 */
	public function get_user_current_tier( int $user_id ): ?array {
		global $wpdb;

		$tiers_table   = $this->get_tiers_table();
		$history_table = $this->get_history_table();

		$query = $wpdb->prepare(
			"SELECT t.*, h.achieved_at 
			FROM {$tiers_table} t
			INNER JOIN {$history_table} h ON t.id = h.tier_id
			WHERE h.user_id = %d
			ORDER BY t.threshold DESC
			LIMIT 1",
			absint( $user_id )
		);

		$row = $wpdb->get_row( $query, ARRAY_A );
		return $row ?: null;
	}

	/**
	 * ثبت ارتقای جدید کاربر به یک سطح.
	 *
	 * @param int $user_id
	 * @param int $tier_id
	 * @return bool|int
	 */
	public function record_tier_achievement( int $user_id, int $tier_id ) {
		global $wpdb;

		return $wpdb->insert(
			$this->get_history_table(),
			array(
				'user_id'     => absint( $user_id ),
				'tier_id'     => absint( $tier_id ),
				'achieved_at' => current_time( 'mysql' ),
			),
			array( '%d', '%d', '%s' )
		);
	}

	/**
	 * ایجاد یا به‌روزرسانی سطح.
	 *
	 * @param array $data
	 * @return int|bool
	 */
	public function save_tier( array $data ) {
		global $wpdb;
		$table = $this->get_tiers_table();

		$tier_id = ! empty( $data['id'] ) ? absint( $data['id'] ) : 0;

		$fields = array(
			'name'       => sanitize_text_field( $data['name'] ?? '' ),
			'threshold'  => floatval( $data['threshold'] ?? 0 ),
			'benefits'   => isset( $data['benefits'] ) ? ( is_array( $data['benefits'] ) ? wp_json_encode( $data['benefits'] ) : sanitize_text_field( $data['benefits'] ) ) : '{}',
			'sort_order' => absint( $data['sort_order'] ?? 0 ),
			'is_active'  => isset( $data['is_active'] ) ? absint( $data['is_active'] ) : 1,
		);

		if ( $tier_id > 0 ) {
			return $wpdb->update( $table, $fields, array( 'id' => $tier_id ) );
		} else {
			$wpdb->insert( $table, $fields );
			return $wpdb->insert_id;
		}
	}

	/**
	 * حذف سطح بر اساس ID.
	 *
	 * @param int $tier_id
	 * @return bool|int
	 */
	public function delete_tier( int $tier_id ) {
		global $wpdb;
		$table = $this->get_tiers_table();
		return $wpdb->delete( $table, array( 'id' => absint( $tier_id ) ), array( '%d' ) );
	}
}

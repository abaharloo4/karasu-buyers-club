<?php
namespace KarasuBuyersClub\Database\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * مدیریت دسترسی به دیتابیس برای دفتر تراکنش‌های امتیاز (kbc_points_ledger).
 *
 * @package KarasuBuyersClub\Database\Repositories
 */
class PointsRepository {

	/**
	 * نام جدول.
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'kbc_points_ledger';
	}

	/**
	 * ثبت تراکنش امتیاز جدید.
	 *
	 * @param int         $user_id      شناسه کاربر
	 * @param float       $amount       مبلغ/تعداد امتیاز
	 * @param string      $type         نوع تراکنش (earned, redeemed, expired, adjusted)
	 * @param string      $source       منبع تراکنش (purchase, signup, review, referral, occasion, admin)
	 * @param string|null $reference_id مرجع تراکنش (مثلاً شماره سفارش)
	 * @param string|null $expires_at   تاریخ انقضا (Y-m-d H:i:s)
	 * @return int|bool ID رکورد درج‌شده یا false
	 */
	public function add_transaction( int $user_id, float $amount, string $type, string $source, ?string $reference_id = null, ?string $expires_at = null ) {
		global $wpdb;

		$table_name = $this->get_table_name();

		$data = array(
			'user_id'      => absint( $user_id ),
			'amount'       => floatval( $amount ),
			'type'         => sanitize_key( $type ),
			'source'       => sanitize_key( $source ),
			'reference_id' => $reference_id ? sanitize_text_field( $reference_id ) : null,
			'expires_at'   => $expires_at ? sanitize_text_field( $expires_at ) : null,
			'created_at'   => current_time( 'mysql' ),
		);

		$formats = array( '%d', '%f', '%s', '%s', '%s', '%s', '%s' );

		$result = $wpdb->insert( $table_name, $data, $formats );

		if ( false === $result ) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * دریافت موجودی فعلی امتیاز کاربر.
	 *
	 * @param int $user_id
	 * @return float
	 */
	public function get_user_balance( int $user_id ): float {
		global $wpdb;

		$table_name = $this->get_table_name();

		// مجموع امتیازات دریافتی کاربر که هنوز انقضا نشده‌اند یا مصرف نشده‌اند.
		$query = $wpdb->prepare(
			"SELECT 
				SUM(CASE WHEN type IN ('earned', 'adjusted') AND amount > 0 THEN amount ELSE 0 END) -
				SUM(CASE WHEN type IN ('redeemed', 'expired') OR (type = 'adjusted' AND amount < 0) THEN ABS(amount) ELSE 0 END)
			FROM {$table_name}
			WHERE user_id = %d",
			absint( $user_id )
		);

		$balance = $wpdb->get_var( $query );

		return max( 0.0, floatval( $balance ) );
	}

	/**
	 * دریافت سابقه تراکنش‌های امتیاز کاربر.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get_user_history( int $user_id, int $limit = 20, int $offset = 0 ): array {
		global $wpdb;

		$table_name = $this->get_table_name();

		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name}
			WHERE user_id = %d
			ORDER BY created_at DESC
			LIMIT %d OFFSET %d",
			absint( $user_id ),
			absint( $limit ),
			absint( $offset )
		);

		return $wpdb->get_results( $query, ARRAY_A ) ?: array();
	}

	/**
	 * پردازش و منقضی کردن امتیازاتی که تاریخ انقضای آن‌ها گذشته است.
	 *
	 * @return int تعداد کدهای منقضی شده
	 */
	public function process_expired_points(): int {
		global $wpdb;

		$table_name   = $this->get_table_name();
		$current_time = current_time( 'mysql' );

		// دریافت لیست رکوردهای منقضی‌شده که هنوز ثبت انقضا برای آن‌ها انجام نشده است.
		$query = $wpdb->prepare(
			"SELECT id, user_id, amount FROM {$table_name}
			WHERE type = 'earned' 
			AND expires_at IS NOT NULL 
			AND expires_at <= %s",
			$current_time
		);

		$expired_rows = $wpdb->get_results( $query, ARRAY_A );
		$processed   = 0;

		if ( ! empty( $expired_rows ) ) {
			foreach ( $expired_rows as $row ) {
				// ثبت تراکنش منفی انقضا.
				$inserted = $this->add_transaction(
					absint( $row['user_id'] ),
					floatval( $row['amount'] ),
					'expired',
					'system',
					'exp_' . $row['id']
				);

				if ( $inserted ) {
					$processed++;
				}
			}
		}

		return $processed;
	}
}

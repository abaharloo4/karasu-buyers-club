<?php
namespace KarasuBuyersClub\Database\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * دسترسی دیتابیس به جدول کیف‌پول داخلی (kbc_wallet_ledger).
 *
 * @package KarasuBuyersClub\Database\Repositories
 */
class WalletRepository {

	/**
	 * نام جدول.
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'kbc_wallet_ledger';
	}

	/**
	 * ثبت تراکنش کیف‌پول.
	 *
	 * @param int         $user_id
	 * @param float       $amount
	 * @param string      $type (credit, debit)
	 * @param string      $source (redemption, cashback, checkout, admin)
	 * @param string|null $reference_id
	 * @return int|bool
	 */
	public function add_transaction( int $user_id, float $amount, string $type, string $source, ?string $reference_id = null ) {
		global $wpdb;

		$table = $this->get_table_name();

		$data = array(
			'user_id'      => absint( $user_id ),
			'amount'       => floatval( $amount ),
			'type'         => sanitize_key( $type ),
			'source'       => sanitize_key( $source ),
			'reference_id' => $reference_id ? sanitize_text_field( $reference_id ) : null,
			'created_at'   => current_time( 'mysql' ),
		);

		$result = $wpdb->insert( $table, $data, array( '%d', '%f', '%s', '%s', '%s', '%s' ) );

		return false !== $result ? $wpdb->insert_id : false;
	}

	/**
	 * استعلام موجودی کیف‌پول کاربر.
	 *
	 * @param int $user_id
	 * @return float
	 */
	public function get_user_balance( int $user_id ): float {
		global $wpdb;

		$table = $this->get_table_name();

		$query = $wpdb->prepare(
			"SELECT 
				SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) -
				SUM(CASE WHEN type = 'debit' THEN ABS(amount) ELSE 0 END)
			FROM {$table}
			WHERE user_id = %d",
			absint( $user_id )
		);

		$balance = $wpdb->get_var( $query );

		return max( 0.0, floatval( $balance ) );
	}

	/**
	 * تاریخچه تراکنش‌های کیف‌پول کاربر.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get_user_history( int $user_id, int $limit = 20, int $offset = 0 ): array {
		global $wpdb;

		$table = $this->get_table_name();

		$query = $wpdb->prepare(
			"SELECT * FROM {$table}
			WHERE user_id = %d
			ORDER BY created_at DESC
			LIMIT %d OFFSET %d",
			absint( $user_id ),
			absint( $limit ),
			absint( $offset )
		);

		return $wpdb->get_results( $query, ARRAY_A ) ?: array();
	}
}

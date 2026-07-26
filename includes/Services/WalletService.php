<?php
namespace KarasuBuyersClub\Services;

use KarasuBuyersClub\Database\Repositories\WalletRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * سرویس مدیریت کیف‌پول داخلی مشتری.
 *
 * @package KarasuBuyersClub\Services
 */
class WalletService {

	/**
	 * مخزن کیف‌پول.
	 *
	 * @var WalletRepository
	 */
	private $wallet_repository;

	/**
	 * سازنده.
	 *
	 * @param WalletRepository|null $repository
	 */
	public function __construct( ?WalletRepository $repository = null ) {
		$this->wallet_repository = $repository ?? new WalletRepository();
	}

	/**
	 * شارژ (واریز به) کیف‌پول کاربر.
	 *
	 * @param int         $user_id
	 * @param float       $amount
	 * @param string      $source
	 * @param string|null $reference_id
	 * @return int|bool
	 */
	public function credit_wallet( int $user_id, float $amount, string $source, ?string $reference_id = null ) {
		if ( $amount <= 0 ) {
			return false;
		}

		$tx_id = $this->wallet_repository->add_transaction( $user_id, $amount, 'credit', $source, $reference_id );

		if ( $tx_id ) {
			do_action( 'kbc_wallet_credited', $user_id, $amount, $source, $reference_id );
		}

		return $tx_id;
	}

	/**
	 * کسر از کیف‌پول کاربر (در صورت وجود موجودی کافی).
	 *
	 * @param int         $user_id
	 * @param float       $amount
	 * @param string      $source
	 * @param string|null $reference_id
	 * @return int|bool
	 */
	public function debit_wallet( int $user_id, float $amount, string $source, ?string $reference_id = null ) {
		if ( $amount <= 0 ) {
			return false;
		}

		$current_balance = $this->wallet_repository->get_user_balance( $user_id );

		if ( $current_balance < $amount ) {
			return false; // موجودی کافی نیست.
		}

		$tx_id = $this->wallet_repository->add_transaction( $user_id, $amount, 'debit', $source, $reference_id );

		if ( $tx_id ) {
			do_action( 'kbc_wallet_debited', $user_id, $amount, $source, $reference_id );
		}

		return $tx_id;
	}

	/**
	 * استعلام موجودی کیف‌پول.
	 *
	 * @param int $user_id
	 * @return float
	 */
	public function get_balance( int $user_id ): float {
		return $this->wallet_repository->get_user_balance( $user_id );
	}
}

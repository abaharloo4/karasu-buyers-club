<?php
namespace KarasuBuyersClub\REST\Controllers;

use KarasuBuyersClub\Database\Repositories\WalletRepository;
use KarasuBuyersClub\REST\Permissions\PermissionChecker;
use KarasuBuyersClub\Services\WalletService;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کنترلر REST کیف‌پول مشتری.
 *
 * @package KarasuBuyersClub\REST\Controllers
 */
class WalletController extends WP_REST_Controller {

	/**
	 * فضای نام API.
	 *
	 * @var string
	 */
	protected $namespace = 'karasu-buyers-club/v1';

	/**
	 * ثبت مسیرها.
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/wallet/balance',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_balance' ),
				'permission_callback' => array( PermissionChecker::class, 'check_logged_in' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/wallet/history',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_history' ),
				'permission_callback' => array( PermissionChecker::class, 'check_logged_in' ),
				'args'                => array(
					'limit'  => array(
						'sanitize_callback' => 'absint',
						'default'           => 20,
					),
					'offset' => array(
						'sanitize_callback' => 'absint',
						'default'           => 0,
					),
				),
			)
		);
	}

	/**
	 * استعلام موجودی کیف‌پول.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_balance( WP_REST_Request $request ): WP_REST_Response {
		$user_id        = get_current_user_id();
		$wallet_service = new WalletService();
		$balance        = $wallet_service->get_balance( $user_id );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'balance' => $balance,
				),
			),
			200
		);
	}

	/**
	 * دریافت سابقه تراکنش‌های کیف‌پول.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_history( WP_REST_Request $request ): WP_REST_Response {
		$user_id = get_current_user_id();
		$limit   = $request->get_param( 'limit' );
		$offset  = $request->get_param( 'offset' );

		$repository = new WalletRepository();
		$history    = $repository->get_user_history( $user_id, $limit, $offset );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $history,
			),
			200
		);
	}
}

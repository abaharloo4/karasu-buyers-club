<?php
namespace KarasuBuyersClub\REST\Controllers;

use KarasuBuyersClub\Database\Repositories\PointsRepository;
use KarasuBuyersClub\REST\Permissions\PermissionChecker;
use KarasuBuyersClub\Services\RedemptionService;
use KarasuBuyersClub\Services\TierService;
use KarasuBuyersClub\Services\WalletService;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کنترلر REST امتیازات مشتری.
 *
 * @package KarasuBuyersClub\REST\Controllers
 */
class PointsController extends WP_REST_Controller {

	/**
	 * فضای نام API.
	 *
	 * @var string
	 */
	protected $namespace = 'karasu-buyers-club/v1';

	/**
	 * ثبت مسیرها (Routes).
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/points/summary',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_summary' ),
				'permission_callback' => array( PermissionChecker::class, 'check_logged_in' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/points/history',
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

		register_rest_route(
			$this->namespace,
			'/points/redeem',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'redeem' ),
				'permission_callback' => function( $request ) {
					$logged_in = PermissionChecker::check_logged_in( $request );
					if ( is_wp_error( $logged_in ) ) {
						return $logged_in;
					}
					return PermissionChecker::check_nonce( $request );
				},
				'args'                => array(
					'points'      => array(
						'required'          => true,
						'sanitize_callback' => 'floatval',
					),
					'reward_type' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);
	}

	/**
	 * دریافت خلاصه وضعیت امتیاز، کیف‌پول و سطح کاربر.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_summary( WP_REST_Request $request ): WP_REST_Response {
		$user_id = get_current_user_id();

		$points_repo    = new PointsRepository();
		$tier_service   = new TierService();
		$wallet_service = new WalletService();

		$balance = $points_repo->get_user_balance( $user_id );
		$tier    = $tier_service->recalculate_user_tier( $user_id );
		$wallet  = $wallet_service->get_balance( $user_id );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'points_balance' => $balance,
					'wallet_balance' => $wallet,
					'current_tier'   => $tier,
				),
			),
			200
		);
	}

	/**
	 * دریافت تاریخچه امتیازات کاربر.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_history( WP_REST_Request $request ): WP_REST_Response {
		$user_id = get_current_user_id();
		$limit   = $request->get_param( 'limit' );
		$offset  = $request->get_param( 'offset' );

		$points_repo = new PointsRepository();
		$history     = $points_repo->get_user_history( $user_id, $limit, $offset );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $history,
			),
			200
		);
	}

	/**
	 * تبدیل امتیاز به پاداش.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function redeem( WP_REST_Request $request ): WP_REST_Response {
		$user_id     = get_current_user_id();
		$points      = $request->get_param( 'points' );
		$reward_type = $request->get_param( 'reward_type' );

		$service = new RedemptionService();
		$result  = $service->redeem_points( $user_id, $points, $reward_type );

		$status_code = $result['success'] ? 200 : 400;

		return new WP_REST_Response( $result, $status_code );
	}
}

<?php
namespace KarasuBuyersClub\REST\Controllers;

use KarasuBuyersClub\Database\Repositories\ReferralRepository;
use KarasuBuyersClub\REST\Permissions\PermissionChecker;
use KarasuBuyersClub\Services\ReferralService;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کنترلر REST معرفی دوست.
 *
 * @package KarasuBuyersClub\REST\Controllers
 */
class ReferralController extends WP_REST_Controller {

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
			'/referral/my-code',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_my_code' ),
				'permission_callback' => array( PermissionChecker::class, 'check_logged_in' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/referral/stats',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_stats' ),
				'permission_callback' => array( PermissionChecker::class, 'check_logged_in' ),
			)
		);
	}

	/**
	 * دریافت یا ایجاد کد معرف اختصاصی کاربر.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_my_code( WP_REST_Request $request ): WP_REST_Response {
		$user_id  = get_current_user_id();
		$service  = new ReferralService();
		$code     = $service->get_user_code( $user_id );
		$ref_link = add_query_arg( 'ref', $code, home_url( '/' ) );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'referral_code' => $code,
					'referral_link' => $ref_link,
				),
			),
			200
		);
	}

	/**
	 * دریافت آمار معرفی‌های کاربر.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_stats( WP_REST_Request $request ): WP_REST_Response {
		$user_id    = get_current_user_id();
		$repository = new ReferralRepository();
		$stats      = $repository->get_referral_stats( $user_id );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $stats,
			),
			200
		);
	}
}

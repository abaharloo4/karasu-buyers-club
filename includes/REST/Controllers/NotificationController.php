<?php
namespace KarasuBuyersClub\REST\Controllers;

use KarasuBuyersClub\Database\Repositories\NotificationRepository;
use KarasuBuyersClub\REST\Permissions\PermissionChecker;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کنترلر REST مرکز اعلان‌های داخلی مشتری.
 *
 * @package KarasuBuyersClub\REST\Controllers
 */
class NotificationController extends WP_REST_Controller {

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
			'/notifications',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_notifications' ),
				'permission_callback' => array( PermissionChecker::class, 'check_logged_in' ),
				'args'                => array(
					'limit' => array(
						'sanitize_callback' => 'absint',
						'default'           => 20,
					),
				),
			)
		);
	}

	/**
	 * دریافت لیست اعلان‌های داخلی کاربر.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_notifications( WP_REST_Request $request ): WP_REST_Response {
		$user_id    = get_current_user_id();
		$limit      = $request->get_param( 'limit' );
		$repository = new NotificationRepository();

		$notifications = $repository->get_user_in_app_notifications( $user_id, $limit );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $notifications,
			),
			200
		);
	}
}

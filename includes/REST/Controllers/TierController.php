<?php
namespace KarasuBuyersClub\REST\Controllers;

use KarasuBuyersClub\Database\Repositories\TierRepository;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کنترلر REST سطوح مشتریان.
 *
 * @package KarasuBuyersClub\REST\Controllers
 */
class TierController extends WP_REST_Controller {

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
			'/tiers',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_tiers' ),
				'permission_callback' => '__return_true', // عمومی
			)
		);

		register_rest_route(
			$this->namespace,
			'/admin/tiers',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_tier' ),
				'permission_callback' => function( $request ) {
					$admin = \KarasuBuyersClub\REST\Permissions\PermissionChecker::check_admin( $request );
					if ( is_wp_error( $admin ) ) {
						return $admin;
					}
					return \KarasuBuyersClub\REST\Permissions\PermissionChecker::check_nonce( $request );
				},
			)
		);

		register_rest_route(
			$this->namespace,
			'/admin/tiers/(?P<id>[\d]+)',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_tier' ),
				'permission_callback' => function( $request ) {
					$admin = \KarasuBuyersClub\REST\Permissions\PermissionChecker::check_admin( $request );
					if ( is_wp_error( $admin ) ) {
						return $admin;
					}
					return \KarasuBuyersClub\REST\Permissions\PermissionChecker::check_nonce( $request );
				},
			)
		);
	}

	/**
	 * دریافت لیست تمام سطوح فعال.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_tiers( WP_REST_Request $request ): WP_REST_Response {
		$repository = new TierRepository();
		$tiers      = $repository->get_all_tiers();

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $tiers,
			),
			200
		);
	}

	/**
	 * ذخیره یا ویرایش سطح توسط ادمین.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function save_tier( WP_REST_Request $request ): WP_REST_Response {
		$params     = $request->get_json_params() ?: $request->get_body_params();
		$repository = new TierRepository();
		$res        = $repository->save_tier( $params );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'سطح با موفقیت ذخیره شد.', 'karasu-buyers-club' ),
			),
			200
		);
	}

	/**
	 * حذف سطح توسط ادمین.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function delete_tier( WP_REST_Request $request ): WP_REST_Response {
		$id         = absint( $request->get_param( 'id' ) );
		$repository = new TierRepository();
		$repository->delete_tier( $id );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'سطح با موفقیت حذف شد.', 'karasu-buyers-club' ),
			),
			200
		);
	}
}

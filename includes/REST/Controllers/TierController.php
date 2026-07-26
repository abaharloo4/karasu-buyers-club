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
}

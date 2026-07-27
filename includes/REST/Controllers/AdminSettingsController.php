<?php
namespace KarasuBuyersClub\REST\Controllers;

use KarasuBuyersClub\Database\Repositories\PointsRepository;
use KarasuBuyersClub\Database\Repositories\WalletRepository;
use KarasuBuyersClub\REST\Permissions\PermissionChecker;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کنترلر REST پنل ادمین (تنظیمات، اعضا، ویرایش دستی).
 *
 * @package KarasuBuyersClub\REST\Controllers
 */
class AdminSettingsController extends WP_REST_Controller {

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
			'/admin/settings',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( PermissionChecker::class, 'check_admin' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'save_settings' ),
					'permission_callback' => function( $request ) {
						$admin = PermissionChecker::check_admin( $request );
						if ( is_wp_error( $admin ) ) {
							return $admin;
						}
						return PermissionChecker::check_nonce( $request );
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/admin/members',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_members' ),
				'permission_callback' => array( PermissionChecker::class, 'check_admin' ),
				'args'                => array(
					'search' => array(
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
					),
					'number' => array(
						'sanitize_callback' => 'absint',
						'default'           => 20,
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/admin/members/(?P<id>[\d]+)/adjust',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'adjust_member' ),
				'permission_callback' => function( $request ) {
					$admin = PermissionChecker::check_admin( $request );
					if ( is_wp_error( $admin ) ) {
						return $admin;
					}
					return PermissionChecker::check_nonce( $request );
				},
				'args'                => array(
					'type'   => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_key',
					),
					'amount' => array(
						'required'          => true,
						'sanitize_callback' => 'floatval',
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/admin/members/(?P<id>[\d]+)/history',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_member_history' ),
				'permission_callback' => array( PermissionChecker::class, 'check_admin' ),
			)
		);
	}

	/**
	 * دریافت تنظیمات عمومی افزونه.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_settings( WP_REST_Request $request ): WP_REST_Response {
		$settings = array(
			'purchase_earn_rate' => floatval( get_option( 'kbc_purchase_earn_rate', 10000 ) ),
			'redemption_rate'    => floatval( get_option( 'kbc_redemption_rate', 1000 ) ),
			'expiry_enabled'     => (bool) get_option( 'kbc_expiry_enabled', false ),
			'expiry_months'      => absint( get_option( 'kbc_expiry_months', 6 ) ),
			'sms_enabled'        => (bool) get_option( 'kbc_sms_enabled', false ),
			'sms_gateway'        => sanitize_key( get_option( 'kbc_sms_gateway', 'kavenegar' ) ),
			'email_enabled'      => (bool) get_option( 'kbc_email_enabled', true ),
		);

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $settings,
			),
			200
		);
	}

	/**
	 * ذخیره تنظیمات افزونه.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function save_settings( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();

		$allowed_keys = array(
			'purchase_earn_rate',
			'redemption_rate',
			'expiry_enabled',
			'expiry_months',
			'sms_enabled',
			'sms_gateway',
			'sms_api_key',
			'email_enabled',
		);

		foreach ( $allowed_keys as $key ) {
			if ( isset( $params[ $key ] ) ) {
				$val = $params[ $key ];
				if ( is_bool( $val ) ) {
					update_option( 'kbc_' . $key, $val );
				} else {
					update_option( 'kbc_' . $key, sanitize_text_field( (string) $val ) );
				}
			}
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'تنظیمات با موفقیت ذخیره شد.', 'karasu-buyers-club' ),
			),
			200
		);
	}

	/**
	 * لیست اعضا.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_members( WP_REST_Request $request ): WP_REST_Response {
		$search = $request->get_param( 'search' );
		$number = $request->get_param( 'number' );

		$args = array(
			'number' => $number,
			'search' => $search ? '*' . $search . '*' : '',
		);

		$user_query = new \WP_User_Query( $args );
		$users      = $user_query->get_results();
		$points_repo = new PointsRepository();
		$wallet_repo = new WalletRepository();

		$members_data = array();
		foreach ( $users as $u ) {
			$members_data[] = array(
				'id'             => $u->ID,
				'display_name'   => $u->display_name,
				'user_email'     => $u->user_email,
				'points_balance' => $points_repo->get_user_balance( $u->ID ),
				'wallet_balance' => $wallet_repo->get_user_balance( $u->ID ),
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $members_data,
			),
			200
		);
	}

	/**
	 * تغییر دستی امتیاز یا کیف‌پول کاربر توسط ادمین.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function adjust_member( WP_REST_Request $request ): WP_REST_Response {
		$user_id = absint( $request->get_param( 'id' ) );
		$type    = $request->get_param( 'type' ); // points یا wallet
		$amount  = floatval( $request->get_param( 'amount' ) );

		if ( ! get_userdata( $user_id ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'کاربر مورد نظر یافت نشد.', 'karasu-buyers-club' ),
				),
				404
			);
		}

		if ( 'points' === $type ) {
			$repo = new PointsRepository();
			$repo->add_transaction( $user_id, $amount, 'adjusted', 'admin', 'admin_adj_' . get_current_user_id() );
		} elseif ( 'wallet' === $type ) {
			$repo   = new WalletRepository();
			$tx_type = $amount >= 0 ? 'credit' : 'debit';
			$repo->add_transaction( $user_id, abs( $amount ), $tx_type, 'admin', 'admin_adj_' . get_current_user_id() );
		} else {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'نوع تغییر نامعتبر است.', 'karasu-buyers-club' ),
				),
				400
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'تغییرات با موفقیت اعمال شد.', 'karasu-buyers-club' ),
			),
			200
		);
	}

	/**
	 * دریافت ریز تاریخچه امتیاز، کیف‌پول و ارتقای سطح یک کاربر.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_member_history( WP_REST_Request $request ): WP_REST_Response {
		$user_id = absint( $request->get_param( 'id' ) );

		if ( ! get_userdata( $user_id ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'کاربر مورد نظر یافت نشد.', 'karasu-buyers-club' ),
				),
				404
			);
		}

		$points_repo = new PointsRepository();
		$wallet_repo = new WalletRepository();
		$tier_repo   = new \KarasuBuyersClub\Database\Repositories\TierRepository();

		$user_data = get_userdata( $user_id );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'user'           => array(
						'id'           => $user_id,
						'name'         => $user_data->display_name,
						'email'        => $user_data->user_email,
						'registered'   => $user_data->user_registered,
						'current_tier' => $tier_repo->get_user_current_tier( $user_id ),
					),
					'points_history' => $points_repo->get_user_ledger( $user_id ),
					'wallet_history' => $wallet_repo->get_user_ledger( $user_id ),
				),
			),
			200
		);
	}
}

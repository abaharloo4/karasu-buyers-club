<?php
namespace KarasuBuyersClub\REST\Permissions;

use WP_Error;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کنترل‌کننده سطح دسترسی‌ها و Nonce درخواست‌های REST API.
 *
 * @package KarasuBuyersClub\REST\Permissions
 */
class PermissionChecker {

	/**
	 * بررسی لاگین بودن کاربر.
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public static function check_logged_in( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'kbc_rest_unauthorized',
				__( 'برای دسترسی به این بخش باید وارد حساب کاربری خود شوید.', 'karasu-buyers-club' ),
				array( 'status' => 401 )
			);
		}

		return true;
	}

	/**
	 * بررسی دسترسی ادمین ووکامرس (manage_woocommerce).
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public static function check_admin( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return new WP_Error(
				'kbc_rest_forbidden',
				__( 'شما دسترسی لازم برای انجام این عملیات را ندارید.', 'karasu-buyers-club' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * اعتبارسنجی Nonce استاندارد وردپرس (wp_rest).
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public static function check_nonce( WP_REST_Request $request ) {
		$nonce = $request->get_header( 'x_wp_nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error(
				'kbc_rest_invalid_nonce',
				__( 'کد امنیتی Nonce معتبر نیست.', 'karasu-buyers-club' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}
}

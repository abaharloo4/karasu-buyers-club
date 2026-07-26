<?php
namespace KarasuBuyersClub\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * لودکننده اسکریپت‌ها و استایل‌های بیلد پنل مدیریت ادمین.
 *
 * @package KarasuBuyersClub\Admin
 */
class AssetsLoader {

	/**
	 * Enqueue کردن بیلد React ادمین.
	 */
	public static function enqueue_admin_assets(): void {
		$js_file  = KBC_PLUGIN_URL . 'assets/dist/admin.js';
		$css_file = KBC_PLUGIN_URL . 'assets/dist/admin.css';

		wp_enqueue_style(
			'kbc-admin-app',
			$css_file,
			array(),
			KBC_VERSION
		);

		wp_enqueue_script(
			'kbc-admin-app',
			$js_file,
			array(),
			KBC_VERSION,
			true
		);

		wp_localize_script(
			'kbc-admin-app',
			'kbcAdminData',
			array(
				'restUrl' => esc_url_raw( rest_url( 'karasu-buyers-club/v1' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
	}
}

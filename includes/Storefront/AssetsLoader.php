<?php
namespace KarasuBuyersClub\Storefront;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * لودکننده اسکریپت‌ها و استایل‌های بیلد فرانت‌اند مشتری.
 *
 * @package KarasuBuyersClub\Storefront
 */
class AssetsLoader {

	/**
	 * Enqueue کردن بیلد React مشتری.
	 */
	public static function enqueue_storefront_assets(): void {
		$js_file  = KBC_PLUGIN_URL . 'assets/dist/storefront.js';
		$css_file = KBC_PLUGIN_URL . 'assets/dist/storefront.css';

		wp_enqueue_style(
			'kbc-storefront-app',
			$css_file,
			array(),
			KBC_VERSION
		);

		wp_enqueue_script(
			'kbc-storefront-app',
			$js_file,
			array(),
			KBC_VERSION,
			true
		);

		wp_localize_script(
			'kbc-storefront-app',
			'kbcData',
			array(
				'restUrl' => esc_url_raw( rest_url( 'karasu-buyers-club/v1' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'isUser'  => is_user_logged_in(),
			)
		);
	}
}

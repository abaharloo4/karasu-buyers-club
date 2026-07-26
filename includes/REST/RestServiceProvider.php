<?php
namespace KarasuBuyersClub\REST;

use KarasuBuyersClub\REST\Controllers\PointsController;
use KarasuBuyersClub\REST\Controllers\WalletController;
use KarasuBuyersClub\REST\Controllers\TierController;
use KarasuBuyersClub\REST\Controllers\ReferralController;
use KarasuBuyersClub\REST\Controllers\NotificationController;
use KarasuBuyersClub\REST\Controllers\AdminSettingsController;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ارائه دهنده خدمات REST API (ثبت مسیرها و کنترلرها).
 *
 * @package KarasuBuyersClub\REST
 */
class RestServiceProvider {

	/**
	 * ثبت هوک rest_api_init.
	 */
	public static function init(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'register_all_routes' ) );
	}

	/**
	 * ثبت تمام مسیرهای API افزونه.
	 */
	public static function register_all_routes(): void {
		$controllers = array(
			new PointsController(),
			new WalletController(),
			new TierController(),
			new ReferralController(),
			new NotificationController(),
			new AdminSettingsController(),
		);

		foreach ( $controllers as $controller ) {
			$controller->register_routes();
		}
	}
}

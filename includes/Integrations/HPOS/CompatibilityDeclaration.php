<?php
namespace KarasuBuyersClub\Integrations\HPOS;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * اعلام سازگاری رسمی با WooCommerce High-Performance Order Storage (HPOS).
 *
 * @package KarasuBuyersClub\Integrations\HPOS
 */
class CompatibilityDeclaration {

	/**
	 * ثبت هوک اعلام سازگاری HPOS.
	 */
	public static function init(): void {
		add_action( 'before_woocommerce_init', array( __CLASS__, 'declare_hpos_compatibility' ) );
	}

	/**
	 * اعلام سازگاری به ووکامرس.
	 */
	public static function declare_hpos_compatibility(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility(
				'custom_order_tables',
				KBC_PLUGIN_FILE,
				true
			);
		}
	}
}

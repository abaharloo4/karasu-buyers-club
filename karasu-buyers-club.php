<?php
/**
 * Plugin Name: Karasu Buyers Club
 * Plugin URI:  https://github.com/abaharloo4/karasu-buyers-club
 * Description: سیستم هوشمند و مستقل باشگاه مشتریان ووکامرس (امتیازدهی، کیف‌پول، سطح‌بندی، معرفی دوست و اطلاع‌رسانی)
 * Version:     0.9.0
 * Author:      Karasu
 * Author URI:  https://github.com/abaharloo4/karasu-buyers-club
 * Text Domain: karasu-buyers-club
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * WC requires at least: 7.0
 * WC tests up to: 8.9
 *
 * @package KarasuBuyersClub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// تعریف ثوابت پروژه.
define( 'KBC_VERSION', '0.9.0' );
define( 'KBC_PLUGIN_FILE', __FILE__ );
define( 'KBC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KBC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// بارگذاری Autoloader (Composer).
if ( file_exists( KBC_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once KBC_PLUGIN_DIR . 'vendor/autoload.php';
} else {
	// Autoloader fallback در صورت عدم وجود vendor.
	spl_autoload_register(
		function ( $class ) {
			$prefix   = 'KarasuBuyersClub\\';
			$base_dir = KBC_PLUGIN_DIR . 'includes/';
			$len      = strlen( $prefix );

			if ( 0 !== strncmp( $prefix, $class, $len ) ) {
				return;
			}

			$relative_class = substr( $class, $len );
			$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	);
}

/**
 * ثبت هوک فعال‌سازی افزونه.
 */
register_activation_hook(
	__FILE__,
	function () {
		KarasuBuyersClub\Core\Activator::activate();
	}
);

/**
 * ثبت هوک غیرفعال‌سازی افزونه.
 */
register_deactivation_hook(
	__FILE__,
	function () {
		KarasuBuyersClub\Core\Deactivator::deactivate();
	}
);

/**
 * راه‌اندازی افزونه.
 */
add_action(
	'plugins_loaded',
	function () {
		KarasuBuyersClub\Core\Plugin::instance();
	}
);

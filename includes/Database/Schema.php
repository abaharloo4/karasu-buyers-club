<?php
namespace KarasuBuyersClub\Database;

use KarasuBuyersClub\Database\Migrations\Migration_1_0_0;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * مدیریت ساختار و مایگریشن دیتابیس Karasu Buyers Club.
 *
 * @package KarasuBuyersClub\Database
 */
class Schema {

	/**
	 * نسخه جاری اسکیما دیتابیس.
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * بررسی و اجرای مایگریشن‌ها در صورت لزوم.
	 */
	public static function init(): void {
		$installed_ver = get_option( 'kbc_db_version', '0.0.0' );

		if ( version_compare( $installed_ver, self::DB_VERSION, '<' ) ) {
			self::run_migrations( $installed_ver );
			update_option( 'kbc_db_version', self::DB_VERSION );
		}
	}

	/**
	 * اجرای ترتیب مایگریشن‌ها.
	 *
	 * @param string $installed_version
	 */
	private static function run_migrations( string $installed_version ): void {
		if ( version_compare( $installed_version, '1.0.0', '<' ) ) {
			Migration_1_0_0::up();
		}
	}
}

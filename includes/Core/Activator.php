<?php
namespace KarasuBuyersClub\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کلاس مربوط به عملیات هنگام فعال‌سازی افزونه.
 *
 * @package KarasuBuyersClub\Core
 */
class Activator {

	/**
	 * اجرا هنگام فعال‌سازی.
	 */
	public static function activate(): void {
		// ذخیره نسخه دیتابیس اولیه در صورت عدم وجود.
		if ( ! get_option( 'kbc_db_version' ) ) {
			add_option( 'kbc_db_version', KBC_VERSION );
		}

		// بازنویسی قوانین پیوند یکتا (Rewrite Rules).
		flush_rewrite_rules();
	}
}

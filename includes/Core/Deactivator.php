<?php
namespace KarasuBuyersClub\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کلاس مربوط به عملیات هنگام غیرفعال‌سازی افزونه.
 *
 * @package KarasuBuyersClub\Core
 */
class Deactivator {

	/**
	 * اجرا هنگام غیرفعال‌سازی.
	 */
	public static function deactivate(): void {
		// پاک‌سازی صف Cron زمان‌بندی‌شده.
		wp_clear_scheduled_hook( 'kbc_daily_cron_action' );

		// بازنویسی قوانین پیوند یکتا.
		flush_rewrite_rules();
	}
}

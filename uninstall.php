<?php
/**
 * پاک‌سازی داده‌های افزونه هنگام حذف از پنل وردپرس.
 *
 * @package KarasuBuyersClub
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// پاک‌سازی تمام جداول و گزینه‌ها در صورت فعال بودن تنظیمات پاک‌سازی کامل.
if ( get_option( 'kbc_remove_data_on_uninstall', false ) ) {
	global $wpdb;

	$tables = array(
		"{$wpdb->prefix}kbc_points_ledger",
		"{$wpdb->prefix}kbc_wallet_ledger",
		"{$wpdb->prefix}kbc_tiers",
		"{$wpdb->prefix}kbc_user_tier_history",
		"{$wpdb->prefix}kbc_referrals",
		"{$wpdb->prefix}kbc_redemption_rules",
		"{$wpdb->prefix}kbc_notifications_log",
	);

	foreach ( $tables as $table ) {
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	delete_option( 'kbc_db_version' );
	delete_option( 'kbc_settings' );
	delete_option( 'kbc_remove_data_on_uninstall' );
}

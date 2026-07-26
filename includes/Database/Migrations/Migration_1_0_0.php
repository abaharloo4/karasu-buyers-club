<?php
namespace KarasuBuyersClub\Database\Migrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * مایگریشن نسخه 1.0.0 — ساخت جداول اولیه افزونه Karasu Buyers Club.
 *
 * @package KarasuBuyersClub\Database\Migrations
 */
class Migration_1_0_0 {

	/**
	 * اجرای مایگریشن ساخت جداول.
	 */
	public static function up(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// ۱. جدول kbc_points_ledger
		$sql_points = "CREATE TABLE {$wpdb->prefix}kbc_points_ledger (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			amount decimal(12,2) NOT NULL DEFAULT 0.00,
			type varchar(20) NOT NULL,
			source varchar(50) NOT NULL,
			reference_id varchar(100) DEFAULT NULL,
			expires_at datetime DEFAULT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY type (type),
			KEY expires_at (expires_at)
		) $charset_collate;";

		// ۲. جدول kbc_wallet_ledger
		$sql_wallet = "CREATE TABLE {$wpdb->prefix}kbc_wallet_ledger (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			amount decimal(12,2) NOT NULL DEFAULT 0.00,
			type varchar(20) NOT NULL,
			source varchar(50) NOT NULL,
			reference_id varchar(100) DEFAULT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY type (type)
		) $charset_collate;";

		// ۳. جدول kbc_tiers
		$sql_tiers = "CREATE TABLE {$wpdb->prefix}kbc_tiers (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(100) NOT NULL,
			threshold decimal(12,2) NOT NULL DEFAULT 0.00,
			perks longtext DEFAULT NULL,
			sort_order int(11) NOT NULL DEFAULT 0,
			is_active tinyint(1) NOT NULL DEFAULT 1,
			PRIMARY KEY  (id),
			KEY threshold (threshold)
		) $charset_collate;";

		// ۴. جدول kbc_user_tier_history
		$sql_tier_history = "CREATE TABLE {$wpdb->prefix}kbc_user_tier_history (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			tier_id bigint(20) unsigned NOT NULL,
			achieved_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// ۵. جدول kbc_referrals
		$sql_referrals = "CREATE TABLE {$wpdb->prefix}kbc_referrals (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			referral_code varchar(50) NOT NULL,
			referred_by_user_id bigint(20) unsigned DEFAULT NULL,
			status varchar(20) NOT NULL DEFAULT 'pending',
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			UNIQUE KEY referral_code (referral_code)
		) $charset_collate;";

		// ۶. جدول kbc_redemption_rules
		$sql_redemption = "CREATE TABLE {$wpdb->prefix}kbc_redemption_rules (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			type varchar(50) NOT NULL,
			rate decimal(12,4) NOT NULL DEFAULT 1.0000,
			is_active tinyint(1) NOT NULL DEFAULT 1,
			PRIMARY KEY  (id)
		) $charset_collate;";

		// ۷. جدول kbc_notifications_log
		$sql_notifications = "CREATE TABLE {$wpdb->prefix}kbc_notifications_log (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			channel varchar(20) NOT NULL,
			template_key varchar(50) NOT NULL,
			status varchar(20) NOT NULL,
			sent_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		dbDelta( $sql_points );
		dbDelta( $sql_wallet );
		dbDelta( $sql_tiers );
		dbDelta( $sql_tier_history );
		dbDelta( $sql_referrals );
		dbDelta( $sql_redemption );
		dbDelta( $sql_notifications );
	}
}

<?php
namespace KarasuBuyersClub\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * سرویس مدیریت مناسبت‌های خاص (تولد و سالگرد عضویت).
 *
 * @package KarasuBuyersClub\Services
 */
class OccasionService {

	/**
	 * ثبت فیلد تاریخ تولد در پروفایل وردپرس.
	 */
	public static function init(): void {
		add_action( 'show_user_profile', array( __CLASS__, 'render_birthdate_field' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'render_birthdate_field' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'save_birthdate_field' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'save_birthdate_field' ) );

		// اکشن کرون روزانه.
		add_action( 'kbc_daily_cron_action', array( __CLASS__, 'check_daily_occasions' ) );
	}

	/**
	 * رندر فیلد تاریخ تولد در ویرایش پروفایل کاربر.
	 *
	 * @param \WP_User $user
	 */
	public static function render_birthdate_field( $user ): void {
		$birthdate = get_user_meta( $user->ID, '_kbc_birthdate', true );
		?>
		<h3><?php echo esc_html__( 'اطلاعات باشگاه مشتریان Karasu', 'karasu-buyers-club' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="kbc_birthdate"><?php echo esc_html__( 'تاریخ تولد', 'karasu-buyers-club' ); ?></label></th>
				<td>
					<input type="date" name="kbc_birthdate" id="kbc_birthdate" value="<?php echo esc_attr( $birthdate ); ?>" class="regular-text" />
					<p class="description"><?php echo esc_html__( 'تاریخ تولد جهت دریافت پاداش سالانه تولد.', 'karasu-buyers-club' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * ذخیره فیلد تاریخ تولد.
	 *
	 * @param int $user_id
	 */
	public static function save_birthdate_field( int $user_id ): void {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( isset( $_POST['kbc_birthdate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$birthdate = sanitize_text_field( wp_unslash( $_POST['kbc_birthdate'] ) );
			update_user_meta( $user_id, '_kbc_birthdate', $birthdate );
		}
	}

	/**
	 * بررسی زمان‌بندی روزانه کرون برای اعطای پاداش‌های تولد و سالگرد عضویت.
	 */
	public static function check_daily_occasions(): void {
		$today_month_day = date( 'm-d', current_time( 'timestamp' ) );

		// ۱. بررسی تولدها.
		$birthday_users = get_users(
			array(
				'meta_key'     => '_kbc_birthdate',
				'meta_value'   => $today_month_day,
				'meta_compare' => 'LIKE',
			)
		);

		$points_engine = new PointsEngineService();
		$birthday_pts  = floatval( get_option( 'kbc_birthday_points', 100 ) );

		foreach ( $birthday_users as $user ) {
			if ( $birthday_pts > 0 ) {
				$points_engine->award_for_action( $user->ID, 'birthday', date( 'Y' ) );
			}
		}

		// ۲. اجرای کرون انقضای امتیازات در همین بازه روزانه.
		$points_engine->run_expiry_cron();
	}
}

<?php
namespace KarasuBuyersClub\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ثبت منوی مدیریت ادمین در پنل وردپرس (wp-admin).
 *
 * @package KarasuBuyersClub\Admin
 */
class AdminMenu {

	/**
	 * ثبت هوک‌های منوی ادمین.
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
	}

	/**
	 * افزودن منوی اصلی افزونه.
	 */
	public static function add_admin_menu(): void {
		$hook_suffix = add_menu_page(
			__( 'باشگاه مشتریان Karasu', 'karasu-buyers-club' ),
			__( 'باشگاه مشتریان', 'karasu-buyers-club' ),
			'manage_woocommerce',
			'karasu-buyers-club',
			array( __CLASS__, 'render_admin_page' ),
			'dashicons-awards',
			58
		);

		add_action( 'admin_print_styles-' . $hook_suffix, array( AssetsLoader::class, 'enqueue_admin_assets' ) );
	}

	/**
	 * رندر کانتینر اصلی اپلیکیشن React ادمین.
	 */
	public static function render_admin_page(): void {
		?>
		<div id="kbc-admin-app">
			<div style="padding: 20px; text-align: center; color: #64748b;">
				<?php echo esc_html__( 'در حال بارگذاری پنل مدیریت باشگاه مشتریان...', 'karasu-buyers-club' ); ?>
			</div>
		</div>
		<?php
	}
}

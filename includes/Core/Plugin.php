<?php
namespace KarasuBuyersClub\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کلاس اصلی راه‌انداز افزونه (Singleton).
 *
 * @package KarasuBuyersClub\Core
 */
final class Plugin {

	/**
	 * نمونه نمونه‌سازی‌شده کلاس.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * دریافت تک‌نمونه از کلاس.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * سازنده خصوصی جهت جلوگیری از نمونه‌سازی مستقیم.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * راه‌اندازی اولیه ماژول‌ها و هوک‌ها.
	 */
	private function init(): void {
		// بررسی وابستگی ووکامرس.
		if ( ! $this->is_woocommerce_active() ) {
			add_action( 'admin_notices', array( $this, 'notice_woocommerce_missing' ) );
			return;
		}

		// اعلام سازگاری با HPOS.
		\KarasuBuyersClub\Integrations\HPOS\CompatibilityDeclaration::init();

		// راه‌اندازی دیتابیس و مایگریشن‌ها.
		\KarasuBuyersClub\Database\Schema::init();

		// راه‌اندازی هوک‌های ووکامرس.
		\KarasuBuyersClub\Integrations\WooCommerce\OrderHooks::init();

		// راه‌اندازی دروازه کیف‌پول در چک‌اوت.
		\KarasuBuyersClub\Integrations\WooCommerce\CheckoutWalletGateway::init();

		// راه‌اندازی سرویس مناسبت‌های خاص.
		\KarasuBuyersClub\Services\OccasionService::init();

		// راه‌اندازی REST API.
		\KarasuBuyersClub\REST\RestServiceProvider::init();

		// راه‌اندازی تب حساب کاربری و شورت‌کد فرانت‌اند مشتری.
		\KarasuBuyersClub\Storefront\MyAccountTab::init();
		\KarasuBuyersClub\Storefront\ClubPageController::init();

		// راه‌اندازی منوی مدیریت ادمین.
		\KarasuBuyersClub\Admin\AdminMenu::init();

		// بارگذاری فایل‌های زبان.
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * بررسی فعال بودن افزونه ووکامرس.
	 *
	 * @return bool
	 */
	public function is_woocommerce_active(): bool {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * هشدار عدم فعال بودن ووکامرس در پنل ادمین.
	 */
	public function notice_woocommerce_missing(): void {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				echo esc_html__(
					'افزونه Karasu Buyers Club برای کارکرد نیازمند فعال بودن ووکامرس (WooCommerce) است.',
					'karasu-buyers-club'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * بارگذاری فایل‌های ترجمه.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'karasu-buyers-club',
			false,
			dirname( plugin_basename( KBC_PLUGIN_FILE ) ) . '/languages/'
		);
	}
}

<?php
namespace KarasuBuyersClub\Storefront;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * افزودن تب اختصاصی «باشگاه مشتریان» به صفحه حساب کاربری من ووکامرس.
 *
 * @package KarasuBuyersClub\Storefront
 */
class MyAccountTab {

	/**
	 * ثبت هوک‌های حساب کاربری ووکامرس.
	 */
	public static function init(): void {
		add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'add_my_account_tab' ) );
		add_action( 'init', array( __CLASS__, 'add_endpoint' ) );
		add_action( 'woocommerce_account_loyalty-club_endpoint', array( __CLASS__, 'render_tab_content' ) );
	}

	/**
	 * افزودن آیتم منو.
	 *
	 * @param array $items
	 * @return array
	 */
	public static function add_my_account_tab( array $items ): array {
		$new_items = array();

		foreach ( $items as $key => $value ) {
			$new_items[ $key ] = $value;
			if ( 'dashboard' === $key ) {
				$new_items['loyalty-club'] = __( 'باشگاه مشتریان', 'karasu-buyers-club' );
			}
		}

		return $new_items;
	}

	/**
	 * افزودن Endpoint اختصاصی.
	 */
	public static function add_endpoint(): void {
		add_rewrite_endpoint( 'loyalty-club', EP_ROOT | EP_PAGES );
	}

	/**
	 * رندر کانتینر اپلیکیشن React مشتری.
	 */
	public static function render_tab_content(): void {
		AssetsLoader::enqueue_storefront_assets();
		?>
		<div id="kbc-storefront-app">
			<div className="p-4 text-center text-slate-500">
				<?php echo esc_html__( 'در حال بارگذاری اطلاعات باشگاه مشتریان...', 'karasu-buyers-club' ); ?>
			</div>
		</div>
		<?php
	}
}

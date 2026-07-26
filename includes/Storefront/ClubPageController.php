<?php
namespace KarasuBuyersClub\Storefront;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کنترلر شورت‌کد صفحه اختصاصی باشگاه مشتریان [karasu_buyers_club].
 *
 * @package KarasuBuyersClub\Storefront
 */
class ClubPageController {

	/**
	 * ثبت شورت‌کد.
	 */
	public static function init(): void {
		add_shortcode( 'karasu_buyers_club', array( __CLASS__, 'render_shortcode' ) );
	}

	/**
	 * رندر شورت‌کد.
	 *
	 * @return string
	 */
	public static function render_shortcode(): string {
		AssetsLoader::enqueue_storefront_assets();

		ob_start();
		?>
		<div id="kbc-storefront-app">
			<div style="padding: 20px; text-align: center; color: #64748b;">
				<?php echo esc_html__( 'در حال بارگذاری اطلاعات باشگاه مشتریان...', 'karasu-buyers-club' ); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

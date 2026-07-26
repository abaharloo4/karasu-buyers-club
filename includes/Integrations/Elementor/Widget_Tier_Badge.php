<?php
namespace KarasuBuyersClub\Integrations\Elementor;

use Elementor\Widget_Base;
use KarasuBuyersClub\Services\TierService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ویجت المنتور نمایش بج سطح عضویت کاربر.
 *
 * @package KarasuBuyersClub\Integrations\Elementor
 */
class Widget_Tier_Badge extends Widget_Base {

	public function get_name() {
		return 'kbc_tier_badge';
	}

	public function get_title() {
		return __( 'بج سطح عضویت Karasu', 'karasu-buyers-club' );
	}

	public function get_icon() {
		return 'eicon-badge';
	}

	public function get_categories() {
		return array( 'karasu-buyers-club' );
	}

	protected function render() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id      = get_current_user_id();
		$tier_service = new TierService();
		$tier         = $tier_service->recalculate_user_tier( $user_id );
		$tier_name    = $tier ? $tier['name'] : __( 'برنزی', 'karasu-buyers-club' );
		?>
		<div style="direction:rtl; display:inline-flex; items-center:center; gap:8px; background:#fef3c7; color:#92400e; padding:6px 14px; border-radius:9999px; font-size:12px; font-weight:700;">
			<span>🏅 <?php echo esc_html__( 'سطح عضویت:', 'karasu-buyers-club' ); ?> <?php echo esc_html( $tier_name ); ?></span>
		</div>
		<?php
	}
}

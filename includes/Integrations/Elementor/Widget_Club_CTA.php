<?php
namespace KarasuBuyersClub\Integrations\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ویجت المنتور دکمه فراخوان به عمل (CTA) باشگاه.
 *
 * @package KarasuBuyersClub\Integrations\Elementor
 */
class Widget_Club_CTA extends Widget_Base {

	public function get_name() {
		return 'kbc_club_cta';
	}

	public function get_title() {
		return __( 'دکمه فراخوان باشگاه Karasu', 'karasu-buyers-club' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return array( 'karasu-buyers-club' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'تنظیمات دکمه', 'karasu-buyers-club' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'متن دکمه', 'karasu-buyers-club' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'ورود به باشگاه مشتریان', 'karasu-buyers-club' ),
			)
		);

		$this->add_control(
			'button_url',
			array(
				'label'   => __( 'لینک دکمه', 'karasu-buyers-club' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => wc_get_account_endpoint_url( 'loyalty-club' ) ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$button_url = ! empty( $settings['button_url']['url'] ) ? $settings['button_url']['url'] : wc_get_account_endpoint_url( 'loyalty-club' );
		?>
		<div style="direction:rtl; text-align:center;">
			<a href="<?php echo esc_url( $button_url ); ?>" style="display:inline-block; background:#0284c7; color:#ffffff; padding:12px 24px; border-radius:12px; font-weight:700; text-decoration:none; box-shadow:0 4px 6px -1px rgba(2,132,199,0.2); transition:all 0.2s;">
				🎁 <?php echo esc_html( $settings['button_text'] ); ?>
			</a>
		</div>
		<?php
	}
}

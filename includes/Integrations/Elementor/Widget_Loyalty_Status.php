<?php
namespace KarasuBuyersClub\Integrations\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use KarasuBuyersClub\Database\Repositories\PointsRepository;
use KarasuBuyersClub\Services\WalletService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ویجت المنتور نمایش وضعیت امتیاز و کیف‌پول کاربر.
 *
 * @package KarasuBuyersClub\Integrations\Elementor
 */
class Widget_Loyalty_Status extends Widget_Base {

	public function get_name() {
		return 'kbc_loyalty_status';
	}

	public function get_title() {
		return __( 'وضعیت باشگاه مشتریان Karasu', 'karasu-buyers-club' );
	}

	public function get_icon() {
		return 'eicon-rating';
	}

	public function get_categories() {
		return array( 'karasu-buyers-club' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'تنظیمات ظاهری', 'karasu-buyers-club' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_wallet',
			array(
				'label'        => __( 'نمایش موجودی کیف‌پول', 'karasu-buyers-club' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'بله', 'karasu-buyers-club' ),
				'label_off'    => __( 'خیر', 'karasu-buyers-club' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! is_user_logged_in() ) {
			echo '<div style="padding:15px; background:#f8fafc; border-radius:12px; text-align:center; color:#64748b;">';
			echo esc_html__( 'جهت مشاهده وضعیت باشگاه، وارد حساب کاربری خود شوید.', 'karasu-buyers-club' );
			echo '</div>';
			return;
		}

		$user_id     = get_current_user_id();
		$points_repo = new PointsRepository();
		$wallet_serv = new WalletService();

		$points   = $points_repo->get_user_balance( $user_id );
		$wallet   = $wallet_serv->get_balance( $user_id );
		$settings = $this->get_settings_for_display();
		?>
		<div style="direction:rtl; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); display:flex; justify-content:space-around; text-align:center;">
			<div>
				<div style="font-size:12px; color:#64748b; font-weight:600;"><?php echo esc_html__( 'امتیاز شما', 'karasu-buyers-club' ); ?></div>
				<div style="font-size:22px; font-weight:800; color:#d97706; margin-top:4px;"><?php echo esc_html( number_format_i18n( $points ) ); ?></div>
			</div>
			<?php if ( 'yes' === $settings['show_wallet'] ) : ?>
				<div style="border-right:1px solid #f1f5f9; padding-right:20px;">
					<div style="font-size:12px; color:#64748b; font-weight:600;"><?php echo esc_html__( 'موجودی کیف‌پول', 'karasu-buyers-club' ); ?></div>
					<div style="font-size:22px; font-weight:800; color:#059669; margin-top:4px;"><?php echo esc_html( number_format_i18n( $wallet ) ); ?> <span style="font-size:12px; font-weight:normal;"><?php echo esc_html__( 'تومان', 'karasu-buyers-club' ); ?></span></div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

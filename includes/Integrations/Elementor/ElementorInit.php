<?php
namespace KarasuBuyersClub\Integrations\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * راه‌اندازی و ثبت ویجت‌های اختصاصی المنتور.
 *
 * @package KarasuBuyersClub\Integrations\Elementor
 */
class ElementorInit {

	/**
	 * ثبت هوک‌های المنتور.
	 */
	public static function init(): void {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'add_widget_category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
	}

	/**
	 * افزودن دسته‌بندی اختصاصی در پنل المنتور.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager
	 */
	public static function add_widget_category( $elements_manager ): void {
		$elements_manager->add_category(
			'karasu-buyers-club',
			array(
				'title' => __( 'باشگاه مشتریان Karasu', 'karasu-buyers-club' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * ثبت ویجت‌های سه گانه.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public static function register_widgets( $widgets_manager ): void {
		$widgets_manager->register( new Widget_Loyalty_Status() );
		$widgets_manager->register( new Widget_Tier_Badge() );
		$widgets_manager->register( new Widget_Club_CTA() );
	}
}

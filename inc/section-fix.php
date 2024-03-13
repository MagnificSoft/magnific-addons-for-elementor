<?php
namespace MagnificAddons;


defined('ABSPATH') || die();

class Aee_Columns_Alignment_Fix_For_Elementor {

	private static $instance;




	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}


		return self::$instance;
	}


	protected function __construct() {
		add_action( 'elementor/element/section/section_layout/before_section_end', array( $this, 'add_setting' ), 10, 2 );
		// add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'add_css' ) );
	}


	public function add_setting( $element, $args ) {

		// $is_legacy_mode_active = \Elementor\Plugin::instance()->get_legacy_mode( 'elementWrappers' );


		// $mae_fix_columns_alignment_selector = $is_legacy_mode_active ? ' > .elementor-row' : '';
		
		// $element->add_control(
		// 	'mae_fix_columns_alignment',
		// 	array(
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'label' => esc_html__( 'MAE Section Width Fix', 'magnific-addons' ),
		// 		'description' => esc_html__( 'It will remove the "weird" columns gap added by Elementor on the left and right side of each section (when `Columns Gap` is active). This helps you to have consistent content width without having to manually readjust it everytime you create sections with `Columns Gap`', 'magnific-addons' ),
		// 		'return_value' => 'enabled',
		// 		'default' => '',
		// 		'separator' => 'before',
		// 		// 'prefix_class' => 'elementor-section-width-fix-',
		// 		'selectors' => [
		// 			'{{WRAPPER}}' => 'overflow-x: hidden;',
		// 			'{{WRAPPER}} > .elementor-column-gap-extended' . $mae_fix_columns_alignment_selector => 'width: calc(100% + 10px); margin-left: -5px; margin-right: -5px;',
		// 			'{{WRAPPER}} > .elementor-column-gap-wide' . $mae_fix_columns_alignment_selector => 'width: calc(100% + 20px); margin-left: -10px; margin-right: -10px;',
		// 			'{{WRAPPER}} > .elementor-column-gap-wider' . $mae_fix_columns_alignment_selector => 'width: calc(100% + 40px); margin-left: -20px; margin-right: -20px;',

		// 		]
		// 	)
		// );


		$mae_fix_columns_wrap_selector = '{{WRAPPER}} > .elementor-container';

		$element->add_control(
			'mae_fix_columns_wrap',
			array(
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => esc_html__( 'MAE Desktop Columns Wrap', 'magnific-addons' ),
				'description' => esc_html__( 'Allows to stack column verticaly on desktop like on tablet and mobile. You can use MAE custom column width feature to set column width to 100% on desktop', 'magnific-addons' ),
				'return_value' => 'enabled',
				'default' => '',
				'selectors' => [
					$mae_fix_columns_wrap_selector => 'flex-wrap: wrap',
				]
			)
		);
	}

}
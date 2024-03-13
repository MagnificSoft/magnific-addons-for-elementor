<?php
namespace MagnificAddons;


defined('ABSPATH') || die();

use \Elementor\Controls_Manager;
// use \Elementor\Element_Column;

class Aee_Flex_Order {

	private static $instance;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	protected function __construct() {
      add_action( 'elementor/element/column/section_advanced/before_section_end', array( $this, 'add_setting' ) );
      add_action( 'elementor/element/common/_section_style/before_section_end', array( $this, 'add_setting' ) );
	}


	public function add_setting( $element ) {
      $element->add_responsive_control(
         'mae_flex_order',
         [
             'label' => __( 'MAE Flex Order', 'magnific-addons' ),
             'type' => Controls_Manager::NUMBER,
             'separator' => 'before',
            //  'label_block' => true,
             'description' => __( 'Flex order for this element. To make it work properly set order for all elements in flex container', 'magnific-addons' ),
             'selectors' => [
                 '{{WRAPPER}}' => 'order: {{VALUE}};',
             ],
         ]
     );
	}
}
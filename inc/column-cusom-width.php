<?php
namespace MagnificAddons;


defined('ABSPATH') || die();

use \Elementor\Controls_Manager;
// use \Elementor\Element_Column;

class Aee_Columns_Custom_Width_For_Elementor {

	private static $instance;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	protected function __construct() {
      add_action( 'elementor/element/column/layout/before_section_end', array( $this, 'add_setting' ) );
	}


	public function add_setting( $element ) {
      $element->add_responsive_control(
         'mae_custom_column_width',
         [
             'label' => __( 'MAE Custom Column Width', 'magnific-addons' ),
             'type' => Controls_Manager::TEXT,
             'separator' => 'before',
             'label_block' => true,
             'description' => __( 'You can use any CSS value like 100px, 10%, and calc(100% - 100px)', 'magnific-addons' ),
             'selectors' => [
                 '{{WRAPPER}}.elementor-column' => 'width: {{VALUE}};',
             ],
         ]
     );
	}
}
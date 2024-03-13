<?php
namespace MagnificAddons;


defined('ABSPATH') || die();

include_once ABSPATH . 'wp-admin/includes/plugin.php';

use \Elementor\Controls_Manager;
use \Elementor\Core\Responsive\Responsive;
use \Elementor\Core\DynamicTags\Dynamic_CSS;
// use \Elementor\Element_Column;

class Aee_Responsive_Custom_CSS {

	private static $instance;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	protected function __construct() {

      if ( is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) { // for Elementor Pro
         add_action( 'elementor/element/section/section_custom_css/before_section_end', array( $this, 'add_setting' ), 10 );
         add_action( 'elementor/element/column/section_custom_css/before_section_end', array( $this, 'add_setting' ), 10 );
         add_action( 'elementor/element/common/section_custom_css/before_section_end', array( $this, 'add_setting' ), 10 );
         add_action( 'elementor/element/parse_css', array( $this,  'add_post_css' ), 100, 2 ); // after default CSS
      }

	}


	public function add_setting( $element ) {
      $element->start_injection( [
         'at' => 'before',
         'of' => 'custom_css_description',
      ] );
      $repeater = new \Elementor\Repeater();

      $breakpoints = Responsive::get_breakpoints();

      // echo '<pre>' . print_r($breakpoints, true) . '</pre><br>';
      // $reponsive_names = array(
      //    'desktop' => sprintf( __( 'Desktop (>= %dpx)', 'magnific-addons' ), $breakpoints['lg'] ),
      //    'tablet' => sprintf( __( 'Tablet (%dpx - %dpx)', 'magnific-addons' ), intval($breakpoints['lg']) - 1, $breakpoints['md'] ),
      //    'mobile' => sprintf( __( 'Mobile (<= %dpx)', 'magnific-addons' ), intval($breakpoints['md']) - 1 ),
      //    'desktop+tablet' => sprintf( __( 'Desktop + Tablet (>= %dpx)', 'magnific-addons' ), $breakpoints['md'] ),
      //    'tablet+mobile' => sprintf( __( 'Tablet + Mobile (< %dpx)', 'magnific-addons' ), $breakpoints['lg'] ),
      // );
      $reponsive_names = array(
         'desktop' => __( 'Desktop', 'magnific-addons'),
         'tablet' =>  __( 'Tablet', 'magnific-addons' ),
         'mobile' =>  __( 'Mobile', 'magnific-addons' ),
         'desktop+tablet' =>  __( 'Desktop & Tablet', 'magnific-addons' ),
         'tablet+mobile' =>  __( 'Tablet & Mobile', 'magnific-addons' ),
      );

 
		$repeater->add_control(
			'item_breakpoint',
			[
				'label' => __( 'Breakpoint', 'plugin-domain' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'label_block' => true,
				'options' => $reponsive_names,
			]
		);
    

		$repeater->add_control(
         'item_custom_css',
         [
            'type' => Controls_Manager::CODE,
            'label' => __( 'Custom CSS', 'elementor-pro' ),
            'language' => 'css',
            'render_type' => 'ui',
            'show_label' => false,
            'separator' => 'none',
            'condition' => [
               'item_breakpoint!' => '',
            ]
         ]
      );


      $element->add_control(
         'responsive_custom_css_repeater',
         [
            'label' => __( 'MAE Responsive Custom CSS', 'magnific-addons' ),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '
               <# 
                  if (item_breakpoint == "desktop") print("Desktop");
                  if (item_breakpoint == "tablet") print("Tablet");
                  if (item_breakpoint == "mobile") print("Mobile");
                  if (item_breakpoint == "desktop+tablet") print("Desktop + Tablet");
                  if (item_breakpoint == "tablet+mobile") print("Tablet + Mobile");
               #>
            ',
            'prevent_empty' => false,
         ]
      );
      $element->end_injection();
   }
   
   public function add_post_css( $post_css, $element ) {

		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$element_settings = $element->get_settings();

      if (isset($element_settings['responsive_custom_css_repeater']) && !empty($element_settings['responsive_custom_css_repeater'])) {

         $breakpoints = Responsive::get_breakpoints();



         // echo '<pre>' . print_r($breakpoints, true) . '</pre><br>';
         // echo '<pre>' . print_r($element_settings['responsive_custom_css_repeater'], true) . '</pre><br>';

         foreach($element_settings['responsive_custom_css_repeater'] as $item) {

            $css = trim( $item['item_custom_css'] );

            if ( empty( $css ) ) {
               return;
            }
            $media_query = '';

            if ($item['item_breakpoint'] == 'desktop') {
               $media_query = '@media(min-width:' . $breakpoints['lg'] . 'px)';
            } elseif ($item['item_breakpoint'] == 'tablet') {
               $media_query = '@media(min-width:' . $breakpoints['md'] . 'px) and (max-width:' . intval($breakpoints['lg'] - 1) . 'px)';
            } elseif ($item['item_breakpoint'] == 'mobile') {
               $media_query = '@media(max-width:' . intval($breakpoints['md'] - 1) . 'px)';
            } elseif ($item['item_breakpoint'] == 'desktop+tablet') {
               $media_query = '@media(min-width:' . $breakpoints['md'] . 'px)';
            } elseif ($item['item_breakpoint'] == 'tablet+mobile') {
               $media_query = '@media(max-width:' . intval($breakpoints['lg'] - 1) . 'px)';
            }

            $css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );


            // Add a css comment
            $css = sprintf( '/* Start responsive custom CSS for %s, class: %s */%s', $element->get_name(), $element->get_unique_selector(), $media_query ) . '{' .$css . '}' . '/* End responsive custom CSS */';
      
            $post_css->get_stylesheet()->add_raw_css( $css );

         }
      }

   }
   
}
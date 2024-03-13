<?php
namespace MagnificAddons;


defined('ABSPATH') || die();
include_once ABSPATH . 'wp-admin/includes/plugin.php';
use \Elementor\Controls_Manager;
// use \Elementor\Element_Column;

class Aee_Custom_HTML {

	private static $instance;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	protected function __construct() {
      
      if ( is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) { // for Elementor Pro
         add_action( 'elementor/element/common/section_custom_css/before_section_start', array( $this, 'add_setting' ));
      } else { // for Elementor Free
         add_action( 'elementor/element/common/section_custom_css_pro/before_section_start', array( $this, 'add_setting' ));
      }



	}


	public function add_setting( $element ) {

      $element->start_controls_section(
         'section_custom_html',
         [
            'label' => __( 'MAE Custom HTML', 'magnific-addons' ),
            'tab' => Controls_Manager::TAB_ADVANCED,
         ]
      );
         $element->add_control(
            'custom_html_title',
            [
               'raw' => __( 'Add your own custom HTML here', 'magnific-addons' ),
               'type' => Controls_Manager::RAW_HTML,
            ]
         );
         $element->add_control(
            'custom_html_code',
            [
               'label' => __( 'Custom HTML', 'magnific-addons' ),
               'type' => Controls_Manager::CODE,
               'language' => 'html',
               'rows' => 20,
               'show_label' => false,
               'separator' => 'none',
               // 'render_type' => 'ui',
            ]
         );
         $element->add_control(
            'custom_html_render',
            [
               'label' => __( 'Enable', 'magnific-addons' ),
               'type' => Controls_Manager::SWITCHER,
               'label_on' => __( 'On', 'magnific-addons' ),
               'label_off' => __( 'Off', 'magnific-addons' ),
               'return_value' => 'yes',
               'default' => 'no',
            ]
         );
         $element->add_control(
            'custom_html_description',
            [
               'raw' => __( 'You can use this field to add any html code to this widget including custom scripts and styles in &lt;script&gt; and &lt;style&gt; tags', 'magnific-addons' ),
               'type' => Controls_Manager::RAW_HTML,
               'content_classes' => 'elementor-descriptor',
            ]
         );
      $element->end_controls_section();
      
   }
   

   public function render_html( $content, $widget ) {
      $settings = $widget->get_settings();
      if ($settings['custom_html_render'] === 'yes') {
         $content = '<div class="mae-custom-html">' . $settings['custom_html_code'] . '</div>' . $content;
      }
      return $content;
   }


}
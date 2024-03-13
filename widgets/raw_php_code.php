<?php
namespace MagnificAddons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
// use \Elementor\Group_Control_Typography;
// use \Elementor\Group_Control_Border;
// use \Elementor\Core\Schemes\Typography;
// use \Elementor\Group_Control_Text_Shadow;
// use \Elementor\Group_Control_Box_Shadow;



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Aew_Raw_Php_Code_Widget extends Widget_Base {

	public function get_name() {
		return 'mae_raw_php_code';
	}

	public function get_title() {
		return __( 'Raw PHP Code', 'magnific-addons' );
	}

	public function get_icon() {
		return 'eicon-editor-code';
	}

	public function get_categories() {
		return [ 'mae-widgets' ];
	}



	// public function get_style_depends() {
	// 	return [ 'advanced-elementor-widgets-style' ];
	// }

	protected function _register_controls() {

		// MAIN SECTION
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Raw PHP Code', 'magnific-addons' ),
			]
      );
      if( current_user_can('administrator') || !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
         $this->add_control(
            'enabled',
            [
               'label' => __( 'Enable', 'magnific-addons' ),
               'type' => \Elementor\Controls_Manager::SWITCHER,
               'label_on' => __( 'On', 'magnific-addons' ),
               'label_off' => __( 'Off', 'magnific-addons' ),
               'return_value' => 'yes',
               'default' => 'yes',
            ]
         );
         $this->add_control(
            'custom_php_code',
            [
               'label'   => __( 'Custom PHP', 'magnific-addons' ),
               'show_label' => false,
               'type'    => Controls_Manager::CODE,
               'language' => 'php',
               //'description' => '<div style="/*display: none;*/" class="alert notice warning mae-notice-phpraw mae-notice mae-error mae-notice-error"><strong>ALERT</strong>: php code seem to be in error, please check it before save, or your page will be corrupted by fatal error!</div>',
            ]
         );


         if (defined('MAE_PHP_SAFE_MODE')) {
            $this->add_control(
               'custom_php_notice_safe_mode',
               [
                  'type'    => Controls_Manager::RAW_HTML,
                  'raw' => __( '<div class="mae-notice mae-error mae-notice-error">SAFE MODE ENABLED.</div>', 'dynamic-content-for-elementor' ),
                  'content_classes' => 'mae',
               ]
             );           
         }

      } else {

         $this->add_control(
           'custom_php_notice',
           [
              'type'    => Controls_Manager::RAW_HTML,
              'raw' => __( '<div class="mae-notice mae-error mae-notice-error">You must be admin to set this widget.</div>', 'dynamic-content-for-elementor' ),
              'content_classes' => 'mae',
           ]
         );

      }


		$this->end_controls_section(); 
	}

	public function get_script_depends() {
		return [ 'mae_widgets-script' ];
	}

   protected function render() {

      $settings = $this->get_settings_for_display();


      if ($settings['enabled'] == 'yes') {
         if (!defined('MAE_PHP_SAFE_MODE')) {
            $evalError = false;
            try {
               $this->execPhp('?>' . $settings['custom_php_code']);
            } catch (ParseError $e) {
               $evalError = true;
            } catch (Exception $e) {
               $evalError = true;
            } catch (Error $e) {
               $evalError = true;
            } catch (Error $e) {
               $evalError = true;
            }
            
            if ($evalError) {
               if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                     echo '<strong>';
                     _e( 'Please check your PHP code', 'magnific-addons' );
                     echo '</strong><br>';
                     echo 'ERROR: ',  $e->getMessage(), "\n";
               }
            }
         
         } else {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
               echo '<pre>' . __('RAW PHP CODE WIDGET IN SAFE MODE', 'magnific-addons') . '</pre>';
            }
         }

      }

   
   }



   protected function execPhp($code) {
         @eval($code);
   }

}







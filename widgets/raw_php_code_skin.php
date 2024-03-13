<?php
namespace ElementorPro\Modules\Posts\Skins;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Schemes\Color;
use \Elementor\Core\Schemes\Typography;
use \Elementor\Widget_Base;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Posts_HTML extends Skin_Base {

	private $template_cache=[];
	private $pid;


	
	public function get_id() {
		return 'custom_php_skin';
	}

	public function get_title() {
		return __( 'Custom PHP Skin', 'elementor-pro' );
	}

	protected function _register_controls_actions() {
		add_action( 'elementor/element/archive-posts/section_layout/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/archive-posts/section_query/after_section_end', [ $this, 'register_style_sections' ] );
		
		add_action( 'elementor/element/posts/section_layout/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/posts/section_query/after_section_end', [ $this, 'register_style_sections' ] );


		add_action( 'elementor/element/archive-posts/section_layout/before_section_end', [ $this, 'remove_controls' ] );
		add_action( 'elementor/element/posts/section_layout/before_section_end', [ $this, 'remove_controls' ] );
	}	
	
	public function register_controls( Widget_Base $widget ) {


		// MAIN SECTION
		$this->start_controls_section(
			'section_custom_php',
			[
				'label' => __( 'Custom PHP item code', 'magnific-addons' ),
			]
		);
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
				'php_code',
				[
					'label' => __( 'Custom loop item HTML/PHP', 'magnific-addons' ),
					'type' => \Elementor\Controls_Manager::CODE,
					'language' => 'php',
					'rows' => 20,
					'show_label' => false,
					'default' => __( 'Custom loop item HTML/PHP', 'magnific-addons' ),
				]
			);

			$this->add_control(//this would make use of 100% if width
				'view',
				[
					'label' => __( 'View', 'elecustomskin' ),
					'type' => \Elementor\Controls_Manager::HIDDEN,
					'default' => 'top',
					'prefix_class' => 'elementor-posts--thumbnail-',
				]
			);
			


		$this->end_controls_section(); // end main


	
	}

	public function remove_controls( Widget_Base $widget ) {

		$this->parent = $widget;

			parent::register_controls($widget);

			$this->remove_control( 'img_border_radius' );
			$this->remove_control( 'meta_data' );
			$this->remove_control( 'item_ratio' );
			$this->remove_control( 'image_width' );
			$this->remove_control( 'show_title' );
			$this->remove_control( 'title_tag' );
			$this->remove_control( 'masonry' );
			$this->remove_control( 'thumbnail' );
			$this->remove_control( 'thumbnail_size' );
			$this->remove_control( 'show_read_more' );
			$this->remove_control( 'read_more_text' );
			$this->remove_control( 'show_excerpt' );
			$this->remove_control( 'excerpt_length' );



	
	}

	private function get_post_id(){
		return $this->pid;
	}



	public function render_amp() {

	}



	protected function render_post_header() {
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( [ 'elementor-post elementor-grid-item' ] ); ?>>
		<?php
   }
   
   protected function execPhp($code) {
      @eval($code);
   }

	protected function render_post() {
		
		$this->render_post_header();

		$settings = $this->parent->get_settings_for_display();

		// error_log( "settings\n" . print_r($settings, true) . "\n" );

		if (!defined('MAE_PHP_SAFE_MODE')) {
			if ($settings['custom_php_skin_enabled'] == 'yes') {
				$evalError = false;
				try {
					$this->execPhp('?>' . $settings['custom_php_skin_php_code']);
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
			}
		} else {
			echo '<pre>' . __('RAW PHP CODE SKIN IN SAFE MODE', 'magnific-addons') . '</pre>';
		}

      


		$this->render_post_footer();

	}


}


// it seems the same skin brakes if set to 2 widgets in the same time

class Skin_Archive_HTML extends Skin_Posts_HTML {

	private $template_cache=[];
	private $pid;


	
	public function get_id() {
		return 'archive_custom_php_skin';
	}

	public function get_title() {
		return __( 'Custom PHP Skin', 'elementor-pro' );
	}
}

// Add a custom skin for the POSTS widget
    add_action( 'elementor/widget/posts/skins_init', function( $widget ) {
       $widget->add_skin( new Skin_Posts_HTML( $widget ) );
    } );
// Add a custom skin for the POST Archive widget
    add_action( 'elementor/widget/archive-posts/skins_init', function( $widget ) {
       $widget->add_skin( new Skin_Archive_HTML( $widget ) );
    } );
    
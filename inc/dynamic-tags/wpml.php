<?php

add_action( 'elementor/dynamic_tags/register_tags', function( $dynamic_tags ) {

	// register category
   $tags_config = \Elementor\Plugin::$instance->dynamic_tags->get_config();
   if (!isset($tags_config['groups']['mae'])) {
      \Elementor\Plugin::$instance->dynamic_tags->register_group( 'mae', [
         'title' => 'Magnific Addons'
      ]);
   }
	
	// conditional archive title
	class MAE_WPML_current_lang extends \Elementor\Core\DynamicTags\Tag {
		public function get_name() {
			return 'mae_wpml_current_lang';
		}
		public function get_categories() {
			return [ 'text' ];
		}
		public function get_group() {
			return [ 'mae' ];
		}
		public function get_title() {
			return 'WPML Current Language';
		}
		protected function _register_controls() {
			$this->add_control(
				'param_name',
				[
					'label' => __( 'Return', 'elementor-pro' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'native_name',
					'options' => [
						'native_name' => __( 'Native Name', 'magnific-addons' ),
						'translated_name' => __( 'Translated Name', 'magnific-addons' ),
						'language_code' => __( 'Language Code', 'magnific-addons' ),
					],
				]
			);
		}
		public function render() {
			$param_name = $this->get_settings('param_name');
			$value = $this->get_current_lang()[$param_name];
			if ( !empty( $value ) ) {
				echo wp_kses_post( $value );
			}
		}

		public function get_current_lang() {
         $languages = icl_get_languages('skip_missing=1');
         $curr_lang = array();
         if(!empty($languages)) {
               foreach($languages as $language) {
                  if(!empty($language['active'])) {
                     $curr_lang = $language;
                     break;
                  }
               }
         }
         return $curr_lang;
		}
		
	}
	$dynamic_tags->register_tag( 'MAE_WPML_current_lang' );
	
	
	
	
	// conditional archive image
	class MAE_WPML_Flag extends \Elementor\Core\DynamicTags\Data_Tag {
		public function get_name() {
			return 'mae_wpml_flag_image';
		}
		public function get_categories() {
			return [ 'image' ];
		}
		public function get_group() {
			return [ 'mae' ];
		}
		public function get_title() {
			return 'WPML Flag';
		}
		protected function _register_controls() {
			$this->add_control(
				'field_name',
				[
					'label' => 'Meta Name',
					'type' => 'text',
				]
			);
		}
		public function get_value(array $options = []) {
			// $id = get_post_meta(get_the_ID(), $this->get_settings('field_name'), true);
			$src = $this->get_current_lang()['country_flag_url'];
			$image_data = array(
				// 'id' => $id,
				'url' => $src,
			);
			return $image_data;
      }
      public function get_current_lang() {
         $languages = icl_get_languages('skip_missing=1');
         $curr_lang = array();
         if(!empty($languages)) {
               foreach($languages as $language) {
                  if(!empty($language['active'])) {
                     $curr_lang = $language;
                     break;
                  }
               }
         }
         return $curr_lang;
      }
	}
	$dynamic_tags->register_tag( 'MAE_WPML_Flag' );
	
} );

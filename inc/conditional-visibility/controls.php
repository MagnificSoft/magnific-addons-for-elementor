<?php
namespace MagnificAddons;


defined('ABSPATH') || die();

use \Elementor\Controls_Manager;
// use \Elementor\Element_Column;

class Aee_Сonditional_Visibility {

	public $conditions;

	public function __construct() {

		/** 
		 * 
		 * define conditions
		 * 
		 *	to add new conditions place file `your_condition_name.php` to conditions folder
		 *	and add `your_condition_name` key to array here

		 */

		$this->conditions = [
			'tax_term' => __('Taxonomy term', 'magnific-addons' ),
			'meta_field' => __('Meta field', 'magnific-addons' ),
			'post_info' => __('Post info', 'magnific-addons' ),
			'user' => __('User', 'magnific-addons' ),
			'mobile' => __('Device', 'magnific-addons' ),
		];

		// include conditions files
		$this->conditions_include();

		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'add_setting' ));
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'add_setting' ));
		add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'add_setting' ));
	}


	public function conditions_include() {
		foreach ( $this->conditions as $file => $title ) {
			$file = str_replace( '_', '-', strtolower( $file ) );
			if ( file_exists( MAE_PATH . 'inc/conditional-visibility/conditions/' . $file . '.php' ) ) {
				include_once MAE_PATH . 'inc/conditional-visibility/conditions/' . $file . '.php';
			}
		}
	}


	public function add_setting( $element ) {

		// error_log( "element\n" . print_r($element->get_type(), true) . "\n" );

      $element->start_controls_section(
			'section_mae_conditional_visibility',
			[
				'label' => __( 'MAE Сonditional Visibility', 'magnific-addons' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

         $element->add_control(
            'mae_cv_enabled',
            [
               'label' => __( 'Enable', 'magnific-addons' ),
               'type' => \Elementor\Controls_Manager::SWITCHER,
               'label_on' => __( 'Yes', 'magnific-addons' ),
               'label_off' => __( 'No', 'magnific-addons' ),
               'return_value' => '1',
               'default' => '',
               'render_type' => 'ui',
            ]
			);  
			$element->add_control(
				'mae_cv_item_action',
				[
					// 'label' => __('Conditions', 'magnific-addons' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'hide' => __('Hide if', 'magnific-addons' ),
						'show' => __('Show if', 'magnific-addons' ),
					],
					'default' => 'hide',
					'condition' => [
						'mae_cv_enabled' => '1',
					]
				]
			);	

			$element->add_control(
				'mae_cv_relation',
				[
					// 'label' => __('Relation', 'magnific-addons' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'and' => __('All condition met (AND)', 'magnific-addons' ),
						'or' => __('Any condition met (OR)', 'magnific-addons' ),
					],
					'default' => 'and',
					'condition' => [
						'mae_cv_enabled' => '1',
					]
				]
			);		

			$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'mae_cv_item_by',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $this->conditions,
						'label_block' => true,
					]
				);	

				$this->add_repeater_controls( $repeater );
			
			$element->add_control(
				'mae_cv_repeater',
				[
					// 'label' => __( 'MAE Responsive Customm CSS', 'magnific-addons' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'title_field' => '<#
						var conditions = ' . json_encode($this->conditions) . ';
						print(conditions[mae_cv_item_by]);
					#>',
					'prevent_empty' => false,
					'condition' => [
						'mae_cv_enabled' => '1',
					]
				]
			);

		$element->end_controls_section();
		
	}


	public function add_repeater_controls ( $repeater ) {

		foreach ( $this->conditions as $condition => $title) {
			$controls_fn_name = 'MagnificAddons\mae_cv_' . $condition . '_controls';
			$controls_fn_name($repeater);
		}

	}

}
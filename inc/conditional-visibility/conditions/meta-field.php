<?php
namespace MagnificAddons;

function mae_cv_meta_field_controls( $repeater ) {

		// taxonomy term
		$repeater->add_control(
			'mae_cv_item_meta_field_name',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Meta field key', 'magnific-addons' ),
				'label_block' => true,
				'condition' => [
					'mae_cv_item_by' => 'meta_field',
				]
			]
		);
		$repeater->add_control(
			'mae_cv_item_meta_field_single',
			[
				'label' => __('Get field as', 'magnific-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				// 'label_block' => true,
				'options' => [
					'true' => __('Single', 'magnific-addons' ),
					'false' => __('Array', 'magnific-addons' ),
				],
				'default' => 'true',
				'condition' => [
					'mae_cv_item_by' => 'meta_field',
				],
				'description' => __( 'Read more about get_post_meta() function <a href="https://developer.wordpress.org/reference/functions/get_term_meta/" target="_blank">here</a>', 'magnific-addons' ),
			]
		);  

		$repeater->add_control(
			'mae_cv_item_meta_operator',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'equal' => __( 'Equal', 'magnific-addons' ),
					'not_equal' => __( 'Not equal', 'magnific-addons' ),
					'empty' => __( 'Empty', 'magnific-addons' ),
					'not_empty' => __( 'Not empty', 'magnific-addons' ),
					'array_empty' => __( 'Array empty', 'magnific-addons' ),
					'array_not_empty' => __( 'Array not empty', 'magnific-addons' ),
					'is_array' => __( 'Is array', 'magnific-addons' ),
					'not_is_array' => __( 'Not is array', 'magnific-addons' ),
					'in_array' => __( 'In array', 'magnific-addons' ),
					'not_in_array' => __( 'Not in array', 'magnific-addons' ),

				],
				'placeholder' => __( 'Term ID', 'magnific-addons' ),
				'label_block' => true,
				'default' => 'equal',
				'condition' => [
					'mae_cv_item_by' => 'meta_field',
				]
			]
		);
		
		$repeater->add_control(
			'mae_cv_item_meta_field_value',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Meta field value', 'magnific-addons' ),
				'label_block' => true,
				'condition' => [
					'mae_cv_item_by' => 'meta_field',
					'mae_cv_item_meta_operator' => ['equal', 'not_equal', 'in_array', 'not_in_array']
				]
			]
		);
}


/*
*
* return 0 if condition or 1 if not
*
*/
function mae_cv_meta_field_compare($condition) {

	// error_log( "condition\n" . print_r($condition, true) . "\n" );

	if (empty($condition['mae_cv_item_meta_field_name'])) {
		return 0;
	}

	// if (empty($condition['mae_cv_item_meta_field_value'])) {
	// 	return 0;
	// }

	$queried_object = get_queried_object();
	// error_log( "queried_object\n" . print_r($queried_object, true) . "\n" );


	// POST PAGE
	if (is_object($queried_object)) {

		if(get_class($queried_object) == 'WP_Post'){
			$value = get_post_meta($queried_object->ID, $condition['mae_cv_item_meta_field_name'], $condition['mae_cv_item_meta_field_single'] == 'true' ? true : false);
		} 
		if(get_class($queried_object) == 'WP_Term') {
			$value = get_term_meta($queried_object->term_id, $condition['mae_cv_item_meta_field_name'], $condition['mae_cv_item_meta_field_single'] == 'true' ? true : false);
		}

		// error_log( "value\n" . print_r($value, true) . "\n" );

		// EQUAL
		if($condition['mae_cv_item_meta_operator'] == 'equal') {
			if ($value == $condition['mae_cv_item_meta_field_value']) {
				return 1;
			}
		}

		// NOT EQUAL
		if($condition['mae_cv_item_meta_operator'] == 'not_equal') {
			if ($value != $condition['mae_cv_item_meta_field_value']) {
				return 1;
			}
		}

		// EMPTY
		if($condition['mae_cv_item_meta_operator'] == 'empty') {
			if (empty($value)) {
				return 1;
			}
		}

		// NOT EMPTY
		if($condition['mae_cv_item_meta_operator'] == 'not_empty') {
			if (!empty($value)) {
				return 1;
			}
		}

		// ARRAY EMPTY
		if($condition['mae_cv_item_meta_operator'] == 'array_empty') {
			if (is_array($value) && empty($value)) {
				return 1;
			}
		}

		// ARRAY NOT EMPTY
		if($condition['mae_cv_item_meta_operator'] == 'array_not_empty') {
			if (is_array($value) && !empty($value)) {
				return 1;
			}
		}



		// IN ARRAY
		if($condition['mae_cv_item_meta_operator'] == 'in_array') {
			if (is_array($value) && !empty($value) && in_array($condition['mae_cv_item_meta_field_value'], $value)) {
				return 1;
			}
		}

		// NOT IN ARRAY
		if($condition['mae_cv_item_meta_operator'] == 'not_in_array') {
			if (is_array($value) && !empty($value) && !in_array($condition['mae_cv_item_meta_field_value'], $value)) {
				return 1;
			}
		}

	}


	return 0;

}
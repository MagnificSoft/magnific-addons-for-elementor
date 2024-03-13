<?php
namespace MagnificAddons;

function mae_cv_post_info_controls( $repeater ) {

   // post info 

   	$repeater->add_control(
			'mae_cv_item_post_info_name',
			[
				// 'label' => __('Post info', 'magnific-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'post_type' => __( 'Post type', 'magnific-addons' ),
					'id' => __( 'Post ID', 'magnific-addons' ),
					'post_title' => __( 'Post title', 'magnific-addons' ),
					'post_excerpt' => __( 'Post excerpt', 'magnific-addons' ),
					'post_content' => __( 'Post content', 'magnific-addons' ),
					'post_status' => __( 'Post status', 'magnific-addons' ),
					'post_date' => __( 'Post date', 'magnific-addons' ),
					'post_author' => __( 'Post author', 'magnific-addons' ),
					'post_name' => __( 'Post slug', 'magnific-addons' ),
					'post_parent' => __( 'Post parent', 'magnific-addons' ),
					'comment_status' => __( 'Comment status', 'magnific-addons' ),
					// 'fatured' => __( 'Featured post', 'magnific-addons' ),
				],
				'label_block' => true,
				'default' => 'post_type',
				'condition' => [
					'mae_cv_item_by' => 'post_info',
				]
			]
		);

		$repeater->add_control(
			'mae_cv_item_post_info_operator',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'equal' => __( 'Equal', 'magnific-addons' ),
					'not_equal' => __( 'Not equal', 'magnific-addons' ),
					'empty' => __( 'Empty', 'magnific-addons' ),
					'not_empty' => __( 'Not empty', 'magnific-addons' ),
					// 'contain' => __( 'Contain string', 'magnific-addons' ),
					// 'not_contain' => __( 'Not contain string', 'magnific-addons' ),
				],
				'placeholder' => __( 'Term ID', 'magnific-addons' ),
				'label_block' => true,
				'default' => 'equal',
				'condition' => [
					'mae_cv_item_by' => 'post_info',
				]
			]
		);
		
		$repeater->add_control(
			'mae_cv_item_post_info_value',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Value', 'magnific-addons' ),
				'label_block' => true,
				'condition' => [
					'mae_cv_item_by' => 'post_info',
					'mae_cv_item_post_info_operator!' => ['empty', 'not_empty']
				]
			]
		);
}


/*
*
* return 0 if condition or 1 if not
*
*/
function mae_cv_post_info_compare($condition) {

	// error_log( "condition\n" . print_r($condition, true) . "\n" );

	$queried_object = get_queried_object();
	// error_log( "queried_object\n" . print_r(get_class($queried_object), true) . "\n" );


	// POST PAGE
	if (is_object($queried_object) && get_class($queried_object) == 'WP_Post') {

		$queried_object = (array) $queried_object;

		$value = $queried_object[$condition['mae_cv_item_post_info_name']];
		// error_log( "value\n" . print_r($value, true) . "\n" );

		// EQUAL
		if($condition['mae_cv_item_post_info_operator'] == 'equal') {
			if ($value == $condition['mae_cv_item_post_info_value']) {
				return 1;
			}
		}

		// NOT EQUAL
		if($condition['mae_cv_item_post_info_operator'] == 'not_equal') {
			if ($value != $condition['mae_cv_item_post_info_value']) {
				return 1;
			}
		}

		// EMPTY
		if($condition['mae_cv_item_post_info_operator'] == 'empty') {
			if (empty($value)) {
				return 1;
			}
		}

		// NOT EMPTY
		if($condition['mae_cv_item_post_info_operator'] == 'not_empty') {
			if (!empty($value)) {
				return 1;
			}
		}

		// // CONTAIN
		// if($condition['mae_cv_item_post_info_operator'] == 'contain') {
		// 	if (is_array($value) && !empty($value) && in_array($condition['mae_cv_item_post_info_value'], $value)) {
		// 		return 1;
		// 	}
		// }

		// // NOT CONTAIN
		// if($condition['mae_cv_item_post_info_operator'] == 'not_contain') {
		// 	if (is_array($value) && !empty($value) && !in_array($condition['mae_cv_item_post_info_value'], $value)) {
		// 		return 1;
		// 	}
		// }

	}


	return 0;

}
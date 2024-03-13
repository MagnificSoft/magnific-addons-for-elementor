<?php
namespace MagnificAddons;


/*
*
* controls for taxonomy conditions
*
*/
function mae_cv_user_controls( $repeater ) {


	$repeater->add_control(
		'mae_cv_item_user',
		[
			// 'label' => __('Post info', 'magnific-addons' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => [
				'id' => __( 'User ID', 'magnific-addons' ),
				'logged_in' => __( 'Logged In', 'magnific-addons' ),
				'not_logged_in' => __( 'Not Logged In', 'magnific-addons' ),
			],
			'label_block' => true,
			'default' => 'id',
			'condition' => [
				'mae_cv_item_by' => 'user',
			]
		]
	);

	$repeater->add_control(
		'mae_cv_item_user_operator',
		[
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => [
				'equal' => __( 'Equal', 'magnific-addons' ),
				'not_equal' => __( 'Not equal', 'magnific-addons' ),
				// 'empty' => __( 'Empty', 'magnific-addons' ),
				// 'not_empty' => __( 'Not empty', 'magnific-addons' ),
				// 'contain' => __( 'Contain string', 'magnific-addons' ),
				// 'not_contain' => __( 'Not contain string', 'magnific-addons' ),
			],
			// 'placeholder' => __( 'Term ID', 'magnific-addons' ),
			'label_block' => true,
			'default' => 'equal',
			'condition' => [
				'mae_cv_item_by' => 'user',
				'mae_cv_item_user!' => ['logged_in', 'not_logged_in']
			]
		]
	);
	
	$repeater->add_control(
		'mae_cv_item_user_value',
		[
			'type' => \Elementor\Controls_Manager::TEXT,
			'placeholder' => __( 'Value', 'magnific-addons' ),
			'label_block' => true,
			'condition' => [
				'mae_cv_item_by' => 'user',
				'mae_cv_item_user!' => ['logged_in', 'not_logged_in']
			]
		]
	);
   
}








/*
*
* return 0 if condition or 1 if not
*
*/
function mae_cv_user_compare($condition) {

	// error_log( "condition\n" . print_r($condition, true) . "\n" );



	$user = wp_get_current_user();
	// error_log( "user\n" . print_r($user, true) . "\n" );



	// USER NOT LOGGED IN
	if (isset($user->ID) && $user->ID == 0) {

		// NOT LOGGED IN
		if($condition['mae_cv_item_user'] == 'not_logged_in') {
			return 1;
		}

	}
	  

	// USER LOGGED IN
	if (isset($user->ID) && !$user->ID == 0) {

		// LOGGED IN
		if($condition['mae_cv_item_user'] == 'logged_in') {
			return 1;
		}

	}


	return 0;

}
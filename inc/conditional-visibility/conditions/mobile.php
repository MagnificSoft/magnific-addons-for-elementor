<?php
namespace MagnificAddons;


/*
*
* controls for taxonomy conditions
*
*/
function mae_cv_mobile_controls( $repeater ) {


	$repeater->add_control(
		'mae_cv_item_mobile',
		[
			// 'label' => __('Post info', 'magnific-addons' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => [
				'mobile' => __( 'Mobile Device', 'magnific-addons' ),
				'not_mobile' => __( 'Not Mobile Device', 'magnific-addons' ),
			],
			'label_block' => true,
			'default' => 'id',
			'condition' => [
				'mae_cv_item_by' => 'mobile',
			]
		]
	);
	
   
}








/*
*
* return 0 if condition or 1 if not
*
*/
function mae_cv_mobile_compare($condition) {


	// MOBILE DEVICE
	if ($condition['mae_cv_item_mobile'] == 'mobile') {

		if(wp_is_mobile()) {
			return 1;
		}

   }

	// NOT MOBILE DEVICE
	if ($condition['mae_cv_item_mobile'] == 'not_mobile') {

		if(!wp_is_mobile()) {
			return 1;
		}

   }
	
	return 0;

}
<?php
namespace MagnificAddons;


/*
*
* controls for taxonomy conditions
*
*/
function mae_cv_tax_term_controls( $repeater ) {

		// taxonomy term
		$repeater->add_control(
			'mae_cv_item_tax_name',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Taxonomy name', 'magnific-addons' ),
				'label_block' => true,
				'condition' => [
					'mae_cv_item_by' => 'tax_term',
				]
			]
      );
      
		$repeater->add_control(
			'mae_cv_item_tax_operator',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'is' => __( 'Is', 'magnific-addons' ),
					'not' => __( 'Not', 'magnific-addons' ),
					'child_of' => __( 'Child of', 'magnific-addons' ),
					'not_child_of' => __( 'Not child of', 'magnific-addons' ),
				],
				'placeholder' => __( 'Term ID', 'magnific-addons' ),
				'label_block' => true,
				'default' => 'is',
				'condition' => [
					'mae_cv_item_by' => 'tax_term',
				]
			]
      );
      
		$repeater->add_control(
			'mae_cv_item_tax_term_id',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Term ID', 'magnific-addons' ),
				'label_block' => true,
				'condition' => [
					'mae_cv_item_by' => 'tax_term',
				],
				'description' => __( 'Term ID or comma separated list of ID`s', 'magnific-addons' ),
			]
		);
   
}








/*
*
* return 0 if condition or 1 if not
*
*/
function mae_cv_tax_term_compare($condition) {

	// error_log( "condition\n" . print_r($condition, true) . "\n" );

	if (empty($condition['mae_cv_item_tax_name'])) {
		return 0;
	}

	if (empty($condition['mae_cv_item_tax_term_id'])) {
		return 0;
	}

	$queried_object = get_queried_object();
	// error_log( "queried_object\n" . print_r($queried_object, true) . "\n" );

	$term_ids = explode(',', $condition['mae_cv_item_tax_term_id']);

	// POST PAGE
	if (is_object($queried_object) && get_class($queried_object) == 'WP_Post') {

		// IS
		if($condition['mae_cv_item_tax_operator'] == 'is') {
			$is_in = is_object_in_term( $queried_object->ID, $condition['mae_cv_item_tax_name'], $term_ids );
			if( !is_wp_error($is_in) && $is_in) {
				return 1;
			}
		}

		// NOT
		if($condition['mae_cv_item_tax_operator'] == 'not') {
			$is_in = is_object_in_term( $queried_object->ID, $condition['mae_cv_item_tax_name'], $term_ids );
			if( !is_wp_error($is_in) && !$is_in) {
				return 1;
			}
		}		

		// CHILD OF
		if($condition['mae_cv_item_tax_operator'] == 'child_of') {
			$post_terms = wp_get_object_terms($queried_object->ID, $condition['mae_cv_item_tax_name'] );
			foreach($post_terms as $term) {
				$ancestors = get_ancestors( $term->term_id, $condition['mae_cv_item_tax_name'] );
				if(count(array_intersect($term_ids, $ancestors))) {
					return 1;
				}
			}
		}

		// NOT CHILD OF
		if($condition['mae_cv_item_tax_operator'] == 'not_child_of') {
			$post_terms = wp_get_object_terms($queried_object->ID, $condition['mae_cv_item_tax_name'] );
			foreach($post_terms as $term) {
				$ancestors = get_ancestors( $term->term_id, $condition['mae_cv_item_tax_name'] );
				if(!count(array_intersect($term_ids, $ancestors))) {
					return 1;
				}
			}
		}
		
	}
	  

	// ARCHIVE PAGE
	if (is_object($queried_object) && get_class($queried_object) == 'WP_Term') {

		// error_log( "queried_object\n" . print_r($queried_object, true) . "\n" );

		// IS
		if($condition['mae_cv_item_tax_operator'] == 'is') {
			$is_in = in_array( $queried_object->term_id, $term_ids );
			if( !is_wp_error($is_in) && $is_in) {
				return 1;
			}
		}
		
		// NOT
		if($condition['mae_cv_item_tax_operator'] == 'not') {
			$is_in = in_array( $queried_object->term_id, $term_ids );
			if( !is_wp_error($is_in) && !$is_in) {
				return 1;
			}
		}		
		
		// CHILD OF
		if($condition['mae_cv_item_tax_operator'] == 'child_of') {
			foreach($term_ids as $term) {
				$ancestors = get_ancestors($queried_object->term_id, $condition['mae_cv_item_tax_name']);
				$is_in = in_array( $term, $ancestors );
				if( !is_wp_error($is_in) && $is_in) {
					return 1;
				}
			}
		}

		// NOT CHILD OF
		if($condition['mae_cv_item_tax_operator'] == 'not_child_of') {
			foreach($term_ids as $term) {
				$ancestors = get_ancestors($queried_object->term_id, $condition['mae_cv_item_tax_name']);
				$is_in = in_array( $term, $ancestors );
				if( !is_wp_error($is_in) && !$is_in) {
					return 1;
				}
			}
		}
	}


	return 0;

}
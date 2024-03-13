<?php
namespace MagnificAddons;

function mae_conditional_visibility_is_display( $visible, $element ) {

   $settings = $element->get_settings();

   // error_log( "settings\n" . print_r($settings, true) . "\n" );

   if(isset($settings['mae_cv_enabled']) && $settings['mae_cv_enabled'] == '1') {

      // error_log( "mae_cv_repeater\n" . print_r($settings['mae_cv_repeater'], true) . "\n" );

      if(empty($settings['mae_cv_repeater'])) return $visible;
      foreach($settings['mae_cv_repeater'] as $index => $condition) {

         $controls_fn_name = 'MagnificAddons\mae_cv_' . $condition['mae_cv_item_by'] . '_compare';
			$conditions[$index] = $controls_fn_name($condition); // return true if conditionn match

      }

      // error_log( print_r($settings['mae_cv_relation'], true) );
      // error_log( print_r($conditions, true) );
      // error_log( "conditions\n" . print_r($conditions, true) . "\n" );

      // AND - if one of condition true
      if ($settings['mae_cv_relation'] == 'and') { 
         $visible = in_array(0, $conditions) ? 1 : 0; 
      }
      
      // OR - if any of condition true
      if ($settings['mae_cv_relation'] == 'or') {
         $visible = in_array(1, $conditions) ? 0 : 1;
      }

      // error_log( print_r($visible, true) );

      if($settings['mae_cv_item_action'] == 'show') {
         $visible = !$visible;
      }

      return $visible;

   } 

   return $visible; // display element by default
   
}
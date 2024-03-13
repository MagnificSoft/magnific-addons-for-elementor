<?php
namespace MagnificAddons;


use \Elementor\Core\Responsive\Responsive;
use \Elementor\Core\DynamicTags\Dynamic_CSS;


class MAE_Post_CSS {

   private $widget_type;

   private $target;

   public function parse_css($css) {

      // get processed post id
      $post_id = $css->get_post_id();
      
      // get processed document instance by post id
      $document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend($post_id);

      // get elementor data from document
      $elements_data = $document->get_elements_data();

      // get array of $widget_type in $elementor_data
      $widgets = $this->search_in_array($elements_data, 'widgetType', $this->widget_type);

      // error_log( "widgets\n" . print_r($widgets, true) . "\n" );

      foreach($widgets as $widget) {

         // get widget id
         $id = $widget['id'];

         // error_log( "id\n" . print_r($id, true) . "\n" );


         // get target selector control value or exit if empty
         if (!isset($widget['settings'][$this->target]) || $widget['settings'][$this->target] == '') {
            continue;
         }

         $target_selector = $widget['settings'][$this->target];

         // get rules from elementor stylesheet object
         $devices_rules = $css->get_stylesheet()->get_rules();

         $selector_to_replace = '.elementor-'.$post_id.' .elementor-element.elementor-element-'.$id;

         // loop through media queries ('all', 'max_tablet', 'max-mobile')
         foreach($devices_rules as $device => $rules) {

            

            foreach($rules as $key => $value) {

               if (stristr($key, $selector_to_replace)) {
                  $new_key = str_replace($selector_to_replace, $target_selector, $key);
                  // error_log( "new_key\n" . print_r($new_key, true) . "\n" );
                  // error_log( "value\n" . print_r($value, true) . "\n" );




                  // construct new media query
                  $device_array = explode("_", $device);
                  if ($device_array[0] == 'all') { // all devices
                     $media_query = null;
                  } 
                  elseif ($device_array[0] == 'max') { // max_tablet
                     $media_query = array(
                        'max' => $device_array[1],
                     );
                  } 
                  elseif ($device_array[0] == 'min') { // max_mobile
                     $media_query = array(
                        'min' => $device_array[1],
                     );
                  }

                  // add new rules for media query
                  foreach($value as $css_property => $css_value) {
                     $css->get_stylesheet()->add_rules( $new_key, $css_property . ':' . $css_value, $media_query );
                  }
               }


            }

         }
         
      }
      
   }

   private function search_in_array($array, $key, $value) { 

      $results = array(); 
      
      // if it is array 
      if (is_array($array)) { 
            
         // if array has required key and value 
         // matched store result  
         if (isset($array[$key]) && $array[$key] == $value) { 
            $results[] = $array; 
         } 
         
         // Iterate for each element in array 
         foreach ($array as $subarray) { 
               
            // recur through each element and append result  
            $results = array_merge($results, $this->search_in_array($subarray, $key, $value)); 

         } 
      } 

      return $results; 

   } 

   public function __construct($widget_type, $target) {
      $this->widget_type = $widget_type;
      $this->target = $target;
      add_action('elementor/css-file/post/parse', [ $this, 'parse_css' ] );
      // error_log( print_r($widget_type, true) );
   }
}







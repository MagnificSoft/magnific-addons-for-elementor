<?php

class MAE_CodeAutoUpdate {

    # URL to check for updates, this is where the index.php script goes
    public $api_url;
    private $slug;
    public $plugin;

    
    public function __construct($api_url, $slug, $plugin) {
        $this->api_url = $api_url;
        $this->slug    = $slug;
        $this->plugin  = $plugin;

    }
    
    
    public function check_for_plugin_update($checked_data) {

        if (empty($checked_data->checked) || !isset($checked_data->checked[$this->plugin])) {
            return $checked_data;
        }
            
        
        $request_string = $this->prepare_request('plugin_update');
        if($request_string === FALSE) {
            return $checked_data;
        }
            
        
        // Start checking for an update
        $request_uri = $this->api_url . '?' . http_build_query( $request_string , '', '&');
        $data = wp_remote_get( $request_uri );
        
        if(is_wp_error( $data ) || $data['response']['code'] != 200) {
            return $checked_data;
        }
            
        $response_block = json_decode($data['body']);
        
        if(!is_array($response_block) || count($response_block) < 1) {
            return $checked_data;
        }
        
        //retrieve the last message within the $response_block
        $response_block = $response_block[count($response_block) - 1];
        $response = isset($response_block->message) ? $response_block->message : '';
        
        // Feed the update data into WP updater (UPDATE)
        if (is_object($response) && !empty($response)) { 

            //include slug and plugin data
            $response->slug = $this->slug;
            $response->plugin = $this->plugin;
            
            $checked_data->response[$this->plugin] = $response;
            

        // Feed the update data into WP updater (INFO ONLY)
        } else {

            $request_string = $this->prepare_request('code_version');
            if($request_string === FALSE) {
                return $checked_data;
            }
                
            
            // Start checking for an update info
            $request_uri = $this->api_url . '?' . http_build_query( $request_string , '', '&');
            $data = wp_remote_get( $request_uri );
            
            if(is_wp_error( $data ) || $data['response']['code'] != 200) {
                return $checked_data;
            }
                
            $response_block = json_decode($data['body']);
            
            if(!is_array($response_block) || count($response_block) < 1) {
                return $checked_data;
            }
            
            //retrieve the last message within the $response_block
            $response_block = $response_block[count($response_block) - 1];
            $response = isset($response_block->message) ? $response_block->message : '';

            if (!isset($new_response)) {
				$new_response = new stdClass();
            }
            
            $new_response->slug = $this->slug;
            $new_response->plugin = $this->plugin;

            $new_response->new_version = $response->version;
            $new_response->date = $response->last_updated;
            $new_response->package = '';
            $new_response->upgrade_notice = $response->upgrade_notice;
            $new_response->author = $response->author;
            $new_response->tested = $response->tested;
            $new_response->homepage = $response->homepage;
            
            if (MAE_VERSION == $new_response->new_version) {
                return $checked_data;
            }

            $checked_data->response[$this->plugin] = $new_response;

        }

        return $checked_data;
    }
    
    
    public function plugins_api_call($def, $action, $args) {

        if (!is_object($args) || !isset($args->slug) || $args->slug != $this->slug) {
            return $def;
        }
        
        $request_string = $this->prepare_request($action, $args);

        if($request_string === FALSE) {
            return new WP_Error('plugins_api_failed', __('An error occour when try to identify the pluguin.' , 'advanced-elementor-widgetst') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'advanced-elementor-widgetst' ) .'&lt;/a>');
        }
        
        $request_uri = $this->api_url . '?' . http_build_query( $request_string , '', '&');
        $data = wp_remote_get( $request_uri );
        
        if(is_wp_error( $data ) || $data['response']['code'] != 200) {
            return new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.' , 'advanced-elementor-widgetst') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'advanced-elementor-widgetst' ) .'&lt;/a>', $data->get_error_message());
        }
        
        $response_block = json_decode($data['body']);
        //retrieve the last message within the $response_block
        $response_block = $response_block[count($response_block) - 1];
        $response = $response_block->message;

        // Feed the update data into WP updater
        if (is_object($response) && !empty($response)) {

            //include slug and plugin data
            $response->slug = $this->slug;
            $response->plugin = $this->plugin;
            
            $response->sections = (array)$response->sections;
            $response->banners = (array)$response->banners;
            
            return $response;

        } else {


            $action = 'code_version';
            $request_string = $this->prepare_request($action, $args);

            if($request_string === FALSE) {
                return new WP_Error('plugins_api_failed', __('An error occour when try to identify the pluguin.' , 'advanced-elementor-widgetst') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'advanced-elementor-widgetst' ) .'&lt;/a>');
            }
            
            $request_uri = $this->api_url . '?' . http_build_query( $request_string , '', '&');
            $data = wp_remote_get( $request_uri );
            
            if(is_wp_error( $data ) || $data['response']['code'] != 200) {
                return new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.' , 'advanced-elementor-widgetst') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'advanced-elementor-widgetst' ) .'&lt;/a>', $data->get_error_message());
            }
            
            $response_block = json_decode($data['body']);
            //retrieve the last message within the $response_block
            $response_block = $response_block[count($response_block) - 1];
            $response = $response_block->message;

            $new_response = new \stdClass();

            $new_response->name = $response->name;
            $new_response->version = $response->version;
            $new_response->last_updated = $response->last_updated;
            $new_response->package = '';
            $new_response->download_link = '';
            $new_response->upgrade_notice = $response->upgrade_notice;
            $new_response->author = $response->author;
            $new_response->tested = $response->tested;
            $new_response->requires = $response->requires;
            $new_response->homepage = $response->homepage;
            $new_response->sections = (array)$response->sections;
            $new_response->banners = (array)$response->banners;
            $new_response->slug = $this->slug;
            $new_response->plugin = $this->plugin;

            return $new_response;

        }
    }
    
    public function prepare_request($action, $args = array()) {
        global $wp_version;
        
        $license_data = get_site_option('mae_license'); 
        
        return array(
                        'woo_sl_action'        => $action,
                        'version'              => MAE_VERSION,
                        'product_unique_id'    => MAE_PRODUCT_ID,
                        'licence_key'          => isset($license_data['key']) ? $license_data['key'] : '',
                        'domain'               => MAE_INSTANCE,
                        'wp-version'           => $wp_version,
        );
    }
}
         
         
    function mae_run_updater() {
         
        $wp_plugin_auto_update = new MAE_CodeAutoUpdate(MAE_APP_API_URL, 'magnific-addons', 'advanced-elementor-widgets/advanced-elementor-widgets.php');
        
        // Take over the update check
        add_filter('pre_set_site_transient_update_plugins', array($wp_plugin_auto_update, 'check_for_plugin_update'));
        
        // Take over the Plugin info screen
        add_filter('plugins_api', array($wp_plugin_auto_update, 'plugins_api_call'), 10, 3);
    
    }
    add_action( 'after_setup_theme', 'mae_run_updater' );



?>
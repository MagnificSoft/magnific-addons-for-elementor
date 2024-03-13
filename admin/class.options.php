<?php

class MAE_options_interface {
        
    var $licence;
    
    function __construct() {
            
        // $this->licence = new MAE_licence();

        if (isset($_GET['page']) && ($_GET['page'] == 'mae-license'  ||  $_GET['page'] == 'mae-license')) {
            add_action( 'init', array($this, 'options_update'), 1 );
        }
                
        add_action( 'admin_menu', array($this, 'admin_menu'), 20);
                                
        // if(!$this->licence->licence_key_verify()) {
        //     // add_action('admin_notices', array($this, 'admin_no_key_notices'));
        //     // add_action('network_admin_notices', array($this, 'admin_no_key_notices'));
        // }

        add_action( 'wp_ajax_mae_dismiss_nokey_notice', array($this,'mae_dismiss_nokey_notice'));
            
    }
        
    function __destruct() {
        
    }
    
        
    function admin_menu() {
        // if(!$this->licence->licence_key_verify()) {
        //     $hookID = add_submenu_page( 
        //         'mae-options', 
        //         'MAE Widgets License', 
        //         'License', 
        //         'manage_options', 
        //         'mae-license', 
        //         array($this, 'licence_form') 
        //     ); 
        // } else {
        //     $hookID = add_submenu_page( 
        //         'mae-options', 
        //         'MAE Widgets License', 
        //         'License', 
        //         'manage_options', 
        //         'mae-license', 
        //         array($this, 'licence_deactivate_form') 
        //     );
        // }
        add_action('load-' . $hookID , array($this, 'load_dependencies'));
        add_action('load-' . $hookID , array($this, 'admin_notices'));
        
        add_action('admin_print_styles-' . $hookID , array($this, 'admin_print_styles'));
        add_action('admin_print_scripts-' . $hookID , array($this, 'admin_print_scripts')); 
    }
        
        
    // function options_interface() {
    //     if(!$this->licence->licence_key_verify() && !is_multisite()) {
    //         $this->licence_form();
    //         return;
    //     }
    // }
    
    // function options_update() {
    //     if (isset($_POST['mae_license_form_submit'])) {
    //         $this->licence_form_submit();
    //         return;
    //     }
    // }

    function load_dependencies() {

    }
        
    function admin_notices() {
        global $slt_form_submit_messages;

        if($slt_form_submit_messages == '')
            return;
        
        $messages = $slt_form_submit_messages;

                
        if(count($messages) > 0) {
            echo "<div id='notice' class='updated fade'><p>". implode("</p><p>", $messages )  ."</p></div>";
        }

    }
            
    function admin_print_styles() {
        wp_register_style( 'mae_lic_admin', MAE_URL . '/admin/css/admin.css' );
        wp_enqueue_style( 'mae_lic_admin' ); 
    }
        
    function admin_print_scripts(){

    }
    
    
    function admin_no_key_notices() {
        if ( !current_user_can('manage_options'))
            return;

        global $current_user;
        $user_id = $current_user->ID;
        if (get_user_meta($user_id, 'mae-plugin-ignore-notice'))
            return;

        $screen = get_current_screen();
            
        if(isset($screen->id) && $screen->id == 'mae-widgets_page_mae-license')
            return;
        
        ?>
        <div class="notice notice-error mae-nokey-notice is-dismissible">
            <p><?php _e( "Magnific Addons for Elementor - Plugin upadates is inactive, please enter your", 'magnific-addons' ) ?> <a href="admin.php?page=mae-license"><?php _e( "Licence Key", 'magnific-addons' ) ?></a></p>
        </div>
        <script>
            (function($){
                $(document).on( 'click', '.ae-nokey-notice .notice-dismiss', function () {
                    $.ajax( ajaxurl,{
                        type: 'POST',
                        data: {
                            action: 'mae_dismiss_nokey_notice'
                        }
                    });
                });
            })(jQuery)
        </script>    
        <?php
        
    }

    // AJAX dismiss admin no key notice
    function mae_dismiss_nokey_notice() {
        global $current_user;
        $user_id = $current_user->ID;
        add_user_meta($user_id, 'ae-plugin-ignore-notice', 'true', true);
        echo "done";
        die;
    }

    

    function licence_form_submit() {
        global $slt_form_submit_messages; 
        
        //check for de-activation
        if (isset($_POST['mae_license_form_submit']) && isset($_POST['mae_license_deactivate']) && wp_verify_nonce($_POST['mae_license_nonce'],'mae_license')) {
            global $slt_form_submit_messages;
            
            $license_data = get_site_option('mae_license');                        
            $license_key = $license_data['key'];

            //build the request query
            $args = array(
                'woo_sl_action' => 'deactivate',
                'licence_key' => $license_key,
                'product_unique_id' => MAE_PRODUCT_ID,
                'domain' => MAE_INSTANCE
            );
            $request_uri    = MAE_APP_API_URL . '?' . http_build_query( $args , '', '&');
            $data           = wp_remote_get( $request_uri );
            
            if(is_wp_error( $data ) || $data['response']['code'] != 200) {
                $slt_form_submit_messages[] .= __('There was a problem connecting to ', 'magnific-addons') . MAE_APP_API_URL;
                return;  
            }
                
            $response_block = json_decode($data['body']);
            //retrieve the last message within the $response_block
            $response_block = $response_block[count($response_block) - 1];
            $response = $response_block->message;
            
            if(isset($response_block->status)) {
                if($response_block->status == 'success' && $response_block->status_code == 's201') {
                    //the license is active and the software is active
                    $slt_form_submit_messages[] = $response_block->message;
                    
                    $license_data = get_site_option('mae_license');
                    
                    //save the license
                    $license_data['key']          = '';
                    $license_data['last_check']   = time();
                    
                    update_site_option('mae_license', $license_data);
                } else { //if message code is e104  force de-activation
                    if (
                        $response_block->status_code == 'e002'
                     || $response_block->status_code == 'e003'
                     || $response_block->status_code == 'e004'
                     || $response_block->status_code == 'e111'
                     ) {
                        $license_data = get_site_option('mae_license');
                
                        //save the license
                        $license_data['key']          = '';
                        $license_data['last_check']   = time();
                        
                        update_site_option('mae_license', $license_data);
                    } else {
                        $slt_form_submit_messages[] = __('There was a problem deactivating the licence: ', 'magnific-addons') . $response_block->message;
                        return;
                    }   
                }
            } else {
                $slt_form_submit_messages[] = __('There was a problem with the data block received from ' . MAE_APP_API_URL, 'magnific-addons');
                return;
            }
                
            //redirect
            $current_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            wp_redirect($current_url);
            die();
            
        }   
        
        
        
        if (isset($_POST['mae_license_form_submit']) && wp_verify_nonce($_POST['mae_license_nonce'],'mae_license')) {
                
            $license_key = isset($_POST['license_key'])? sanitize_key(trim($_POST['license_key'])) : '';

            if($license_key == '') {
                $slt_form_submit_messages[] = __("Licence Key can't be empty", 'magnific-addons');
                return;
            }
                
            //build the request query
            $args = array(
                'woo_sl_action' => 'activate',
                'licence_key' => $license_key,
                'product_unique_id' => MAE_PRODUCT_ID,
                'domain' => MAE_INSTANCE
            );
            $request_uri    = MAE_APP_API_URL . '?' . http_build_query( $args , '', '&');
            $data           = wp_remote_get( $request_uri );
            

            if(is_wp_error( $data ) || $data['response']['code'] != 200) {
                $slt_form_submit_messages[] .= __('There was a problem connecting to ', 'magnific-addons') . MAE_APP_API_URL;
                return;  
            }
                
            $response_block = json_decode($data['body']);
            //retrieve the last message within the $response_block
            $response_block = $response_block[count($response_block) - 1];
            $response = $response_block->message;
            
            if(isset($response_block->status)) {
                if($response_block->status == 'success' && ($response_block->status_code == 's100' || $response_block->status_code == 's101')) {
                    //the license is active and the software is active
                    $slt_form_submit_messages[] = $response_block->message;
                    
                    $license_data = get_site_option('mae_license');
                    
                    //save the license
                    $license_data['key']          = $license_key;
                    $license_data['last_check']   = time();
                    
                    update_site_option('mae_license', $license_data);

                } else {
                    $slt_form_submit_messages[] = __('There was a problem activating the licence: ', 'magnific-addons') . $response_block->message;
                    return;
                }   
            } else {
                $slt_form_submit_messages[] = __('There was a problem with the data block received from ' . MAE_APP_API_URL, 'magnific-addons');
                return;
            }
                
            //redirect
            $current_url    =   'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            wp_redirect($current_url);
            die();
        }   
        
    }
        
    function licence_form() {
        
        ?><div class="wrap"> 

            <h2><?php echo __('Magnific Addons for Elementor License', 'magnific-addons'); ?></h2>
			<?php settings_errors(); ?> 
            
            <form id="form_data" name="form" method="post">
                <div class="postbox">
                    
                    <?php wp_nonce_field('mae_license','mae_license_nonce'); ?>
                    <input type="hidden" name="mae_license_form_submit" value="true" />
                        
                    <div class="section section-text ">
                        <h4 class="heading"><?php _e( "License Key", 'magnific-addons' ) ?></h4>
                        <div class="option">
                            <div class="controls">
                                <input type="text" value="" name="license_key" class="text-input">
                            </div>
                            <div class="explain"><?php _e( "Enter the License Key you got when bought this product. If you lost the key, you can always retrieve it from", 'magnific-addons' ) ?> <a href="https://plugins.magnificsoft.com/my-account/" target="_blank"><?php _e( "My Account", 'magnific-addons' ) ?></a><br />
                            </div>
                        </div> 
                    </div>

                </div>
                
                <p class="submit">
                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save', 'magnific-addons') ?>">
                </p>

            </form> 

        </div> 

        <?php  

    }
    
    function licence_deactivate_form() {

            $license_data = get_site_option('mae_license');
            
            ?><div class="wrap"> 

                <h2><?php echo __('Magnific Addons for Elementor License', 'magnific-addons'); ?></h2>
                <?php settings_errors(); ?> 

                <div id="form_data">
                    <div class="postbox">
                        <form id="form_data" name="form" method="post">    
                            <?php wp_nonce_field('mae_license','mae_license_nonce'); ?>
                            <input type="hidden" name="mae_license_form_submit" value="true" />
                            <input type="hidden" name="mae_license_deactivate" value="true" />

                                <div class="section section-text ">
                                <h4 class="heading"><?php _e( "License Key", 'magnific-addons' ) ?></h4>
                                <div class="option">
                                    <div class="controls">
                                        <?php  
                                            if($this->licence->is_local_instance())
                                            {
                                            ?>
                                            <p>Local instance, no key applied.</p>
                                            <?php   
                                            }
                                            else {
                                            ?>
                                        <p><b><?php echo substr($license_data['key'], 0, 20) ?>-xxxxxxxx-xxxxxxxx</b> &nbsp;&nbsp;&nbsp;<a class="button-secondary" title="Deactivate" href="javascript: void(0)" onclick="jQuery(this).closest('form').submit();">Deactivate</a></p>
                                        <?php } ?>
                                    </div>
                                    <div class="explain"><?php _e( "You can generate more keys from", 'magnific-addons' ) ?> <a href="https://plugins.magnificsoft.com/my-account/" target="_blank">My Account</a> 
                                    </div>
                                </div> 
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        <?php  

        }


        
}

                                   

?>
<?php 

/* ----------------------------------------------------------------------------- */
/* Add Menu Page */
/* ----------------------------------------------------------------------------- */ 



define('MAE_APP_API_URL', 'https://plugins.magnificsoft.com/index.php');
define('MAE_PRODUCT_ID', 'MAE');
define('MAE_INSTANCE', str_replace(array ("https://" , "http://"), "", network_site_url()));

include_once(MAE_PATH . 'admin/class.wooslt.php');
// include_once(MAE_PATH . 'admin/class.licence.php');
include_once(MAE_PATH . 'admin/class.options.php');
include_once(MAE_PATH . 'admin/class.updater.php');


// function MAE_activated() 
// 	 {

// 	 }

// function MAE_deactivated() 
// 	 {

// 	 }

global $MAE;
$MAE = new MAE();


add_filter('plugin_action_links_' . MAE_BASENAME , function ( $links ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=mae-options' ) . '">' . __('Settings') . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
});


// add `check for updates` or `Activate license` links to plugin meta
add_filter( 'plugin_row_meta', function ($links, $file) {
	if ( $file === 'magnific-addons-for-elementor/magnific-addons-for-elementor.php' ) {
		// add `view details` link
		if (!isset(get_plugin_updates()[$file])) {
			$links[] = '<a class="thickbox open-plugin-details-modal" href="' . add_query_arg( 'tab', 'plugin-information&plugin=magnific-addons-for-elementor&TB_iframe=true&width=772&height=970', admin_url('plugin-install.php')) . '" title="' . __('View details', 'magnific-addons') . '" data-title="Magnific Addons for Elementor">' . __('View details', 'magnific-addons') . '</a>';
		}
		// add `check for updates` / `buy license`
		// if (isset(get_site_option('mae_license')['key']) && get_site_option('mae_license')['key'] != '') {
		// 	$links[] = '<a href="' . add_query_arg( 'action', 'mae_check_updates', admin_url('plugins.php')) . '" title="' . __('Check for updates', 'magnific-addons') . '">' . __('Check for updates', 'magnific-addons') . '</a>';
		// } else {
		// 	$links[] = '<a href="' . admin_url("admin.php?page=mae-license") . '" title="' . __('Enter license', 'magnific-addons') . '">' . __('Activate license', 'magnific-addons') . '</a> to receive plugin updates';
		// }
	}
	return $links;
}, 10, 2 );


// NO LICENSE UPDATE NOTICE
$license_data = get_site_option('mae_license');
if (!isset($license_data['key'])) {
	add_action( 'in_plugin_update_message-magnific-addons-for-elementor/magnific-addons-for-elementor.php', function ( $plugin_data, $r ) {
		echo '<br />' . sprintf( __('To enable updates, please enter your license key on the <a href="%s">License</a> page. If you don\'t have a licence key, please <a href="%s">buy license</a>.', 'magnific-addons'), admin_url('admin.php?page=mae-license'), 'https://plugins.magnificsoft.com/magnific-addons-for-elementor/' );
	}  , 10, 2 );
}



// FORCE CHECK FOR PLUGINS UPDATES
add_action( 'admin_init', function() {
	if( !isset( $_GET['action'] ) || 'mae_check_updates' != $_GET['action'] ) {
		return;
	}
	if( !current_user_can( 'install_plugins' ) ) {
		return;
	}
	set_site_transient( 'update_plugins', null );
	wp_safe_redirect( network_admin_url( 'plugins.php' ) ); 
	exit;
});


function mae_add_plugin_menu() {
	add_menu_page (
		'Magnific Addons',	// page title 
		'Magnific Addons', // menu title
		'manage_options',	// capability
		'mae-options',	// menu-slug
		'mae_options',	// function that will render its output
		'dashicons-star-filled', // link to the icon that will be displayed in the sidebar
		99 // position of the menu option
	);
	add_submenu_page( 
		'mae-options', 
		'MAE Settings', 
		'Settings', 
		'manage_options', 
		'mae-options', 
		'mae_options'
	); 

}
add_action('admin_menu', 'mae_add_plugin_menu');

function mae_options() {
		?>
		<div class="wrap">
			<h2><?php echo __('Magnific Addons for Elementor Settings', 'magnific-addons'); ?></h2>
			<?php settings_errors(); ?> 


			<form method="post" action="options.php"> 
			<?php
				settings_fields( 'mae-main-settings-group' );
				do_settings_sections( 'mae-options-main' );
				submit_button(); 
			?> 
			</form> 

		</div>
		<?php
}


// MAIN PLUGIN SETTINGS
function mae_initialize_main_options() {  
	register_setting(  
		'mae-main-settings-group',  
		'mae_settings'  
	);
	add_settings_section(  
		'main_section',
		'Widgets Settings',
		'mae_main_section_callback',
		'mae-options-main'
	);

	add_settings_field (   
		'enabled_widgets',	   
		'Widgets',	   
		'mae_enabled_widgets_callback',	   
		'mae-options-main',    
		'main_section'    
	);


	add_settings_field (   
		'php_safe_mode',	   
		'Safe Mode',	   
		'mae_php_safe_mode_callback',	   
		'mae-options-main',    
		'main_section'    
	);

	add_settings_field (   
		'enabled_popups',	   
		'Popups',	   
		'mae_enabled_popups_callback',	   
		'mae-options-main',    
		'main_section'    
	);

	add_settings_field (   
		'custom_html',	   
		'Custom HTML',	   
		'mae_custom_html_callback',	   
		'mae-options-main',    
		'main_section'    
	);


	add_settings_field (   
		'section_fix',	   
		'Section Columns Wrap',	   
		'mae_section_fix_callback',	   
		'mae-options-main',    
		'main_section'    
	);

	add_settings_field (   
		'custom_column_width',	   
		'Custom Column Width',	   
		'mae_custom_column_width_callback',	   
		'mae-options-main',    
		'main_section'    
	);

	add_settings_field (   
		'flex_order',	   
		'Flex Order',	   
		'mae_flex_order_callback',	   
		'mae-options-main',    
		'main_section'    
	);

	add_settings_field (   
		'responsive_custom_css',	   
		'Responsive Custom CSS',	   
		'mae_responsive_custom_css_callback',	   
		'mae-options-main',    
		'main_section'    
	);
	add_settings_field (   
		'conditional_visibility',	   
		'Сonditional Visibility',	   
		'mae_conditional_visibility_callback',	   
		'mae-options-main',    
		'main_section'    
	);


	// DEFAULTS
	if ( get_option( 'mae_settings' ) === false ) {
		$defaults = array(
			'enabled_widgets' => array(
				'enable_raw_php_code_widget'   => '1',
				'enable_current_year_widget'   => '1',
			),

		);
		//$settings = wp_parse_args( get_option( 'mae_settings', $defaults), $defaults );
		update_option('mae_settings', $defaults);
	}


		// DEFAULTS
		$options = get_option( 'mae_settings' );
		if(!$options) {
			$options = array();
		}
		$updated = false;

		if (!isset($options['enabled_widgets']['enable_raw_php_code_widget'])) {
			$options['enabled_widgets']['enable_raw_php_code_widget'] = '1';
			$updated = true;
		}
		
		if (!isset($options['enabled_widgets']['enable_current_year_widget'])) {
			$options['enabled_widgets']['enable_current_year_widget'] = '1';
			$updated = true;
		}
		

		if (!isset($options['php_safe_mode']['enable_php_safe_mode'])) {
			$options['php_safe_mode']['enable_php_safe_mode'] = '0';
			$updated = true;
		}	


		if (!isset($options['enabled_popups']['enable_code_popup'])) {
			$options['enabled_popups']['enable_code_popup'] = '1';
			$updated = true;
		}
		if (!isset($options['enabled_popups']['enable_wysiwyg_popup'])) {
			$options['enabled_popups']['enable_wysiwyg_popup'] = '1';
			$updated = true;
		}
		if (!isset($options['enabled_popups']['enable_textarea_popup'])) {
			$options['enabled_popups']['enable_textarea_popup'] = '1';
			$updated = true;
		}


		if (!isset($options['custom_html'])) {
			$options['custom_html'] = '1';
			$updated = true;
		}
		if (!isset($options['section_fix'])) {
			$options['section_fix'] = '1';
			$updated = true;
		}
		if (!isset($options['custom_column_width'])) {
			$options['custom_column_width'] = '1';
			$updated = true;
		}
		if (!isset($options['flex_order'])) {
			$options['flex_order'] = '1';
			$updated = true;
		}
		if (!isset($options['responsive_custom_css'])) {
			$options['responsive_custom_css'] = '1';
			$updated = true;
		}
		if (!isset($options['conditional_visibility'])) {
			$options['conditional_visibility'] = '1';
			$updated = true;
		}

	
		// update only if something changes
		if($updated) {
			update_option('mae_settings', $options);
		}


}
add_action('admin_init', 'mae_initialize_main_options');

function mae_main_section_callback() {  
	//echo '<p>Here you can enable or disable widgets</p>';  
}

function mae_enabled_widgets_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[enabled_widgets][enable_raw_php_code_widget]" value="0" />
			<input type="checkbox" name="mae_settings[enabled_widgets][enable_raw_php_code_widget]" value="1"<?php checked(get_option('mae_settings')['enabled_widgets']['enable_raw_php_code_widget']); ?> />
			<?php echo __( 'Raw PHP Code', 'magnific-addons' ); ?>
		</label>
		<br>
		<label style="display: block">
			<input type="hidden" name="mae_settings[enabled_widgets][enable_current_year_widget]" value="0" />
			<input type="checkbox" name="mae_settings[enabled_widgets][enable_current_year_widget]" value="1"<?php checked(get_option('mae_settings')['enabled_widgets']['enable_current_year_widget']); ?> />
			<?php echo __( 'Current Year', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}

function mae_php_safe_mode_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[php_safe_mode][enable_php_safe_mode]" value="0" />
			<input type="checkbox" name="mae_settings[php_safe_mode][enable_php_safe_mode]" value="1"<?php checked(get_option('mae_settings')['php_safe_mode']['enable_php_safe_mode'], '1'); ?> />
			<?php echo __( 'Enable Safe Mode', 'magnific-addons' ); ?>
		</label>
		<br>
		
	<?php
}


function mae_enabled_popups_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[enabled_popups][enable_code_popup]" value="0" />
			<input type="checkbox" name="mae_settings[enabled_popups][enable_code_popup]" value="1"<?php checked(get_option('mae_settings')['enabled_popups']['enable_code_popup'], '1'); ?> />
			<?php echo __( 'Popup for code control', 'magnific-addons' ); ?>
		</label>
		<br>

		<label style="display: block">
			<input type="hidden" name="mae_settings[enabled_popups][enable_wysiwyg_popup]" value="0" />
			<input type="checkbox" name="mae_settings[enabled_popups][enable_wysiwyg_popup]" value="1"<?php checked(get_option('mae_settings')['enabled_popups']['enable_wysiwyg_popup']); ?> />
			<?php echo __( 'Popup for WYSIWYG control', 'magnific-addons' ); ?>
		</label>
		<br>

		<label style="display: block">
			<input type="hidden" name="mae_settings[enabled_popups][enable_textarea_popup]" value="0" />
			<input type="checkbox" name="mae_settings[enabled_popups][enable_textarea_popup]" value="1"<?php checked(get_option('mae_settings')['enabled_popups']['enable_textarea_popup']); ?> />
			<?php echo __( 'Popup for textarea control', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}






function mae_custom_html_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[custom_html]]" value="0" />
			<input type="checkbox" name="mae_settings[custom_html]]" value="1"<?php checked(get_option('mae_settings')['custom_html'], '1'); ?> />
			<?php echo __( 'Custom HTML', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}

function mae_section_fix_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[section_fix]" value="0" />
			<input type="checkbox" name="mae_settings[section_fix]" value="1"<?php checked(get_option('mae_settings')['section_fix'], '1'); ?> />
			<?php echo __( 'Section Columns Wrap', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}

function mae_custom_column_width_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[custom_column_width]" value="0" />
			<input type="checkbox" name="mae_settings[custom_column_width]" value="1"<?php checked(get_option('mae_settings')['custom_column_width'], '1'); ?> />
			<?php echo __( 'Custom Column Width', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}
function mae_flex_order_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[flex_order]" value="0" />
			<input type="checkbox" name="mae_settings[flex_order]" value="1"<?php checked(get_option('mae_settings')['flex_order'], '1'); ?> />
			<?php echo __( 'Flex Order', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}
function mae_wrapper_link_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[wrapper_link]" value="0" />
			<input type="checkbox" name="mae_settings[wrapper_link]" value="1"<?php checked(get_option('mae_settings')['wrapper_link'], '1'); ?> />
			<?php echo __( 'Wrapper Link', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}
function mae_responsive_custom_css_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[responsive_custom_css]" value="0" />
			<input type="checkbox" name="mae_settings[responsive_custom_css]" value="1"<?php checked(get_option('mae_settings')['responsive_custom_css'], '1'); ?> />
			<?php echo __( 'Responsive Custom CSS', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}
function mae_conditional_visibility_callback($args) {  
	?>
		<label style="display: block">
			<input type="hidden" name="mae_settings[conditional_visibility]" value="0" />
			<input type="checkbox" name="mae_settings[conditional_visibility]" value="1"<?php checked(get_option('mae_settings')['conditional_visibility'], '1'); ?> />
			<?php echo __( 'Сonditional Visibility', 'magnific-addons' ); ?>
		</label>
		<br>
	<?php
}















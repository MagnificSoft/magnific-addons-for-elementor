<?php
/**
* Plugin Name: Magnific Addons for Elementor
* Description: This plugin is addon for Elementor Page Builder plugin with range of great features and widgets
* Plugin URI: https://magnificsoft.com/
* Version: 1.4.1
* Author: MagnificSoft
* Author URI: https://magnificsoft.com/
* License: GPL 3.0
* Text Domain: magnific-addons
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! function_exists('get_plugin_data') ){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
define('MAE_VERSION', get_plugin_data( __FILE__ )['Version']);
define('MAE_PATH', plugin_dir_path(__FILE__));
define('MAE_URL', plugins_url('', __FILE__));
define('MAE_BASENAME', plugin_basename(__FILE__));

if (is_admin()) {
	include( plugin_dir_path( __FILE__ ) . 'admin/admin.php');
}

final class Magnific_Addons {

	const MINIMUM_ELEMENTOR_VERSION = '3.6.0';

	const MINIMUM_PHP_VERSION = '5.6';

	public function __construct() {

		// Load translation
		add_action( 'init', array( $this, 'i18n' ) );

		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function i18n() {
		load_plugin_textdomain( 'magnific-addons', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
	}

	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'plugin.php' );
		//include( plugin_dir_path( __FILE__ ) . 'widgets.php');

		// register web devs category
		add_action( 'elementor/elements/categories_registered', function($elements_manager) {
			$elements_manager->add_category(
				'mae-widgets',
				[
					'title' => __( 'Magnific Widgets', 'magnific-addons' ),
					'icon' => 'fa fa-plug',
				]
			);
		});

	}

	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'magnific-addons' ),
			'<strong>' . esc_html__( 'Magnific Addons for Elementor', 'magnific-addons' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'magnific-addons' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'magnific-addons' ),
			'<strong>' . esc_html__( 'Magnific Addons for Elementor', 'magnific-addons' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'magnific-addons' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'magnific-addons' ),
			'<strong>' . esc_html__( 'Magnific Addons for Elementor', 'magnific-addons' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'magnific-addons' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
}

// Instantiate Magnific_Addons.
new Magnific_Addons();











register_activation_hook(__FILE__, 'mae_activation');
function mae_activation() {
	if(is_plugin_active( 'advanced-elementor-editor/advanced-elementor-editor.php')) {
		deactivate_plugins('advanced-elementor-editor/advanced-elementor-editor.php');
		set_transient( 'advanced-elementor-editor-deactivated', true, 5 );
	} 
	if(is_plugin_active( 'advanced-elementor-widgets/advanced-elementor-widgets.php')) {
		deactivate_plugins('advanced-elementor-widgets/advanced-elementor-widgets.php');
		set_transient( 'advanced-elementor-widgets-deactivated', true, 5 );
	}
	if(is_plugin_active( 'advanced-elementor-addons/advanced-elementor-addons.php')) {
		error_log('active');
		deactivate_plugins('advanced-elementor-addons/advanced-elementor-addons.php');
		set_transient( 'advanced-elementor-addons-deactivated', true, 5 );
	}

	if( ! get_option('mae_db_updated') ) {
		$update1 = mae_search_replace_db('aew_','mae_');
		$update2 = mae_search_replace_db('aee_','mae_');
		if ($update1 && $update2) {
			mae_clear_elementor_css_cache();
		}
		update_option( 'mae_db_updated', '1' );
	}
}

add_action( 'admin_notices', 'mae_activation_check_activation_notice' );
function mae_activation_check_activation_notice(){
	if(get_transient('advanced-elementor-editor-deactivated')){
		?>
		<div class="error notice is-dismissible">
			<p>Advanced Elementor Editor plugin deactivated. Please remove Advanced Elementor Editor plugin.</p>
		</div>
		<?php
		delete_transient('advanced-elementor-editor-deactivated');
	}
	if(get_transient('advanced-elementor-widgets-deactivated')){
		?>
		<div class="error notice is-dismissible">
		<p>Advanced Elementor Widgets plugin deactivated. Please remove Advanced Elementor Widgets plugin.</p>
		</div>
		<?php
		delete_transient('advanced-elementor-widgets-deactivated');
	}
	if(get_transient('advanced-elementor-addons-deactivated')){
		?>
		<div class="error notice is-dismissible">
		<p>Advanced Elementor Addons plugin deactivated. Please remove Advanced Elementor Widgets plugin.</p>
		</div>
		<?php
		delete_transient('advanced-elementor-addons-deactivated');
	}
}


add_action("after_plugin_row_advanced-elementor-editor/advanced-elementor-editor.php", function( $plugin_file, $plugin_data, $status ) {
	echo '<tr class="plugin-update-tr">
		<td class="plugin-update colspanchange" colspan="4">
			<div class="inline error notice"><p>Please remove Advanced Elementor Editor plugin.</p></div>
		</td>
	</tr>';
}, 10, 3 );
add_action("after_plugin_row_advanced-elementor-widgets/advanced-elementor-widgets.php", function( $plugin_file, $plugin_data, $status ) {
	echo '<tr class="plugin-update-tr">
		<td class="plugin-update colspanchange" colspan="4">
			<div class="inline error notice"><p>Please remove Advanced Elementor Widgets plugin.</p></div>
		</td>
	</tr>';
}, 10, 3 );

add_action("after_plugin_row_advanced-elementor-addons/advanced-elementor-addons.php", function( $plugin_file, $plugin_data, $status ) {
	echo '<tr class="plugin-update-tr">
		<td class="plugin-update colspanchange" colspan="4">
			<div class="inline error notice"><p>Please remove Advanced Elementor Addons plugin.</p></div>
		</td>
	</tr>';
}, 10, 3 );


function mae_search_replace_db($from, $to) {
   global $wpdb;
   $rows_affected = $wpdb->query(
      "UPDATE {$wpdb->postmeta} " .
      "SET `meta_value` = REPLACE(`meta_value`, '" . str_replace( '/', '\\\/', $from ) . "', '" . str_replace( '/', '\\\/', $to ) . "') " .
      "WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE '[%' ;" ); // meta_value LIKE '[%' are json formatted
   return $rows_affected;
}
function mae_clear_elementor_css_cache() {
	error_log('run css clear cache');
}

// Checking for updates
require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/MagnificSoft/magnific-addons-for-elementor',
		__FILE__,
		'MagnificAddons'
	);


	$myUpdateChecker->setBranch('main');
<?php
namespace MagnificAddons;

use \Elementor\Core\Settings\Manager as SettingsManager;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;

use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\ThemeBuilder\Documents\Loop;
use ElementorPro\Plugin;
use ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document;
use Elementor\Core\Documents_Manager;
use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;



// RAW PHP CODE PROTECTION 
add_action('init', function() {
	if( isset( $_GET['mae_safe']) || (isset(get_option('mae_settings')['php_safe_mode']['enable_php_safe_mode']) && get_option('mae_settings')['php_safe_mode']['enable_php_safe_mode'] == '1')) {
		define('MAE_PHP_SAFE_MODE', true);
	}
});




// RAW PHP CODE WIDGET
if (isset(get_option('mae_settings')['enabled_widgets']['enable_raw_php_code_widget']) && get_option('mae_settings')['enabled_widgets']['enable_raw_php_code_widget'] == '1') {
	new Aew_Raw_Php_Code();
}
class Aew_Raw_Php_Code {
	public function widget_scripts() {
		wp_register_script('mae_widgets-script', plugins_url( '/assets/js/mae_widgets-script.js', __FILE__ ), [ 'jquery', 'elementor-frontend' ], MAE_VERSION, true);
		wp_register_style('mae_widgets-styles', plugins_url( '/assets/css/mae_widgets-styles.css', __FILE__ ));
		wp_enqueue_style('mae_widgets-styles');
	}
	private function include_widgets_files() {
		require_once( MAE_PATH . '/widgets/raw_php_code.php' );
	}
	public function register_widgets() {
		$this->include_widgets_files();
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Aew_Raw_Php_Code_Widget() );
	}
	public function __construct() {
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
	}
}
// CURRENT YEAR WIDGET
if (isset(get_option('mae_settings')['enabled_widgets']['enable_current_year_widget']) && get_option('mae_settings')['enabled_widgets']['enable_current_year_widget'] == '1') {
	new Current_Year();
}
class Current_Year {
	public function widget_scripts() {
		wp_register_script('mae_widgets-script', plugins_url( '/assets/js/mae_widgets-script.js', __FILE__ ), [ 'jquery', 'elementor-frontend' ], MAE_VERSION, true);
	}
	private function include_widgets_files() {
		require_once( MAE_PATH . '/widgets/current_year.php' );
	}
	public function register_widgets() {
		$this->include_widgets_files();
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Current_Year_Widget() );
	}
	public function __construct() {
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
	}
}

class Aew_Raw_Php_Code_Skin {
	public function widget_scripts() {
		// wp_register_script('mae_widgets-script', plugins_url( '/assets/js/mae_widgets-script.js', __FILE__ ), [ 'jquery', 'elementor-frontend' ], MAE_VERSION, true);
		// wp_register_style('mae_widgets-styles', plugins_url( '/assets/css/mae_widgets-styles.css', __FILE__ ));
		// wp_enqueue_style('mae_widgets-styles');
	}
	private function include_widgets_files() {
		require_once( MAE_PATH . '/widgets/raw_php_code_skin.php' );
	}
	public function register_widgets() {
		$this->include_widgets_files();
	}
	public function __construct() {
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
	}
}




// enqueue editor js
add_action( 'elementor/editor/after_enqueue_scripts', function() {
	wp_enqueue_script( 'mae-editor', plugin_dir_url( __FILE__ ) . '/assets/js/magnific-addons.js' );

	$plugin_settings = array(
		'mae_plugin_url' => plugin_dir_url( __FILE__ ),
	);

	if (isset(get_option('mae_settings')['enabled_popups']['enable_wysiwyg_popup']) && get_option('mae_settings')['enabled_popups']['enable_wysiwyg_popup'] == '1') {
		$plugin_settings['mae_text_popup_enabled'] = '1';
	}
	if (isset(get_option('mae_settings')['enabled_popups']['enable_code_popup']) && get_option('mae_settings')['enabled_popups']['enable_code_popup'] == '1') {
		$plugin_settings['mae_code_popup_enabled'] = '1';
	}
	if (isset(get_option('mae_settings')['enabled_popups']['enable_textarea_popup']) && get_option('mae_settings')['enabled_popups']['enable_textarea_popup'] == '1') {
		$plugin_settings['mae_textarea_popup_enabled'] = '1';
	}

	wp_localize_script('mae-editor', 'MagnificAddons', $plugin_settings);
});

// enqueue frontend js
add_action( 'elementor/frontend/after_enqueue_scripts', function() {
	wp_enqueue_script( 'mae-editor-frontend', plugin_dir_url( __FILE__ ) . '/assets/js/magnific-addons-frontend.js', [ 'jquery' ], MAE_VERSION );
	// wp_localize_script('frontend', 'MagnificAddonsFrontend', array(
	// 	'mae_plugin_url' => plugin_dir_url( __FILE__ ),
  	// ));
});
add_action( 'elementor/preview/enqueue_scripts', function() {
	wp_enqueue_style('magnific-addons-preview-preview', plugin_dir_url( __FILE__ ) . '/assets/css/magnific-addons-preview.css', [], MAE_VERSION);
});



// enqueue css
add_action( 'elementor/editor/after_enqueue_styles', function() {

	$ui_theme = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );
	if ( 'light' !== $ui_theme ) {
		$ui_theme_media_queries = 'all';
		if ( 'auto' === $ui_theme ) {
			$ui_theme_media_queries = '(prefers-color-scheme: dark)';
		}
		wp_enqueue_style(
			'-dark-mode',
			plugin_dir_url( __FILE__ ) . 'assets/css/magnific-addons-dark-mode.css',
			'',
			'',
			$ui_theme_media_queries
		);
	}
	
	wp_enqueue_style('magnific-addons', plugin_dir_url( __FILE__ ) . '/assets/css/magnific-addons.css', [], MAE_VERSION);
} );




/*
*
* WIDGETS STATISTIC
*
*/
// Add action to elementor/element/post/document_settings/after_section_end' hook
add_action( 'elementor/element/wp-page/section_custom_css/after_section_end', 'MagnificAddons\page_sections_controls',10, 2);
add_action( 'elementor/element/wp-post/section_custom_css/after_section_end', 'MagnificAddons\page_sections_controls',10, 2);

function page_sections_controls( $page, $args ) {
	// Header Options
	$page->start_controls_section(
		'widgets_statistic_section',
		[
			'label' => __( 'Widgets Statistic', 'molotov-form' ),
			'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
		]
	);

	$form_description = '<p>Hello></p>';

	$page->add_control(
		'widgets_statistic_section_description',
		[
			'raw' => __( $form_description, 'molotov-form' ),
			'type' => \Elementor\Controls_Manager::RAW_HTML,
		]
	);

	$page->end_controls_section();
    
}




// custom html
if (isset(get_option('mae_settings')['custom_html']) && get_option('mae_settings')['custom_html'] == '1') {
	require_once( __DIR__ . '/inc/custom-html/controls.php' );
	Aee_Custom_HTML::instance();
	
	require_once( __DIR__ . '/inc/custom-html/render.php' );
	add_action( 'elementor/widget/render_content', 'MagnificAddons\mae_render_custom_html', 10, 3 );
	add_action('elementor/widget/print_template',  'MagnificAddons\mae_render_js_custom_html', 10, 3 );
}

// section fix
if (isset(get_option('mae_settings')['section_fix']) && get_option('mae_settings')['section_fix'] == '1') {
	require_once( __DIR__ . '/inc/section-fix.php' );
	Aee_Columns_Alignment_Fix_For_Elementor::instance();
}


// column custom width
if (isset(get_option('mae_settings')['custom_column_width']) && get_option('mae_settings')['custom_column_width'] == '1') {
	require_once( __DIR__ . '/inc/column-cusom-width.php' );
	Aee_Columns_Custom_Width_For_Elementor::instance();
}


// flex order
if (isset(get_option('mae_settings')['flex_order']) && get_option('mae_settings')['flex_order'] == '1') {
	require_once( __DIR__ . '/inc/flex-order.php' );
	Aee_Flex_Order::instance();
}


// responsive custom css
if (isset(get_option('mae_settings')['responsive_custom_css']) && get_option('mae_settings')['responsive_custom_css'] == '1') {
	require_once( __DIR__ . '/inc/responsive-custom-css.php' );
	Aee_Responsive_Custom_CSS::instance();
}

























// element conditional visibility
if (isset(get_option('mae_settings')['conditional_visibility']) && get_option('mae_settings')['conditional_visibility'] == '1') {
	require_once( __DIR__ . '/inc/conditional-visibility/controls.php' );
	new Aee_Сonditional_Visibility();

	// logic
	require_once( __DIR__ . '/inc/conditional-visibility/compare.php' );
	add_filter( 'elementor/frontend/section/should_render', 'MagnificAddons\mae_conditional_visibility_is_display', 10, 3);
   add_filter( 'elementor/frontend/column/should_render', 'MagnificAddons\mae_conditional_visibility_is_display', 10, 3);
   add_filter( 'elementor/frontend/widget/should_render', 'MagnificAddons\mae_conditional_visibility_is_display', 10, 3);

}













// allow to edit _elementor_data custom field
add_filter('elementor/template_library/sources/local/register_post_type_args', function($args) {
	array_push($args['supports'], 'custom-fields');
	return $args;
});
add_filter( 'is_protected_meta', function( $protected, $meta_key, $meta_type ) {
    $allowed = array( '_elementor_data' );
    if( in_array( $meta_key, $allowed ) )
        return false;
    return $protected;
}, 10, 3 );





// TO DO add settings
// ICONS-LIST TEXTAREA
add_action( 'elementor/element/icon-list/section_icon/before_section_end', function( $widget, $args ) {
	
	$control_data = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'icon_list' );
	if ( is_wp_error( $control_data ) ) {
		return;
	}

	$control_data['fields']['text']['type'] = \Elementor\Controls_Manager::TEXTAREA;

	$widget->update_control( 'icon_list', $control_data );

},10 ,2 );






// TO DO add settings
add_action( 'elementor/element/posts/section_query/before_section_end', function( $widget, $args ) {

	$widget->start_injection( [
		'at' => 'after',
		'of' => 'posts_ignore_sticky_posts',
	] );
	$widget->add_control(
		'only_sticky_posts',
		[
			'label' => __( 'MAE Only Sticky Posts', 'magnific-addons' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label_on' => __( 'On', 'magnific-addons' ),
			'label_off' => __( 'Off', 'magnific-addons' ),
			'return_value' => 'yes',
			'default' => '',
			'description' => __( 'Sticky-posts ordering is visible on frontend only.', 'magnific-addons' ),
		]
	);
	$widget->end_injection();

	$widget->add_control(
		'enable_custom_query',
		[
			'label' => __( 'MAE Use Custom Query', 'magnific-addons' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label_on' => __( 'On', 'magnific-addons' ),
			'label_off' => __( 'Off', 'magnific-addons' ),
			'return_value' => 'yes',
			'default' => '',
			'description' => __( 'This will override all previous query settings.', 'magnific-addons' ),
			'separator' => 'before',
		]
	);
	$widget->add_control(
		'custom_query_params',
		[
			'label' => __( 'MAE Custom Query', 'magnific-addons' ),
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'rows' => 8,
			'description' => __( 'Insert here WP_Query args in JSON format, you can use this <a href="https://crocoblock.com/freemium/tools/wp-query-generator/" target="blank">generator</a>', 'magnific-addons' ),
			'condition' => [
				'enable_custom_query' => 'yes'
			],
		]
	);

}, 10 , 2 );

add_filter( 'elementor/query/query_args', function( $args, $widget ) {

	// only sticky posts
	if ($widget->get_settings('only_sticky_posts') == 'yes') {
		$args['post__in'] = get_option('sticky_posts');
	}

	// custom query params
	$custom_query_params = json_decode($widget->get_settings('custom_query_params'));
	if ($widget->get_settings('enable_custom_query') == 'yes' && $custom_query_params && is_object($custom_query_params)) {
		$args = $custom_query_params;
	}

	return $args;

}, 10, 2);





























add_action( 'elementor/theme/register_conditions', function( $conditions_manager ) {
	class MAE_Mobile extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base {
		public static function get_type() {
			return 'singular';
		}
		public static function get_priority() {
			return 100;
		}
		public function get_name() {
			return 'mobile';
		}
		public function get_label() {
			return __( 'Mobile', 'elementor-pro' );
		}
		public function check( $args ) {
			return wp_is_mobile();
		}
	}
	$conditions_manager->get_condition('singular')->register_sub_condition( new MAE_Mobile() );
 },100);

 add_action( 'elementor/theme/register_conditions', function( $conditions_manager ) {
	class MAE_Desktop extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base {
		public static function get_type() {
			return 'singular';
		}
		public static function get_priority() {
			return 20;
		}
		public function get_name() {
			return 'desktop';
		}
		public function get_label() {
			return __( 'Desktop', 'elementor-pro' );
		}
		public function check( $args ) {
			return !wp_is_mobile();
		}
	}
	$conditions_manager->get_condition('singular')->register_sub_condition( new MAE_Desktop() );
 },100);



add_action( 'elementor/theme/register_conditions', function( $conditions_manager ) {
	class MAE_Device_Type  extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base {
		protected $sub_conditions = [];
		public static function get_type() {
			return 'device_type';
		}
		public function get_name() {
			return 'device_type';
		}
		public static function get_priority() {
			return 60;
		}
		public function get_label() {
			return __( 'Device Type', 'ele-custom-skin' );
		}
		public function get_all_label() {
			return __( 'All', 'ele-custom-skin' );
		}
		public function register_sub_conditions() {
			$this->sub_conditions[] = 'desktop';
			$this->sub_conditions[] = 'mobile';
		}
		public function check( $args ) {
			return true;
		}
	}
  	$conditions_manager->get_condition('general')->register_sub_condition( new MAE_Device_Type() );
},100);
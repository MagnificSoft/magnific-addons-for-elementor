<?php
namespace MagnificAddons;


function mae_render_custom_html( $content, $widget ) {

   $settings = $widget->get_settings();
   
	if ($settings['custom_html_render'] === 'yes') {

      $content = '<div class="mae-custom-html">' . $settings['custom_html_code'] . '</div>' . $content;

   }
   
	return $content;
}

// RENDER WIDGET JS TEMPLATE
function mae_render_js_custom_html($template, $widget) {
	if (!$template) return; // if widget dont have js render template
	ob_start();
	?>
	<# if ( settings.custom_html_render === 'yes' ) { #>
		<div class="mae-custom-html">{{{ settings.custom_html_code }}}</div>
	<# } #>
	<?php
	echo $template;
	$template = ob_get_clean();
  return $template;
}
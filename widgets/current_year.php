<?php
namespace MagnificAddons;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;


if (!defined("ABSPATH")) {
    exit();
} // Exit if accessed directly

class Current_Year_Widget extends Widget_Base
{
    public function get_name()
    {
        return "mae_current_year";
    }

    public function get_title()
    {
        return __("Current Year", "magnific-addons");
    }

    public function get_icon()
    {
        return "eicon-clock";
    }

    public function get_categories()
    {
        return ["mae-widgets"];
    }

    // public function get_style_depends() {
    // 	return [ 'advanced-elementor-widgets-style' ];
    // }

    protected function _register_controls()
    {
        // MAIN SECTION
        $this->start_controls_section("content_section", [
            "label" => __("Current Year", "magnific-addons"),
            "tab" => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

         $this->add_control("mae_current_year_before_text", [
               "label" => __("Before Text", "magnific-addons"),
               "type" => \Elementor\Controls_Manager::TEXT,
         ]);
         $this->add_control("mae_current_year_after_text", [
               "label" => __("After Text", "magnific-addons"),
               "type" => \Elementor\Controls_Manager::TEXT,
         ]);

      
        $this->end_controls_section();

        // MAIN STYLE SETTINGS
        $this->start_controls_section("main_style_section", [
            "label" => esc_html__("Style", "magnific-addons"),
            "tab" => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control(
         'main_alignment',
         [
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'label' => esc_html__( 'Alignment', 'magnific-addons' ),
            'options' => [
               'left' => [
                  'title' => esc_html__( 'Left', 'magnific-addons' ),
                  'icon' => 'eicon-text-align-left',
               ],
               'center' => [
                  'title' => esc_html__( 'Center', 'magnific-addons' ),
                  'icon' => 'eicon-text-align-center',
               ],
               'right' => [
                  'title' => esc_html__( 'Right', 'magnific-addons' ),
                  'icon' => 'eicon-text-align-right',
               ],
            ],
            "selectors" => [
               "{{WRAPPER}} .elementor-widget-container" => "text-align: {{VALUE}}",
           ],
            // "selector" => "{{WRAPPER}} .elementor-widget-container" => "text-align: {{VALUE}}",
            'default' => 'center',
         ]
      );


        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                "name" => "content_typography",
                "selector" => "{{WRAPPER}} .elementor-widget-container",
            ]
        );

        $this->add_control("main_section_color", [
            "label" => esc_html__("Color", "magnific-addons"),
            "type" => \Elementor\Controls_Manager::COLOR,
            "selectors" => [
                "{{WRAPPER}} .elementor-widget-container" => "color: {{VALUE}}",
            ],
        ]);

        $this->end_controls_section();

        // BEFORE TEXT STYLE SETTINGS
        $this->start_controls_section("before_text_style_section", [
            "label" => esc_html__("Before Text Style", "magnific-addons"),
            "tab" => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                "name" => "before_text_typography",
                "selector" => "{{WRAPPER}} .mae_current_year_before_text",
            ]
        );

        $this->add_control("before_text_color", [
            "label" => esc_html__("Color", "magnific-addons"),
            "type" => \Elementor\Controls_Manager::COLOR,
            "selectors" => [
                "{{WRAPPER}} .mae_current_year_before_text" =>
                    "color: {{VALUE}}",
            ],
        ]);

        $this->end_controls_section();
        // AFTER TEXT STYLE SETTINGS
        $this->start_controls_section("after_text_style_section", [
            "label" => esc_html__("After Text Style", "magnific-addons"),
            "tab" => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                "name" => "after_text_typography",
                "selector" => "{{WRAPPER}} .mae_current_year_after_text",
            ]
        );

        $this->add_control("after_text_color", [
            "label" => esc_html__("Color", "magnific-addons"),
            "type" => \Elementor\Controls_Manager::COLOR,
            "selectors" => [
                "{{WRAPPER}} .mae_current_year_after_text" =>
                    "color: {{VALUE}}",
            ],
        ]);

        $this->end_controls_section();
    }

    public function get_script_depends()
    {
        return ["mae_widgets-script"];
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (!defined("MAE_PHP_SAFE_MODE")) {
            echo '<span class="mae_current_year_before_text">' .
                $settings["mae_current_year_before_text"] .
                ' </span><span class="mae_current_year"></span><span class="mae_current_year_after_text"> ' .
                $settings["mae_current_year_after_text"] .
                "</span>";
        } else {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo "<pre>" .
                    __("CURRENT YEAR WIDGET IN SAFE MODE", "magnific-addons") .
                    "</pre>";
            }
        }
    }

    protected function content_template()
    {
        if (!defined("MAE_PHP_SAFE_MODE")) { ?>
			<span class="mae_current_year_before_text">{{{ settings.mae_current_year_before_text }}} </span><span class="mae_current_year"></span><span class="mae_current_year_after_text"> {{{ settings.mae_current_year_after_text }}}</span>

      <?php } else {if (
                \Elementor\Plugin::$instance->editor->is_edit_mode()
            ) { ?>
             <pre>CURRENT YEAR WIDGET IN SAFE MODE</pre>
          <?php }}
    }
}

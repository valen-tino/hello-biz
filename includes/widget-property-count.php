<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Elementor_Property_Count_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'property_search_count'; }
    public function get_title() { return 'Property Result Count'; }
    public function get_icon() { return 'eicon-number'; }
    public function get_categories() { return [ 'general' ]; }

    public function get_script_depends() {
        wp_register_script( 'hello-biz-property-count', get_template_directory_uri() . '/assets/js/widget-property-count.js', ['jquery'], '1.0.0', true );
        return [ 'hello-biz-property-count' ];
    }

    public function get_style_depends() {
        wp_register_style( 'hello-biz-property-count', get_template_directory_uri() . '/assets/css/widget-property-count.css', [], '1.0.0' );
        return [ 'hello-biz-property-count' ];
    }

    protected function register_controls() {
        $this->start_controls_section( 'content_section', [ 'label' => 'Settings' ] );
        $this->add_control( 'query_id', [ 'label' => 'Query ID', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'main_search' ] );
        $this->add_control( 'prefix', [ 'label' => 'Prefix', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Showing' ] );
        $this->add_control( 'suffix', [ 'label' => 'Suffix', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Properties' ] );
        $this->add_responsive_control( 'align', [ 'label' => 'Alignment', 'type' => \Elementor\Controls_Manager::CHOOSE, 'options' => [ 'left' => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ], 'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ], 'right' => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ] ], 'selectors' => [ '{{WRAPPER}} .property-count-wrapper' => 'text-align: {{VALUE}};' ] ]);
        $this->end_controls_section();
        
        $this->start_controls_section( 'style_section', [ 'label' => 'Style', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'typography', 'selector' => '{{WRAPPER}} .property-count-wrapper' ] );
        $this->add_control( 'text_color', [ 'label' => 'Color', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .property-count-wrapper' => 'color: {{VALUE}};' ] ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $query_id = $settings['query_id'];
        

        
        ?>
        <div class="property-count-wrapper" id="count-<?php echo esc_attr($query_id); ?>">
            <span class="pc-prefix"><?php echo esc_html($settings['prefix']); ?></span>
            <span class="pc-number">...</span>
            <span class="pc-suffix"><?php echo esc_html($settings['suffix']); ?></span>
        </div>
        <?php
    }
}
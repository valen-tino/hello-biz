<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Elementor_Property_Search_Results_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'property_search_results'; }
    public function get_title() { return 'Property Results Grid'; }
    public function get_icon() { return 'eicon-gallery-grid'; }
    public function get_categories() { return [ 'general' ]; }

    public function get_script_depends() {
        wp_register_script( 'hello-biz-property-results', get_template_directory_uri() . '/assets/js/widget-property-results.js', ['jquery'], '1.0.0', true );
        return [ 'hello-biz-property-results' ];
    }

    public function get_style_depends() {
        wp_register_style( 'hello-biz-property-results', get_template_directory_uri() . '/assets/css/widget-property-results.css', [], '1.0.0' );
        return [ 'hello-biz-property-results' ];
    }

    protected function register_controls() {
        $this->start_controls_section( 'content_section', [ 'label' => 'Query Settings' ] );
        $this->add_control( 'query_id', [ 'label' => 'Query ID', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'main_search', 'description' => 'Must match ID in Form.' ] );
        $this->add_control( 'template_id', [ 'label' => 'Loop Template', 'type' => \Elementor\Controls_Manager::SELECT2, 'options' => $this->get_templates() ] );
        $this->add_control( 'posts_per_page', [ 'label' => 'Posts Per Page', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 9 ] );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $query_id = $settings['query_id'];
        $nonce = wp_create_nonce('property_filter_nonce');
        ?>
        <div class="property-results-wrapper" 
             id="results-wrapper-<?php echo esc_attr($query_id); ?>"
             data-template="<?php echo esc_attr($settings['template_id']); ?>"
             data-ppp="<?php echo esc_attr($settings['posts_per_page']); ?>"
             data-nonce="<?php echo esc_attr($nonce); ?>">

            <div class="search-loader" style="display:none; position:absolute; top:50px; left:50%; transform:translateX(-50%); z-index:10;">
                <div class="spinner" style="width:40px;height:40px;border:4px solid #ddd;border-top:4px solid #000;border-radius:50%;animation:spin 1s linear infinite;"></div>
            </div>

            <div class="property-grid-results"></div>
            
            <div class="property-pagination"></div>
        </div>
        <?php
    }
    private function get_templates() { $t=\Elementor\Plugin::instance()->templates_manager->get_source('local')->get_items(); $o=[]; if($t) foreach($t as $x) $o[$x['template_id']]=$x['title']; return $o; }
}

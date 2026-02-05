<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Widget: Project Floorplan Tabs
 */
class Project_Floorplan_Tabs_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'project_floorplan_tabs';
    }

    public function get_title() {
        return esc_html__( 'Project Floorplans Tabs', 'hello-elementor' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_script_depends() {
        wp_register_script( 'hello-biz-project-floorplan-tabs', get_template_directory_uri() . '/assets/js/project-floorplan-tabs.js', ['jquery'], '1.0.0', true );
        return [ 'hello-biz-project-floorplan-tabs' ];
    }

    public function get_style_depends() {
        wp_register_style( 'hello-biz-project-floorplan-tabs', get_template_directory_uri() . '/assets/css/project-floorplan-tabs.css', [], '1.0.0' );
        return [ 'hello-biz-project-floorplan-tabs' ];
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'ACF Settings', 'hello-elementor' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'field_name',
            [
                'label' => esc_html__( 'ACF Relationship Field Name', 'hello-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'floorplans',
                'description' => 'Enter the exact Field Name of your ACF Relationship field.',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $field_name = $settings['field_name'];

        // Get the ACF field (Relationship)
        $floorplans = get_field( $field_name );

        // Safety check: Empty or invalid data
        if ( ! $floorplans || ! is_array( $floorplans ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="elementor-alert elementor-alert-warning">No Floorplans found. Check the ACF Field Name or ensure this project has connected floorplans.</div>';
            } else {
                echo '<div style="padding: 40px 20px; text-align: center; background: #f9f9f9; border: 1px solid #e5e5e5; color: #666; font-size: 16px; font-family: var(--e-global-typography-primary-font-family);">Geen plattegrond beschikbaar</div>';
            }
            return;
        }

        // --- START HTML OUTPUT ---
        ?>



        ?>
        
        <div class="fp-tabs-widget" id="fp-widget-<?php echo $this->get_id(); ?>">
            
            <div class="fp-tabs-nav">
                <?php foreach( $floorplans as $index => $item ): 
                    // Handle both Post Object and Post ID formats
                    $post_id = ( is_object( $item ) ) ? $item->ID : $item;
                    $title = get_the_title( $post_id );
                    $active_class = ( $index === 0 ) ? 'active' : '';
                ?>
                    <button class="fp-tab-btn <?php echo $active_class; ?>" 
                            data-target="pane-<?php echo $this->get_id(); ?>-<?php echo $post_id; ?>">
                        <?php echo esc_html( $title ); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="fp-content-wrapper">
                <?php foreach( $floorplans as $index => $item ): 
                    $post_id = ( is_object( $item ) ) ? $item->ID : $item;
                    $img_url = get_the_post_thumbnail_url( $post_id, 'full' );
                    $active_class = ( $index === 0 ) ? 'active' : '';
                    $pane_id = 'pane-' . $this->get_id() . '-' . $post_id;
                ?>
                    <div id="<?php echo $pane_id; ?>" class="fp-pane <?php echo $active_class; ?>">
                        <?php if ( $img_url ) : ?>
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title($post_id) ); ?>">
                        <?php else : ?>
                            <p style="padding:20px; color:#777;">No Featured Image found for this floorplan.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            

        </div>
        <?php
    }
}
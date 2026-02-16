<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Widget: Unit Type Showcase
 */
class Unit_Type_Showcase_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'unit_type_showcase_widget';
    }

    public function get_title() {
        return esc_html__( 'Unit Type Showcase', 'hello-elementor' );
    }

    public function get_icon() {
        return 'eicon-tabs';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_script_depends() {
        wp_register_script( 'hello-biz-unit-showcase', get_template_directory_uri() . '/assets/js/unit-showcase-widget.js', ['jquery'], '1.0.0', true );
        return [ 'hello-biz-unit-showcase' ];
    }

    public function get_style_depends() {
        wp_register_style( 'hello-biz-unit-showcase', get_template_directory_uri() . '/assets/css/unit-showcase-widget.css', [], '1.0.0' );
        return [ 'hello-biz-unit-showcase' ];
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
            'repeater_field_name',
            [
                'label' => esc_html__( 'Repeater Field Name', 'hello-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'unit_types',
            ]
        );

        $this->add_control(
            'enable_field_name',
            [
                'label' => esc_html__( 'Enable Switch Name', 'hello-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'enable_this',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $repeater_name = $settings['repeater_field_name'];
        $enable_name = $settings['enable_field_name'];

        if ( ! get_field( $enable_name ) ) return; 

        $units = get_field( $repeater_name );

        if ( ! $units ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="elementor-alert elementor-alert-warning">Enabled, but no Unit Types found.</div>';
            }
            return;
        }



        ?>
        
        <div class="unit-type-showcase" id="showcase-<?php echo $this->get_id(); ?>">
            
            <div class="unit-type-tabs">
                <?php foreach( $units as $index => $unit ): 
                    $active_class = ($index === 0) ? 'active' : ''; 
                ?>
                    <button class="unit-type-tab-btn <?php echo $active_class; ?>" data-tab="unit-<?php echo $this->get_id(); ?>-<?php echo $index; ?>">
                        <?php echo esc_html( $unit['tab_label'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="unit-type-content-wrapper">
                <?php foreach( $units as $index => $unit ): 
                    $active_class = ($index === 0) ? 'active' : ''; 
                    $pane_id = 'unit-' . $this->get_id() . '-' . $index;
                    $floorplans = $unit['select_associated_floorplans'];
                ?>
                    <div class="unit-type-pane <?php echo $active_class; ?>" id="<?php echo $pane_id; ?>">
                        
                        <div class="unit-type-image">
                            <?php 
                            $img = $unit['unit_image'];
                            if( $img && is_array($img) ) echo wp_get_attachment_image( $img['id'], 'large' );
                            elseif( $img ) echo wp_get_attachment_image( $img, 'large' );
                            ?>
                        </div>

                        <div class="unit-type-details">
                            <h2><?php echo esc_html( $unit['unit_title'] ); ?></h2>
                            <div class="unit-type-meta">
                                <span><?php echo esc_html( $unit['bedroom_count'] ); ?> Bedrooms</span>
                                <span><?php echo esc_html( $unit['bathroom_count'] ); ?> Bathrooms</span>
                                <span><?php echo esc_html( $unit['sizearea'] ); ?> m²</span>
                            </div>
                            <div class="unit-type-desc"><?php echo wp_kses_post( $unit['unit_description'] ); ?></div>
                            <div class="unit-type-features">
                                <h3>Key Features</h3>
                                <?php echo $unit['key_features']; ?>
                            </div>
                            <div class="unit-type-price">
                                <h3>Price</h3>
                                <p><?php echo esc_html( $unit['price'] ); ?></p>
                            </div>

                            <?php if( $floorplans ): 
                                $first_fp = is_object($floorplans[0]) ? $floorplans[0]->ID : $floorplans[0];
                                $first_img = get_the_post_thumbnail_url($first_fp, 'full');
                            ?>
                                <button class="unit-type-btn-floorplan" onclick="jQuery('#modal-<?php echo $pane_id; ?>').fadeIn(200);">
                                    View Floorplans
                                </button>

                                <div id="modal-<?php echo $pane_id; ?>" class="unit-type-modal" style="display:none;">
                                    <div class="unit-type-modal-content">
                                        
                                        <div class="unit-type-modal-header">
                                            <h3 class="unit-type-modal-title">Floorplans</h3>
                                            <span class="unit-type-close" onclick="jQuery('#modal-<?php echo $pane_id; ?>').fadeOut(200);">&times;</span>
                                        </div>

                                        <div class="unit-type-main-view">
                                            <a id="main-link-<?php echo $pane_id; ?>" href="<?php echo esc_url($first_img); ?>" data-elementor-open-lightbox="yes">
                                                <img id="main-view-<?php echo $pane_id; ?>" src="<?php echo esc_url($first_img); ?>" alt="Floorplan View">
                                            </a>
                                        </div>

                                        <div class="unit-type-nav-scroller">
                                            <?php foreach( $floorplans as $key => $fp_post ): 
                                                $pid = ( is_object($fp_post) ) ? $fp_post->ID : $fp_post;
                                                $fp_img_url = get_the_post_thumbnail_url( $pid, 'full' );
                                                $fp_title = get_the_title( $pid );
                                                $is_active = ($key === 0) ? 'active-nav' : '';

                                                if( $fp_img_url ): 
                                                    // Generate AVIF URL
                                                    $fp_img_avif = preg_replace('/\.(png|jpg|jpeg)$/i', '.avif', $fp_img_url);
                                                ?>
                                                    <div class="unit-type-nav-item <?php echo $is_active; ?>" 
                                                         data-target="#main-view-<?php echo $pane_id; ?>"
                                                         data-link="#main-link-<?php echo $pane_id; ?>"
                                                         data-src="<?php echo esc_url($fp_img_url); ?>"
                                                         data-src-avif="<?php echo esc_url($fp_img_avif); ?>">
                                                        <?php echo esc_html($fp_title); ?>
                                                    </div>
                                                <?php endif; 
                                            endforeach; ?>
                                        </div>

                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            

        </div>
        <?php
    }
}
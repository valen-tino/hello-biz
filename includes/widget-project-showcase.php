<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Elementor_Project_Showcase_Widget extends \Elementor\Widget_Base {

    public function get_name() { 
        return 'project_showcase'; 
    }
    
    public function get_title() { 
        return 'Project Showcase'; 
    }
    
    public function get_icon() { 
        return 'eicon-gallery-grid'; 
    }
    
    public function get_categories() { 
        return [ 'general' ]; 
    }

    public function get_script_depends() {
        wp_register_script( 'hello-biz-project-showcase', get_template_directory_uri() . '/assets/js/widget-project-showcase.js', ['jquery'], '1.0.0', true );
        return [ 'hello-biz-project-showcase' ];
    }

    public function get_style_depends() {
        wp_register_style( 'hello-biz-project-showcase', get_template_directory_uri() . '/assets/css/widget-project-showcase.css', [], '1.0.0' );
        return [ 'hello-biz-project-showcase' ];
    }

    protected function register_controls() {
        
        // Content Section - Project Selection
        $this->start_controls_section(
            'query_section',
            [
                'label' => 'Project Selection',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'selected_projects',
            [
                'label' => 'Select Projects',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_available_projects(),
                'label_block' => true,
                'description' => 'Select multiple projects. They will rotate in order on each page refresh.',
            ]
        );

        $this->end_controls_section();

        // Field Mapping Section
        $this->start_controls_section(
            'field_mapping_section',
            [
                'label' => 'Field Mapping',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'field_mapping_note',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<p style="font-size: 12px; color: #666;">Map your ACF field names to display project data. Leave empty to use defaults.</p>',
            ]
        );

        $this->add_control(
            'subtitle_field',
            [
                'label' => 'Subtitle Field Name',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'e.g., project_subtitle',
                'description' => 'ACF field for subtitle. Leave empty to use "Featured Project"',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'description_field',
            [
                'label' => 'Description Field Name',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'excerpt' => 'Post Excerpt',
                    'content' => 'Post Content',
                    'custom' => 'Custom ACF Field',
                ],
                'default' => 'excerpt',
            ]
        );

        $this->add_control(
            'custom_description_field',
            [
                'label' => 'Custom Description Field',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'e.g., project_description',
                'condition' => [
                    'description_field' => 'custom',
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'info_box_1_value_field',
            [
                'label' => 'Info Box 1 - Value Field',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'e.g., woonoppervlakte',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'info_box_1_label_field',
            [
                'label' => 'Info Box 1 - Label Field',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'e.g., woonoppervlakte_label',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'info_box_2_value_field',
            [
                'label' => 'Info Box 2 - Value Field',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'e.g., energieklasse',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'info_box_2_label_field',
            [
                'label' => 'Info Box 2 - Label Field',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'e.g., energieklasse_label',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'button_text_field',
            [
                'label' => 'Button Text Field',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'e.g., cta_text',
                'description' => 'Leave empty to use "Bekijk Project"',
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        // Layout Section
        $this->start_controls_section(
            'layout_section',
            [
                'label' => 'Layout',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'image_position',
            [
                'label' => 'Image Position',
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => 'Left',
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => 'Right',
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => false,
            ]
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => 'Image Width (%)',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => 30,
                        'max' => 70,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .project-showcase-image' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .project-showcase-content' => 'flex: 0 0 calc(100% - {{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => 'Gap',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .project-showcase-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Image
        $this->start_controls_section(
            'style_image',
            [
                'label' => 'Image',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .project-showcase-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Subtitle
        $this->start_controls_section(
            'style_subtitle',
            [
                'label' => 'Subtitle',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'selector' => '{{WRAPPER}} .project-subtitle',
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'subtitle_spacing',
            [
                'label' => 'Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .project-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Title
        $this->start_controls_section(
            'style_title',
            [
                'label' => 'Title',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .project-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => 'Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .project-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Description
        $this->start_controls_section(
            'style_description',
            [
                'label' => 'Description',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .project-description',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'description_spacing',
            [
                'label' => 'Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .project-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Info Boxes
        $this->start_controls_section(
            'style_info_boxes',
            [
                'label' => 'Info Boxes',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'info_boxes_gap',
            [
                'label' => 'Gap Between Boxes',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .project-info-boxes' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'info_box_background',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f8f8',
                'selectors' => [
                    '{{WRAPPER}} .info-box' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'info_box_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .info-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'info_box_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .info-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'info_value_heading',
            [
                'label' => 'Value',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'info_value_typography',
                'selector' => '{{WRAPPER}} .info-value',
            ]
        );

        $this->add_control(
            'info_value_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .info-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'info_label_heading',
            [
                'label' => 'Label',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'info_label_typography',
                'selector' => '{{WRAPPER}} .info-label',
            ]
        );

        $this->add_control(
            'info_label_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .info-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'info_boxes_spacing',
            [
                'label' => 'Bottom Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .project-info-boxes' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Button
        $this->start_controls_section(
            'style_button',
            [
                'label' => 'Button',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .project-button',
            ]
        );

        $this->start_controls_tabs('button_tabs');

        $this->start_controls_tab(
            'button_normal',
            [
                'label' => 'Normal',
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover',
            [
                'label' => 'Hover',
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .project-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .project-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get available projects for the select2 control
     */
    private function get_available_projects() {
        $projects = get_posts([
            'post_type' => 'project',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        $options = [];
        foreach ($projects as $project) {
            $options[$project->ID] = $project->post_title;
        }

        return $options;
    }

    /**
     * Get field value with fallback
     */
    private function get_field_value($post_id, $field_name, $fallback = '') {
        if (empty($field_name)) {
            return $fallback;
        }

        // Try ACF first
        if (function_exists('get_field')) {
            $value = get_field($field_name, $post_id);
            if ($value) {
                return $value;
            }
        }

        // Fallback to post meta
        $value = get_post_meta($post_id, $field_name, true);
        return $value ? $value : $fallback;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $selected_projects = $settings['selected_projects'];
        
        if (empty($selected_projects)) {
            echo '<p>Please select at least one project in the widget settings.</p>';
            return;
        }

        // Prepare data for all selected projects
        $projects_data = [];
        
        foreach ($selected_projects as $project_id) {
            $project_id = intval($project_id); // Ensure integer
            $project = get_post($project_id);
            if (!$project) continue;

            // Get Image - Use 'medium_large' size (768px) for better PageSpeed performance
            $featured_image = get_the_post_thumbnail_url($project_id, 'medium_large');
            $image_id = get_post_thumbnail_id($project_id);
            $image_meta = wp_get_attachment_metadata($image_id);
            
            // Get image dimensions for explicit width/height (improves CLS)
            $image_width = 768; // default for 'medium_large' size
            $image_height = 576; // default aspect ratio
            
            if ($image_meta && isset($image_meta['sizes']['medium_large'])) {
                $image_width = $image_meta['sizes']['medium_large']['width'];
                $image_height = $image_meta['sizes']['medium_large']['height'];
            } elseif ($image_meta && isset($image_meta['width']) && isset($image_meta['height'])) {
                // Fallback to original dimensions if 'medium_large' size doesn't exist
                $image_width = $image_meta['width'];
                $image_height = $image_meta['height'];
            }
            
            if (!$featured_image) {
                $featured_image = \Elementor\Utils::get_placeholder_image_src();
                $image_width = 768;
                $image_height = 576;
            }

            // Get Text Fields
            $subtitle = $this->get_field_value($project_id, $settings['subtitle_field'], 'Uitgelicht project');
            $title = html_entity_decode(get_the_title($project_id));
            
            // Get Description
            $description = '';
            if ($settings['description_field'] === 'excerpt') {
                $description = get_the_excerpt($project_id);
            } elseif ($settings['description_field'] === 'content') {
                // Use get_post_field for content outside the loop
                $content = get_post_field('post_content', $project_id);
                $description = wp_trim_words(strip_shortcodes($content), 30);
            } else {
                $description = $this->get_field_value($project_id, $settings['custom_description_field'], get_the_excerpt($project_id));
            }

            // Get Info Boxes
            $ib1_val = $this->get_field_value($project_id, $settings['info_box_1_value_field']);
            $ib1_lbl = $this->get_field_value($project_id, $settings['info_box_1_label_field']);
            $ib2_val = $this->get_field_value($project_id, $settings['info_box_2_value_field']);
            $ib2_lbl = $this->get_field_value($project_id, $settings['info_box_2_label_field']);
            
            // Get Button
            $button_text = $this->get_field_value($project_id, $settings['button_text_field'], 'Bekijk Project');
            $button_link = get_permalink($project_id);

            $projects_data[] = [
                'id' => $project_id,
                'image' => $featured_image,
                'image_width' => $image_width,
                'image_height' => $image_height,
                'subtitle' => $subtitle,
                'title' => $title,
                'description' => $description,
                'ib1_val' => $ib1_val,
                'ib1_lbl' => $ib1_lbl,
                'ib2_val' => $ib2_val,
                'ib2_lbl' => $ib2_lbl,
                'btn_text' => $button_text,
                'btn_link' => $button_link
            ];
        }

        if (empty($projects_data)) return;

        // Use the first project as initial server-side render
        $initial = $projects_data[0];
        $flex_direction = $settings['image_position'] === 'right' ? 'row-reverse' : 'row';
        $unique_id = 'ps_' . $this->get_id();
        
        ?>
        <div class="project-showcase-container" id="<?php echo esc_attr($unique_id); ?>" style="opacity: 0; transition: opacity 0.4s ease;">
            <div class="project-showcase-wrapper" style="display: flex; flex-direction: <?php echo esc_attr($flex_direction); ?>; align-items: center;">
                
                <div class="project-showcase-image">
                    <img id="<?php echo esc_attr($unique_id); ?>_img" src="<?php echo esc_url($initial['image']); ?>" alt="<?php echo esc_attr($initial['title']); ?>" width="<?php echo esc_attr($initial['image_width']); ?>" height="<?php echo esc_attr($initial['image_height']); ?>">
                </div>
                
                <div class="project-showcase-content">
                    <span id="<?php echo esc_attr($unique_id); ?>_sub" class="project-subtitle"><?php echo esc_html($initial['subtitle']); ?></span>
                    
                    <h2 id="<?php echo esc_attr($unique_id); ?>_title" class="project-title"><?php echo esc_html($initial['title']); ?></h2>
                    
                    <p id="<?php echo esc_attr($unique_id); ?>_desc" class="project-description"><?php echo esc_html($initial['description']); ?></p>
                    
                    <div class="project-info-boxes" id="<?php echo esc_attr($unique_id); ?>_boxes">
                        <!-- Populated via JS -->
                    </div>
                    
                    <a id="<?php echo esc_attr($unique_id); ?>_btn" href="<?php echo esc_url($initial['btn_link']); ?>" class="project-button">
                        <span class="txt"><?php echo esc_html($initial['btn_text']); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                
            </div>
        </div>


        <script>
        jQuery(document).ready(function($) {
            if (window.HelloBizProjectShowcase) {
                window.HelloBizProjectShowcase.init(
                    "<?php echo esc_js($unique_id); ?>", 
                    <?php echo json_encode($projects_data); ?>
                );
            }
        });
        </script>
        <?php
    }
}

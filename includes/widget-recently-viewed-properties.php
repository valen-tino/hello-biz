<?php
/**
 * Recently Viewed Properties Widget for Elementor
 * Displays properties the visitor has recently viewed
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Elementor_Recently_Viewed_Properties_Widget extends \Elementor\Widget_Base {

    public function get_name() { 
        return 'recently_viewed_properties'; 
    }
    
    public function get_title() { 
        return 'Recently Viewed Properties'; 
    }
    
    public function get_icon() { 
        return 'eicon-history'; 
    }
    
    public function get_categories() { 
        return [ 'general' ]; 
    }

    public function get_script_depends() {
        wp_register_script( 'recently-viewed-properties', get_template_directory_uri() . '/assets/js/recently-viewed-properties.js', ['jquery'], '1.0.0', true );
        return [ 'recently-viewed-properties' ];
    }

    public function get_style_depends() {
        wp_register_style( 'hello-biz-recently-viewed-properties', get_template_directory_uri() . '/assets/css/widget-recently-viewed-properties.css', [], '1.0.0' );
        return [ 'hello-biz-recently-viewed-properties' ];
    }

    protected function register_controls() {
        
        // Content Section - Settings
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Settings',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'max_properties',
            [
                'label' => 'Max Properties to Show',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'default' => 5,
            ]
        );

        $this->add_control(
            'no_results_message',
            [
                'label' => 'No Results Message',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'No recently viewed properties.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'widget_title',
            [
                'label' => 'Widget Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Recently Viewed',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => 'Show Title',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
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
            'price_field',
            [
                'label' => 'Price Field Name',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'huurprijs',
                'placeholder' => 'e.g., huurprijs or koopprijs',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'price_suffix',
            [
                'label' => 'Price Suffix',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '/ P.M. EX.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'surface_field',
            [
                'label' => 'Surface Area Field Name',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'woonoppervlakte',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'bedrooms_field',
            [
                'label' => 'Bedrooms Field Name',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'slaapkamers',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'bathrooms_field',
            [
                'label' => 'Bathrooms Field Name',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'badkamers',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'location_taxonomy',
            [
                'label' => 'Location Taxonomy',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'property-city',
                'placeholder' => 'e.g., property-city',
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        // Style Section - Container
        $this->start_controls_section(
            'style_container',
            [
                'label' => 'Container',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'container_background',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .rv-property-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .rv-property-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .rv-property-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'cards_gap',
            [
                'label' => 'Gap Between Cards',
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
                    '{{WRAPPER}} .rv-properties-list' => 'gap: {{SIZE}}{{UNIT}};',
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
            'image_width',
            [
                'label' => 'Image Width',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 400,
                    ],
                    '%' => [
                        'min' => 20,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 180,
                ],
                'selectors' => [
                    '{{WRAPPER}} .rv-property-image' => 'width: {{SIZE}}{{UNIT}}; flex: 0 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => 'Image Height',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 80,
                        'max' => 300,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 120,
                ],
                'selectors' => [
                    '{{WRAPPER}} .rv-property-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => '8',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '8',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .rv-property-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Typography
        $this->start_controls_section(
            'style_typography',
            [
                'label' => 'Typography',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_heading',
            [
                'label' => 'Property Title',
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'property_title_typography',
                'selector' => '{{WRAPPER}} .rv-property-title',
            ]
        );

        $this->add_control(
            'property_title_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .rv-property-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'location_heading',
            [
                'label' => 'Location',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'location_typography',
                'selector' => '{{WRAPPER}} .rv-property-location',
            ]
        );

        $this->add_control(
            'location_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#cccccc',
                'selectors' => [
                    '{{WRAPPER}} .rv-property-location' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'price_heading',
            [
                'label' => 'Price',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .rv-property-price',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .rv-property-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'details_heading',
            [
                'label' => 'Property Details',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'details_typography',
                'selector' => '{{WRAPPER}} .rv-property-details span',
            ]
        );

        $this->add_control(
            'details_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .rv-property-details span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Widget Title
        $this->start_controls_section(
            'style_widget_title',
            [
                'label' => 'Widget Title',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'widget_title_typography',
                'selector' => '{{WRAPPER}} .rv-widget-title',
            ]
        );

        $this->add_control(
            'widget_title_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rv-widget-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'widget_title_spacing',
            [
                'label' => 'Bottom Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .rv-widget-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $max_properties = $settings['max_properties'] ?? 5;
        $no_results_message = $settings['no_results_message'] ?? 'No recently viewed properties.';
        $widget_title = $settings['widget_title'] ?? 'Recently Viewed';
        $show_title = $settings['show_title'] === 'yes';
        
        // Get properties from cookie
        $property_ids = array();
        if (isset($_COOKIE['recently_viewed_properties'])) {
             $cookie_value = stripslashes($_COOKIE['recently_viewed_properties']);
             $decoded = json_decode($cookie_value, true);
             if (!$decoded) {
                 $decoded = json_decode(urldecode($cookie_value), true);
             }
             if (is_array($decoded)) {
                 $property_ids = $decoded;
             }
        }

        // Filter valid IDs
        $property_ids = array_map('intval', $property_ids);
        $property_ids = array_filter($property_ids);
        
        // Limit
        if (count($property_ids) > $max_properties) {
            $property_ids = array_slice($property_ids, 0, $max_properties);
        }

        $has_results = false;
        
        ?>
        <div class="rv-widget-container">
            <?php if ($show_title && !empty($widget_title)): ?>
                <h3 class="rv-widget-title"><?php echo esc_html($widget_title); ?></h3>
            <?php endif; ?>
            
            <div class="recently-viewed-properties-widget">
                <?php 
                if (!empty($property_ids)) {
                    // Query properties
                    $args = array(
                        'post_type' => 'property',
                        'post__in' => $property_ids,
                        'orderby' => 'post__in',
                        'posts_per_page' => count($property_ids),
                        'post_status' => 'publish',
                    );
                    
                    $query = new \WP_Query($args);
                    
                    if ($query->have_posts()) {
                        $has_results = true;
                        echo '<div class="rv-properties-list">';
                        
                        while ($query->have_posts()) {
                            $query->the_post();
                            $post_id = get_the_ID();
                            
                            // Get featured image
                            $image_url = get_the_post_thumbnail_url($post_id, 'medium');
                            if (!$image_url) {
                                $image_url = \Elementor\Utils::get_placeholder_image_src();
                            }
                            
                            // Get ACF fields
                            $price = '';
                            $surface = '';
                            $bedrooms = '';
                            $bathrooms = '';
                            
                            if (function_exists('get_field')) {
                                $price = get_field($settings['price_field'] ?? 'huurprijs', $post_id);
                                if (!$price) $price = get_field('koopprijs', $post_id); // Fallback
                                $surface = get_field($settings['surface_field'] ?? 'woonoppervlakte', $post_id);
                                $bedrooms = get_field($settings['bedrooms_field'] ?? 'slaapkamers', $post_id);
                                $bathrooms = get_field($settings['bathrooms_field'] ?? 'badkamers', $post_id);
                            }
                            
                            // Get location
                            $location = '';
                            $terms = get_the_terms($post_id, $settings['location_taxonomy'] ?? 'property-city');
                            if ($terms && !is_wp_error($terms)) {
                                $location = $terms[0]->name;
                            }
                            
                            // Format price & suffix
                            $formatted_price = '';
                            $suffix = $settings['price_suffix'] ?? '/ P.M. EX.';

                            if ($price) {
                                // check if price is numeric (or numeric string)
                                if (is_numeric($price)) {                                    
                                    $formatted_price = '€' . number_format(floatval($price), 0, ',', '.') . ' ' . $suffix;
                                } else {
                                    // If not numeric, show as is, but sanitized for HTML
                                    $formatted_price = wp_kses_post($price . ' ' . $suffix);
                                }
                            }
                            
                            ?>
                            <div class="rv-property-card">
                                <a href="<?php echo esc_url(get_permalink()); ?>">
                                    <div class="rv-property-image">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    </div>
                                    <div class="rv-property-content">
                                        <h4 class="rv-property-title"><?php echo esc_html(get_the_title()); ?></h4>
                                        
                                        <?php if ($location): ?>
                                            <div class="rv-property-location">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span><?php echo esc_html(strtoupper($location)); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($formatted_price): ?>
                                            <div class="rv-property-price"><?php echo wp_kses_post($formatted_price); ?></div>
                                        <?php endif; ?>
                                        
                                        <div class="rv-property-details">
                                            <?php if ($surface): ?>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><path d="M23.5 9.5H30.5V16.5" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M30.5002 9.5L22.3335 17.6667" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9.5 30.5002L17.6667 22.3335" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M16.5 30.5H9.5V23.5" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path></svg> <?php echo esc_html($surface); ?> m²</span>
                                                <?php endif; ?>
                                            <?php if ($bedrooms): ?>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" fill="none" stroke="currentColor"><path d="M6.6665 10.6665V29.3332" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6.6665 15.3335H30.6665C31.3737 15.3335 32.052 15.5793 32.5521 16.0169C33.0522 16.4545 33.3332 17.048 33.3332 17.6668V29.3335" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6.6665 25.8335H33.3332" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 15.3335V25.8335" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path></svg> <?php echo esc_html($bedrooms); ?></span>
                                            <?php endif; ?>
                                            <?php if ($bathrooms): ?>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" fill="none" stroke="currentColor"><path d="M17.3332 10.6665L14.6665 12.9998" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M26.6665 28.1665V30.4998" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6.6665 20H33.3332" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13.3335 28.1665V30.4998" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M16.0002 11.8333L14.1615 10.2244C13.7759 9.88548 13.2863 9.65156 12.752 9.55096C12.2177 9.45037 11.6615 9.48742 11.1507 9.65763C10.6399 9.82783 10.1964 10.1239 9.87394 10.51C9.55144 10.8961 9.36372 11.3558 9.3335 11.8333V25.8333C9.3335 26.4521 9.61445 27.0456 10.1145 27.4832C10.6146 27.9208 11.2929 28.1666 12.0002 28.1666H28.0002C28.7074 28.1666 29.3857 27.9208 29.8858 27.4832C30.3859 27.0456 30.6668 26.4521 30.6668 25.8333V19.9999" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path></svg> <?php echo esc_html($bathrooms); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                        
                        echo '</div>';
                    }
                    wp_reset_postdata();
                }
                
                if (!$has_results) {
                    echo '<div class="rv-no-results">' . esc_html($no_results_message) . '</div>';
                }
                ?>
            </div>
        </div>
        
        <?php

    }
}

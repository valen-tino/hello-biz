<?php

/**
 * Register Widget.
 *
 * Include widget file and register widget class.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_map_preview_widget( $widgets_manager ) {

    // 1. Define the Widget Class *inside* the hook to ensure Elementor\Widget_Base exists.
    if ( ! class_exists( 'Elementor_Map_Preview_Widget' ) ) {
        
        class Elementor_Map_Preview_Widget extends \Elementor\Widget_Base {

            public function get_name() {
                return 'map_preview';
            }

            public function get_title() {
                return esc_html__( 'Map Previewer', 'elementor-map-preview' );
            }

            public function get_icon() {
                return 'eicon-google-maps';
            }

            public function get_categories() {
                return [ 'general' ];
            }

            public function get_keywords() {
                return [ 'map', 'google', 'location', 'embed', 'preview' ];
            }

            protected function register_controls() {

                $this->start_controls_section(
                    'content_section',
                    [
                        'label' => esc_html__( 'Map Settings', 'elementor-map-preview' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]
                );

                $this->add_control(
                    'map_url',
                    [
                        'label' => esc_html__( 'Google Maps URL', 'elementor-map-preview' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'dynamic' => [
                            'active' => true,
                        ],
                        'placeholder' => esc_html__( 'https://maps.app.goo.gl/...', 'elementor-map-preview' ),
                        'description' => esc_html__( 'Paste a full or shortened Google Maps link. Supports Dynamic Tags (ACF, etc).', 'elementor-map-preview' ),
                        'default' => '',
                    ]
                );

                $this->add_responsive_control(
                    'height',
                    [
                        'label' => esc_html__( 'Height', 'elementor-map-preview' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', 'vh' ],
                        'range' => [
                            'px' => [
                                'min' => 100,
                                'max' => 1000,
                            ],
                            'vh' => [
                                'min' => 10,
                                'max' => 100,
                            ],
                        ],
                        'default' => [
                            'unit' => 'px',
                            'size' => 350,
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .map-preview-container iframe' => 'height: {{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .map-preview-error' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_control(
                    'show_error',
                    [
                        'label' => esc_html__( 'Show Error Message', 'elementor-map-preview' ),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__( 'Show', 'elementor-map-preview' ),
                        'label_off' => esc_html__( 'Hide', 'elementor-map-preview' ),
                        'return_value' => 'yes',
                        'default' => 'yes',
                        'description' => esc_html__( 'Show a gray error box if the URL is invalid. Disable to hide the widget completely on error.', 'elementor-map-preview' ),
                    ]
                );

                $this->add_control(
                    'disable_cache',
                    [
                        'label' => esc_html__( 'Disable Cache (Dev)', 'elementor-map-preview' ),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__( 'Yes', 'elementor-map-preview' ),
                        'label_off' => esc_html__( 'No', 'elementor-map-preview' ),
                        'return_value' => 'yes',
                        'default' => '',
                        'description' => esc_html__( 'Useful when testing dynamic tags or short links. Disables the 24h caching.', 'elementor-map-preview' ),
                    ]
                );

                $this->end_controls_section();

                $this->start_controls_section(
                    'style_section',
                    [
                        'label' => esc_html__( 'Style', 'elementor-map-preview' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );

                $this->add_control(
                    'border_radius',
                    [
                        'label' => esc_html__( 'Border Radius', 'elementor-map-preview' ),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                            '{{WRAPPER}} .map-preview-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                        ],
                    ]
                );

                $this->add_group_control(
                    \Elementor\Group_Control_Box_Shadow::get_type(),
                    [
                        'name' => 'box_shadow',
                        'label' => esc_html__( 'Box Shadow', 'elementor-map-preview' ),
                        'selector' => '{{WRAPPER}} .map-preview-container',
                    ]
                );

                $this->end_controls_section();
            }

            /**
             * Helper to expand short URLs serverside
             */
            private function expand_short_url( $url ) {
                // Return original if not a short link
                if ( strpos( $url, 'goo.gl' ) === false && strpos( $url, 'g.co' ) === false ) {
                    return $url;
                }

                // 1. Try to fetch the page content
                // We use wp_remote_get which follows redirects by default (limit 5)
                $response = wp_remote_get( $url, [
                    'timeout' => 10,
                    'redirection' => 5,
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ] );

                if ( is_wp_error( $response ) ) {
                    return $url; // Fail gracefully
                }

                $body = wp_remote_retrieve_body( $response );
                
                // 2. Look for the canonical Google Maps URL in the response
                // Google often puts the real URL in <meta property="og:url"> or similar
                if ( preg_match( '/https:\/\/www\.google\.com\/maps\/place\/[^"\'\s<]+/', $body, $matches ) ) {
                    return $matches[0];
                }

                return $url;
            }

            /**
             * Parse the URL to get the embed source
             */
            private function get_embed_src( $url ) {
                // 1. Coordinates: @lat,lng
                if ( preg_match( '/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches ) ) {
                    $lat = $matches[1];
                    $lng = $matches[2];
                    return "https://maps.google.com/maps?q={$lat},{$lng}&z=15&output=embed";
                }

                // 2. Place Name: /place/Name
                if ( preg_match( '/\/place\/([^\/]+)/', $url, $matches ) ) {
                    $query = $matches[1];
                    return "https://maps.google.com/maps?q={$query}&z=15&output=embed";
                }

                // 3. Search Query: /search/Query
                if ( preg_match( '/\/search\/([^\/]+)/', $url, $matches ) ) {
                    $query = $matches[1];
                    return "https://maps.google.com/maps?q={$query}&z=15&output=embed";
                }

                return '';
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $input_url = trim( $settings['map_url'] );

                if ( empty( $input_url ) ) {
                    return;
                }

                // Cache Key generation
                $cache_key = 'map_embed_' . md5( $input_url );
                $embed_src = false;

                // Check cache if not disabled
                if ( $settings['disable_cache'] !== 'yes' ) {
                    $embed_src = get_transient( $cache_key );
                }

                // If not in cache, process the URL
                if ( false === $embed_src ) {
                    $processed_url = $input_url;

                    // Expand if short link
                    if ( strpos( $input_url, 'goo.gl' ) !== false || strpos( $input_url, 'g.co' ) !== false ) {
                        $processed_url = $this->expand_short_url( $input_url );
                    }

                    $embed_src = $this->get_embed_src( $processed_url );

                    // Cache for 24 hours to improve performance
                    if ( ! empty( $embed_src ) && $settings['disable_cache'] !== 'yes' ) {
                        set_transient( $cache_key, $embed_src, DAY_IN_SECONDS );
                    }
                }

                // If still empty (invalid), and user wants to hide errors, return early
                if ( empty( $embed_src ) && $settings['show_error'] !== 'yes' ) {
                    return;
                }

                ?>
                <div class="map-preview-container" style="width: 100%; position: relative; background: #f0f0f0;">
                    <?php if ( ! empty( $embed_src ) ) : ?>
                        <iframe 
                            width="100%" 
                            height="100%" 
                            frameborder="0" 
                            scrolling="no" 
                            marginheight="0" 
                            marginwidth="0" 
                            src="<?php echo esc_url( $embed_src ); ?>"
                            title="Map Preview"
                            loading="lazy"
                            style="border:0; display: block;"
                        ></iframe>
                    <?php else : ?>
                        <div class="map-preview-error" style="display: flex; align-items: center; justify-content: center; color: #666; font-family: sans-serif; padding: 20px; text-align: center; background: #f8f9fa;">
                            <span><?php esc_html_e( 'Invalid Map URL provided.', 'elementor-map-preview' ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            }
        }
    }

    // 2. Register the widget instance
    $widgets_manager->register( new \Elementor_Map_Preview_Widget() );
}

add_action( 'elementor/widgets/register', 'register_map_preview_widget' );
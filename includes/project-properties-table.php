<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Elementor_Project_Properties_Table extends Widget_Base {

    public function get_name() { return 'project_properties_table'; }
    public function get_title() { return 'Project Properties Table'; }
    public function get_icon() { return 'eicon-table'; }
    public function get_categories() { return [ 'general' ]; }

    public function get_style_depends() {
        wp_register_style( 'hello-biz-project-properties-table', get_template_directory_uri() . '/assets/css/project-properties-table.css', [], '1.0.0' );
        return [ 'hello-biz-project-properties-table' ];
    }

    protected function register_controls() {

        // --- SECTION 1: Query Logic ---
        $this->start_controls_section(
            'query_section',
            [
                'label' => 'Query & Sorting',
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'parent_project_id',
            [
                'label' => 'Parent Project ID',
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'placeholder' => 'Select Dynamic Tag > Post ID',
                'default' => '',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => 'Order By',
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => 'Date Published',
                    'title' => 'Title (Woning)',
                    'meta_value_num' => 'Custom Field (Numeric - e.g. Price)',
                ],
            ]
        );

        $this->add_control(
            'meta_key_sort',
            [
                'label' => 'Sort Key (if Custom Field selected)',
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'e.g. price',
                'condition' => [
                    'orderby' => 'meta_value_num',
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => 'Order',
                'type' => Controls_Manager::SELECT,
                'default' => 'ASC',
                'options' => [
                    'ASC' => 'Ascending (A-Z, Low-High)',
                    'DESC' => 'Descending (Z-A, High-Low)',
                ],
            ]
        );

        $this->end_controls_section();

        // --- SECTION 2: Table Columns (Repeater) ---
        $this->start_controls_section(
            'columns_section',
            [
                'label' => 'Table Columns',
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'column_header',
            [
                'label' => 'Column Header Label',
                'type' => Controls_Manager::TEXT,
                'default' => 'Header',
            ]
        );

        $repeater->add_control(
            'source_type',
            [
                'label' => 'Data Source',
                'type' => Controls_Manager::SELECT,
                'default' => 'acf_field',
                'options' => [
                    'post_title' => 'Post Title (Woning)',
                    'acf_field' => 'ACF Field (Text/Number)',
                    'taxonomy_badge' => 'Taxonomy (Badge Style)',
                    'taxonomy_simple' => 'Taxonomy',
                    'action_button' => 'Details Button',
                ],
            ]
        );

        $repeater->add_control(
            'field_key',
            [
                'label' => 'ACF Field Name / Taxonomy Slug',
                'type' => Controls_Manager::TEXT,
                'description' => 'Enter ACF Name (e.g., "price") or Taxonomy Slug (e.g., "availability-status")',
                'condition' => [
                    'source_type' => ['acf_field', 'taxonomy_badge', 'taxonomy_simple'],
                ],
            ]
        );

        $repeater->add_control(
            'prefix',
            [
                'label' => 'Prefix (e.g., €)',
                'type' => Controls_Manager::TEXT,
                'condition' => [ 'source_type' => 'acf_field' ],
            ]
        );

        $repeater->add_control(
            'suffix',
            [
                'label' => 'Suffix (e.g., m²)',
                'type' => Controls_Manager::TEXT,
                'condition' => [ 'source_type' => 'acf_field' ],
            ]
        );

        $this->add_control(
            'table_columns',
            [
                'label' => 'Columns',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [ 'column_header' => 'Woning', 'source_type' => 'post_title' ],
                    [ 'column_header' => 'Status', 'source_type' => 'taxonomy_badge', 'field_key' => 'availability-status' ],
                    [ 'column_header' => '', 'source_type' => 'action_button' ],
                ],
                'title_field' => '{{{ column_header }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // 1. Get the Current Project ID
        $current_project_id = $settings['parent_project_id'];
        
        // Fallback: If the dynamic tag isn't set, use the current page ID
        if ( empty( $current_project_id ) ) {
            $current_project_id = get_the_ID();
        }

        // 2. Fetch ALL Properties first (We filter them manually below)
        $args = [
            'post_type'      => 'property',
            'numberposts'    => -1, // Get all properties
            'orderby'        => $settings['orderby'],
            'order'          => $settings['order'],
            'suppress_filters' => false // Important for some ACF setups
        ];

        // Custom sorting (if selected)
        if ( $settings['orderby'] === 'meta_value_num' && ! empty( $settings['meta_key_sort'] ) ) {
            $args['meta_key'] = $settings['meta_key_sort'];
        }

        $all_properties = get_posts( $args );
        $matched_properties = [];

        // 3. THE "PHP FILTER" LOGIC
        if ( ! empty( $all_properties ) ) {
            foreach ( $all_properties as $property ) {
                
                // Use ACF's native function. This automatically converts the "Post Object" format into a clean array we can use.
                $related_projects = get_field( 'parent_project', $property->ID );

                if ( $related_projects ) {
                    // ACF Relationship returns an ARRAY, even for single items.We loop through the relationships to find a match.
                    foreach ( $related_projects as $p ) {
                        
                        // Handle "Post Object" Return Format
                        $id_to_check = ( is_object( $p ) ) ? $p->ID : $p;

                        // Does this property belong to our Current Project?
                        if ( $id_to_check == $current_project_id ) {
                            $matched_properties[] = $property;
                            break; // Match found! Stop checking this property.
                        }
                    }
                }
            }
        }

        // 4. RENDER (Using the filtered list)
        if ( ! empty( $matched_properties ) ) {
            // Styles loaded via get_style_depends

            echo '<div class="property-table-container">';
            // Styles moved to /assets/css/project-properties-table.css
            echo '<table class="property-table">';
            
            // Header
            echo '<thead><tr>';
            foreach ( $settings['table_columns'] as $col ) {
                echo '<th>' . esc_html( $col['column_header'] ) . '</th>';
            }
            echo '</tr></thead>';
            
            // Body
            echo '<tbody>';
            foreach ( $matched_properties as $post ) {
                // Setup global post so get_field works naturally
                setup_postdata( $post ); 
                $post_id = $post->ID;

                echo '<tr>';
                foreach ( $settings['table_columns'] as $col ) {
                    echo '<td>';
    
                    switch ( $col['source_type'] ) {
                        case 'post_title':
                            echo '<strong>' . get_the_title( $post_id ) . '</strong>';
                            break;

                        case 'acf_field':
                            $val = get_field( $col['field_key'], $post_id );
                            if( $val ) {
                                // Sanitize: strip all HTML tags and decode entities
                                $clean_val = wp_strip_all_tags( $val );
                                $clean_val = html_entity_decode( $clean_val, ENT_QUOTES | ENT_HTML5 );
                                // Trim spaces and format
                                $clean_val = trim( $clean_val );
                                echo esc_html( $col['prefix'] . $clean_val . $col['suffix'] );
                            } else {
                                echo '-';
                            }
                            break;

                        case 'taxonomy_badge':
                            $terms = get_the_terms( $post_id, $col['field_key'] );
                            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                                $status_name = $terms[0]->name;
                                $status_slug = $terms[0]->slug;
                                echo '<span class="status-badge status-' . esc_attr( $status_slug ) . '">' . esc_html( $status_name ) . '</span>';
                            }
                            break;

                        case 'taxonomy_simple':
                            $terms = get_the_terms( $post_id, $col['field_key'] );
                            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                                echo esc_html( $terms[0]->name );
                            } else {
                                echo '-';
                            }
                            break;

                        case 'action_button':
                            echo '<a href="' . get_permalink( $post_id ) . '" class="details-btn">Details <i class="fas fa-chevron-right"></i></a>';
                            break;
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            
            // CRITICAL: Reset the global post object back to the main page
            wp_reset_postdata();

        } else {
            // Admin Debugging
            if ( current_user_can('administrator') ) {
                echo '<p style="color:red; font-size: 12px;">Debug: Checked ' . count($all_properties) . ' properties. No match found for Project ID: ' . $current_project_id . '</p>';
            } else {
                echo '<p style="font-size:24px; color:black; font-family:"Proxima Nova",sans-serif;>Geen eigendommen gevonden.</p>';
            }
        }
    }
}
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Elementor_Property_Search_Form_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'property_search_form'; }
    public function get_title() { return 'Property Search Form'; }
    public function get_icon() { return 'eicon-form-horizontal'; }
    public function get_categories() { return [ 'general' ]; }

    public function get_script_depends() {
        wp_register_script( 'hello-biz-property-search-form', get_template_directory_uri() . '/assets/js/widget-property-search-form.js', ['jquery'], '1.0.0', true );
        return [ 'hello-biz-property-search-form' ];
    }

    public function get_style_depends() {
        wp_register_style( 'hello-biz-property-search-form', get_template_directory_uri() . '/assets/css/widget-property-search-form.css', [], '1.0.0' );
        return [ 'hello-biz-property-search-form' ];
    }

    protected function register_controls() {
        
        $this->start_controls_section( 'content_section', [ 'label' => 'Form Settings' ] );

        $this->add_control( 'query_id', [ 
            'label' => 'Query ID', 
            'type' => \Elementor\Controls_Manager::TEXT, 
            'default' => 'main_search',
            'description' => 'Link this to your Results & Count widgets.'
        ]);

        $this->add_control( 'project_ids', [ 
            'label' => 'Scope (Projects)', 
            'type' => \Elementor\Controls_Manager::SELECT2, 
            'label_block' => true, 
            'multiple' => true,
            'options' => $this->get_project_posts(), 
            'default' => [], 
            'description' => 'Select specific projects or leave empty for "All Projects".'
        ]);

        // Widget Mode Control
        $this->add_control( 'widget_mode', [
            'label' => 'Widget Mode',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'results_grid' => 'Use Results Grid',
                'standalone' => 'Standalone (Redirect)',
            ],
            'default' => 'results_grid',
            'separator' => 'before',
            'description' => 'Choose how this widget behaves.'
        ]);

        // Results Page Selection (only for standalone mode)
        $this->add_control( 'results_page', [
            'label' => 'Results Page',
            'type' => \Elementor\Controls_Manager::SELECT2,
            'label_block' => true,
            'options' => $this->get_all_pages(),
            'default' => '',
            'condition' => [
                'widget_mode' => 'standalone',
            ],
            'description' => 'Select the page to redirect to with search results.'
        ]);

        // Target Query ID (for targeting specific widget in tabs)
        $this->add_control( 'target_query_id', [
            'label' => 'Target Query ID',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'condition' => [
                'widget_mode' => 'standalone',
            ],
            'description' => 'Enter the Query ID of the target widget on the results page. Leave empty to use this widget\'s Query ID.'
        ]);

        // Disclaimer Notice
        $this->add_control( 'standalone_disclaimer', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<div style="background: #000000ff; border: 1px solid #ffc107; padding: 10px; border-radius: 4px; margin-top: 10px;">
                <strong>⚠️ Important:</strong><br>
                The selected results page must have this same widget placed on it with <strong>"Use Results Grid"</strong> mode enabled.<br><br>
                If using tabs, enter the <strong>Query ID</strong> of the target widget so the correct tab is automatically selected.
            </div>',
            'condition' => [
                'widget_mode' => 'standalone',
            ],
        ]);

        $repeater = new \Elementor\Repeater();
        
        // VISIBILITY TOGGLE
        $repeater->add_control( 'show_on_frontend', [ 
            'label' => 'Show on Frontend?', 
            'type' => \Elementor\Controls_Manager::SWITCHER, 
            'default' => 'yes',
            'return_value' => 'yes',
            'description' => 'If OFF: The field is hidden (CSS) but still exists in the form to affect the query.'
        ]);

        $repeater->add_control( 'filter_label', [ 'label' => 'Label', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Label' ] );
        $repeater->add_control( 'filter_placeholder', [ 'label' => 'Placeholder', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Select...' ] );
        
        $repeater->add_control( 'filter_width', [ 
            'label' => 'Width (%)', 
            'type' => \Elementor\Controls_Manager::SLIDER, 
            'size_units' => [ '%' ], 
            'range' => [ '%' => [ 'min' => 10, 'max' => 100 ] ], 
            'default' => [ 'unit' => '%', 'size' => 100 ],
            'description' => 'Desktop width. Mobile forces 100%.'
        ]);

        $repeater->add_control( 'filter_type', [ 
            'label' => 'Type', 
            'type' => \Elementor\Controls_Manager::SELECT, 
            'options' => [ 'keyword'=>'Keyword', 'taxonomy'=>'Taxonomy (Multi)', 'taxonomy_single'=>'Taxonomy (Single)', 'meta_numeric'=>'Numeric (Multi)', 'meta_range'=>'Range (Single)', 'meta_relation'=>'Relationship' ], 
            'default' => 'keyword' 
        ]);

        $repeater->add_control( 'is_project_field', [ 'label' => 'Parent Project?', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes' ] );
        
        // Taxonomy dropdown - shown for taxonomy types
        $repeater->add_control( 'filter_key', [ 
            'label' => 'Taxonomy', 
            'type' => \Elementor\Controls_Manager::SELECT2, 
            'label_block' => true,
            'options' => $this->get_all_taxonomies(),
            'condition' => [
                'filter_type' => ['taxonomy', 'taxonomy_single']
            ],
            'description' => 'Select the taxonomy to filter by.'
        ]);
        
        // Meta key text field - shown for other types
        $repeater->add_control( 'filter_meta_key', [ 
            'label' => 'Meta Key', 
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => [
                'filter_type' => ['meta_numeric', 'meta_range', 'meta_relation']
            ],
            'description' => 'Enter the meta field key.'
        ]);
        $repeater->add_control( 'custom_options', [ 'label' => 'Range Options', 'type' => \Elementor\Controls_Manager::TEXTAREA, 'condition' => ['filter_type' => 'meta_range'], 'description' => 'Format: 0-100|Label (One per line)' ] );
        
        // DEFAULT VALUE for taxonomy types - Only shown when "Show on Frontend" is OFF
        $repeater->add_control( 'default_value', [ 
            'label' => 'Default Value', 
            'type' => \Elementor\Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple' => true,
            'options' => $this->get_all_taxonomy_terms(),
            'default' => [],
            'condition' => [
                'show_on_frontend' => '',
                'filter_type' => ['taxonomy', 'taxonomy_single']
            ],
            'description' => 'Select pre-selected term(s). Use the search to find terms from any taxonomy.'
        ]);
        
        // DEFAULT VALUE for meta types - Only shown when "Show on Frontend" is OFF
        $repeater->add_control( 'default_value_meta', [ 
            'label' => 'Default Value', 
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'condition' => [
                'show_on_frontend' => '',
                'filter_type' => ['meta_numeric', 'meta_range', 'meta_relation']
            ],
            'description' => 'Pre-selected value. For multiple values, separate with commas.'
        ]);

        $this->add_control( 'filters_list', [ 
            'label' => 'Filters', 
            'type' => \Elementor\Controls_Manager::REPEATER, 
            'fields' => $repeater->get_controls(), 
            'title_field' => '{{{ filter_label }}}',
            'default' => [
                ['filter_label'=>'ADRES', 'filter_type'=>'keyword', 'show_on_frontend'=>'yes', 'filter_placeholder'=>'Zoek op adres', 'filter_width'=>['size'=>30,'unit'=>'%']],
                ['filter_label'=>'KAMERS', 'filter_type'=>'meta_numeric', 'show_on_frontend'=>'yes', 'filter_key'=>'bedroom', 'filter_placeholder'=>'Aantal kamers', 'filter_width'=>['size'=>15,'unit'=>'%']],
                ['filter_label'=>'PLAATS', 'filter_type'=>'taxonomy', 'show_on_frontend'=>'yes', 'filter_key'=>'location', 'filter_placeholder'=>'Zoek op plaats', 'filter_width'=>['size'=>15,'unit'=>'%']],
                ['filter_label'=>'OPPERVLAKTE', 'filter_type'=>'meta_range', 'show_on_frontend'=>'yes', 'filter_key'=>'area_size', 'filter_placeholder'=>'m²', 'filter_width'=>['size'=>15,'unit'=>'%'], 'custom_options'=>"0-50|0-50\n50-100|50-100\n100-150|100-150\n150+|150+"],
                ['filter_label'=>'HUURPRIJS', 'filter_type'=>'meta_range', 'show_on_frontend'=>'yes', 'filter_key'=>'price', 'filter_placeholder'=>'Per Maand', 'filter_width'=>['size'=>15,'unit'=>'%'], 'custom_options'=>"0-1000|Tot €1.000\n1000-1500|€1.000 - €1.500\n1500-2000|€1.500 - €2.000\n2000+|€2.000+"],
                ['filter_label'=>'WONINGTYPE', 'filter_type'=>'taxonomy', 'show_on_frontend'=>'yes', 'filter_key'=>'housing_type', 'filter_placeholder'=>'Selecteer woningtype', 'filter_width'=>['size'=>20,'unit'=>'%']],
            ]
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $query_id = $settings['query_id'];
        $filters = $settings['filters_list'];
        $project_ids = isset($settings['project_ids']) ? $settings['project_ids'] : [];
        $widget_mode = isset($settings['widget_mode']) ? $settings['widget_mode'] : 'results_grid';
        $results_page_id = isset($settings['results_page']) ? $settings['results_page'] : '';
        $results_page_url = $results_page_id ? get_permalink($results_page_id) : '';
        
        $dynamic_data = $this->get_broad_dynamic_options($filters, $project_ids); 

        ?>
        <div class="property-search-form-wrapper"
             data-query-id="<?php echo esc_attr($query_id); ?>"
             data-scope-ids='<?php echo esc_attr(json_encode($project_ids)); ?>'
             data-widget-mode="<?php echo esc_attr($widget_mode); ?>"
             data-results-url="<?php echo esc_url($results_page_url); ?>"
             data-target-query-id="<?php echo esc_attr(!empty($settings['target_query_id']) ? $settings['target_query_id'] : ''); ?>">
             

            
            <div class="mobile-filter-toggle" id="toggle-<?php echo esc_attr($query_id); ?>">
                <span class="toggle-text">ZOEKEN</span>
                <svg class="toggle-icon" width="14" height="9" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L7 7L13 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <form id="search-form-<?php echo esc_attr($query_id); ?>" class="live-search-form" onsubmit="return false;">
                <div class="form-fields-wrapper">
                    
                    <?php foreach($filters as $index => $filter) : 
                        
                        // --- VISIBILITY LOGIC ---
                        //  render it but hide it via CSS.
                        $is_hidden = ( isset($filter['show_on_frontend']) && $filter['show_on_frontend'] !== 'yes' );
                        $hidden_style = $is_hidden ? 'display: none !important;' : '';

                        $type = $filter['filter_type'];
                        // Use filter_key for taxonomy types, filter_meta_key for others
                        $key = in_array($type, ['taxonomy', 'taxonomy_single']) 
                            ? (isset($filter['filter_key']) ? $filter['filter_key'] : '') 
                            : (isset($filter['filter_meta_key']) ? $filter['filter_meta_key'] : '');
                        $is_project = ($filter['is_project_field'] === 'yes') ? '_project' : '';
                        $input_name = $type . $is_project . '__' . $key;
                        $data_key = ($is_project ? 'project_' : '') . $key;
                        $label = $filter['filter_label'];
                        $placeholder = $filter['filter_placeholder'];
                        $safe_id = $query_id . '_' . $index;
                        
                        // Get default value for hidden filters
                        // For taxonomy types, default_value is an array from SELECT2 (but could also be string)
                        // For meta types, default_value_meta is a text field
                        if (in_array($type, ['taxonomy', 'taxonomy_single'])) {
                            $raw_default = isset($filter['default_value']) ? $filter['default_value'] : [];
                            // Handle both array and string formats
                            if (is_array($raw_default)) {
                                $default_values = array_filter($raw_default); // Remove empty values
                            } elseif (is_string($raw_default) && !empty($raw_default)) {
                                $default_values = array_map('trim', explode(',', $raw_default));
                            } else {
                                $default_values = [];
                            }
                        } else {
                            $default_value = isset($filter['default_value_meta']) ? trim($filter['default_value_meta']) : '';
                            $default_values = $default_value ? array_map('trim', explode(',', $default_value)) : [];
                        }
                        
                        $width = isset($filter['filter_width']['size']) ? $filter['filter_width']['size'] . '%' : 'auto';
                        $base_style = ($width !== 'auto' && $width !== '100%') ? "flex: 0 0 $width; max-width: $width;" : "flex: 1 1 150px;"; 
                        
                        // Combine Styles
                        $final_style = $base_style . $hidden_style;
                    ?>
                        <div class="filter-group type-<?php echo $type; ?>" id="group-<?php echo $safe_id; ?>" style="<?php echo esc_attr($final_style); ?>">
                            <label for="input-<?php echo $safe_id; ?>"><?php echo esc_html($label); ?></label>
                            
                            <?php if($type === 'keyword'): ?>
                                <div class="input-wrapper"><input type="text" id="input-<?php echo $safe_id; ?>" name="keyword" class="no-enter" placeholder="<?php echo esc_attr($placeholder); ?>"></div>
                            
                            <?php elseif($type === 'meta_range'): ?>
                                <div class="select-wrapper">
                                    <select name="<?php echo esc_attr($input_name); ?>" id="input-<?php echo $safe_id; ?>">
                                        <option value=""><?php echo esc_html($placeholder); ?></option>
                                        <?php 
                                        foreach(explode("\n", $filter['custom_options']) as $line): 
                                            $p=explode('|',$line); 
                                            if(count($p)==2) {
                                                $opt_value = trim($p[0]);
                                                $opt_label = trim($p[1]);
                                                $selected = ($is_hidden && in_array($opt_value, $default_values)) ? 'selected' : '';
                                                echo "<option value='" . esc_attr($opt_value) . "' $selected>" . esc_html($opt_label) . "</option>";
                                            }
                                        endforeach; 
                                        ?>
                                    </select>
                                </div>

                            <?php elseif($type === 'taxonomy_single'): ?>
                                <?php 
                                
                                // For hidden filters with defaults, output hidden inputs directly
                                if ($is_hidden && !empty($default_values)): 
                                ?>
                                    <?php foreach($default_values as $dv): ?>
                                        <input type="hidden" name="<?php echo esc_attr($input_name); ?>" value="<?php echo esc_attr($dv); ?>">
                                    <?php endforeach; ?>
                                <?php elseif($is_hidden): ?>
                                    <!-- Hidden filter but no default values set -->
                                <?php else: ?>
                                    <?php $options = !empty($dynamic_data[$data_key]) ? $dynamic_data[$data_key] : []; ?>
                                    <div class="select-wrapper">
                                        <select name="<?php echo esc_attr($input_name); ?>" id="input-<?php echo $safe_id; ?>">
                                            <option value=""><?php echo esc_html($placeholder); ?></option>
                                            <?php foreach($options as $v => $l): 
                                                // Pre-select default values for VISIBLE dropdowns too
                                                $selected = (!empty($default_values) && in_array($v, $default_values)) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo esc_attr($v); ?>" <?php echo $selected; ?>><?php echo esc_html($l); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>

                            <?php else: // MULTI-SELECT DROPDOWN ?>
                                <?php 
                                // For hidden filters with defaults, just output hidden inputs directly
                                if ($is_hidden && !empty($default_values)): 
                                ?>
                                    <?php foreach($default_values as $dv): ?>
                                        <input type="hidden" name="<?php echo esc_attr($input_name); ?>[]" value="<?php echo esc_attr($dv); ?>">
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php $options = !empty($dynamic_data[$data_key]) ? $dynamic_data[$data_key] : []; ?>
                                    <div class="custom-multiselect" data-name="<?php echo esc_attr($input_name); ?>" data-default="">
                                        <select name="<?php echo esc_attr($input_name); ?>[]" multiple style="display:none;">
                                            <?php foreach($options as $v => $l): 
                                                $selected = ($is_hidden && in_array($v, $default_values)) ? 'selected' : '';
                                            ?><option value="<?php echo esc_attr($v); ?>" <?php echo $selected; ?>><?php echo esc_html($l); ?></option><?php endforeach; ?>
                                        </select>
                                        <div class="multi-trigger" id="input-<?php echo $safe_id; ?>" role="button" tabindex="0" aria-label="<?php echo esc_attr($label); ?>">
                                            <span class="trigger-text"><?php echo esc_html($placeholder); ?></span>
                                            <span class="custom-arrow" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2210%22%20height%3D%226%22%20viewBox%3D%220%200%2010%206%22%20fill%3D%22none%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M1%201L5%205L9%201%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%221.5%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E'); width: 10px; height: 6px; display: inline-block;"></span>
                                        </div>
                                        <div class="multi-dropdown">
                                            <div class="multi-search-wrapper"><input type="text" class="multi-search-input" placeholder="Search..."></div>
                                            <ul class="multi-list">
                                                <?php foreach($options as $v => $l): 
                                                    $selected_class = ($is_hidden && in_array($v, $default_values)) ? 'selected' : '';
                                                ?>
                                                    <li data-value="<?php echo esc_attr($v); ?>" class="<?php echo $selected_class; ?>"><span class="item-label"><?php echo esc_html($l); ?></span><span class="item-check"></span></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="filter-group submit-group" style="flex: 1; min-width: 120px;">
                        <label>&nbsp;</label>
                        <button type="button" id="btn-<?php echo esc_attr($query_id); ?>" class="elementor-button search-btn">ZOEKEN</button>
                    </div>

                </div>
            </form>
        </div>

        <?php
    }

    private function get_project_posts() {
        $projects = get_posts([
            'post_type' => 'project',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        $options = [];
        foreach ($projects as $project) {
            $options[$project->ID] = html_entity_decode($project->post_title);
        }
        return $options;
    }

    private function get_all_taxonomies() {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        $options = ['' => '-- Select taxonomy --'];
        
        // Exclude some common WordPress core taxonomies that aren't useful
        $exclude = ['post_tag', 'post_format', 'nav_menu', 'link_category', 'wp_theme', 'wp_template_part_area'];
        
        foreach ($taxonomies as $taxonomy) {
            if (!in_array($taxonomy->name, $exclude)) {
                $options[$taxonomy->name] = $taxonomy->label . ' (' . $taxonomy->name . ')';
            }
        }
        return $options;
    }

    private function get_all_taxonomy_terms() {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        $options = [];
        
        // Exclude some common WordPress core taxonomies that aren't useful
        $exclude = ['post_tag', 'post_format', 'nav_menu', 'link_category', 'wp_theme', 'wp_template_part_area'];
        
        foreach ($taxonomies as $taxonomy) {
            if (!in_array($taxonomy->name, $exclude)) {
                $terms = get_terms([
                    'taxonomy' => $taxonomy->name,
                    'hide_empty' => false,
                ]);
                
                if (!is_wp_error($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        // Use taxonomy prefix to help identify which taxonomy the term belongs to
                        $options[$term->slug] = $term->name . ' (' . $taxonomy->label . ')';
                    }
                }
            }
        }
        
        // Sort alphabetically by label
        asort($options);
        
        return $options;
    }

    private function get_all_pages() {
        $pages = get_posts([
            'post_type' => 'page',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        $options = [ '' => '-- Select a page --' ];
        foreach ($pages as $page) {
            $options[$page->ID] = $page->post_title;
        }
        return $options;
    }

    private function get_broad_dynamic_options($filters, $project_ids = []) {
        // Smart Caching with Versioning Strategy
        $data_version = get_option('hello_biz_property_data_version', '1.0');
        $cache_key = 'hbiz_prop_opts_' . md5(json_encode($filters) . json_encode($project_ids) . $data_version);
        
        $cached_data = get_transient($cache_key);
        if ($cached_data !== false) {
            return $cached_data;
        }

        global $wpdb; 
        $meta = $wpdb->prefix.'postmeta'; 
        $data = [];
        
        // If specific projects are selected, use them directly
        // Otherwise, get all projects
        if (!empty($project_ids) && is_array($project_ids)) {
            $proj_ids = array_map('intval', $project_ids);
        } else {
            $proj_ids = get_posts(['post_type'=>'project','posts_per_page'=>-1,'fields'=>'ids']);
        }
        $proj_str = implode(',', $proj_ids);
        
        $p_str = ''; 
        if($proj_ids) { 
            $meta_q = ['relation'=>'OR']; 
            foreach($proj_ids as $pid) $meta_q[] = ['key'=>'parent_project','value'=>'"'.$pid.'"','compare'=>'LIKE']; 
            $p_ids = get_posts(['post_type'=>'property','posts_per_page'=>-1,'fields'=>'ids','meta_query'=>$meta_q]); 
            $p_str = implode(',', $p_ids); 
        }
        
        foreach($filters as $f) {
            // Use filter_key for taxonomy types, filter_meta_key for others
            $type = $f['filter_type'];
            $key = in_array($type, ['taxonomy', 'taxonomy_single']) 
                ? (isset($f['filter_key']) ? $f['filter_key'] : '') 
                : (isset($f['filter_meta_key']) ? $f['filter_meta_key'] : '');
            if(!$key) continue;
            $is_proj = ($f['is_project_field'] === 'yes'); 
            $ids = $is_proj ? $proj_str : $p_str; 
            if(empty($ids)) continue;
            
            $d_key = ($is_proj ? 'project_' : '') . $key;
            
            if($f['filter_type'] == 'taxonomy' || $f['filter_type'] == 'taxonomy_single') { 
                $terms = wp_get_object_terms(explode(',',$ids), $key); 
                if(!is_wp_error($terms)) foreach($terms as $t) $data[$d_key][$t->slug] = $t->name; 
            }
            elseif($f['filter_type'] == 'meta_numeric') { 
                $vals = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT meta_value FROM $meta WHERE meta_key=%s AND post_id IN ($ids) ORDER BY meta_value+0 ASC",$key)); 
                $vals = array_filter($vals, function($v){ return $v !== '' && $v !== null; }); 
                if(!empty($vals)) $data[$d_key] = array_combine($vals, $vals); 
            }
            elseif($f['filter_type'] == 'meta_relation') { 
                $raw = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT meta_value FROM $meta WHERE meta_key=%s AND post_id IN ($ids)",$key)); 
                $clean=[]; 
                foreach($raw as $r){ 
                    if(is_serialized($r)) { 
                        $u=unserialize($r); 
                        if(is_array($u))$clean=array_merge($clean,$u); 
                    } else $clean[]=$r; 
                } 
                $clean=array_unique($clean); 
                foreach($clean as $id) { 
                    if(is_numeric($id)) $data[$d_key][$id] = get_the_title($id); 
                } 
            }
        }
        
        // Cache for 7 days (invalidation handles updates)
        set_transient($cache_key, $data, 7 * DAY_IN_SECONDS);
        
        return $data;
    }
}
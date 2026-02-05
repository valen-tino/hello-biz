<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ==============================================================================
 * PART 1: REGISTER CUSTOM DYNAMIC TAGS
 * ==============================================================================
 */
add_action( 'elementor/dynamic_tags/register', function( $dynamic_tags_manager ) {

	if ( ! class_exists( '\Elementor\Core\DynamicTags\Tag' ) ) {
		return;
	}

	/**
	 * TAG 1: REPEATER TEXT (For Floorplans/Features Loop)
	 * Uses get_sub_field()
	 */
	class ACF_Repeater_Text_Tag extends \Elementor\Core\DynamicTags\Tag {
		public function get_name() { return 'acf-repeater-text'; }
		public function get_title() { return 'ACF Repeater Text'; }
		public function get_group() { return 'acf'; }
		public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ]; }

		protected function register_controls() {
			$this->add_control('sub_field_key', [
				'label' => 'Sub Field Name',
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'Enter the exact slug (e.g., floorplan_title)',
			]);
		}

		public function render() {
			$key = $this->get_settings('sub_field_key');
			if( function_exists('get_sub_field') && $key ) {
				echo wp_kses_post( get_sub_field($key) );
			}
		}
	}

	/**
	 * TAG 2: REPEATER IMAGE (For Floorplans/Features Loop)
	 * Uses get_sub_field() and formats as Array
	 */
	class ACF_Repeater_Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag {
		public function get_name() { return 'acf-repeater-image'; }
		public function get_title() { return 'ACF Repeater Image'; }
		public function get_group() { return 'acf'; }
		public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY ]; }

		protected function register_controls() {
			$this->add_control('sub_field_key', [
				'label' => 'Sub Field Name',
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'Enter the exact slug (e.g., floorplan_image)',
			]);
		}

		public function get_value( array $options = [] ) {
			$key = $this->get_settings('sub_field_key');
			if( ! function_exists('get_sub_field') || ! $key ) return [];

			$value = get_sub_field($key);

			if ( is_numeric( $value ) ) {
				return [ 'id' => $value, 'url' => wp_get_attachment_image_url( $value, 'full' ) ];
			}
			if ( is_array( $value ) && isset( $value['url'] ) ) return $value;
			if ( is_string( $value ) ) return [ 'id' => null, 'url' => $value ];

			return [];
		}
	}

	/**
	 * TAG 3: LINKED TEXT (For Rental Agent Email/Phone/Name)
	 * Uses get_field() on the ACTIVE post (respects context switch)
	 */
	class ACF_Linked_Text_Tag extends \Elementor\Core\DynamicTags\Tag {
		public function get_name() { return 'acf-linked-text'; }
		public function get_title() { return 'ACF Linked Text'; }
		public function get_group() { return 'acf'; }
		public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ]; }

		protected function register_controls() {
			$this->add_control('field_key', [
				'label' => 'Field Name',
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'Enter the field slug from the Agent post type (e.g. email, phone)',
			]);
		}

		public function render() {
			$key = $this->get_settings('field_key');
			// get_field() without ID fetches from current global post (which is the Agent)
			if( function_exists('get_field') && $key ) {
				echo wp_kses_post( get_field($key) );
			}
		}
	}

	/**
	 * TAG 4: LINKED URL (For Rental Agent Button Links)
	 * Uses get_field() and cleans it for href usage
	 */
	class ACF_Linked_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {
		public function get_name() { return 'acf-linked-url'; }
		public function get_title() { return 'ACF Linked URL'; }
		public function get_group() { return 'acf'; }
		public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ]; }

		protected function register_controls() {
			$this->add_control('field_key', [
				'label' => 'Field Name',
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'Enter the field slug (e.g. email, website_link)',
			]);
		}

		public function get_value( array $options = [] ) {
			$key = $this->get_settings('field_key');
			if( function_exists('get_field') && $key ) {
				$val = get_field($key);
				// If it's an array (like a Link Object), return the URL part
				if( is_array($val) && isset($val['url']) ) return $val['url'];
				// Otherwise return string
				return (string)$val;
			}
			return '';
		}
	}

	// Register All 4 Tags
	$dynamic_tags_manager->register( new ACF_Repeater_Text_Tag() );
	$dynamic_tags_manager->register( new ACF_Repeater_Image_Tag() );
	$dynamic_tags_manager->register( new ACF_Linked_Text_Tag() );
	$dynamic_tags_manager->register( new ACF_Linked_URL_Tag() );

} );


/**
 * ==============================================================================
 * PART 2: REGISTER WIDGETS
 * ==============================================================================
 */
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	
	if ( ! function_exists('have_rows') || ! class_exists( '\Elementor\Widget_Base' ) ) {
		return;
	}

	function get_elementor_templates_list_safe() {
		$templates = get_posts( [
			'post_type' => 'elementor_library', 'posts_per_page' => -1, 'post_status' => 'publish',
		] );
		$options = [ '0' => 'Select Template' ];
		if ( ! empty( $templates ) ) {
			foreach ( $templates as $template ) {
				$options[ $template->ID ] = $template->post_title; 
			}
		}
		return $options;
	}

	/**
	 * WIDGET 1: ACF REPEATER LOOP
	 */
	class ACF_Repeater_Loop_Widget extends \Elementor\Widget_Base {
		public function get_name() { return 'acf_repeater_loop'; }
		public function get_title() { return esc_html__( 'ACF Repeater Loop', 'hello-biz' ); }
		public function get_icon() { return 'eicon-loop-builder'; }
		public function get_categories() { return [ 'general' ]; }
		
		protected function register_controls() {
			$this->start_controls_section( 'section_content', [ 'label' => 'Repeater Settings' ] );
			$this->add_control( 'repeater_field_name', [ 'label' => 'Repeater Field Name', 'type' => \Elementor\Controls_Manager::TEXT ]);
			$this->add_control( 'template_id', [ 'label' => 'Select Template', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => get_elementor_templates_list_safe(), 'default' => '0' ]);
			$this->end_controls_section();

			$this->start_controls_section( 'section_style', [ 'label' => 'Layout', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
			$this->add_responsive_control( 'grid_columns', [ 'label' => 'Columns', 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 1, 'max' => 6, 'desktop_default' => 3, 'tablet_default' => 2, 'mobile_default' => 1, 'selectors' => [ '{{WRAPPER}} .acf-repeater-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ] ]);
			$this->add_responsive_control( 'grid_gap', [ 'label' => 'Gap', 'type' => \Elementor\Controls_Manager::SLIDER, 'default' => [ 'size' => 20, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .acf-repeater-grid' => 'gap: {{SIZE}}{{UNIT}};' ] ]);
			$this->end_controls_section();
		}

		protected function render() {
			$settings = $this->get_settings_for_display();
			$template_id = (int)$settings['template_id'];
			$field_name = $settings['repeater_field_name'];

			if ( empty( $field_name ) || empty( $template_id ) ) return;
			if ( $template_id === get_the_ID() ) { echo 'Error: Recursion loop detected.'; return; }

			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
				$css_file->print_css();
			}

			if ( have_rows( $field_name ) ) {
				echo '<div class="acf-repeater-grid" style="display: grid;">';
				while ( have_rows( $field_name ) ) {
					the_row(); 
					echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
				}
				echo '</div>';
			}
		}
	}

	/**
	 * WIDGET 2: ACF LINKED POST
	 */
	class ACF_Linked_Post_Widget extends \Elementor\Widget_Base {
		public function get_name() { return 'acf_linked_post'; }
		public function get_title() { return esc_html__( 'ACF Linked Post', 'hello-biz' ); }
		public function get_icon() { return 'eicon-post-list'; }
		public function get_categories() { return [ 'general' ]; }
		
		protected function register_controls() {
			$this->start_controls_section( 'section_content', [ 'label' => 'Linked Post Settings' ] );

			// The Field Name for the Target Post (e.g., rental_agent)
			$this->add_control( 'linked_field_name', [ 
				'label' => 'Target Field Name', 
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'The field name of the Post Object you want to display (e.g., rental_agent)',
			]);

			// The Template to Render
			$this->add_control( 'template_id', [ 
				'label' => 'Select Template', 
				'type' => \Elementor\Controls_Manager::SELECT, 
				'options' => get_elementor_templates_list_safe(), 
				'default' => '0' 
			]);

			$this->add_control( 'separator_1', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );

			// Deep Fetch Logic (Enables "Chain of Command Flow")
			$this->add_control( 'use_parent_logic', [
				'label' => 'Get from Parent Project?',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'default' => 'no',
				'description' => 'Enable this if the Agent is assigned to the PROJECT, not this specific Unit.',
			]);

			$this->add_control( 'parent_field_name', [
				'label' => 'Parent Relationship Field',
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'parent_project',
				'condition' => [ 'use_parent_logic' => 'yes' ],
				'description' => 'The field on this page that links to the Project (e.g., parent_project).',
			]);
			$this->end_controls_section();
		}

		protected function render() {
			$settings = $this->get_settings_for_display();
			$template_id = (int)$settings['template_id'];
			$target_field = $settings['linked_field_name']; // e.g., rental_agent

			if ( empty( $target_field ) || empty( $template_id ) ) return;

			// 1. Resolve the ID
			$final_linked_id = 0;

			// PRIORITY CHECK: If this property has "Verhuurd" status, use Verhuurd rental agent
		// This overrides both direct and parent project logic
		$current_post_id = get_the_ID();
		$current_post_type = get_post_type( $current_post_id );
		
		// In Elementor editor/preview, try to get the actual post being previewed
		if ( ! $current_post_id || $current_post_type === 'elementor_library' ) {
			// Try to get from URL parameter (Elementor preview)
			if ( isset( $_GET['preview_id'] ) ) {
				$current_post_id = intval( $_GET['preview_id'] );
				$current_post_type = get_post_type( $current_post_id );
			} elseif ( isset( $_GET['post'] ) ) {
				$current_post_id = intval( $_GET['post'] );
				$current_post_type = get_post_type( $current_post_id );
			}
		}
		
		if ( 'property' === $current_post_type && 'rental_agent' === $target_field ) {
			$availability_terms = get_the_terms( $current_post_id, 'avaliability-status' );
			$is_verhuurd = false;
			
			if ( ! empty( $availability_terms ) && ! is_wp_error( $availability_terms ) ) {
				foreach ( $availability_terms as $term ) {
					if ( strtolower( $term->slug ) === 'verhuurd' ) {
						$is_verhuurd = true;
						break;
					}
				}
			}
			
			// If property is Verhuurd, force the Verhuurd rental agent
			if ( $is_verhuurd ) {
				$verhuurd_agent = get_posts( array(
					'post_type'   => 'rental-agent',
					'name'        => 'verhuurd',
					'post_status' => 'publish',
					'numberposts' => 1,
				) );
				
				if ( ! empty( $verhuurd_agent ) ) {
					$final_linked_id = $verhuurd_agent[0]->ID;
				}
			}
		}
		
			// If not Verhuurd or Verhuurd agent not found, proceed with normal logic
			if ( ! $final_linked_id ) {
				if ( 'yes' === $settings['use_parent_logic'] ) {
					// Path B: The "Double Jump" (Property -> Project -> Agent)
					$parent_field = $settings['parent_field_name']; // e.g., parent_project
					
					// 1. Get Project ID from Property
					$project_id = get_post_meta( get_the_ID(), $parent_field, true );
					if ( is_array( $project_id ) ) $project_id = $project_id[0] ?? 0;

					if ( $project_id ) {
						// 2. Get Agent ID from Project
						$agent_id = get_post_meta( $project_id, $target_field, true );
						if ( is_array( $agent_id ) ) $agent_id = $agent_id[0] ?? 0;
						$final_linked_id = $agent_id;
					}

				} else {
					// Path A: Direct (Project -> Agent)
					$direct_id = get_post_meta( get_the_ID(), $target_field, true );
					if ( is_array( $direct_id ) ) $direct_id = $direct_id[0] ?? 0;
					$final_linked_id = $direct_id;
				}
			}

			// 2. Validation
			// Editor Preview Feedback
			if ( ! $final_linked_id && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div style="padding: 20px; border: 1px dashed #ccc; background:#f9f9f9;">';
				echo '<strong>Linked Post Widget</strong><br>';
				if ( 'yes' === $settings['use_parent_logic'] ) {
					echo 'Could not find Agent ID on the Parent Project.<br>';
					echo 'Checked Parent Field: <code>' . esc_html($settings['parent_field_name']) . '</code><br>';
					echo 'Checked Target Field: <code>' . esc_html($target_field) . '</code>';
				} else {
					echo 'No Linked Post found in field: <code>' . esc_html($target_field) . '</code>';
				}
				echo '</div>';
				return;
			}

			if ( ! $final_linked_id ) return; // Frontend: Hide if empty

			// 3. Render
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
				$css_file->print_css();
			}

			global $post;
			$original_post = $post;
			
			// Switch context to the final ID
			$post = get_post( $final_linked_id );
			setup_postdata( $post );

			echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );

			$post = $original_post;
			wp_reset_postdata();
		}
	}

	$widgets_manager->register( new ACF_Repeater_Loop_Widget() );
	$widgets_manager->register( new ACF_Linked_Post_Widget() );

} );
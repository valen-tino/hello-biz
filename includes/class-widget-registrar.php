<?php
/**
 * Widget Registrar Class
 * 
 * Centralizes all Elementor widget registration in a single, organized class.
 * This improves maintainability and follows single-responsibility principle.
 * 
 * @package HelloBiz
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Hello_Biz_Widget_Registrar {
    
    /**
     * Widget files to register with their class names
     */
    private static $widgets = array(
        'unit-showcase-widget.php'          => 'Unit_Type_Showcase_Widget',
        'project-floorplan-tabs.php'        => 'Project_Floorplan_Tabs_Widget',
        'project-properties-table.php'      => 'Elementor_Project_Properties_Table',
        'widget-property-search-form.php'   => 'Elementor_Property_Search_Form_Widget',
        'widget-property-results.php'       => 'Elementor_Property_Search_Results_Widget',
        'widget-property-count.php'         => 'Elementor_Property_Count_Widget',
        'widget-project-showcase.php'       => 'Elementor_Project_Showcase_Widget',
        'widget-recently-viewed-properties.php' => 'Elementor_Recently_Viewed_Properties_Widget',
    );
    
    /**
     * Initialize widget registration
     */
    public static function init() {
        add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
    }
    
    /**
     * Register all custom Elementor widgets
     * 
     * @param \Elementor\Widgets_Manager $widgets_manager
     */
    public static function register_widgets( $widgets_manager ) {
        $includes_path = HELLO_BIZ_PATH . '/includes/';
        
        foreach ( self::$widgets as $file => $class_name ) {
            $widget_file = $includes_path . $file;
            
            if ( file_exists( $widget_file ) ) {
                require_once $widget_file;
                
                // Check if class exists before instantiating
                if ( class_exists( $class_name ) ) {
                    $widgets_manager->register( new $class_name() );
                } elseif ( class_exists( '\\' . $class_name ) ) {
                    $full_class = '\\' . $class_name;
                    $widgets_manager->register( new $full_class() );
                }
            }
        }
    }
    
    /**
     * Add a widget to the registration list
     * 
     * @param string $file      Widget file name
     * @param string $class     Widget class name
     */
    public static function add_widget( $file, $class ) {
        self::$widgets[ $file ] = $class;
    }
}

// Initialize the widget registrar
Hello_Biz_Widget_Registrar::init();

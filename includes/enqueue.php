<?php
/**
 * Enqueue scripts and styles
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue global theme styles and scripts
 */
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'my-theme-main-style', get_stylesheet_uri() );
    wp_enqueue_style( 'my-theme-custom-style', get_theme_file_uri( 'style.css' ), array(), '1.0', 'all' );
    
    // Enqueue icon list filter JavaScript
    wp_enqueue_script( 
        'icon-list-filter', 
        get_theme_file_uri( 'assets/js/icon-list-filter.js' ), 
        array(), 
        '1.0', 
        true 
    );
    
    // Enqueue Multistep Blossem Form assets
    wp_enqueue_style(
        'multistep-blossem-form',
        get_theme_file_uri( 'assets/css/multistep-blossem-form.css' ),
        array(),
        '1.0.0',
        'all'
    );
    
    wp_enqueue_script(
        'multistep-blossem-form',
        get_theme_file_uri( 'assets/js/multistep-blossem-form.js' ),
        array( 'jquery' ),
        '1.0.0',
        true
    );
    
    // Localize script for Dutch formatting and translations
    wp_localize_script( 'multistep-blossem-form', 'multistepFormData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'locale' => array(
            'thousandsSeparator' => '.',
            'decimalSeparator' => ',',
            'currencySymbol' => '€'
        ),
        'i18n' => array(
            'fieldRequired' => __('This field is required.', 'hello-biz'),
            'emailsMatch' => __('Email addresses must match.', 'hello-biz'),
            'postalCodeInvalid' => __('Please enter a valid Dutch postal code (e.g., 1234 AB).', 'hello-biz'),
            'propertyTypeRequired' => __('Please select at least one property type.', 'hello-biz'),
            'submitting' => __('Submitting...', 'hello-biz'),
            'submit' => __('Yes, I want to register!', 'hello-biz'),
            'connectionError' => __('An error occurred. Please check your connection and try again.', 'hello-biz'),
            'generalError' => __('An error occurred. Please try again.', 'hello-biz'),
            'successTitle' => __('Registration Successful!', 'hello-biz')
        )
    ));
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/**
 * Enqueue Recently Viewed Properties Script
 */
function enqueue_recently_viewed_properties_script() {
    wp_enqueue_script(
        'recently-viewed-properties',
        get_theme_file_uri('assets/js/recently-viewed-properties.js'),
        array(),
        '1.0.0',
        true
    );
    
    // Get current property ID if on single property page
    $current_property_id = 0;
    if (is_singular('property')) {
        $current_property_id = get_the_ID();
    }
    
    wp_localize_script('recently-viewed-properties', 'recentlyViewedData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('recently_viewed_nonce'),
        'currentPropertyId' => $current_property_id,
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_recently_viewed_properties_script');

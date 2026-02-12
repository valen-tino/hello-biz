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
            'fieldRequired' => __('Dit veld is verplicht.', 'hello-biz'),
            'emailsMatch' => __('E-mailadressen moeten overeenkomen.', 'hello-biz'),
            'postalCodeInvalid' => __('Voer een geldige Nederlandse postcode in (bijv. 1234 AB).', 'hello-biz'),
            'propertyTypeRequired' => __('Selecteer ten minste één type woning.', 'hello-biz'),
            'submitting' => __('Versturen...', 'hello-biz'),
            'submit' => __('Ja, ik wil me inschrijven!', 'hello-biz'),
            'connectionError' => __('Er is een fout opgetreden. Controleer uw verbinding en probeer het opnieuw.', 'hello-biz'),
            'generalError' => __('Er is een fout opgetreden. Probeer het opnieuw.', 'hello-biz'),
            'successTitle' => __('Inschrijving Succesvol!', 'hello-biz')
        )
    ));
    
    // Enqueue Contact Formulier assets
    wp_enqueue_style(
        'contact-formulier',
        get_theme_file_uri( 'assets/css/contact-formulier.css' ),
        array(),
        '1.0.0',
        'all'
    );
    
    wp_enqueue_script(
        'contact-formulier',
        get_theme_file_uri( 'assets/js/contact-formulier.js' ),
        array( 'jquery' ),
        '1.0.0',
        true
    );
    
    // Localize script for Contact Formulier
    wp_localize_script( 'contact-formulier', 'contactFormData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' )
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

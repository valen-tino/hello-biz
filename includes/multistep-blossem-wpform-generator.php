<?php
/**
 * WPForms - Multistep Blossem Form Generator
 * 
 * This script programmatically creates the WPForms form that matches
 * our custom frontend Multistep Blossem template.
 * 
 * Usage: 
 * 1. Go to WordPress admin
 * 2. Navigate to your theme's settings or create a temporary page
 * 3. Add shortcode: [create_multistep_blossem_wpform]
 * 4. Visit that page once to create the form
 * 5. Remove the shortcode after form is created
 * 
 * @package Hello_Biz
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create the Multistep Blossem WPForms form programmatically
 */
function create_multistep_blossem_wpform() {
    // Check if WPForms is active
    if (!function_exists('wpforms')) {
        return '<p style="color: red;">WPForms plugin is not active. Please activate WPForms first.</p>';
    }

    // Check if form already exists
    $existing_form = get_posts(array(
        'post_type' => 'wpforms',
        'post_status' => 'publish',
        'title' => 'Multistep Blossem Registration',
        'posts_per_page' => 1
    ));

    if (!empty($existing_form)) {
        $form_id = $existing_form[0]->ID;
        return '<p style="color: green;">✅ WPForms "Multistep Blossem Registration" already exists! Form ID: <strong>' . $form_id . '</strong></p>';
    }

    // Define the form structure
    $form_data = array(
        'fields' => array(
            // ========================================
            // Step 1: Personal Information (Page 1)
            // ========================================
            
            // Page Break - Step 1 Header
            1 => array(
                'id' => '1',
                'type' => 'pagebreak',
                'title' => 'Informatie huurder',
                'nav_align' => 'center',
                'indicator' => 'circles',
                'indicator_color' => '#000000',
                'position' => 'top',
            ),
            
            // First Name
            2 => array(
                'id' => '2',
                'type' => 'name',
                'label' => 'Naam',
                'required' => '1',
                'format' => 'first-last',
                'size' => 'large',
            ),
            
            // Email
            3 => array(
                'id' => '3',
                'type' => 'email',
                'label' => 'Email',
                'required' => '1',
                'confirmation' => '1',
                'size' => 'large',
            ),
            
            // Address
            4 => array(
                'id' => '4',
                'type' => 'address',
                'label' => 'Adres',
                'required' => '1',
                'scheme' => 'international',
                'size' => 'large',
            ),
            
            // Phone
            5 => array(
                'id' => '5',
                'type' => 'phone',
                'label' => 'Telefoonnummer',
                'required' => '1',
                'format' => 'international',
                'size' => 'large',
            ),
            
            // Language
            6 => array(
                'id' => '6',
                'type' => 'select',
                'label' => 'Taal',
                'required' => '1',
                'choices' => array(
                    1 => array('label' => 'Nederlands', 'value' => 'Nederlands'),
                    2 => array('label' => 'Engels', 'value' => 'Engels'),
                ),
                'size' => 'large',
            ),
            
            // Page Break - Next to Step 2
            7 => array(
                'id' => '7',
                'type' => 'pagebreak',
                'title' => 'Informatie partner',
                'position' => 'normal',
                'prev' => 'Vorige',
                'prev_toggle' => '1',
                'next' => 'Volgende',
            ),
            
            // ========================================
            // Step 2: Partner Information (Page 2)
            // ========================================
            
            // Rent Alone Question
            8 => array(
                'id' => '8',
                'type' => 'select',
                'label' => 'Gaat u alleen huren?',
                'required' => '1',
                'choices' => array(
                    1 => array('label' => 'Ja', 'value' => 'Ja'),
                    2 => array('label' => 'Nee', 'value' => 'Nee'),
                ),
                'size' => 'large',
            ),
            
            // Partner Full Name
            9 => array(
                'id' => '9',
                'type' => 'text',
                'label' => "Volledige naam van uw partner",
                'size' => 'large',
            ),
            
            // Partner Email
            10 => array(
                'id' => '10',
                'type' => 'email',
                'label' => "E-mail van uw partner",
                'confirmation' => '1',
                'size' => 'large',
            ),
            
            // Partner Address
            11 => array(
                'id' => '11',
                'type' => 'address',
                'label' => "Adres van uw partner",
                'scheme' => 'international',
                'size' => 'large',
            ),
            
            // Partner Phone
            12 => array(
                'id' => '12',
                'type' => 'phone',
                'label' => "Telefoonnummer van uw partner",
                'format' => 'international',
                'size' => 'large',
            ),
            
            // Partner Language
            13 => array(
                'id' => '13',
                'type' => 'select',
                'label' => "Taal van uw partner",
                'choices' => array(
                    1 => array('label' => 'Nederlands', 'value' => 'Nederlands'),
                    2 => array('label' => 'Engels', 'value' => 'Engels'),
                ),
                'size' => 'large',
            ),
            
            // Page Break - Next to Step 3
            14 => array(
                'id' => '14',
                'type' => 'pagebreak',
                'title' => 'Inkomensgegevens',
                'position' => 'normal',
                'prev' => 'Vorige',
                'prev_toggle' => '1',
                'next' => 'Volgende',
            ),
            
            // ========================================
            // Step 3: Income Information (Page 3)
            // ========================================
            
            // Employer
            15 => array(
                'id' => '15',
                'type' => 'text',
                'label' => 'Werkgever',
                'size' => 'large',
            ),
            
            // Income Status
            16 => array(
                'id' => '16',
                'type' => 'select',
                'label' => 'Inkomenssituatie',
                'required' => '1',
                'choices' => array(
                    1 => array('label' => 'Onbepaalde tijd', 'value' => 'Onbepaalde tijd'),
                    2 => array('label' => 'Bepaalde tijd', 'value' => 'Bepaalde tijd'),
                    3 => array('label' => 'Zelfstandig', 'value' => 'Zelfstandig'),
                    4 => array('label' => 'Student', 'value' => 'Student'),
                ),
                'size' => 'large',
            ),
            
            // Gross Income
            17 => array(
                'id' => '17',
                'type' => 'text',
                'label' => 'Bruto inkomen',
                'required' => '1',
                'description' => 'Als u (niet) samen met een partner huurt, gebruik dan uw bruto inkomen samen. Het toegevoegde inkomen wordt gebruikt voor de minimale inkomensbepaling.',
                'size' => 'large',
            ),
            
            // Warranty Statement
            18 => array(
                'id' => '18',
                'type' => 'select',
                'label' => 'Borgstelling',
                'choices' => array(
                    1 => array('label' => 'Ja', 'value' => 'Ja'),
                    2 => array('label' => 'Nee', 'value' => 'Nee'),
                ),
                'description' => 'Als u geen vast arbeidscontract heeft, heeft u een borg nodig en vertrouwen in Nederland.',
                'size' => 'large',
            ),
            
            // Page Break - Next to Step 4
            19 => array(
                'id' => '19',
                'type' => 'pagebreak',
                'title' => 'Voorkeuren',
                'position' => 'normal',
                'prev' => 'Vorige',
                'prev_toggle' => '1',
                'next' => 'Volgende',
            ),
            
            // ========================================
            // Step 4: Preferences (Page 4)
            // ========================================
            
            // Current Living Situation
            20 => array(
                'id' => '20',
                'type' => 'select',
                'label' => 'Huidige woonsituatie',
                'required' => '1',
                'choices' => array(
                    1 => array('label' => 'Huurwoning', 'value' => 'Huurwoning'),
                    2 => array('label' => 'Koopwoning', 'value' => 'Koopwoning'),
                    3 => array('label' => 'Inwonend', 'value' => 'Inwonend'),
                    4 => array('label' => 'Studentenhuis', 'value' => 'Studentenhuis'),
                ),
                'size' => 'large',
            ),
            
            // Search for Home by Date
            21 => array(
                'id' => '21',
                'type' => 'date-time',
                'label' => 'Zoekt woning per',
                'required' => '1',
                'format' => 'date',
                'date_format' => 'd-m-Y',
                'size' => 'large',
            ),
            
            // Maximum Price
            22 => array(
                'id' => '22',
                'type' => 'text',
                'label' => 'Maximale prijs',
                'required' => '1',
                'description' => 'Het minimum wordt niet gesteld en mag niet hoger zijn dan 3 keer het inkomen.',
                'size' => 'large',
            ),
            
            // Interested In
            23 => array(
                'id' => '23',
                'type' => 'select',
                'label' => 'Geïnteresseerd in',
                'required' => '1',
                'choices' => array(
                    1 => array('label' => 'Appartement', 'value' => 'Appartement'),
                    2 => array('label' => 'Bedrijfsruimte', 'value' => 'Bedrijfsruimte'),
                    3 => array('label' => 'Gezinswoning', 'value' => 'Gezinswoning'),
                ),
                'size' => 'large',
            ),
            
            // Number of Rooms
            24 => array(
                'id' => '24',
                'type' => 'select',
                'label' => 'Aantal kamers',
                'required' => '1',
                'choices' => array(
                    1 => array('label' => '1', 'value' => '1'),
                    2 => array('label' => '2', 'value' => '2'),
                    3 => array('label' => '3', 'value' => '3'),
                    4 => array('label' => '4', 'value' => '4'),
                    5 => array('label' => '5', 'value' => '5'),
                    6 => array('label' => '6', 'value' => '6'),
                ),
                'size' => 'large',
            ),
            
            // Interior
            25 => array(
                'id' => '25',
                'type' => 'select',
                'label' => 'Interieur',
                'required' => '1',
                'choices' => array(
                    1 => array('label' => 'Gemeubileerd', 'value' => 'Gemeubileerd'),
                    2 => array('label' => 'Ongemeubileerd', 'value' => 'Ongemeubileerd'),
                ),
                'size' => 'large',
            ),
            
            // Privacy Policy Checkbox
            26 => array(
                'id' => '26',
                'type' => 'checkbox',
                'label' => 'Bevestig het volgende',
                'required' => '1',
                'choices' => array(
                    1 => array('label' => 'Ik verklaar dat de informatie die ik in het formulier heb ingevuld naar waarheid is ingevuld', 'value' => 'bevestigd'),
                    2 => array('label' => 'Ik heb het privacybeleid gelezen en ga hiermee akkoord', 'value' => 'privacy_akkoord'),
                ),
            ),
        ),
        'settings' => array(
            'form_title' => 'Multistep Blossem Registration',
            'form_desc' => 'Inschrijfformulier voor huurwoningen',
            'submit_text' => 'Ja, ik wil me inschrijven!',
            'submit_text_processing' => 'Bezig met verzenden...',
            'honeypot' => '1',
            'notification_enable' => '1',
            'notifications' => array(
                1 => array(
                    'notification_name' => 'Admin Notification',
                    'email' => '{admin_email}',
                    'subject' => 'Nieuwe Inschrijving: {field_id="2"}',
                    'sender_name' => get_bloginfo('name'),
                    'sender_address' => '{admin_email}',
                    'replyto' => '{field_id="3"}',
                    'message' => '{all_fields}',
                ),
            ),
            'confirmations' => array(
                1 => array(
                    'type' => 'message',
                    'message' => '<p>Bedankt voor uw inschrijving! We hebben uw aanvraag ontvangen en nemen binnenkort contact met u op.</p>',
                    'message_scroll' => '1',
                ),
            ),
        ),
    );

    // Create the form post
    $form_post = array(
        'post_title' => 'Multistep Blossem Registration',
        'post_status' => 'publish',
        'post_type' => 'wpforms',
        'post_content' => wpforms_encode($form_data),
    );

    $form_id = wp_insert_post($form_post);

    if (is_wp_error($form_id)) {
        return '<p style="color: red;">❌ Error creating form: ' . $form_id->get_error_message() . '</p>';
    }

    // Update the form post meta
    update_post_meta($form_id, 'wpforms_submit_button_text', 'Ja, ik wil me inschrijven!');

    // Store the form ID for reference
    update_option('multistep_blossem_wpform_id', $form_id);

    return '<p style="color: green;">✅ WPForms "Multistep Blossem Registration" created successfully! Form ID: <strong>' . $form_id . '</strong></p>
            <p>You can now:</p>
            <ul>
                <li>View the form in WPForms → All Forms</li>
                <li>Edit the form settings as needed</li>
                <li>The custom frontend template will submit to this form</li>
            </ul>';
}

/**
 * Shortcode to trigger form creation
 */
function create_multistep_blossem_wpform_shortcode() {
    // Only allow admins to create the form
    if (!current_user_can('manage_options')) {
        return '<p style="color: red;">You do not have permission to create forms.</p>';
    }

    return create_multistep_blossem_wpform();
}
add_shortcode('create_multistep_blossem_wpform', 'create_multistep_blossem_wpform_shortcode');

/**
 * Get the Multistep Blossem WPForms form ID
 */
function get_multistep_blossem_wpform_id() {
    return get_option('multistep_blossem_wpform_id', 0);
}

/**
 * Helper function to properly encode form data for WPForms
 */
if (!function_exists('wpforms_encode')) {
    function wpforms_encode($data) {
        return wp_json_encode($data);
    }
}

<?php
/**
 * Job Application Formulier Template
 * 
 * Een aangepast sollicitatieformulier met Nederlandse opmaak.
 * Dit sjabloon biedt de frontend structuur en verwerkt inzendingen via AJAX.
 * 
 * @package Hello_Biz
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the Job Application Formulier
 */
function render_job_application_formulier() {
    ob_start();
    ?>
    <div class="job-application-formulier-wrapper">
        <div class="job-application-formulier-container">
            
            <div class="form-header">
                <h2>Sollicitatieformulier</h2>
                <p>Vul onderstaand formulier in om te solliciteren op een vacature bij Blossem Group.</p>
            </div>

            <!-- Form -->
            <form id="job-application-formulier-form" class="job-application-form" method="post" enctype="multipart/form-data" novalidate>
                
                <!-- Hidden Fields for Backend Integration -->
                <input type="hidden" name="action" value="job_application_formulier_submit">
                <?php wp_nonce_field('job_application_formulier_nonce', 'job_application_nonce'); ?>
                
                <!-- Name Field (First and Last) -->
                <div class="form-row">
                    <div class="form-group half">
                        <label for="first_name">Naam <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" placeholder="Voornaam" required>
                        <span class="field-sublabel">Voornaam</span>
                    </div>
                    <div class="form-group half">
                        <label for="last_name">&nbsp;</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Achternaam" required>
                        <span class="field-sublabel">Achternaam</span>
                    </div>
                </div>

                <!-- Email Address (with confirmation) -->
                <div class="form-row">
                    <div class="form-group half">
                        <label for="email">E-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="E-mail" required>
                        <span class="field-sublabel">E-mail</span>
                    </div>
                    <div class="form-group half">
                        <label for="confirm_email">&nbsp;</label>
                        <input type="email" id="confirm_email" name="confirm_email" placeholder="E-mailadres bevestigen" required>
                        <span class="field-sublabel">E-mailadres bevestigen</span>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone">Telefoonnummer <span class="required">*</span></label>
                    <div class="phone-input-wrapper">
                        <select id="phone_country" name="phone_country" class="phone-country-select">
                            <option value="+31" data-flag="NL">🇳🇱 +31</option>
                            <option value="+32" data-flag="BE">🇧🇪 +32</option>
                            <option value="+49" data-flag="DE">🇩🇪 +49</option>
                            <option value="+44" data-flag="UK">🇬🇧 +44</option>
                        </select>
                        <input type="tel" id="phone" name="phone" placeholder="0812-345-678" required>
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address_line_1">Adres <span class="required">*</span></label>
                    <input type="text" id="address_line_1" name="address_line_1" placeholder="Adres regel 1" required>
                    <span class="field-sublabel">Straat en huisnummer</span>
                </div>

                <div class="form-group">
                    <input type="text" id="address_line_2" name="address_line_2" placeholder="Adres regel 2">
                    <span class="field-sublabel">Toevoeging (optioneel)</span>
                </div>

                <!-- City, Province/Region, Postal Code, Country -->
                <div class="form-row">
                    <div class="form-group half">
                        <input type="text" id="city" name="city" placeholder="Stad" required>
                        <span class="field-sublabel">Stad</span>
                    </div>
                    <div class="form-group half">
                        <input type="text" id="state_province" name="state_province" placeholder="Staat / Provincie / Regio">
                        <span class="field-sublabel">Staat / Provincie / Regio</span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <input type="text" id="postal_code" name="postal_code" placeholder="Postcode" required>
                        <span class="field-sublabel">Postale Code</span>
                    </div>
                    <div class="form-group half">
                        <select id="country" name="country" required>
                            <option value="">--- Selecteer land ---</option>
                            <option value="Nederland" selected>Nederland</option>
                            <option value="België">België</option>
                            <option value="Duitsland">Duitsland</option>
                            <option value="Frankrijk">Frankrijk</option>
                            <option value="Verenigd Koninkrijk">Verenigd Koninkrijk</option>
                        </select>
                        <span class="field-sublabel">Land</span>
                    </div>
                </div>

                <!-- Vacancy Selection -->
                <div class="form-group">
                    <label for="vacancy">Voor welke vacature solliciteert u? <span class="required">*</span></label>
                    <select id="vacancy" name="vacancy" required>
                        <option value="">Kies een vacature</option>
                        <?php
                        // Get all published vacancies
                        $vacancies = get_posts(array(
                            'post_type' => 'vacancy',
                            'post_status' => 'publish',
                            'posts_per_page' => -1,
                            'orderby' => 'menu_order',
                            'order' => 'ASC',
                        ));
                        
                        if (!empty($vacancies)) {
                            foreach ($vacancies as $vacancy) {
                                echo '<option value="' . esc_attr($vacancy->post_title) . '">' . esc_html($vacancy->post_title) . '</option>';
                            }
                        }
                        ?>
                        <option value="Open Sollicitatie">Open Sollicitatie</option>
                    </select>
                </div>

                <!-- Expected Salary -->
                <div class="form-group">
                    <label for="expected_salary">Wat is uw gewenste salaris (in EUR)? <span class="required">*</span></label>
                    <input type="text" id="expected_salary" name="expected_salary" class="dutch-number-input" placeholder="€ 2.500,00" required>
                </div>

                <!-- How did you find out about this position -->
                <div class="form-group">
                    <label>Hoe heeft u deze vacature gevonden? <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="source" value="Huidige medewerker" required>
                            <span>Huidige medewerker</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="source" value="Zoekmachine" required>
                            <span>Zoekmachine</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="source" value="Social Media" required>
                            <span>Social Media</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="source" value="Anders" required>
                            <span>Anders</span>
                        </label>
                    </div>
                </div>

                <!-- Upload CV -->
                <div class="form-group">
                    <label for="cv_upload">Upload je CV <span class="required">*</span></label>
                    <input type="file" id="cv_upload" name="cv_upload" accept=".pdf,.doc,.docx" required>
                    <p class="field-description">Toegestane bestandstypen: PDF, DOC, DOCX (Max. 5MB)</p>
                </div>

                <!-- Cover Letter (Optional textarea) -->
                <div class="form-group">
                    <label for="cover_letter">Motivatiebrief</label>
                    <textarea id="cover_letter" name="cover_letter" rows="6" placeholder="Vertel ons waarom u geschikt bent voor deze functie..."></textarea>
                </div>

                <!-- Additional Information (Optional textarea) -->
                <div class="form-group">
                    <label for="additional_info">Aanvullende informatie</label>
                    <textarea id="additional_info" name="additional_info" rows="4" placeholder="Overige informatie die u met ons wilt delen..."></textarea>
                </div>

                <!-- Privacy Policy Checkbox -->
                <div class="form-group">
                    <label class="checkbox-option">
                        <input type="checkbox" name="privacy_policy" id="privacy_policy" required>
                        <span>Ik heb het <a href="<?php echo esc_url(get_privacy_policy_url()); ?>" target="_blank">privacybeleid</a> gelezen en ga hiermee akkoord</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">Versturen</button>
                </div>

            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode to display the job application form
 */
function job_application_formulier_shortcode() {
    return render_job_application_formulier();
}
add_shortcode('job_application_formulier', 'job_application_formulier_shortcode');

/**
 * AJAX Handler for Job Application Formulier Submission
 */
add_action('wp_ajax_job_application_formulier_submit', 'handle_job_application_formulier_submit');
add_action('wp_ajax_nopriv_job_application_formulier_submit', 'handle_job_application_formulier_submit');

function handle_job_application_formulier_submit() {
    // Verify nonce
    if (!isset($_POST['job_application_nonce']) || !wp_verify_nonce($_POST['job_application_nonce'], 'job_application_formulier_nonce')) {
        wp_send_json_error(array('message' => __('Beveiligingscontrole mislukt. Vernieuw de pagina en probeer het opnieuw.', 'hello-biz')));
    }

    // Rate limiting: 1 submission per 30 seconds per IP
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $rate_limit_key = 'job_application_form_rate_' . md5($ip_address);
    $rate_limit_seconds = 30;
    
    if (get_transient($rate_limit_key)) {
        wp_send_json_error(array(
            'message' => __('U heeft recent al een sollicitatie ingediend. Wacht alstublieft 30 seconden voordat u opnieuw probeert.', 'hello-biz')
        ));
    }
    
    // Set rate limit transient
    set_transient($rate_limit_key, true, $rate_limit_seconds);

    // Handle file upload
    $cv_file_id = null;
    if (!empty($_FILES['cv_upload']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $file = $_FILES['cv_upload'];
        
        // Validate file size (5MB max)
        if ($file['size'] > 5242880) {
            wp_send_json_error(array('message' => __('Het CV bestand is te groot. Maximale grootte is 5MB.', 'hello-biz')));
        }

        // Validate file type using WordPress file extension check (not the client-supplied MIME type)
        $file_type = wp_check_filetype($file['name']);
        $allowed_extensions = array('pdf', 'doc', 'docx');
        
        if (!$file_type['ext'] || !in_array($file_type['ext'], $allowed_extensions)) {
            wp_send_json_error(array('message' => __('Ongeldig bestandstype. Alleen PDF, DOC en DOCX zijn toegestaan.', 'hello-biz')));
        }

        // Upload file
        $upload_overrides = array('test_form' => false);
        $uploaded_file = wp_handle_upload($file, $upload_overrides);

        if (isset($uploaded_file['error'])) {
            wp_send_json_error(array('message' => __('Fout bij het uploaden van het CV: ', 'hello-biz') . $uploaded_file['error']));
        }

        // Create attachment
        $attachment = array(
            'post_mime_type' => $uploaded_file['type'],
            'post_title' => sanitize_file_name($file['name']),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $cv_file_id = wp_insert_attachment($attachment, $uploaded_file['file']);
    }

    // Collect form data
    $form_data = array(
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'confirm_email' => sanitize_email($_POST['confirm_email'] ?? ''),
        'phone_country' => sanitize_text_field($_POST['phone_country'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'address_line_1' => sanitize_text_field($_POST['address_line_1'] ?? ''),
        'address_line_2' => sanitize_text_field($_POST['address_line_2'] ?? ''),
        'city' => sanitize_text_field($_POST['city'] ?? ''),
        'state_province' => sanitize_text_field($_POST['state_province'] ?? ''),
        'postal_code' => sanitize_text_field($_POST['postal_code'] ?? ''),
        'country' => sanitize_text_field($_POST['country'] ?? ''),
        'vacancy' => sanitize_text_field($_POST['vacancy'] ?? ''),
        'expected_salary' => sanitize_text_field($_POST['expected_salary'] ?? ''),
        'source' => sanitize_text_field($_POST['source'] ?? ''),
        'cv_file_id' => $cv_file_id,
        'cover_letter' => sanitize_textarea_field($_POST['cover_letter'] ?? ''),
        'additional_info' => sanitize_textarea_field($_POST['additional_info'] ?? ''),
        'privacy_policy' => isset($_POST['privacy_policy']) ? 'Yes' : 'No',
    );

    // Store submission as custom post type
    $submission_id = wp_insert_post(array(
        'post_type' => 'job_application',
        'post_title' => $form_data['first_name'] . ' ' . $form_data['last_name'] . ' - ' . $form_data['vacancy'] . ' - ' . date('Y-m-d H:i:s'),
        'post_status' => 'publish',
        'meta_input' => $form_data,
    ));

    if (is_wp_error($submission_id)) {
        // Fallback: Store in options table if custom post type doesn't exist
        $submissions = get_option('job_application_formulier_submissions', array());
        $form_data['submitted_at'] = current_time('mysql');
        // Anonymize IP for GDPR compliance
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($ip)) {
            $form_data['ip_address'] = preg_replace('/\.\d+$/', '.0', $ip);
        }
        $submissions[] = $form_data;
        update_option('job_application_formulier_submissions', $submissions);
        $submission_id = count($submissions);
    }

    // Schedule async email sending for better performance
    $email_data = array(
        'form_data' => $form_data,
        'submission_id' => $submission_id,
    );
    
    // Store email data in transient for the scheduled event
    $transient_key = 'job_application_email_' . $submission_id;
    set_transient($transient_key, $email_data, HOUR_IN_SECONDS);
    
    // Schedule the email to be sent immediately in the background
    wp_schedule_single_event(time(), 'send_job_application_formulier_email', array($transient_key));
    
    // Spawn cron immediately to send the email
    spawn_cron();

    // Return success immediately (email sends in background)
    wp_send_json_success(array(
        'message' => __('Bedankt voor uw sollicitatie! We hebben uw aanvraag ontvangen en nemen spoedig contact met u op.', 'hello-biz'),
        'submission_id' => $submission_id,
    ));
}

/**
 * Async Email Handler - Sends job application emails in the background
 */
add_action('send_job_application_formulier_email', 'handle_job_application_formulier_email');
function handle_job_application_formulier_email($transient_key) {
    // Retrieve email data from transient
    $email_data = get_transient($transient_key);
    
    if (!$email_data) {
        return; // Data expired or doesn't exist
    }
    
    $form_data = $email_data['form_data'];
    $submission_id = $email_data['submission_id'];
    
    // Send admin notification email
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    
    $subject = sprintf('[%s] Nieuwe Sollicitatie: %s %s - %s', $site_name, $form_data['first_name'], $form_data['last_name'], $form_data['vacancy']);
    
    $message = "Er is een nieuwe sollicitatie ontvangen:\n\n";
    $message .= "=== PERSOONLIJKE GEGEVENS ===\n";
    $message .= "Naam: {$form_data['first_name']} {$form_data['last_name']}\n";
    $message .= "E-mail: {$form_data['email']}\n";
    $message .= "Telefoon: {$form_data['phone_country']} {$form_data['phone']}\n";
    $message .= "Adres: {$form_data['address_line_1']}\n";
    if ($form_data['address_line_2']) {
        $message .= "       {$form_data['address_line_2']}\n";
    }
    $message .= "       {$form_data['postal_code']} {$form_data['city']}\n";
    if ($form_data['state_province']) {
        $message .= "       {$form_data['state_province']}\n";
    }
    $message .= "       {$form_data['country']}\n\n";
    
    $message .= "=== SOLLICITATIEGEGEVENS ===\n";
    $message .= "Vacature: {$form_data['vacancy']}\n";
    $message .= "Gewenst salaris: {$form_data['expected_salary']}\n";
    $message .= "Gevonden via: {$form_data['source']}\n\n";
    
    if ($form_data['cover_letter']) {
        $message .= "=== MOTIVATIEBRIEF ===\n";
        $message .= "{$form_data['cover_letter']}\n\n";
    }
    
    if ($form_data['additional_info']) {
        $message .= "=== AANVULLENDE INFORMATIE ===\n";
        $message .= "{$form_data['additional_info']}\n\n";
    }
    
    $message .= "=== BEVESTIGINGEN ===\n";
    $message .= "Privacybeleid akkoord: {$form_data['privacy_policy']}\n\n";
    
    $message .= "---\nIngediend vanaf: " . home_url() . "\n";
    $message .= "Inzending ID: {$submission_id}\n";

    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    // Attach CV file if available
    $attachments = array();
    if ($form_data['cv_file_id']) {
        $cv_file_path = get_attached_file($form_data['cv_file_id']);
        if ($cv_file_path && file_exists($cv_file_path)) {
            $attachments[] = $cv_file_path;
        }
    }
    
    wp_mail($admin_email, $subject, $message, $headers, $attachments);
    wp_mail('valentino@blossemgroup.nl', $subject, $message, $headers, $attachments);
    wp_mail('verhuur@blossemgroup.nl', $subject, $message, $headers, $attachments);
    
    // Clean up transient after sending
    delete_transient($transient_key);
}

/**
 * Register custom post type for vacancies
 */
add_action('init', 'register_vacancy_post_type');
function register_vacancy_post_type() {
    register_post_type('vacancy', array(
        'labels' => array(
            'name' => _x('Vacatures', 'post type general name', 'hello-biz'),
            'singular_name' => _x('Vacature', 'post type singular name', 'hello-biz'),
            'menu_name' => __('Vacatures', 'hello-biz'),
            'all_items' => __('Alle Vacatures', 'hello-biz'),
            'add_new' => __('Nieuwe toevoegen', 'hello-biz'),
            'add_new_item' => __('Nieuwe Vacature toevoegen', 'hello-biz'),
            'edit_item' => __('Vacature bewerken', 'hello-biz'),
            'view_item' => __('Vacature bekijken', 'hello-biz'),
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title', 'editor', 'page-attributes'),
        'menu_icon' => 'dashicons-megaphone',
        'has_archive' => true,
        'rewrite' => array('slug' => 'vacatures'),
    ));
}

/**
 * Register custom post type for job application submissions
 */
add_action('init', 'register_job_application_post_type');
function register_job_application_post_type() {
    register_post_type('job_application', array(
        'labels' => array(
            'name' => _x('Sollicitaties', 'post type general name', 'hello-biz'),
            'singular_name' => _x('Sollicitatie', 'post type singular name', 'hello-biz'),
            'menu_name' => __('Sollicitaties', 'hello-biz'),
            'all_items' => __('Alle Sollicitaties', 'hello-biz'),
            'add_new' => __('Nieuwe toevoegen', 'hello-biz'),
            'add_new_item' => __('Nieuwe Sollicitatie toevoegen', 'hello-biz'),
            'edit_item' => __('Sollicitatie bewerken', 'hello-biz'),
            'view_item' => __('Sollicitatie bekijken', 'hello-biz'),
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title', 'custom-fields'),
        'menu_icon' => 'dashicons-id',
    ));
}

/**
 * Add Meta Box for Job Application Submission Details
 */
add_action('add_meta_boxes', 'add_job_application_submission_meta_box');
function add_job_application_submission_meta_box() {
    add_meta_box(
        'job_application_submission_details',
        __('Sollicitatie Details', 'hello-biz'),
        'render_job_application_submission_meta_box',
        'job_application',
        'normal',
        'high'
    );
}

function render_job_application_submission_meta_box($post) {
    // Helper to get meta value safely
    $get_meta = function($key) use ($post) {
        return get_post_meta($post->ID, $key, true);
    };

    ?>
    <style>
        .job-application-submission-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .job-application-section { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .job-application-section h3 { margin-top: 0; border-bottom: 2px solid #f0f0f1; padding-bottom: 10px; }
        .job-application-field { margin-bottom: 10px; }
        .job-application-label { font-weight: 600; display: block; margin-bottom: 3px; }
        .job-application-value { color: #2c3338; }
        .job-application-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #2271b1;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 10px;
        }
        .job-application-download-btn:hover {
            background: #135e96;
            color: #fff;
        }
        .job-application-download-btn .dashicons {
            font-size: 18px;
            width: 18px;
            height: 18px;
        }
    </style>

    <div class="job-application-submission-view">
        <div class="job-application-section">
            <h3><?php _e('Persoonlijke gegevens', 'hello-biz'); ?></h3>
            <div class="job-application-submission-grid">
                <div class="job-application-field">
                    <span class="job-application-label"><?php _e('Naam', 'hello-biz'); ?></span>
                    <span class="job-application-value"><?php echo esc_html($get_meta('first_name') . ' ' . $get_meta('last_name')); ?></span>
                </div>
                <div class="job-application-field">
                    <span class="job-application-label"><?php _e('E-mail', 'hello-biz'); ?></span>
                    <span class="job-application-value"><a href="mailto:<?php echo esc_attr($get_meta('email')); ?>"><?php echo esc_html($get_meta('email')); ?></a></span>
                </div>
                <div class="job-application-field">
                    <span class="job-application-label"><?php _e('Telefoonnummer', 'hello-biz'); ?></span>
                    <span class="job-application-value"><?php echo esc_html($get_meta('phone_country') . ' ' . $get_meta('phone')); ?></span>
                </div>
                <div class="job-application-field full-width">
                    <span class="job-application-label"><?php _e('Adres', 'hello-biz'); ?></span>
                    <span class="job-application-value">
                        <?php echo esc_html($get_meta('address_line_1')); ?><br>
                        <?php if($get_meta('address_line_2')) echo esc_html($get_meta('address_line_2')) . '<br>'; ?>
                        <?php echo esc_html($get_meta('postal_code') . ' ' . $get_meta('city')); ?><br>
                        <?php if($get_meta('state_province')) echo esc_html($get_meta('state_province')) . '<br>'; ?>
                        <?php echo esc_html($get_meta('country')); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="job-application-section">
            <h3><?php _e('Sollicitatiegegevens', 'hello-biz'); ?></h3>
            <div class="job-application-submission-grid">
                <div class="job-application-field">
                    <span class="job-application-label"><?php _e('Vacature', 'hello-biz'); ?></span>
                    <span class="job-application-value"><?php echo esc_html($get_meta('vacancy')); ?></span>
                </div>
                <div class="job-application-field">
                    <span class="job-application-label"><?php _e('Gewenst salaris', 'hello-biz'); ?></span>
                    <span class="job-application-value"><?php echo esc_html($get_meta('expected_salary')); ?></span>
                </div>
                <div class="job-application-field">
                    <span class="job-application-label"><?php _e('Gevonden via', 'hello-biz'); ?></span>
                    <span class="job-application-value"><?php echo esc_html($get_meta('source')); ?></span>
                </div>
                <div class="job-application-field">
                    <span class="job-application-label"><?php _e('CV', 'hello-biz'); ?></span>
                    <span class="job-application-value">
                        <?php 
                        $cv_file_id = $get_meta('cv_file_id');
                        if ($cv_file_id) {
                            $cv_url = wp_get_attachment_url($cv_file_id);
                            $cv_filename = basename(get_attached_file($cv_file_id));
                            if ($cv_url) {
                                echo '<a href="' . esc_url($cv_url) . '" class="job-application-download-btn" download>';
                                echo '<span class="dashicons dashicons-download"></span>';
                                echo esc_html($cv_filename);
                                echo '</a>';
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <?php if ($get_meta('cover_letter')) : ?>
        <div class="job-application-section">
            <h3><?php _e('Motivatiebrief', 'hello-biz'); ?></h3>
            <div class="job-application-message">
                <?php echo nl2br(esc_html($get_meta('cover_letter'))); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($get_meta('additional_info')) : ?>
        <div class="job-application-section">
            <h3><?php _e('Aanvullende informatie', 'hello-biz'); ?></h3>
            <div class="job-application-message">
                <?php echo nl2br(esc_html($get_meta('additional_info'))); ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="job-application-section">
            <h3><?php _e('Bevestigingen', 'hello-biz'); ?></h3>
            <div class="job-application-field">
                <span class="job-application-label"><?php _e('Privacybeleid akkoord', 'hello-biz'); ?></span>
                <span class="job-application-value"><?php echo esc_html($get_meta('privacy_policy')); ?></span>
            </div>
        </div>
    </div>
    <?php
}

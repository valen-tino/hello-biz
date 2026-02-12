<?php
/**
 * Contact Formulier Template
 * 
 * Een aangepast contactformulier met Nederlandse opmaak.
 * Dit sjabloon biedt de frontend structuur en verwerkt inzendingen via AJAX.
 * 
 * @package Hello_Biz
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the Contact Formulier
 */
function render_contact_formulier() {
    ob_start();
    ?>
    <div class="contact-formulier-wrapper">
        <div class="contact-formulier-container">
            
            <div class="form-header">
                <h2>Contact formulier</h2>
            </div>

            <!-- Form -->
            <form id="contact-formulier-form" class="contact-form" method="post" novalidate>
                
                <!-- Hidden Fields for Backend Integration -->
                <input type="hidden" name="action" value="contact_formulier_submit">
                <?php wp_nonce_field('contact_formulier_nonce', 'contact_nonce'); ?>
                
                <!-- Name Field (First and Last) -->
                <div class="form-row">
                    <div class="form-group half">
                        <label for="first_name">Naam <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" placeholder="First" required>
                        <span class="field-sublabel">First</span>
                    </div>
                    <div class="form-group half">
                        <label for="last_name">&nbsp;</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Last" required>
                        <span class="field-sublabel">Last</span>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone">Telefoonnummer <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" placeholder="Uw Telefoonnummer" required>
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Email adres <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Uw Emailadres" required>
                </div>

                <!-- Where did you hear about us -->
                <div class="form-group">
                    <label for="hear_about_us">Waar heb je over ons gehoord? <span class="required">*</span></label>
                    <select id="hear_about_us" name="hear_about_us" required>
                        <option value="">-</option>
                        <option value="Instagram">Instagram</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Google">Google</option>
                        <option value="Vriend/Familie">Vriend/Familie</option>
                        <option value="Anders">Anders</option>
                    </select>
                </div>

                <!-- Message Field -->
                <div class="form-group">
                    <label for="message">Vul hier uw bericht in...</label>
                    <textarea id="message" name="message" rows="6" placeholder="Uw vraag of bericht"></textarea>
                </div>

                <!-- Privacy Policy Checkbox -->
                <div class="form-group">
                    <label class="checkbox-option">
                        <input type="checkbox" name="privacy_policy" id="privacy_policy" required>
                        <span>Ik ga akkoord met het <a href="<?php echo esc_url(get_privacy_policy_url()); ?>" target="_blank">Privacybeleid</a> van Blossem Group</span>
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
 * Shortcode to display the contact form
 */
function contact_formulier_shortcode() {
    return render_contact_formulier();
}
add_shortcode('contact_formulier', 'contact_formulier_shortcode');

/**
 * AJAX Handler for Contact Formulier Submission
 */
add_action('wp_ajax_contact_formulier_submit', 'handle_contact_formulier_submit');
add_action('wp_ajax_nopriv_contact_formulier_submit', 'handle_contact_formulier_submit');

function handle_contact_formulier_submit() {
    // Verify nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'contact_formulier_nonce')) {
        wp_send_json_error(array('message' => __('Beveiligingscontrole mislukt. Vernieuw de pagina en probeer het opnieuw.', 'hello-biz')));
    }

    // Rate limiting: 1 submission per 30 seconds per IP
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $rate_limit_key = 'contact_form_rate_' . md5($ip_address);
    $rate_limit_seconds = 30;
    
    if (get_transient($rate_limit_key)) {
        wp_send_json_error(array(
            'message' => __('U heeft recent al een bericht ingediend. Wacht alstublieft 30 seconden voordat u opnieuw probeert.', 'hello-biz')
        ));
    }
    
    // Set rate limit transient
    set_transient($rate_limit_key, true, $rate_limit_seconds);

    // Collect form data
    $form_data = array(
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'hear_about_us' => sanitize_text_field($_POST['hear_about_us'] ?? ''),
        'message' => sanitize_textarea_field($_POST['message'] ?? ''),
        'privacy_policy' => isset($_POST['privacy_policy']) ? 'Yes' : 'No',
    );

    // Store submission as custom post type
    $submission_id = wp_insert_post(array(
        'post_type' => 'contact_submission',
        'post_title' => $form_data['first_name'] . ' ' . $form_data['last_name'] . ' - ' . date('Y-m-d H:i:s'),
        'post_status' => 'publish',
        'meta_input' => $form_data,
    ));

    if (is_wp_error($submission_id)) {
        // Fallback: Store in options table if custom post type doesn't exist
        $submissions = get_option('contact_formulier_submissions', array());
        $form_data['submitted_at'] = current_time('mysql');
        // Anonymize IP for GDPR compliance
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($ip)) {
            $form_data['ip_address'] = preg_replace('/\.\d+$/', '.0', $ip);
        }
        $submissions[] = $form_data;
        update_option('contact_formulier_submissions', $submissions);
        $submission_id = count($submissions);
    }

    // Schedule async email sending for better performance
    $email_data = array(
        'form_data' => $form_data,
        'submission_id' => $submission_id,
    );
    
    // Store email data in transient for the scheduled event
    $transient_key = 'contact_email_' . $submission_id;
    set_transient($transient_key, $email_data, HOUR_IN_SECONDS);
    
    // Schedule the email to be sent immediately in the background
    wp_schedule_single_event(time(), 'send_contact_formulier_email', array($transient_key));
    
    // Spawn cron immediately to send the email
    spawn_cron();

    // Return success immediately (email sends in background)
    wp_send_json_success(array(
        'message' => __('Bedankt voor uw bericht! We hebben uw aanvraag ontvangen en nemen spoedig contact met u op.', 'hello-biz'),
        'submission_id' => $submission_id,
    ));
}

/**
 * Async Email Handler - Sends contact form emails in the background
 */
add_action('send_contact_formulier_email', 'handle_contact_formulier_email');
function handle_contact_formulier_email($transient_key) {
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
    
    $subject = sprintf('[%s] Nieuw Contactformulier Bericht: %s %s', $site_name, $form_data['first_name'], $form_data['last_name']);
    
    $message = "Er is een nieuw contactformulier bericht ontvangen:\n\n";
    $message .= "=== CONTACTGEGEVENS ===\n";
    $message .= "Naam: {$form_data['first_name']} {$form_data['last_name']}\n";
    $message .= "E-mail: {$form_data['email']}\n";
    $message .= "Telefoon: {$form_data['phone']}\n";
    $message .= "Waar over ons gehoord: {$form_data['hear_about_us']}\n\n";
    
    $message .= "=== BERICHT ===\n";
    $message .= "{$form_data['message']}\n\n";
    
    $message .= "=== BEVESTIGINGEN ===\n";
    $message .= "Privacybeleid akkoord: {$form_data['privacy_policy']}\n\n";
    
    $message .= "---\nIngediend vanaf: " . home_url() . "\n";
    $message .= "Inzending ID: {$submission_id}\n";

    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    wp_mail($admin_email, $subject, $message, $headers);
    wp_mail('valentino@blossemgroup.nl', $subject, $message, $headers);
    wp_mail('verhuur@blossemgroup.nl', $subject, $message, $headers);
    
    // Clean up transient after sending
    delete_transient($transient_key);
}

/**
 * Register custom post type for contact submissions
 */
add_action('init', 'register_contact_submission_post_type');
function register_contact_submission_post_type() {
    register_post_type('contact_submission', array(
        'labels' => array(
            'name' => _x('Contact Berichten', 'post type general name', 'hello-biz'),
            'singular_name' => _x('Contact Bericht', 'post type singular name', 'hello-biz'),
            'menu_name' => __('Contact Berichten', 'hello-biz'),
            'all_items' => __('Alle Berichten', 'hello-biz'),
            'add_new' => __('Nieuwe toevoegen', 'hello-biz'),
            'add_new_item' => __('Nieuw Bericht toevoegen', 'hello-biz'),
            'edit_item' => __('Bericht bewerken', 'hello-biz'),
            'view_item' => __('Bericht bekijken', 'hello-biz'),
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title', 'custom-fields'),
        'menu_icon' => 'dashicons-email',
    ));
}

/**
 * Add Meta Box for Contact Submission Details
 */
add_action('add_meta_boxes', 'add_contact_submission_meta_box');
function add_contact_submission_meta_box() {
    add_meta_box(
        'contact_submission_details',
        __('Contact Details', 'hello-biz'),
        'render_contact_submission_meta_box',
        'contact_submission',
        'normal',
        'high'
    );
}

function render_contact_submission_meta_box($post) {
    // Helper to get meta value safely
    $get_meta = function($key) use ($post) {
        return get_post_meta($post->ID, $key, true);
    };

    // Enqueue styles for admin
    wp_enqueue_style('contact-formulier', get_theme_file_uri('assets/css/contact-formulier.css'));

    ?>
    <div class="contact-submission-view">
        <div class="contact-section">
            <h3><?php _e('Contactgegevens', 'hello-biz'); ?></h3>
            <div class="contact-submission-grid">
                <div class="contact-field">
                    <span class="contact-label"><?php _e('Naam', 'hello-biz'); ?></span>
                    <span class="contact-value"><?php echo esc_html($get_meta('first_name') . ' ' . $get_meta('last_name')); ?></span>
                </div>
                <div class="contact-field">
                    <span class="contact-label"><?php _e('E-mail', 'hello-biz'); ?></span>
                    <span class="contact-value"><a href="mailto:<?php echo esc_attr($get_meta('email')); ?>"><?php echo esc_html($get_meta('email')); ?></a></span>
                </div>
                <div class="contact-field">
                    <span class="contact-label"><?php _e('Telefoonnummer', 'hello-biz'); ?></span>
                    <span class="contact-value"><?php echo esc_html($get_meta('phone')); ?></span>
                </div>
                <div class="contact-field">
                    <span class="contact-label"><?php _e('Waar over ons gehoord', 'hello-biz'); ?></span>
                    <span class="contact-value"><?php echo esc_html($get_meta('hear_about_us')); ?></span>
                </div>
            </div>
        </div>

        <?php if ($get_meta('message')) : ?>
        <div class="contact-section">
            <h3><?php _e('Bericht', 'hello-biz'); ?></h3>
            <div class="contact-message">
                <?php echo esc_html($get_meta('message')); ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="contact-section">
            <h3><?php _e('Bevestigingen', 'hello-biz'); ?></h3>
            <div class="contact-field">
                <span class="contact-label"><?php _e('Privacybeleid akkoord', 'hello-biz'); ?></span>
                <span class="contact-value"><?php echo esc_html($get_meta('privacy_policy')); ?></span>
            </div>
        </div>
    </div>
    <?php
}

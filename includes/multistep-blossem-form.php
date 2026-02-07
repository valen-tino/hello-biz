<?php
/**
 * Multistep Blossem Form Template
 * 
 * Een aangepast meerstappen formulier met Nederlandse opmaak voor nummers en adressen.
 * Dit sjabloon biedt de frontend structuur en verwerkt inzendingen via AJAX.
 * 
 * @package Hello_Biz
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the Multistep Blossem Form
 */
function render_multistep_blossem_form() {
    ob_start();
    ?>
    <div class="multistep-blossem-form-wrapper">
        <div class="multistep-blossem-form-container">
            
            <!-- Progress Indicator -->
            <div class="multistep-progress-indicator">
                <div class="progress-step active" data-step="1">
                    <div class="progress-circle">1</div>
                    <div class="progress-label"><?php echo esc_html__('Informatie huurder', 'hello-biz'); ?></div>
                </div>
                <div class="progress-step" data-step="2">
                    <div class="progress-circle">2</div>
                    <div class="progress-label"><?php echo esc_html__('Informatie partner', 'hello-biz'); ?></div>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="progress-circle">3</div>
                    <div class="progress-label"><?php echo esc_html__('Inkomensgegevens', 'hello-biz'); ?></div>
                </div>
                <div class="progress-step" data-step="4">
                    <div class="progress-circle">4</div>
                    <div class="progress-label"><?php echo esc_html__('Voorkeuren', 'hello-biz'); ?></div>
                </div>
            </div>

            <!-- Form -->
            <form id="multistep-blossem-form" class="multistep-form" method="post" novalidate>
                
                <!-- Hidden Fields for Backend Integration -->
                <input type="hidden" name="action" value="multistep_blossem_submit">
                <?php wp_nonce_field('multistep_blossem_nonce', 'multistep_nonce'); ?>
                
                <!-- Step 1: Informatie huurder (Personal Information) -->
                <div class="form-step active" data-step="1">
                    <div class="step-header">
                        <h3>Persoonlijke gegevens</h3>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="first_name">Naam <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" placeholder="Voornaam" required>
                        </div>
                        <div class="form-group half">
                            <label for="last_name">&nbsp;</label>
                            <input type="text" id="last_name" name="last_name" placeholder="Achternaam" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="email">E-mail <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="E-mail" required>
                        </div>
                        <div class="form-group half">
                            <label for="confirm_email">&nbsp;</label>
                            <input type="email" id="confirm_email" name="confirm_email" placeholder="Bevestig e-mail" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address_line_1">Adres <span class="required">*</span></label>
                        <input type="text" id="address_line_1" name="address_line_1" placeholder="Straat en huisnummer" required>
                    </div>

                    <div class="form-group">
                        <input type="text" id="address_line_2" name="address_line_2" placeholder="Toevoeging (optioneel)">
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <input type="text" id="city" name="city" placeholder="Stad" required>
                        </div>
                        <div class="form-group half">
                            <select id="province" name="province" required>
                                <option value="">--- Selecteer provincie ---</option>
                                <option value="Drenthe">Drenthe</option>
                                <option value="Flevoland">Flevoland</option>
                                <option value="Friesland">Friesland</option>
                                <option value="Gelderland">Gelderland</option>
                                <option value="Groningen">Groningen</option>
                                <option value="Limburg">Limburg</option>
                                <option value="Noord-Brabant">Noord-Brabant</option>
                                <option value="Noord-Holland">Noord-Holland</option>
                                <option value="Overijssel">Overijssel</option>
                                <option value="Utrecht">Utrecht</option>
                                <option value="Zeeland">Zeeland</option>
                                <option value="Zuid-Holland">Zuid-Holland</option>
                            </select>
                            <label class="field-sublabel">Provincie</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Postcode <span class="required">*</span></label>
                        <input type="text" id="postal_code" name="postal_code" placeholder="1234 AB" pattern="[0-9]{4}\s?[A-Za-z]{2}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefoonnummer <span class="required">*</span></label>
                        <div class="phone-input-wrapper">
                            <select id="phone_country" name="phone_country" class="phone-country-select">
                                <option value="+31" data-flag="NL">NL +31</option>
                            </select>
                            <input type="tel" id="phone" name="phone" placeholder="Telefoonnummer" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="language">Taal <span class="required">*</span></label>
                        <select id="language" name="language" required>
                            <option value="Nederlands">Nederlands</option>
                            <option value="Engels">Engels</option>
                        </select>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-next" data-next="2">Volgende</button>
                    </div>
                </div>

                <!-- Step 2: Informatie partner (Partner Information) -->
                <div class="form-step" data-step="2">
                    <div class="step-header">
                        <h3>Partnergegevens</h3>
                    </div>

                    <div class="form-group">
                        <label for="rent_alone">Gaat u alleen huren? <span class="required">*</span></label>
                        <select id="rent_alone" name="rent_alone" required>
                            <option value="">Kies</option>
                            <option value="Yes">Ja</option>
                            <option value="No">Nee</option>
                        </select>
                    </div>

                    <div id="partner-fields" style="display: none;">
                        <div class="form-group">
                            <label for="partner_name">Volledige naam van uw partner <span class="required">*</span></label>
                            <input type="text" id="partner_name" name="partner_name">
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label for="partner_email">E-mail van uw partner <span class="required">*</span></label>
                                <input type="email" id="partner_email" name="partner_email" placeholder="E-mail">
                            </div>
                            <div class="form-group half">
                                <label for="partner_confirm_email">&nbsp;</label>
                                <input type="email" id="partner_confirm_email" name="partner_confirm_email" placeholder="Bevestig e-mail">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="partner_address_line_1">Adres van uw partner <span class="required">*</span></label>
                            <input type="text" id="partner_address_line_1" name="partner_address_line_1" placeholder="Straat en huisnummer">
                        </div>

                        <div class="form-group">
                            <input type="text" id="partner_address_line_2" name="partner_address_line_2" placeholder="Toevoeging (optioneel)">
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <input type="text" id="partner_city" name="partner_city" placeholder="Stad">
                            </div>
                            <div class="form-group half">
                                <select id="partner_province" name="partner_province">
                                    <option value="">--- Selecteer provincie ---</option>
                                    <option value="Drenthe">Drenthe</option>
                                    <option value="Flevoland">Flevoland</option>
                                    <option value="Friesland">Friesland</option>
                                    <option value="Gelderland">Gelderland</option>
                                    <option value="Groningen">Groningen</option>
                                    <option value="Limburg">Limburg</option>
                                    <option value="Noord-Brabant">Noord-Brabant</option>
                                    <option value="Noord-Holland">Noord-Holland</option>
                                    <option value="Overijssel">Overijssel</option>
                                    <option value="Utrecht">Utrecht</option>
                                    <option value="Zeeland">Zeeland</option>
                                    <option value="Zuid-Holland">Zuid-Holland</option>
                                </select>
                                <label class="field-sublabel">Provincie</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="partner_postal_code">Postcode</label>
                            <input type="text" id="partner_postal_code" name="partner_postal_code" placeholder="1234 AB" pattern="[0-9]{4}\s?[A-Za-z]{2}">
                        </div>

                        <div class="form-group">
                            <label for="partner_phone">Telefoonnummer van uw partner <span class="required">*</span></label>
                            <div class="phone-input-wrapper">
                                <select id="partner_phone_country" name="partner_phone_country" class="phone-country-select">
                                    <option value="+31" data-flag="NL">NL +31</option>
                                </select>
                                <input type="tel" id="partner_phone" name="partner_phone" placeholder="Telefoonnummer">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="partner_language">Taal van uw partner <span class="required">*</span></label>
                            <select id="partner_language" name="partner_language">
                                <option value="Nederlands">Nederlands</option>
                                <option value="Engels">Engels</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-previous" data-prev="1">Vorige</button>
                        <button type="button" class="btn btn-next" data-next="3">Volgende</button>
                    </div>
                </div>

                <!-- Step 3: Inkomensgegevens (Income Information) -->
                <div class="form-step" data-step="3">
                    <div class="step-header">
                        <h3>Inkomensgegevens</h3>
                    </div>

                    <div class="form-group">
                        <label for="employer">Werkgever</label>
                        <input type="text" id="employer" name="employer">
                    </div>

                    <div class="form-group">
                        <label for="income_status">Inkomenssituatie <span class="required">*</span></label>
                        <select id="income_status" name="income_status" required>
                            <option value="">Kies</option>
                            <option value="Onbepaalde tijd">Onbepaalde tijd</option>
                            <option value="Bepaalde tijd">Bepaalde tijd</option>
                            <option value="Zelfstandig">Zelfstandig</option>
                            <option value="Student">Student</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gross_income">Bruto inkomen <span class="required">*</span></label>
                        <input type="text" id="gross_income" name="gross_income" class="dutch-number-input" placeholder="€ 1.000,00" required>
                        <p class="field-description">Als u (niet) samen met een partner huurt, gebruik dan uw bruto inkomen samen. Het toegevoegde inkomen wordt gebruikt voor de minimale inkomensbepaling.</p>
                    </div>

                    <div class="form-group">
                        <label for="warranty_statement">Borgstelling</label>
                        <select id="warranty_statement" name="warranty_statement">
                            <option value="">Kies</option>
                            <option value="Ja">Ja</option>
                            <option value="Nee">Nee</option>
                        </select>
                        <p class="field-description">Als u geen vast arbeidscontract heeft, heeft u een borg nodig en vertrouwen in Nederland.</p>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-previous" data-prev="2">Vorige</button>
                        <button type="button" class="btn btn-next" data-next="4">Volgende</button>
                    </div>
                </div>

                <!-- Step 4: Voorkeuren (Preferences) -->
                <div class="form-step" data-step="4">
                    <div class="step-header">
                        <h3>Voorkeuren</h3>
                    </div>

                    <div class="form-group">
                        <label for="current_living_situation">Huidige woonsituatie <span class="required">*</span></label>
                        <select id="current_living_situation" name="current_living_situation" required>
                            <option value="">Kies</option>
                            <option value="Huurwoning">Huurwoning</option>
                            <option value="Koopwoning">Koopwoning</option>
                            <option value="Inwonend">Inwonend</option>
                            <option value="Studentenhuis">Studentenhuis</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="search_date">Zoekt woning per <span class="required">*</span></label>
                        <input type="date" id="search_date" name="search_date" required>
                    </div>

                    <div class="form-group">
                        <label for="maximum_price">Maximale prijs <span class="required">*</span></label>
                        <input type="text" id="maximum_price" name="maximum_price" class="dutch-number-input" placeholder="€ 1.000,00">
                        <p class="field-description">Het minimum wordt niet gesteld en mag niet hoger zijn dan 3 keer het inkomen.</p>
                    </div>

                    <div class="form-group">
                        <label for="interested_in">Geïnteresseerd in <span class="required">*</span></label>
                        <select id="interested_in" name="interested_in" required>
                            <option value="">Kies</option>
                            <?php
                            $project_types = get_terms(array(
                                'taxonomy' => 'type-of-project',
                                'hide_empty' => false,
                            ));
                            if (!is_wp_error($project_types) && !empty($project_types)) {
                                foreach ($project_types as $term) {
                                    echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="number_of_rooms">Aantal kamers <span class="required">*</span></label>
                        <select id="number_of_rooms" name="number_of_rooms" required>
                            <option value="">Kies</option>
                            <?php for ($i = 1; $i <= 10; $i++) : ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="interior">Interieur <span class="required">*</span></label>
                        <select id="interior" name="interior" required>
                            <option value="">Kies</option>
                            <?php
                            $furnish_terms = get_terms(array(
                                'taxonomy' => 'furnish_status',
                                'hide_empty' => false,
                            ));
                            if (!is_wp_error($furnish_terms) && !empty($furnish_terms)) {
                                foreach ($furnish_terms as $term) {
                                    echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-option">
                            <input type="checkbox" name="privacy_policy" id="privacy_policy" required>
                            <span>Ik verklaar dat de informatie die ik in het formulier heb ingevuld naar waarheid is ingevuld.</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-option">
                            <input type="checkbox" name="terms_agreement" id="terms_agreement" required>
                            <span>Ik heb het privacybeleid gelezen en ga hiermee akkoord</span>
                        </label>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-previous" data-prev="3">Vorige</button>
                        <button type="submit" class="btn btn-submit">Ja, ik wil me inschrijven!</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode to display the multistep form
 */
function multistep_blossem_form_shortcode() {
    return render_multistep_blossem_form();
}
add_shortcode('multistep_blossem_form', 'multistep_blossem_form_shortcode');

/**
 * AJAX Handler for Multistep Blossem Form Submission
 */
add_action('wp_ajax_multistep_blossem_submit', 'handle_multistep_blossem_submit');
add_action('wp_ajax_nopriv_multistep_blossem_submit', 'handle_multistep_blossem_submit');

function handle_multistep_blossem_submit() {
    // Verify nonce
    if (!isset($_POST['multistep_nonce']) || !wp_verify_nonce($_POST['multistep_nonce'], 'multistep_blossem_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed. Please refresh and try again.', 'hello-biz')));
    }

    // Collect form data
    $form_data = array(
        // Step 1: Personal Information
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'confirm_email' => sanitize_email($_POST['confirm_email'] ?? ''),
        'address_line_1' => sanitize_text_field($_POST['address_line_1'] ?? ''),
        'address_line_2' => sanitize_text_field($_POST['address_line_2'] ?? ''),
        'city' => strtoupper(sanitize_text_field($_POST['city'] ?? '')),
        'province' => sanitize_text_field($_POST['province'] ?? ''),
        'postal_code' => strtoupper(sanitize_text_field($_POST['postal_code'] ?? '')),
        'country' => sanitize_text_field($_POST['country'] ?? ''),
        'phone_country' => sanitize_text_field($_POST['phone_country'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'language' => sanitize_text_field($_POST['language'] ?? ''),
        
        // Step 2: Partner Information
        'rent_alone' => sanitize_text_field($_POST['rent_alone'] ?? ''),
        'partner_name' => sanitize_text_field($_POST['partner_name'] ?? ''),
        'partner_email' => sanitize_email($_POST['partner_email'] ?? ''),
        'partner_confirm_email' => sanitize_email($_POST['partner_confirm_email'] ?? ''),
        'partner_address_line_1' => sanitize_text_field($_POST['partner_address_line_1'] ?? ''),
        'partner_address_line_2' => sanitize_text_field($_POST['partner_address_line_2'] ?? ''),
        'partner_city' => strtoupper(sanitize_text_field($_POST['partner_city'] ?? '')),
        'partner_province' => sanitize_text_field($_POST['partner_province'] ?? ''),
        'partner_postal_code' => strtoupper(sanitize_text_field($_POST['partner_postal_code'] ?? '')),
        'partner_country' => sanitize_text_field($_POST['partner_country'] ?? ''),
        'partner_phone_country' => sanitize_text_field($_POST['partner_phone_country'] ?? ''),
        'partner_phone' => sanitize_text_field($_POST['partner_phone'] ?? ''),
        'partner_language' => sanitize_text_field($_POST['partner_language'] ?? ''),
        
        // Step 3: Income Information
        'employer' => sanitize_text_field($_POST['employer'] ?? ''),
        'income_status' => sanitize_text_field($_POST['income_status'] ?? ''),
        'gross_income' => sanitize_text_field($_POST['gross_income'] ?? ''),
        'warranty_statement' => sanitize_text_field($_POST['warranty_statement'] ?? ''),
        
        // Step 4: Preferences
        'current_living_situation' => sanitize_text_field($_POST['current_living_situation'] ?? ''),
        'search_date' => sanitize_text_field($_POST['search_date'] ?? ''),
        'maximum_price' => sanitize_text_field($_POST['maximum_price'] ?? ''),
        'interested_in' => isset($_POST['interested_in']) ? array(sanitize_text_field($_POST['interested_in'])) : array(),
        'number_of_rooms' => sanitize_text_field($_POST['number_of_rooms'] ?? ''),
        'interior' => sanitize_text_field($_POST['interior'] ?? ''),
        'privacy_policy' => isset($_POST['privacy_policy']) ? 'Yes' : 'No',
        'terms_agreement' => isset($_POST['terms_agreement']) ? 'Yes' : 'No',
    );

    // Store submission as custom post type or option
    $submission_id = wp_insert_post(array(
        'post_type' => 'blossem_registration',
        'post_title' => $form_data['first_name'] . ' ' . $form_data['last_name'] . ' - ' . date('Y-m-d H:i:s'),
        'post_status' => 'publish',
        'meta_input' => $form_data,
    ));

    if (is_wp_error($submission_id)) {
        // Fallback: Store in options table if custom post type doesn't exist
        $submissions = get_option('multistep_blossem_submissions', array());
        $form_data['submitted_at'] = current_time('mysql');
        // Anonymize IP for GDPR compliance (replace last octet with 0)
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($ip)) {
            $form_data['ip_address'] = preg_replace('/\\.\\d+$/', '.0', $ip);
        }
        $submissions[] = $form_data;
        update_option('multistep_blossem_submissions', $submissions);
        $submission_id = count($submissions);
    }

    // Schedule async email sending for better performance
    $email_data = array(
        'form_data' => $form_data,
        'submission_id' => $submission_id,
    );
    
    // Store email data in transient for the scheduled event
    $transient_key = 'blossem_email_' . $submission_id;
    set_transient($transient_key, $email_data, HOUR_IN_SECONDS);
    
    // Schedule the email to be sent immediately in the background
    wp_schedule_single_event(time(), 'send_blossem_registration_email', array($transient_key));
    
    // Spawn cron immediately to send the email
    spawn_cron();

    // Return success immediately (email sends in background)
    wp_send_json_success(array(
        'message' => __('Bedankt voor uw registratie! We hebben uw aanvraag ontvangen en nemen spoedig contact met u op.', 'hello-biz'),
        'submission_id' => $submission_id,
    ));
}

/**
 * Async Email Handler - Sends registration emails in the background
 */
add_action('send_blossem_registration_email', 'handle_blossem_registration_email');
function handle_blossem_registration_email($transient_key) {
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
    
    $subject = sprintf('[%s] Nieuwe Huurregistratie: %s %s', $site_name, $form_data['first_name'], $form_data['last_name']);
    
    $message = "Er is een nieuwe huurregistratie ingediend:\n\n";
    $message .= "=== PERSOONLIJKE GEGEVENS ===\n";
    $message .= "Naam: {$form_data['first_name']} {$form_data['last_name']}\n";
    $message .= "E-mail: {$form_data['email']}\n";
    $message .= "Telefoon: {$form_data['phone_country']} {$form_data['phone']}\n";
    $message .= "Adres: {$form_data['address_line_1']}\n";
    if ($form_data['address_line_2']) {
        $message .= "       {$form_data['address_line_2']}\n";
    }
    $message .= "       {$form_data['postal_code']} {$form_data['city']}\n";
    $message .= "       {$form_data['country']}\n";
    $message .= "Taal: {$form_data['language']}\n\n";
    
    if ($form_data['rent_alone'] === 'No') {
        $message .= "=== PARTNER GEGEVENS ===\n";
        $message .= "Partner Naam: {$form_data['partner_name']}\n";
        $message .= "Partner E-mail: {$form_data['partner_email']}\n";
        $message .= "Partner Telefoon: {$form_data['partner_phone_country']} {$form_data['partner_phone']}\n";
        $message .= "Partner Adres: {$form_data['partner_address_line_1']}\n";
        if ($form_data['partner_address_line_2']) {
            $message .= "              {$form_data['partner_address_line_2']}\n";
        }
        $message .= "              {$form_data['partner_postal_code']} {$form_data['partner_city']}\n";
        $message .= "              {$form_data['partner_country']}\n";
        $message .= "Partner Taal: {$form_data['partner_language']}\n\n";
    }
    
    $message .= "=== INKOMENSGEGEVENS ===\n";
    $message .= "Werkgever: {$form_data['employer']}\n";
    $message .= "Inkomensstatus: {$form_data['income_status']}\n";
    $message .= "Brutoinkomen: {$form_data['gross_income']}\n";
    $message .= "Borgstelling: {$form_data['warranty_statement']}\n\n";
    
    $message .= "=== VOORKEUREN ===\n";
    $message .= "Huidige woonsituatie: {$form_data['current_living_situation']}\n";
    $message .= "Zoekt woning per: {$form_data['search_date']}\n";
    $message .= "Maximale prijs: {$form_data['maximum_price']}\n";
    $message .= "Geïnteresseerd in: " . implode(', ', $form_data['interested_in']) . "\n";
    $message .= "Aantal kamers: {$form_data['number_of_rooms']}\n";
    $message .= "Interieur: {$form_data['interior']}\n\n";
    
    $message .= "=== BEVESTIGINGEN ===\n";
    $message .= "Privacybeleid akkoord: {$form_data['privacy_policy']}\n";
    $message .= "Algemene voorwaarden akkoord: {$form_data['terms_agreement']}\n\n";
    
    $message .= "---\nIngediend vanaf: " . home_url() . "\n";
    $message .= "Inzending ID: {$submission_id}\n";

    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    wp_mail($admin_email, $subject, $message, $headers);
    
    // Clean up transient after sending
    delete_transient($transient_key);
}

/**
 * Registreer aangepast berichttype voor inzendingen
 */
add_action('init', 'register_blossem_registration_post_type');
function register_blossem_registration_post_type() {
    register_post_type('blossem_registration', array(
        'labels' => array(
            'name' => _x('Blossem Registraties', 'post type general name', 'hello-biz'),
            'singular_name' => _x('Blossem Registratie', 'post type singular name', 'hello-biz'),
            'menu_name' => __('Registraties', 'hello-biz'),
            'all_items' => __('Alle Registraties', 'hello-biz'),
            'add_new' => __('Nieuwe toevoegen', 'hello-biz'),
            'add_new_item' => __('Nieuwe Registratie toevoegen', 'hello-biz'),
            'edit_item' => __('Registratie bewerken', 'hello-biz'),
            'view_item' => __('Registratie bekijken', 'hello-biz'),
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title', 'custom-fields'),
        'menu_icon' => 'dashicons-clipboard',
    ));
}

/**
 * Add Meta Box for Registration Details
 */
add_action('add_meta_boxes', 'add_blossem_registration_meta_box');
function add_blossem_registration_meta_box() {
    add_meta_box(
        'blossem_registration_details',
        __('Registratie Details', 'hello-biz'),
        'render_blossem_registration_meta_box',
        'blossem_registration',
        'normal',
        'high'
    );
}

function render_blossem_registration_meta_box($post) {
    // Helper to get meta value safely
    $get_meta = function($key) use ($post) {
        return get_post_meta($post->ID, $key, true);
    };

    // Generate export URL
    $export_url = add_query_arg(array(
        'action' => 'export_blossem_registration',
        'post_id' => $post->ID,
        'nonce' => wp_create_nonce('export_blossem_registration_' . $post->ID)
    ), admin_url('admin-ajax.php'));

    ?>
    <style>
        .blossem-registration-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .blossem-section { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .blossem-section h3 { margin-top: 0; border-bottom: 2px solid #f0f0f1; padding-bottom: 10px; }
        .blossem-field { margin-bottom: 10px; }
        .blossem-label { font-weight: 600; display: block; margin-bottom: 3px; }
        .blossem-value { color: #2c3338; }
        .blossem-export-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #2271b1;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .blossem-export-btn:hover {
            background: #135e96;
            color: #fff;
        }
        .blossem-export-btn .dashicons {
            font-size: 18px;
            width: 18px;
            height: 18px;
        }
    </style>

    <div class="blossem-registration-view">
        <a href="<?php echo esc_url($export_url); ?>" class="blossem-export-btn" download>
            <span class="dashicons dashicons-download"></span>
            <?php _e('Exporteren naar TXT', 'hello-biz'); ?>
        </a>
        
        <div class="blossem-section">
            <h3><?php _e('Persoonlijke gegevens', 'hello-biz'); ?></h3>
            <div class="blossem-registration-grid">
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Naam', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('first_name') . ' ' . $get_meta('last_name')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('E-mail', 'hello-biz'); ?></span>
                    <span class="blossem-value"><a href="mailto:<?php echo esc_attr($get_meta('email')); ?>"><?php echo esc_html($get_meta('email')); ?></a></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Telefoonnummer', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('phone_country') . ' ' . $get_meta('phone')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Taal', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('language')); ?></span>
                </div>
                <div class="blossem-field full-width">
                    <span class="blossem-label"><?php _e('Adres', 'hello-biz'); ?></span>
                    <span class="blossem-value">
                        <?php echo esc_html($get_meta('address_line_1')); ?><br>
                        <?php if($get_meta('address_line_2')) echo esc_html($get_meta('address_line_2')) . '<br>'; ?>
                        <?php echo esc_html($get_meta('postal_code') . ' ' . $get_meta('city')); ?><br>
                        <?php echo esc_html($get_meta('country')); ?>
                    </span>
                </div>
            </div>
        </div>

        <?php if ($get_meta('rent_alone') === 'No') : ?>
        <div class="blossem-section">
            <h3><?php _e('Partner gegevens', 'hello-biz'); ?></h3>
            <div class="blossem-registration-grid">
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Naam', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('partner_name')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('E-mail', 'hello-biz'); ?></span>
                    <span class="blossem-value"><a href="mailto:<?php echo esc_attr($get_meta('partner_email')); ?>"><?php echo esc_html($get_meta('partner_email')); ?></a></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Telefoonnummer', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('partner_phone_country') . ' ' . $get_meta('partner_phone')); ?></span>
                </div>
                 <div class="blossem-field full-width">
                    <span class="blossem-label"><?php _e('Adres', 'hello-biz'); ?></span>
                    <span class="blossem-value">
                        <?php echo esc_html($get_meta('partner_address_line_1')); ?><br>
                        <?php if($get_meta('partner_address_line_2')) echo esc_html($get_meta('partner_address_line_2')) . '<br>'; ?>
                        <?php echo esc_html($get_meta('partner_postal_code') . ' ' . $get_meta('partner_city')); ?><br>
                        <?php echo esc_html($get_meta('partner_country')); ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="blossem-section">
            <h3><?php _e('Inkomensgegevens', 'hello-biz'); ?></h3>
            <div class="blossem-registration-grid">
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Werkgever', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('employer')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Inkomensstatus', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('income_status')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Brutoinkomen', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('gross_income')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Garantieverklaring', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('warranty_statement')); ?></span>
                </div>
            </div>
        </div>

        <div class="blossem-section">
            <h3><?php _e('Voorkeuren', 'hello-biz'); ?></h3>
            <div class="blossem-registration-grid">
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Huidige woonsituatie', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('current_living_situation')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Zoekdatum', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('search_date')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Maximale huurprijs', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('maximum_price')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Geïnteresseerd in', 'hello-biz'); ?></span>
                    <span class="blossem-value">
                        <?php 
                        $interested = $get_meta('interested_in');
                        if(is_array($interested)) {
                            echo esc_html(implode(', ', $interested));
                        } else {
                            echo esc_html($interested);
                        }
                        ?>
                    </span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Aantal kamers', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('number_of_rooms')); ?></span>
                </div>
                 <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Interieur', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('interior')); ?></span>
                </div>
            </div>
        </div>

        <div class="blossem-section" style="border-bottom: none;">
            <h3><?php _e('Systeeminformatie', 'hello-biz'); ?></h3>
            <div class="blossem-registration-grid">
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Ingediend op', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('submitted_at')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('IP-adres', 'hello-biz'); ?></span>
                    <span class="blossem-value"><?php echo esc_html($get_meta('ip_address')); ?></span>
                </div>
                <div class="blossem-field">
                    <span class="blossem-label"><?php _e('Overeenkomsten', 'hello-biz'); ?></span>
                    <span class="blossem-value">
                        Privacybeleid: <?php echo esc_html($get_meta('privacy_policy')); ?><br>
                        Algemene voorwaarden: <?php echo esc_html($get_meta('terms_agreement')); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * AJAX Handler voor het exporteren van registratie naar TXT
 */
add_action('wp_ajax_export_blossem_registration', 'handle_export_blossem_registration');
function handle_export_blossem_registration() {
    // Check permissions
    if (!current_user_can('edit_posts')) {
        wp_die(__('Onvoldoende rechten', 'hello-biz'));
    }

    // Get post ID
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    
    if (!$post_id) {
        wp_die(__('Ongeldig bericht ID', 'hello-biz'));
    }

    // Verify nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'export_blossem_registration_' . $post_id)) {
        wp_die(__('Beveiligingscontrole mislukt', 'hello-biz'));
    }

    // Get post
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'blossem_registration') {
        wp_die(__('Registratie niet gevonden', 'hello-biz'));
    }

    // Helper to get meta
    $get_meta = function($key) use ($post_id) {
        return get_post_meta($post_id, $key, true);
    };

    // Build export content
    $content = "================================\n";
    $content .= "BLOSSEM REGISTRATIE EXPORT\n";
    $content .= "================================\n\n";
    $content .= "Titel: " . $post->post_title . "\n";
    $content .= "Datum: " . get_the_date('d-m-Y H:i', $post) . "\n\n";

    $content .= "=== PERSOONLIJKE GEGEVENS ===\n";
    $content .= "Naam: " . $get_meta('first_name') . " " . $get_meta('last_name') . "\n";
    $content .= "E-mail: " . $get_meta('email') . "\n";
    $content .= "Telefoon: " . $get_meta('phone_country') . " " . $get_meta('phone') . "\n";
    $content .= "Adres: " . $get_meta('address_line_1') . "\n";
    if ($get_meta('address_line_2')) {
        $content .= "        " . $get_meta('address_line_2') . "\n";
    }
    $content .= "        " . $get_meta('postal_code') . " " . $get_meta('city') . "\n";
    $content .= "        " . $get_meta('province') . "\n";
    $content .= "Taal: " . $get_meta('language') . "\n\n";

    if ($get_meta('rent_alone') === 'No') {
        $content .= "=== PARTNER GEGEVENS ===\n";
        $content .= "Partner Naam: " . $get_meta('partner_name') . "\n";
        $content .= "Partner E-mail: " . $get_meta('partner_email') . "\n";
        $content .= "Partner Telefoon: " . $get_meta('partner_phone_country') . " " . $get_meta('partner_phone') . "\n";
        $content .= "Partner Adres: " . $get_meta('partner_address_line_1') . "\n";
        if ($get_meta('partner_address_line_2')) {
            $content .= "               " . $get_meta('partner_address_line_2') . "\n";
        }
        $content .= "               " . $get_meta('partner_postal_code') . " " . $get_meta('partner_city') . "\n";
        $content .= "               " . $get_meta('partner_province') . "\n";
        $content .= "Partner Taal: " . $get_meta('partner_language') . "\n\n";
    } else {
        $content .= "=== PARTNER GEGEVENS ===\n";
        $content .= "Alleen huren: Ja\n\n";
    }

    $content .= "=== INKOMENSGEGEVENS ===\n";
    $content .= "Werkgever: " . $get_meta('employer') . "\n";
    $content .= "Inkomensstatus: " . $get_meta('income_status') . "\n";
    $content .= "Brutoinkomen: " . $get_meta('gross_income') . "\n";
    $content .= "Borgstelling: " . $get_meta('warranty_statement') . "\n\n";

    $content .= "=== VOORKEUREN ===\n";
    $content .= "Huidige woonsituatie: " . $get_meta('current_living_situation') . "\n";
    $content .= "Zoekt woning per: " . $get_meta('search_date') . "\n";
    $content .= "Maximale prijs: " . $get_meta('maximum_price') . "\n";
    $interested = $get_meta('interested_in');
    if (is_array($interested)) {
        $content .= "Geïnteresseerd in: " . implode(', ', $interested) . "\n";
    } else {
        $content .= "Geïnteresseerd in: " . $interested . "\n";
    }
    $content .= "Aantal kamers: " . $get_meta('number_of_rooms') . "\n";
    $content .= "Interieur: " . $get_meta('interior') . "\n\n";

    $content .= "=== BEVESTIGINGEN ===\n";
    $content .= "Privacybeleid akkoord: " . $get_meta('privacy_policy') . "\n";
    $content .= "Algemene voorwaarden akkoord: " . $get_meta('terms_agreement') . "\n\n";

    $content .= "=== SYSTEEMINFORMATIE ===\n";
    $content .= "Ingediend op: " . $get_meta('submitted_at') . "\n";
    $content .= "IP-adres: " . $get_meta('ip_address') . "\n";
    $content .= "Inzending ID: " . $post_id . "\n";

    $content .= "\n================================\n";
    $content .= "Einde van export\n";
    $content .= "================================\n";

    // Generate filename
    $filename = sanitize_file_name('registratie-' . $get_meta('first_name') . '-' . $get_meta('last_name') . '-' . $post_id . '.txt');

    // Send headers for file download
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($content));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $content;
    exit;
}

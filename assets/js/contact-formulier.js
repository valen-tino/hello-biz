/**
 * Contact Formulier JavaScript
 * 
 * Handles form validation and AJAX submission
 */

(function ($) {
    'use strict';

    /**
     * Initialize the contact form
     */
    function initContactForm() {
        const $form = $('#contact-formulier-form');

        if ($form.length === 0) {
            return;
        }

        // Initialize form validation
        initFormValidation($form);

        // Initialize form submission
        initFormSubmission($form);
    }

    /**
     * Form Validation
     */
    function initFormValidation($form) {
        // Real-time validation on blur
        $form.on('blur', 'input[required], select[required], textarea[required]', function () {
            validateField($(this));
        });

        // Remove error on input
        $form.on('input', 'input, select, textarea', function () {
            const $field = $(this);
            const $group = $field.closest('.form-group');

            if ($field.val()) {
                $group.removeClass('error');
                $group.find('.error-message').remove();
            }
        });
    }

    /**
     * Validate a single field
     */
    function validateField($field) {
        const $group = $field.closest('.form-group');
        const value = $field.val().trim();
        let isValid = true;
        let errorMessage = 'Dit veld is verplicht';

        // Check if required and empty
        if ($field.prop('required') && !value) {
            isValid = false;
        }

        // Email validation
        if ($field.attr('type') === 'email' && value) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(value)) {
                isValid = false;
                errorMessage = 'Ongeldig e-mailadres';
            }
        }

        // Phone validation (basic)
        if ($field.attr('type') === 'tel' && value) {
            const phonePattern = /^[\d\s\+\-\(\)]+$/;
            if (!phonePattern.test(value)) {
                isValid = false;
                errorMessage = 'Ongeldig telefoonnummer';
            }
        }

        // Update UI based on validation
        if (!isValid) {
            $group.addClass('error');
            if ($group.find('.error-message').length === 0) {
                $group.append('<span class="error-message">' + errorMessage + '</span>');
            }
        } else {
            $group.removeClass('error');
            $group.find('.error-message').remove();
        }

        return isValid;
    }

    /**
     * Validate entire form
     */
    function validateForm($form) {
        let isValid = true;

        $form.find('input[required], select[required], textarea[required]').each(function () {
            if (!validateField($(this))) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Form Submission
     */
    function initFormSubmission($form) {
        $form.on('submit', function (e) {
            e.preventDefault();

            // Validate form
            if (!validateForm($form)) {
                // Scroll to first error
                const $firstError = $form.find('.form-group.error').first();
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 300);
                }
                return;
            }

            // Get submit button and show loading state
            const $submitBtn = $form.find('.btn-submit');
            const originalText = $submitBtn.text();
            $submitBtn.text('Verzenden...').prop('disabled', true);

            // Show processing notice
            showProcessingNotice($form);

            // Prepare form data
            const formData = new FormData($form[0]);

            // Submit via AJAX
            $.ajax({
                url: contactFormData.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // Hide processing notice
                    hideProcessingNotice($form);

                    if (response.success) {
                        // Show success message
                        showSuccessMessage($form, response.data.message);

                        // Reset form
                        $form[0].reset();
                    } else {
                        // Show error message
                        showErrorMessage($form, response.data.message || 'Er is een fout opgetreden. Probeer het opnieuw.');
                        $submitBtn.text(originalText).prop('disabled', false);
                    }
                },
                error: function (xhr, status, error) {
                    // Hide processing notice
                    hideProcessingNotice($form);

                    showErrorMessage($form, 'Er is een fout opgetreden. Controleer uw verbinding en probeer het opnieuw.');
                    $submitBtn.text(originalText).prop('disabled', false);
                }
            });
        });
    }

    /**
     * Show processing notice
     */
    function showProcessingNotice($form) {
        const $container = $form.closest('.contact-formulier-container');

        // Remove any existing notices
        $container.find('.contact-form-processing-notice').remove();

        // Create and show processing notice
        const $processingNotice = $('<div class="contact-form-processing-notice">' +
            '<div class="processing-spinner"></div>' +
            '<p>Even geduld, uw bericht wordt verzonden...</p>' +
            '</div>');

        $container.prepend($processingNotice);

        // Scroll to notice
        $('html, body').animate({
            scrollTop: $container.offset().top - 50
        }, 300);
    }

    /**
     * Hide processing notice
     */
    function hideProcessingNotice($form) {
        const $container = $form.closest('.contact-formulier-container');
        $container.find('.contact-form-processing-notice').fadeOut(300, function () {
            $(this).remove();
        });
    }

    /**
     * Show success message
     */
    function showSuccessMessage($form, message) {
        const $container = $form.closest('.contact-formulier-container');

        // Remove any existing messages
        $container.find('.contact-form-success-message, .contact-form-error-message').remove();

        // Create and show success message
        const $successMessage = $('<div class="contact-form-success-message">' +
            '<div class="success-icon">✓</div>' +
            '<h3>Bericht Verstuurd!</h3>' +
            '<p>' + message + '</p>' +
            '</div>');

        $form.hide();
        $container.append($successMessage);

        // Scroll to message
        $('html, body').animate({
            scrollTop: $container.offset().top - 50
        }, 300);

        // Show form again after 5 seconds
        setTimeout(function () {
            $successMessage.fadeOut(300, function () {
                $(this).remove();
                $form.fadeIn(300);
            });
        }, 5000);
    }

    /**
     * Show error message
     */
    function showErrorMessage($form, message) {
        const $container = $form.closest('.contact-formulier-container');

        // Remove any existing error messages
        $container.find('.contact-form-error-message').remove();

        // Create and show error message
        const $errorMessage = $('<div class="contact-form-error-message">' +
            '<p>' + message + '</p>' +
            '</div>');

        $container.prepend($errorMessage);

        // Scroll to error
        $('html, body').animate({
            scrollTop: $container.offset().top - 50
        }, 300);

        // Auto-remove after 5 seconds
        setTimeout(function () {
            $errorMessage.fadeOut(300, function () {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function () {
        initContactForm();
    });

})(jQuery);

/**
 * Job Application Formulier JavaScript
 * 
 * Handles form validation, file upload validation, and AJAX submission
 */

(function ($) {
    'use strict';

    /**
     * Initialize the job application form
     */
    function initJobApplicationForm() {
        const $form = $('#job-application-formulier-form');

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
        $form.on('input change', 'input, select, textarea', function () {
            const $field = $(this);
            const $group = $field.closest('.form-group');

            if ($field.val()) {
                $group.removeClass('error');
                $group.find('.error-message').remove();
            }
        });

        // Radio button validation - clear error on change
        $form.on('change', 'input[type="radio"]', function () {
            const $group = $(this).closest('.form-group');
            $group.removeClass('error');
            $group.find('.error-message').remove();
        });
    }

    /**
     * Validate a single field
     */
    function validateField($field) {
        const $group = $field.closest('.form-group');
        const value = $field.val() ? $field.val().trim() : '';
        let isValid = true;
        let errorMessage = 'Dit veld is verplicht';

        // For radio buttons, check if any in the group is checked
        if ($field.attr('type') === 'radio') {
            const name = $field.attr('name');
            if ($field.prop('required') && !$('input[name="' + name + '"]:checked').length) {
                isValid = false;
                errorMessage = 'Maak een keuze';
            }
            updateFieldUI($group, isValid, errorMessage);
            return isValid;
        }

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

        // Email confirmation
        if ($field.attr('id') === 'confirm_email' && value) {
            const emailVal = $('#email').val();
            if (value !== emailVal) {
                isValid = false;
                errorMessage = 'E-mailadressen komen niet overeen';
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

        // File validation
        if ($field.attr('type') === 'file' && $field.prop('required')) {
            if (!$field[0].files || !$field[0].files.length) {
                isValid = false;
                errorMessage = 'Upload uw CV';
            } else {
                const file = $field[0].files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                const allowedExtensions = ['.pdf', '.doc', '.docx'];
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

                if (file.size > maxSize) {
                    isValid = false;
                    errorMessage = 'Bestand is te groot. Maximaal 5MB toegestaan.';
                } else if (!allowedExtensions.includes(fileExtension)) {
                    isValid = false;
                    errorMessage = 'Alleen PDF, DOC en DOCX bestanden zijn toegestaan.';
                }
            }
        }

        updateFieldUI($group, isValid, errorMessage);
        return isValid;
    }

    /**
     * Update field UI based on validity
     */
    function updateFieldUI($group, isValid, errorMessage) {
        if (!isValid) {
            $group.addClass('error');
            if ($group.find('.error-message').length === 0) {
                $group.append('<span class="error-message">' + errorMessage + '</span>');
            }
        } else {
            $group.removeClass('error');
            $group.find('.error-message').remove();
        }
    }

    /**
     * Validate entire form
     */
    function validateForm($form) {
        let isValid = true;

        // Validate standard required fields
        $form.find('input[required]:not([type="radio"]), select[required], textarea[required]').each(function () {
            if (!validateField($(this))) {
                isValid = false;
            }
        });

        // Validate radio groups
        const checkedRadioNames = [];
        $form.find('input[type="radio"][required]').each(function () {
            const name = $(this).attr('name');
            if (checkedRadioNames.indexOf(name) === -1) {
                checkedRadioNames.push(name);
                if (!validateField($(this))) {
                    isValid = false;
                }
            }
        });

        // Validate checkbox (privacy policy)
        $form.find('input[type="checkbox"][required]').each(function () {
            const $checkbox = $(this);
            const $group = $checkbox.closest('.form-group');
            if (!$checkbox.is(':checked')) {
                isValid = false;
                $group.addClass('error');
                if ($group.find('.error-message').length === 0) {
                    $group.append('<span class="error-message">U moet akkoord gaan met het privacybeleid</span>');
                }
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

            // Prepare form data (use FormData for file upload support)
            const formData = new FormData($form[0]);

            // Submit via AJAX
            $.ajax({
                url: jobApplicationFormData.ajaxUrl,
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
        const $container = $form.closest('.job-application-formulier-container');

        // Remove any existing notices
        $container.find('.job-application-form-processing-notice').remove();

        // Create and show processing notice
        const $processingNotice = $('<div class="job-application-form-processing-notice">' +
            '<div class="processing-spinner"></div>' +
            '<p>Even geduld, uw sollicitatie wordt verwerkt...</p>' +
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
        const $container = $form.closest('.job-application-formulier-container');
        $container.find('.job-application-form-processing-notice').fadeOut(300, function () {
            $(this).remove();
        });
    }

    /**
     * Show success message
     */
    function showSuccessMessage($form, message) {
        const $container = $form.closest('.job-application-formulier-container');

        // Remove any existing messages
        $container.find('.job-application-form-success-message, .job-application-form-error-message').remove();

        // Create and show success message
        const $successMessage = $('<div class="job-application-form-success-message">' +
            '<div class="success-icon">✓</div>' +
            '<h3>Sollicitatie Verstuurd!</h3>' +
            '<p>' + message + '</p>' +
            '</div>');

        $form.hide();
        $container.append($successMessage);

        // Scroll to message
        $('html, body').animate({
            scrollTop: $container.offset().top - 50
        }, 300);
    }

    /**
     * Show error message
     */
    function showErrorMessage($form, message) {
        const $container = $form.closest('.job-application-formulier-container');

        // Remove any existing error messages
        $container.find('.job-application-form-error-message').remove();

        // Create and show error message
        const $errorMessage = $('<div class="job-application-form-error-message">' +
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
        initJobApplicationForm();
    });

})(jQuery);

/**
 * Multistep Blossem Form JavaScript
 * 
 * Handles multistep navigation, Dutch number formatting, and form validation
 */

(function ($) {
    'use strict';

    // Dutch locale settings
    const DUTCH_LOCALE = {
        thousandsSeparator: '.',
        decimalSeparator: ',',
        currencySymbol: '€'
    };

    /**
     * Initialize the multistep form
     */
    function initMultistepForm() {
        const $form = $('#multistep-blossem-form');

        if ($form.length === 0) {
            return;
        }

        // Initialize step navigation
        initStepNavigation($form);

        // Initialize Dutch number formatting
        initDutchNumberFormatting($form);

        // Initialize postal code formatting
        initPostalCodeFormatting($form);

        // Initialize conditional logic
        initConditionalLogic($form);

        // Initialize form validation
        initFormValidation($form);

        // Initialize city uppercase
        initCityUppercase($form);
    }

    /**
     * Step Navigation
     */
    function initStepNavigation($form) {
        // Next button click
        $form.on('click', '.btn-next', function (e) {
            e.preventDefault();

            const currentStep = $(this).closest('.form-step').data('step');
            const nextStep = $(this).data('next');

            // Validate current step before proceeding
            if (validateStep($form, currentStep)) {
                goToStep($form, nextStep);
            }
        });

        // Previous button click
        $form.on('click', '.btn-previous', function (e) {
            e.preventDefault();

            const prevStep = $(this).data('prev');
            goToStep($form, prevStep);
        });

        // Form submission
        $form.on('submit', function (e) {
            e.preventDefault();

            // Validate final step
            if (validateStep($form, 4)) {
                // Convert Dutch formatted numbers to standard format before submission
                convertDutchNumbersForSubmission($form);

                // Get submit button and show loading state
                const $submitBtn = $form.find('.btn-submit');
                const originalText = $submitBtn.text();
                $submitBtn.text('Submitting...').prop('disabled', true);

                // Prepare form data
                const formData = new FormData($form[0]);

                // Submit via AJAX
                $.ajax({
                    url: multistepFormData.ajaxUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            // Show success message
                            showSuccessMessage($form, response.data.message);

                            // Reset form
                            $form[0].reset();
                            goToStep($form, 1);

                            // Restore Dutch number inputs
                            restoreDutchNumberInputs($form);
                        } else {
                            // Show error message
                            showErrorMessage($form, response.data.message || 'An error occurred. Please try again.');
                            $submitBtn.text(originalText).prop('disabled', false);

                            // Restore Dutch number inputs
                            restoreDutchNumberInputs($form);
                        }
                    },
                    error: function (xhr, status, error) {
                        showErrorMessage($form, 'An error occurred. Please check your connection and try again.');
                        $submitBtn.text(originalText).prop('disabled', false);

                        // Restore Dutch number inputs
                        restoreDutchNumberInputs($form);
                    }
                });
            }
        });
    }

    /**
     * Show success message
     */
    function showSuccessMessage($form, message) {
        const $container = $form.closest('.multistep-blossem-form-container');

        // Remove any existing messages
        $container.find('.form-success-message, .form-error-message').remove();

        // Create and show success message
        const $successMessage = $('<div class="form-success-message">' +
            '<div class="success-icon">✓</div>' +
            '<h3>Registratie Succesvol!</h3>' +
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
        const $container = $form.closest('.multistep-blossem-form-container');

        // Remove any existing error messages
        $container.find('.form-error-message').remove();

        // Create and show error message
        const $errorMessage = $('<div class="form-error-message">' +
            '<p>' + message + '</p>' +
            '</div>');

        $container.prepend($errorMessage);

        // Auto-remove after 5 seconds
        setTimeout(function () {
            $errorMessage.fadeOut(300, function () {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Restore Dutch number inputs after failed submission
     */
    function restoreDutchNumberInputs($form) {
        $form.find('.dutch-number-input').each(function () {
            const $input = $(this);
            const originalValue = $input.attr('data-original-value');
            if (originalValue) {
                $input.val(originalValue);
                $input.removeAttr('data-original-value');
            }
        });
    }

    /**
     * Navigate to a specific step
     */
    function goToStep($form, stepNumber) {
        // Hide all steps
        $form.find('.form-step').removeClass('active');

        // Show target step
        $form.find('.form-step[data-step="' + stepNumber + '"]').addClass('active');

        // Update progress indicator
        updateProgressIndicator(stepNumber);

        // Scroll to top of form
        $('html, body').animate({
            scrollTop: $('.multistep-blossem-form-container').offset().top - 50
        }, 300);
    }

    /**
     * Update progress indicator
     */
    function updateProgressIndicator(currentStep) {
        $('.progress-step').each(function () {
            const stepNum = $(this).data('step');

            $(this).removeClass('active completed');

            if (stepNum < currentStep) {
                $(this).addClass('completed');
            } else if (stepNum === currentStep) {
                $(this).addClass('active');
            }
        });
    }

    /**
     * Dutch Number Formatting
     */
    function initDutchNumberFormatting($form) {
        const $dutchInputs = $form.find('.dutch-number-input');

        $dutchInputs.on('input', function () {
            const $input = $(this);
            const cursorPosition = this.selectionStart;
            const oldValue = $input.val();
            const oldLength = oldValue.length;

            // Remove all formatting
            let value = oldValue.replace(/[^\d,]/g, '');

            // Replace comma with temporary placeholder
            value = value.replace(',', '|');

            // Remove all non-digits except placeholder
            value = value.replace(/[^\d|]/g, '');

            // Split by placeholder
            const parts = value.split('|');
            let integerPart = parts[0] || '';
            let decimalPart = parts[1] || '';

            // Limit decimal to 2 digits
            if (decimalPart.length > 2) {
                decimalPart = decimalPart.substring(0, 2);
            }

            // Format integer part with thousands separator
            integerPart = formatWithThousandsSeparator(integerPart);

            // Combine parts
            let formattedValue = integerPart;
            if (parts.length > 1 || oldValue.includes(',')) {
                formattedValue += ',' + decimalPart;
            }

            // Add currency symbol
            if (formattedValue && !formattedValue.startsWith('€')) {
                formattedValue = '€ ' + formattedValue;
            }

            // Update input value
            $input.val(formattedValue);

            // Restore cursor position
            const newLength = formattedValue.length;
            const lengthDiff = newLength - oldLength;
            const newCursorPosition = cursorPosition + lengthDiff;
            this.setSelectionRange(newCursorPosition, newCursorPosition);
        });

        // Format on blur to ensure proper formatting
        $dutchInputs.on('blur', function () {
            const $input = $(this);
            let value = $input.val();

            if (value) {
                // Ensure it has decimal part
                if (!value.includes(',')) {
                    value = value + ',00';
                } else {
                    const parts = value.split(',');
                    if (parts[1].length === 0) {
                        value = value + '00';
                    } else if (parts[1].length === 1) {
                        value = value + '0';
                    }
                }

                $input.val(value);
            }
        });
    }

    /**
     * Format number with thousands separator
     */
    function formatWithThousandsSeparator(num) {
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, DUTCH_LOCALE.thousandsSeparator);
    }

    /**
     * Convert Dutch formatted numbers to standard format for submission
     */
    function convertDutchNumbersForSubmission($form) {
        $form.find('.dutch-number-input').each(function () {
            const $input = $(this);
            let value = $input.val();

            // Remove currency symbol and spaces
            value = value.replace(/€\s?/g, '');

            // Replace thousands separator
            value = value.replace(/\./g, '');

            // Replace decimal separator
            value = value.replace(/,/g, '.');

            // Store in a hidden field or update the value
            $input.attr('data-original-value', $input.val());
            $input.val(value);
        });
    }

    /**
     * Postal Code Formatting
     */
    function initPostalCodeFormatting($form) {
        const $postalInputs = $form.find('input[name="postal_code"], input[name="partner_postal_code"]');

        $postalInputs.on('input', function () {
            let value = $(this).val().toUpperCase();

            // Remove all non-alphanumeric characters
            value = value.replace(/[^0-9A-Z]/g, '');

            // Format as 1234AB or 1234 AB
            if (value.length > 4) {
                value = value.substring(0, 4) + ' ' + value.substring(4, 6);
            }

            $(this).val(value);
        });
    }

    /**
     * City Uppercase
     */
    function initCityUppercase($form) {
        const $cityInputs = $form.find('input[name="city"], input[name="partner_city"]');

        $cityInputs.on('blur', function () {
            const value = $(this).val();
            $(this).val(value.toUpperCase());
        });
    }

    /**
     * Conditional Logic
     */
    function initConditionalLogic($form) {
        const $rentAloneSelect = $form.find('#rent_alone');
        const $partnerFields = $form.find('#partner-fields');

        $rentAloneSelect.on('change', function () {
            const value = $(this).val();

            if (value === 'No') {
                $partnerFields.slideDown(300);
                // Make partner fields required
                $partnerFields.find('input[type="text"], input[type="email"], select').each(function () {
                    if ($(this).attr('id') !== 'partner_address_line_2') {
                        $(this).prop('required', true);
                    }
                });
            } else {
                $partnerFields.slideUp(300);
                // Remove required from partner fields
                $partnerFields.find('input, select').prop('required', false);
                // Clear partner field values
                $partnerFields.find('input, select').val('');
            }
        });
    }

    /**
     * Form Validation
     */
    function initFormValidation($form) {
        // Email confirmation validation
        $form.on('blur', '#confirm_email', function () {
            validateEmailMatch($form, '#email', '#confirm_email');
        });

        $form.on('blur', '#partner_confirm_email', function () {
            validateEmailMatch($form, '#partner_email', '#partner_confirm_email');
        });

        // Postal code validation
        $form.on('blur', 'input[name="postal_code"], input[name="partner_postal_code"]', function () {
            validatePostalCode($(this));
        });
    }

    /**
     * Validate email match
     */
    function validateEmailMatch($form, emailSelector, confirmSelector) {
        const $email = $form.find(emailSelector);
        const $confirm = $form.find(confirmSelector);
        const $confirmGroup = $confirm.closest('.form-group');

        if ($confirm.val() && $email.val() !== $confirm.val()) {
            $confirmGroup.addClass('error');

            if ($confirmGroup.find('.error-message').length === 0) {
                $confirmGroup.append('<span class="error-message">Emails do not match</span>');
            }

            return false;
        } else {
            $confirmGroup.removeClass('error');
            $confirmGroup.find('.error-message').remove();
            return true;
        }
    }

    /**
     * Validate postal code format
     */
    function validatePostalCode($input) {
        const value = $input.val();
        const $group = $input.closest('.form-group');
        const pattern = /^\d{4}\s?[A-Z]{2}$/;

        if (value && !pattern.test(value)) {
            $group.addClass('error');

            if ($group.find('.error-message').length === 0) {
                $group.append('<span class="error-message">Invalid postal code format (e.g., 1234 AB)</span>');
            }

            return false;
        } else {
            $group.removeClass('error');
            $group.find('.error-message').remove();
            return true;
        }
    }

    /**
     * Validate a specific step
     */
    function validateStep($form, stepNumber) {
        const $step = $form.find('.form-step[data-step="' + stepNumber + '"]');
        let isValid = true;

        // Check required fields
        $step.find('input[required], select[required]').each(function () {
            const $field = $(this);
            const $group = $field.closest('.form-group');

            // Skip if field is in hidden partner section
            if ($field.closest('#partner-fields').length > 0 && !$field.closest('#partner-fields').is(':visible')) {
                return true; // continue
            }

            if (!$field.val()) {
                $group.addClass('error');

                if ($group.find('.error-message').length === 0) {
                    $group.append('<span class="error-message">This field is required</span>');
                }

                isValid = false;
            } else {
                $group.removeClass('error');
                $group.find('.error-message').remove();
            }
        });

        // Step 4 validation: interested_in is now a required select, validated by the loop above

        // Validate email matches
        if (stepNumber === 1) {
            if (!validateEmailMatch($form, '#email', '#confirm_email')) {
                isValid = false;
            }
        }

        if (stepNumber === 2) {
            const rentAlone = $form.find('#rent_alone').val();
            if (rentAlone === 'No') {
                if (!validateEmailMatch($form, '#partner_email', '#partner_confirm_email')) {
                    isValid = false;
                }
            }
        }

        return isValid;
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function () {
        initMultistepForm();
    });

})(jQuery);

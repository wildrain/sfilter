/**
 * SF Checkout Form JavaScript
 */
(function($) {
    'use strict';

    var SFCheckout = {
        init: function() {
            this.form = $('#sf-checkout-form');
            this.submitBtn = $('#sf-download-quotation');
            this.loading = this.form.find('.sf-checkout-loading');
            this.messages = this.form.find('.sf-checkout-messages');

            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            this.submitBtn.on('click', function(e) {
                e.preventDefault();
                self.handleSubmit();
            });

            // Clear errors on input
            this.form.find('input, textarea, select').on('input change', function() {
                var $field = $(this).closest('.sf-field');
                $field.removeClass('has-error');
                $field.find('.sf-field-error').text('').hide();
            });
        },

        handleSubmit: function() {
            var self = this;

            // Clear previous messages
            this.messages.empty();

            // Validate form
            if (!this.validateForm()) {
                return;
            }

            // Disable button and show loading
            this.submitBtn.prop('disabled', true);
            this.loading.show();

            // Collect form data
            var formData = this.collectFormData();

            // Send AJAX request
            $.ajax({
                url: sfCheckout.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    self.handleSuccess(response);
                },
                error: function(xhr, status, error) {
                    self.handleError(error);
                },
                complete: function() {
                    self.submitBtn.prop('disabled', false);
                    self.loading.hide();
                }
            });
        },

        validateForm: function() {
            var self = this;
            var isValid = true;
            var firstError = null;

            // Validate each field
            this.form.find('.sf-field').each(function() {
                var $field = $(this);
                var $input = $field.find('input, textarea, select');
                var fieldName = $input.attr('name');
                var value = $input.val().trim();
                var isRequired = $field.hasClass('sf-field-required');
                var inputType = $input.attr('type') || $input.prop('tagName').toLowerCase();

                // Clear previous error
                $field.removeClass('has-error');
                $field.find('.sf-field-error').text('').hide();

                // Check required
                if (isRequired && !value) {
                    self.setFieldError($field, sfCheckout.i18n.required);
                    isValid = false;
                    if (!firstError) firstError = $input;
                    return;
                }

                // Validate email
                if (inputType === 'email' && value) {
                    if (!self.isValidEmail(value)) {
                        self.setFieldError($field, sfCheckout.i18n.invalidEmail);
                        isValid = false;
                        if (!firstError) firstError = $input;
                        return;
                    }
                }

                // Validate phone
                if (inputType === 'tel' && value) {
                    if (!self.isValidPhone(value)) {
                        self.setFieldError($field, sfCheckout.i18n.invalidPhone);
                        isValid = false;
                        if (!firstError) firstError = $input;
                        return;
                    }
                }

                // Validate select
                if (inputType === 'select' && isRequired && !value) {
                    self.setFieldError($field, sfCheckout.i18n.selectRegion);
                    isValid = false;
                    if (!firstError) firstError = $input;
                    return;
                }
            });

            // Focus first error field
            if (firstError) {
                firstError.focus();
            }

            return isValid;
        },

        setFieldError: function($field, message) {
            $field.addClass('has-error');
            $field.find('.sf-field-error').text(message).show();
        },

        isValidEmail: function(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        isValidPhone: function(phone) {
            var regex = /^[\+]?[0-9\s\-\(\)]{7,20}$/;
            return regex.test(phone);
        },

        collectFormData: function() {
            var data = {
                action: 'sf_checkout_quotation',
                sf_checkout_nonce: this.form.find('[name="sf_checkout_nonce"]').val()
            };

            this.form.find('input, textarea, select').each(function() {
                var $input = $(this);
                var name = $input.attr('name');
                if (name && name !== 'sf_checkout_nonce' && name !== 'action') {
                    data[name] = $input.val();
                }
            });

            return data;
        },

        handleSuccess: function(response) {
            if (response.success) {
                // Show success message
                this.showMessage(sfCheckout.i18n.downloadReady, 'success');

                // Trigger PDF download
                if (response.data.pdf_url) {
                    this.downloadPdf(response.data.pdf_url);
                }

                // Redirect to thank you page after a short delay
                if (response.data.redirect_url) {
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url;
                    }, 1500);
                }
            } else {
                var errorMsg = response.data && response.data.message
                    ? response.data.message
                    : sfCheckout.i18n.error;
                this.showMessage(errorMsg, 'error');

                // Show field-specific errors
                if (response.data && response.data.errors) {
                    this.showFieldErrors(response.data.errors);
                }
            }
        },

        handleError: function(error) {
            this.showMessage(sfCheckout.i18n.error, 'error');
            console.error('SF Checkout Error:', error);
        },

        showMessage: function(message, type) {
            var $msg = $('<div class="sf-message sf-message-' + type + '">' + message + '</div>');
            this.messages.html($msg);
        },

        showFieldErrors: function(errors) {
            var self = this;
            $.each(errors, function(fieldName, message) {
                var $field = self.form.find('[name="' + fieldName + '"]').closest('.sf-field');
                self.setFieldError($field, message);
            });
        },

        downloadPdf: function(url) {
            // Create a temporary link and trigger download
            var link = document.createElement('a');
            link.href = url;
            link.download = '';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    };

    $(document).ready(function() {
        SFCheckout.init();
    });

})(jQuery);

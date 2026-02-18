/**
 * Quote Admin Scripts
 */
(function($) {
    'use strict';

    // PDF download click handler (delegated for order list)
    $(document).on('click', '.wc-action-button-sf_download_pdf', function(e) {
        e.preventDefault();

        var $btn = $(this);
        var $row = $btn.closest('tr');

        // Get order ID from various possible sources
        var orderId = $row.find('input[name="id[]"]').val() ||
                      $row.data('id') ||
                      ($row.attr('id') ? $row.attr('id').replace('order-', '').replace('post-', '') : null);

        if (!orderId) {
            showToast('Could not find order ID', 'error');
            return;
        }

        $btn.addClass('sf-loading');

        $.ajax({
            url: sfQuoteAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'sf_download_pdf',
                order_id: orderId,
                nonce: sfQuoteAdmin.pdfNonce
            },
            success: function(response) {
                if (response.success && response.data.url) {
                    // Trigger download via temporary link
                    var link = document.createElement('a');
                    link.href = response.data.url;
                    link.download = '';
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    showToast(response.data?.message || 'Download failed', 'error');
                }
            },
            error: function() {
                showToast('Download failed', 'error');
            },
            complete: function() {
                $btn.removeClass('sf-loading');
            }
        });
    });

    $(document).ready(function() {
        // Initialize Select2 for assignee dropdown if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('#sf_assignee').select2({
                minimumResultsForSearch: Infinity,
                width: 'calc(100% - 80px)'
            });
        }

        // Save button handler
        $('#sf_assignee_save').on('click', function() {
            var $btn = $(this);
            var $select = $('#sf_assignee');
            var orderId = $('#sf_assignee_order_id').val();
            var assignee = $select.val();
            var nonce = $('#sf_assignee_nonce').val();

            // Show loader
            $btn.addClass('sf-loading').prop('disabled', true);

            $.ajax({
                url: sfQuoteAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'sf_save_assignee',
                    order_id: orderId,
                    assignee: assignee,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.data.message, 'success');
                    } else {
                        showToast(response.data.message, 'error');
                    }
                },
                error: function() {
                    showToast('An error occurred. Please try again.', 'error');
                },
                complete: function() {
                    $btn.removeClass('sf-loading').prop('disabled', false);
                }
            });
        });
    });

    // Toast notification function
    function showToast(message, type) {
        var $toast = $('<div class="sf-toast sf-toast-' + type + '">' + message + '</div>');
        $('body').append($toast);

        setTimeout(function() {
            $toast.addClass('sf-toast-show');
        }, 10);

        setTimeout(function() {
            $toast.removeClass('sf-toast-show');
            setTimeout(function() {
                $toast.remove();
            }, 300);
        }, 3000);
    }

})(jQuery);

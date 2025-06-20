jQuery(document).ready(function($) {
    // Handle source addition
    $('.add-source').on('click', function() {
        var index = $('.source-item').length;
        var template = `
            <div class="source-item">
                <input type="text" name="manga_autoscraper_settings[sources][${index}][name]" 
                       placeholder="Source Name" class="regular-text">
                <input type="url" name="manga_autoscraper_settings[sources][${index}][url]" 
                       placeholder="Source URL" class="regular-text">
                <button type="button" class="button remove-source">Remove</button>
            </div>
        `;
        $('.sources-container').append(template);
    });
    
    // Handle source removal
    $(document).on('click', '.remove-source', function() {
        $(this).closest('.source-item').remove();
    });
    
    // Handle manual run button
    $('#run-scraper').on('click', function(e) {
        e.preventDefault();
        
        if (!mangaAutoscraper || !mangaAutoscraper.nonce) {
            console.error('Security nonce is missing');
            return;
        }
        
        const $button = $(this);
        const $spinner = $button.next('.spinner');
        
        // Disable button and show spinner
        $button.prop('disabled', true);
        $spinner.addClass('is-active');
        
        // Send AJAX request
        $.ajax({
            url: mangaAutoscraper.ajaxUrl,
            type: 'POST',
            data: {
                action: 'manga_autoscraper_run',
                nonce: mangaAutoscraper.nonce,
                _ajax_nonce: mangaAutoscraper.nonce // WordPress nonce
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.data.message);
                    // Reload page to update status
                    location.reload();
                } else {
                    // Show error message
                    const message = response.data && response.data.message ? 
                        response.data.message : 
                        'An error occurred';
                    alert(message);
                }
            },
            error: function(xhr, status, error) {
                // Log error details
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                // Show user-friendly error message
                alert('Failed to communicate with the server. Please try again later.');
            },
            complete: function() {
                // Re-enable button and hide spinner
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });

    // Handle manual scrape button click
    $('#mas-scrape-now').on('click', function() {
        var $button = $(this);
        var $spinner = $button.next('.spinner');
        var $status = $('#mas-scrape-status');
        
        $button.prop('disabled', true);
        $spinner.addClass('is-active');
        $status.html('');
        
        $.ajax({
            url: masAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'mas_scrape_now',
                nonce: masAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.html('<div class="notice notice-success"><p>' + response.data + '</p></div>');
                } else {
                    $status.html('<div class="notice notice-error"><p>' + response.data + '</p></div>');
                }
            },
            error: function() {
                $status.html('<div class="notice notice-error"><p>An error occurred while scraping.</p></div>');
            },
            complete: function() {
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });

    // Handle schedule interval change
    $('select[name="mas_schedule_interval"]').on('change', function() {
        var $select = $(this);
        var $spinner = $('<span class="spinner is-active"></span>');
        
        // Remove any existing notices before adding a new one
        $select.siblings('.notice').remove();
        $select.after($spinner);
        
        $.ajax({
            url: masAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'mas_update_schedule',
                nonce: masAdmin.nonce,
                interval: $select.val()
            },
            success: function(response) {
                $select.siblings('.notice').remove(); // Remove again in case AJAX is fast
                var $notice;
                if (response.success) {
                    $notice = $('<div class="notice notice-success"><p>Schedule updated successfully.</p></div>');
                    $select.after($notice);
                } else {
                    $notice = $('<div class="notice notice-error"><p>' + response.data + '</p></div>');
                    $select.after($notice);
                }
                setTimeout(function() {
                    $notice.fadeOut(400, function() { $(this).remove(); });
                }, 2000);
            },
            error: function() {
                $select.siblings('.notice').remove();
                $select.after('<div class="notice notice-error"><p>Failed to update schedule.</p></div>');
            },
            complete: function() {
                $spinner.remove();
            }
        });
    });

    // Handle source checkboxes
    $('input[name^="mas_sources"]').on('change', function() {
        var $checkbox = $(this);
        var $spinner = $('<span class="spinner is-active"></span>');
        
        $checkbox.after($spinner);
        
        $.ajax({
            url: masAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'mas_update_sources',
                nonce: masAdmin.nonce,
                source: $checkbox.attr('name').match(/\[(.*?)\]/)[1],
                enabled: $checkbox.is(':checked')
            },
            success: function(response) {
                $checkbox.siblings('.notice').remove();
                var $notice;
                var isChecked = $checkbox.is(':checked');
                var message = '';
                if (response.success) {
                    message = isChecked ? 'Source enabled successfully.' : 'Source disabled successfully.';
                    $notice = $('<div class="notice notice-success"><p>' + message + '</p></div>');
                } else {
                    message = isChecked ? 'Failed to enable source.' : 'Failed to disable source.';
                    $notice = $('<div class="notice notice-error"><p>' + message + '</p></div>');
                }
                $checkbox.after($notice);
                setTimeout(function() {
                    $notice.fadeOut(400, function() { $(this).remove(); });
                }, 2000);
            },
            error: function() {
                $checkbox.siblings('.notice').remove();
                var isChecked = $checkbox.is(':checked');
                var message = isChecked ? 'Failed to enable source.' : 'Failed to disable source.';
                var $notice = $('<div class="notice notice-error"><p>' + message + '</p></div>');
                $checkbox.after($notice);
                setTimeout(function() {
                    $notice.fadeOut(400, function() { $(this).remove(); });
                }, 2000);
            },
            complete: function() {
                $spinner.remove();
            }
        });
    });
}); 
(function($) {
    'use strict';

    // Cross References
    var crossRefIndex = $('#sf-pb-rows tr').length;

    $('#sf-pb-add-row').on('click', function(e) {
        e.preventDefault();
        var row = '<tr class="sf-pb-row">' +
            '<td><input type="text" name="sf_cross_refs[' + crossRefIndex + '][manufacturer]" value="" class="widefat"></td>' +
            '<td><textarea name="sf_cross_refs[' + crossRefIndex + '][codes]" class="widefat" rows="3"></textarea></td>' +
            '<td class="sf-pb-col-action"><button type="button" class="button sf-pb-remove-row">&times;</button></td>' +
            '</tr>';
        $('#sf-pb-rows').append(row);
        crossRefIndex++;
    });

    // Applications
    var appIndex = $('#sf-pb-app-rows tr').length;

    $('#sf-pb-add-app-row').on('click', function(e) {
        e.preventDefault();
        var row = '<tr class="sf-pb-row">' +
            '<td><input type="text" name="sf_applications[' + appIndex + '][make]" value="" class="widefat"></td>' +
            '<td><input type="text" name="sf_applications[' + appIndex + '][model]" value="" class="widefat"></td>' +
            '<td><input type="number" name="sf_applications[' + appIndex + '][year_from]" value="" class="widefat" min="1900" max="2100"></td>' +
            '<td><input type="number" name="sf_applications[' + appIndex + '][year_to]" value="" class="widefat" min="1900" max="2100"></td>' +
            '<td class="sf-pb-col-action"><button type="button" class="button sf-pb-remove-row">&times;</button></td>' +
            '</tr>';
        $('#sf-pb-app-rows').append(row);
        appIndex++;
    });

    // Remove row (shared for both metaboxes)
    $(document).on('click', '.sf-pb-remove-row', function(e) {
        e.preventDefault();
        var $row = $(this).closest('tr');
        var $tbody = $row.closest('tbody');
        if ($tbody.find('tr').length > 1) {
            $row.remove();
        } else {
            $row.find('input, textarea').val('');
        }
    });

})(jQuery);

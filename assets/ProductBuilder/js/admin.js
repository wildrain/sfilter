(function($) {
    'use strict';

    var rowIndex = $('#sf-pb-rows tr').length;

    $('#sf-pb-add-row').on('click', function(e) {
        e.preventDefault();
        var row = '<tr class="sf-pb-row">' +
            '<td><input type="text" name="sf_cross_refs[' + rowIndex + '][manufacturer]" value="" class="widefat"></td>' +
            '<td><textarea name="sf_cross_refs[' + rowIndex + '][codes]" class="widefat" rows="3"></textarea></td>' +
            '<td class="sf-pb-col-action"><button type="button" class="button sf-pb-remove-row">&times;</button></td>' +
            '</tr>';
        $('#sf-pb-rows').append(row);
        rowIndex++;
    });

    $(document).on('click', '.sf-pb-remove-row', function(e) {
        e.preventDefault();
        var $row = $(this).closest('tr');
        if ($('#sf-pb-rows tr').length > 1) {
            $row.remove();
        } else {
            $row.find('input, textarea').val('');
        }
    });

})(jQuery);

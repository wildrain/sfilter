(function ($) {
  $('#sfilter-enquiry-form form').on('submit', function (event) {
    event.preventDefault();

    var data = $(this).serialize();

    $.post(sfilter_data.ajax_url, data, function (response) {
      console.log('response ', response);
    }).fail(function () {
      console.log(sfilter_data.message);
    });
  });
})(jQuery);

"use strict";

(function ($, window) {
  // from jquery.validate.additional.js
  $.validator.addMethod("matadorMaxsize", function (value, element, param) {
    if (this.optional(element)) {
      return true;
    }

    if ($(element).attr("type") === "file") {
      if (element.files && element.files.length) {
        for (var i = 0; i < element.files.length; i++) {
          if (element.files[i].size > param) {
            return false;
          }
        }
      }
    }

    return true;
  }, $.validator.format("File size must not exceed {0} bytes each.")); // from jquery.validate.additional.js

  $.validator.addMethod("matadorExtension", function (value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
  }, $.validator.format("Please enter a value with a valid extension."));
  $('#matador-application-form').validate({
    submitHandler: function submitHandler(form) {
      $("[name='submit']").attr("disabled", true).addClass('madator-disabled');
      $('#matador-upload-overlay').show();
      return true;
    }
  });
  $('.inputfile').each(function () {
    var $input = $(this);
    $input.rules('add', {
      matadorMaxsize: 1048576,
      matadorExtension: 'pdf|doc|docx|html|htm|txt'
    });
    $input.on('change', function (e) {
      var fileName = '',
          $current_input = $(e.target),
          $label = $current_input.prev('label'),
          labelVal = $label.html();
      if (this.files && this.files.length > 1) fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);else if (e.target.value) fileName = e.target.value.split('\\').pop();
      if (fileName) $label.find('span').html(fileName);else $label.html(labelVal);
    }); // Firefox bug fix

    $input.on('focus', function () {
      $input.addClass('has-focus');
    }).on('blur', function () {
      $input.removeClass('has-focus');
    });
  }); // auto reload page on select change for filters

  $('.matador-terms-select select[data-method="link"], .matador-terms-select select[data-method="filter"]').each(function () {
    $(this).on('change', function () {
      window.location = $(this).find('option:selected').data('url');
    });
  });
})(jQuery, window);
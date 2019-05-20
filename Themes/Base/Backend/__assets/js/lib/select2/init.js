(function ($) {
    $('.select2').each(function(index, element) {
        $(element).select2({
            dropdownParent: $(element).parent(),
            placeholder: $(element).data('placeholder'),
            allowClear: true,
            dropdownAutoWidth: true,
            containerCss : { width: '100%', }
        });
    });
})(jQuery);

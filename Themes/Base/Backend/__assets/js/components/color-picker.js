(function (window, document, $) {
    $('.oforge-color-picker').colorpicker({
        component: '.add-on.oforge-color-picker--button, .input-group-addon.oforge-color-picker--button',
        align: 'left'
    });
    $('.oforge-color-picker--reset').click(function () {
        $(this).parent().colorpicker('setValue', $(this).data('color'));
    });
})(window, document, jQuery);

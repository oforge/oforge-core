(function ($) {
    $('.oforge-color-picker').colorpicker();

    var $dataTimePickerConfiguration = {
        toolbarPlacement: 'top',
        widgetPositioning: {horizontal: 'left', vertical: 'auto'},
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
        }
    };
    var dateTimePickers = [
        [$(".oforge-date-picker"), $dataTimePickerConfiguration],
        [$(".oforge-time-picker"), $dataTimePickerConfiguration],
        [$(".oforge-datetime-picker"), $dataTimePickerConfiguration],
    ];
    dateTimePickers.forEach(function (array) {
        var $pickers = array[0];
        var pickerConfig = array[1];
        $pickers.each(function () {
            $(this).datetimepicker($.extend({}, pickerConfig, $(this).data('configuration')));
        });
        $pickers.focusout(function () {
            $pickers.datetimepicker('hide');
        })
    });

})(jQuery);

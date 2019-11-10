(function ($) {
    $('.oforge-listbox').each(function (index, element) {
        var config = {};
        if (element.dataset.sort) {
            config.keepRenderingSort = true;
        }
        if (element.dataset.search) {
            config.search = {
                left: '<input type="text" class="form-control" placeholder="' + element.dataset.search + '" />',
                right: '<input type="text" class="form-control" placeholder="' + element.dataset.search + '" />'
            };
        }
        if (element.dataset.search_min) {
            config.fireSearch = function (value) {
                return value.length > element.dataset.search_min;
            }
        }
        $(element).multiselect(config);
    });
})(jQuery);

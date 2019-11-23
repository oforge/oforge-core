(function ($) {
    $('.oforge-listbox').each(function (index, element) {
        var config = {
            keepRenderingSort: !!element.dataset.sorted
        };
        if (element.dataset.searchable) {
            config.search = {
                left: '#' + element.id + '_leftSearch',
                right: '#' + element.id + '_rightSearch',
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

(function ($) {
    var $form = $('#oforge-crud-filters');
    var $filterFields = $form.find('*[name]');

    $form.submit(function (event) {
        if (typeof Oforge !== 'undefined') {
            event.preventDefault();
            var href = window.location.href;
            $filterFields.each(function() {
                href = Oforge.updateQueryString($(this).attr('name'), $(this).val(), href, true);
            });
            document.location.href = href.replace(/&$/, "");
            return false;
        }
    });
})(jQuery);

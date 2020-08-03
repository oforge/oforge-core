(function ($, Oforge, window, document, undefined) {

    function initTextareaAutoresize($elements) {
        $elements.each(function () {
            const $element = $(this).removeAttr('data-autoresize');
            const offset = $element[0].offsetHeight - $element[0].clientHeight;
            $element.on('input', function (event) {
                event.target.style.height = 'auto';
                event.target.style.height = event.target.scrollHeight + offset + 'px';
            });
        })
    }

    initTextareaAutoresize($('textarea[data-autoresize]'));

    if (typeof Oforge !== 'undefined') {
        Oforge.initTextareaAutoresize = initTextareaAutoresize;
    }

})(jQuery, Oforge, window, document);

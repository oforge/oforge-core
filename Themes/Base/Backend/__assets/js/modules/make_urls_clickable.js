(function ($, Oforge, window, document, undefined) {
    var convertExpresion = /(\b((https?|ftp|file):\/\/|www.)[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;

    function makeUrlsClickable($elements) {
        $elements.each(function () {
            const $element = $(this);
            $element.html($element.html().replace(convertExpresion, '<a target="_blank" href="$1">$1</a>'));
        })
    }

    makeUrlsClickable($('.make_urls_clickable').removeClass('make_urls_clickable'));

    if (typeof Oforge !== 'undefined') {
        Oforge.makeUrlsClickable = makeUrlsClickable;
    }

})(jQuery, Oforge, window, document);

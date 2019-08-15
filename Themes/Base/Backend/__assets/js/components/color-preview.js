(function (window, document, $) {
    const $elements = $('.oforge-color-preview');
    if ($elements.length === 0) {
        return;
    }
    $elements.each(function () {
        var $element = $(this);
        var baseText = $element.text();
        if ($element.data('text')) {
            var $textSource = $($element.data('text'));
            if ($textSource) {
                $textSource.keyup(function () {
                    $element.text($textSource.val() === '' ? baseText : $textSource.val());
                }).keyup();
            }
        }
        if ($element.data('bg')) {
            var $backgroundSource = $($element.data('bg'));
            if ($backgroundSource) {
                $backgroundSource.keyup(function () {
                    $element.css('background-color', $backgroundSource.val());
                });
                $backgroundSource.change(function () {
                    $element.css('background-color',$backgroundSource.val());
                }).change();
            }
        }
        if ($element.data('fg')) {
            var $colorSource = $($element.data('fg'));
            if ($colorSource) {
                $colorSource.keyup(function () {
                    $element.css('color', $colorSource.val());
                });
                $colorSource.change(function () {
                    $element.css('color', $colorSource.val());
                }).change();
            }
        }
    });


})(window, document, jQuery);

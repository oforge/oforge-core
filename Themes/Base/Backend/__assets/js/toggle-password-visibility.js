(function ($) {
    $('.oforge-toggle-password-visibility').click(function () {
        var $prev = $(this).parent().prev();
        $prev.attr('type', $prev.attr('type') === 'text' ? 'password' : 'text');
    });
})(jQuery);

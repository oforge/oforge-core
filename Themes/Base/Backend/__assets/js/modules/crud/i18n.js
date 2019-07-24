(function ($) {
    $('#module_i18n_snippet_comparator_swap_language').click(function () {
        var $inputL1 = $(this).parent().find('[name=language1]'), $inputL2 = $(this).parent().find('[name=language2]');
        var tmp = $inputL1.val();
        $inputL1.val($inputL2.val()).change();
        $inputL2.val(tmp).change();
    });
    $('.module-i18n-snippet-comparator-language-copy').click(function(){
        var $parent = $(this).parent().parent().parent();
        var $src = $parent.find('[data-copy_id=' + this.dataset.src + ']');
        var $dst = $parent.find('[data-copy_id=' + this.dataset.dst + ']');
        $dst.val($src.val());
    });
})(jQuery);

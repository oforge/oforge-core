if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'CmsContentTypeEditorHtml',
        selector: '.content-type-form-html',
        init: function () {
            var $target = $(this.target);
            var $field = $target.find('#content-type-form-html-text');
            var $preview = $target.find('#content-type-form-html-text-preview');
            $field.on('change keyup paste', function () {
                $preview.html($field.val());
            }).change();
        }
    });
}

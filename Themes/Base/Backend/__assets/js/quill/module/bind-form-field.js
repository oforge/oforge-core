(function ($, Quill) {
    if (!Quill) {
        return;
    }

    class BindFormField {
        constructor(quill, field) {
            this.quill = quill;
            this.field = field;
            this.quill.on('text-change', this.onQuillChange.bind(this));
        }

        onQuillChange() {
            this.field.value = this.quill.root.innerHTML;
        }
    }

    Quill.register('modules/BindFormField', BindFormField);

    // Quill.register('modules/BindFormField', function(quill, field) {
    //     quill.on('text-change', function() {
    //         field.value = quill.root.innerHTML;
    //     });
    // });
})(jQuery, Quill);

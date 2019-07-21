/* Quill module: BindFormField */
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
})(jQuery, Quill);

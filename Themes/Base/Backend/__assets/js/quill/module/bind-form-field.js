/* Quill module: BindFormField */
(function ($, Quill) {
    if (!Quill) {
        return;
    }

    class BindFormField {
        constructor(quill, options) {
            this.quill = quill;
            this.options = options;
            this.quill.on('text-change', this.onQuillChange.bind(this));
        }

        onQuillChange() {
            if(this.update) {
                this.options.formField.value = this.quill.root.innerHTML;
            }
            this.update = true;
        }
    }

    Quill.register('modules/BindFormField', BindFormField);
})(jQuery, Quill);

/* Quill module: BindFormField */
(function ($, Quill) {
    if (!Quill) {
        return;
    }

    class WhitespaceButton {
        constructor(quill, field) {
            this.quill = quill;
            this.button = $(quill.container).prev().find('.ql-WhitespaceButton');
            this.button.click(this.onToolbarButtonClick.bind(this));
        }
        onToolbarButtonClick() {
            this.button.toggleClass('ql-active');
            const range = this.quill.getSelection();
            if (range) {
                this.quill.clipboard.dangerouslyPasteHTML(range.index, '<span>&nbsp;</span>');
            }
            this.button.toggleClass('ql-active');
        }
    }

    Quill.register('modules/WhitespaceButton', WhitespaceButton);
    let icons = Quill.import('ui/icons');
    icons['WhitespaceButton'] = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path class="ql-fill" d="M3 4a1 1 0 1 0 0 2h1v18H3a1 1 0 1 0 0 2h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H3zm3 11c0 .256.097.512.293.707l3 3A1 1 0 0 0 11 18v-2h8v2a.999.999 0 0 0 1.707.707l3-3a.996.996 0 0 0 0-1.414l-3-3A1 1 0 0 0 19 12v2h-8v-2a.999.999 0 0 0-1.707-.707l-3 3A.996.996 0 0 0 6 15zm18 0v10a1 1 0 0 0 1 1h2a1 1 0 1 0 0-2h-1V6h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v10z"/></svg>';
})(jQuery, Quill);

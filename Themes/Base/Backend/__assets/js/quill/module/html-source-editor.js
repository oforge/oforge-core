(function ($, Quill) {
    if (!Quill) {
        return;
    }

    class HtmlSourceEditor {
        constructor(quill, options) {
            this.quill = quill;
            this.options = options;

            this.avoidUpdate = false;
            this.button = $(quill.container).prev().find('.ql-HtmlSourceEditor');
            this.textarea = $('<textarea></textarea>').hide();

            this.customContainer = quill.addContainer('ql-html-source-editor');
            this.customContainer.appendChild(this.textarea.get(0));


            this.quill.on('text-change', this.onQuillChange.bind(this));
            this.textarea.on('change keyup paste', this.onTextareaChange.bind(this));
            this.button.click(this.onToolbarButtonClick.bind(this));
        }

        onQuillChange() {
            if (!this.avoidUpdate) {
                this.textarea.val(this.quill.root.innerHTML);
            }
            this.avoidUpdate = false;
        }

        onTextareaChange() {
            this.avoidUpdate = true;
            this.quill.root.innerHTML = this.textarea.val();
        }

        onToolbarButtonClick() {
            this.textarea.toggle();
            this.button.toggleClass('ql-active');
        }

    }

    Quill.register('modules/HtmlSourceEditor', HtmlSourceEditor);

    let icons = Quill.import('ui/icons');
    icons['HtmlSourceEditor'] = '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="file-code" class="svg-inline--fa fa-file-code fa-w-12" role="img" viewBox="0 0 384 512"><path fill="currentColor" d="M149.9 349.1l-.2-.2-32.8-28.9 32.8-28.9c3.6-3.2 4-8.8.8-12.4l-.2-.2-17.4-18.6c-3.4-3.6-9-3.7-12.4-.4l-57.7 54.1c-3.7 3.5-3.7 9.4 0 12.8l57.7 54.1c1.6 1.5 3.8 2.4 6 2.4 2.4 0 4.8-1 6.4-2.8l17.4-18.6c3.3-3.5 3.1-9.1-.4-12.4zm220-251.2L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM256 51.9l76.1 76.1H256zM336 464H48V48h160v104c0 13.3 10.7 24 24 24h104zM209.6 214c-4.7-1.4-9.5 1.3-10.9 6L144 408.1c-1.4 4.7 1.3 9.6 6 10.9l24.4 7.1c4.7 1.4 9.6-1.4 10.9-6L240 231.9c1.4-4.7-1.3-9.6-6-10.9zm24.5 76.9l.2.2 32.8 28.9-32.8 28.9c-3.6 3.2-4 8.8-.8 12.4l.2.2 17.4 18.6c3.3 3.5 8.9 3.7 12.4.4l57.7-54.1c3.7-3.5 3.7-9.4 0-12.8l-57.7-54.1c-3.5-3.3-9.1-3.2-12.4.4l-17.4 18.6c-3.3 3.5-3.1 9.1.4 12.4z"></path></svg>';

    // Quill.register('modules/htmlSource', function(quill, options) {
    //     var avoidUpdate = false;
    //     var $button = $(quill.container)
    //         .prev()
    //         .find('.ql-HtmlSourceEditor');
    //     var $textarea = $('<textarea></textarea>').addClass('ql-html-source-editor').hide();
    //     var customContainer = quill.addContainer('ql-custom');
    //     customContainer.appendChild($textarea.get(0));
    //
    //     $textarea.on('change keyup paste', function() {
    //         avoidUpdate = true;
    //         quill.root.innerHTML = $textarea.val();
    //     });
    //
    //     quill.on('text-change', (delta, oldDelta, source) => {
    //         if (!avoidUpdate) {
    //             $textarea.val(quill.root.innerHTML);
    //         }
    //         avoidUpdate = false;
    //     });
    //
    //     $button.click(function() {
    //         $textarea.toggle();
    //         $button.toggleClass('ql-active');
    //     });
    // });
})(jQuery, Quill);

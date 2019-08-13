/*
 * Oforge Quill wrapper.
 */
var OforgeQuill = (function ($) {
    const toolbarConfig = [
        [{header: [1, 2, 3, 4, 5, 6, false]}],
        [{font: []}],
        [{size: ['small', false, 'large', 'huge']}], // custom dropdown
        ['bold', 'italic', 'underline', 'strike', {script: 'sub'}, {script: 'super'}], // toggled buttons, superscript/subscript
        [{color: []}, {background: []}], // dropdown with defaults from theme
        [{align: []}, {indent: '-1'}, {indent: '+1'}], // outdent/indent

        //[{ 'header': 1 }, { 'header': 2 }],               // custom button values
        ['blockquote', 'code-block', {list: 'ordered'}, {list: 'bullet'}, {list: 'check'}],
        ['link', 'image'], // ['link', 'video'],
        ['clean'], // remove formatting button
        ['HtmlSourceEditor']
    ];

    const initFormFields = function () {
        const formFieldSelector = '.oforge-quill-editable';
        $(formFieldSelector).each(function (index, field) {
            var $inputField = $(field).hide();
            var $editorContentContainer = $('<div></div>').html($inputField.val());
            var $editorWrapper = $('<div></div>').addClass('html-editor-wrapper')
                .append($editorContentContainer).insertAfter($inputField);
            var quill = new Quill($editorContentContainer.get(0), {
                modules: {
                    imageResize: {
                        displaySize: true
                    },
                    toolbar: toolbarConfig,
                    BindFormField: $inputField.get(0),
                    HtmlSourceEditor: true,
                },
                // placeholder: 'Compose an epic...',
                debug: 'error',
                theme: 'snow'
            });

            // Handlers can also be added post initialization
            var toolbar = quill.getModule('toolbar');
            toolbar.addHandler('image',  function () {
                Oforge.Media.open({
                    callback: function (media) {
                        // Save current cursor state
                        const range = quill.getSelection(true);
                        // Insert uploaded image
                        quill.insertEmbed(range.index, 'image', media.path);
                    }
                });
            });
        });
    };

    const init = function () {
        initFormFields();
    };

    return {
        init: init,
    }
})(jQuery);

OforgeQuill.init();

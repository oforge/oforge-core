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
        ['link',], // ['link', 'video'],
        ['clean'], // remove formatting button
        ['HtmlSourceEditor']
    ];

    const initFormFields = function () {
        const formFieldSelector = '.oforge-quill-editable';
        $(formFieldSelector).each(function (index, field) {
            let $inputField = $(field).hide();
            let $editorContentContainer = $('<div></div>').html($inputField.val());
            let $editorWrapper = $('<div></div>').addClass('html-editor-wrapper')
                .append($editorContentContainer).insertAfter($inputField);
            let quill = new Quill($editorContentContainer.get(0), {
                modules: {
                    toolbar: toolbarConfig,
                    BindFormField: $inputField.get(0),
                    HtmlSourceEditor: true,
                },
                // placeholder: 'Compose an epic...',
                debug: 'error',
                theme: 'snow'
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

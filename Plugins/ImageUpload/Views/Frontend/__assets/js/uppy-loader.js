if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'uppyUpload',
        selector: '#drag-drop-area',
        init: function () {
            var self = this;
            var fileQueue = [];
            var url = document.querySelector(self.selector).dataset.url;
            var uppy = Uppy.Core();
            uppy.use(Uppy.Dashboard, {
                inline: true,
                target: self.selector,
            }).use(Uppy.XHRUpload, {
                endpoint: url,
                method: 'post',
                formData: true,
                fieldName: 'files[]',
            });

            // TODO: Upload queue
            uppy.on('file-added', function(file) {
                // TODO: Add file to upload queue
                // TODO: disable next button
            });

            uppy.on('upload-success', function(file, response) {
                console.log(response);
                // TODO: Remove file from upload queue
                // TODO: if last file activate next button
            });
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}


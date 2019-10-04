if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'uppyUpload',
        selector: '#drag-drop-area',
        init: function () {
            var self = this;
            var fileQueue = 0;
            var url = document.querySelector(self.selector).dataset.url;
            var uppy = Uppy.Core({
                autoProceed: true
            });
            uppy.use(Uppy.Dashboard, {
                inline: true,
                target: self.selector,
            }).use(Uppy.XHRUpload, {
                endpoint: url,
                method: 'post',
                formData: true,
                fieldName: 'files[]',
            });
            console.log("blub");

            uppy.on('file-added', function(file) {
                // Todo: disable button
                fileQueue += 1;
            });

            uppy.on('upload-success', function(file, response) {
                console.log(response);
                if(response.status >= 200 && response.status < 300) {
                    fileQueue -= 1;
                    if(fileQueue === 0) {
                        // Todo: activate button
                    }
                }
            });
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}


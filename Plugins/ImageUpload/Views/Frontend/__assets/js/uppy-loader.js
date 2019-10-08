if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'uppyUpload',
        selector: '#drag-drop-area',
        init: function () {
            var dataInput = document.querySelector("#imagesBlub");
            var self = this;
            var url = document.querySelector(self.selector).dataset.url;
            var uppy = Uppy.Core({
                locale: Uppy.locales.de_DE,
                allowedFileTypes: ['image/*'],
            });
            uppy.use(Uppy.Dashboard, {
                inline: true,
                width: 750,
                height: 300,
                target: self.selector,
                proudlyDisplayPoweredByUppy: true
            }).use(Uppy.XHRUpload, {
                endpoint: url,
                method: 'post',
                formData: true,
                fieldName: 'files[]',
            });

            var data = dataInput.dataset.img;

            fetch("/var/public/images/6b/72/asdawdawdwad_usc50foc051b021001_600.jpg").then(function (response) {
                return response.blob();
            }).then(function (blob) {
                uppy.addFile({
                    name: 'image.jpg',
                    type: blob.type,
                    data: blob // changed blob -> data
                });

                console.log("1");
            }).then(function () {
                window.timtom = uppy;
                uppy.getFiles().forEach(function (file) {

                    console.log("2", file.id);
                    uppy.setFileState(file.id, {
                        progress: {uploadComplete: true, uploadStarted: false}
                    })
                })
            });
            console.log(uppy);

            uppy.on('upload', function (file) {
                // TODO: waiting for kevin
                console.log("upload started");
            });

            uppy.on('upload-retry', function (file) {
                // TODO: waiting for kevin
            });

            uppy.on('upload-error', function (file, response) {
                // TODO: waiting for kevin
            });

            uppy.on('upload-success', function (file, response) {
                // TODO: waiting for kevin
                console.log(response);
                if (response.status >= 200 && response.status < 300) {
                    var data = dataInput.dataset.img;
                    data = JSON.parse(data);
                    console.log(data);
                    data.push(response.body.imageData);
                    dataInput.dataset.img = JSON.stringify(data);
                }
            });
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}


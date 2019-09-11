if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'videoUpload',
        selector: '[data-upload-video]',
        selectors: {
            uploadButton: '[data-video-upload-button]',
            videoFileInput: '[data-file-input-video]'
        },
        init: function () {
            var self = this;
            var videoTypes = [
                'video/mp4',
            ];
            var maxFileUploadSize = 100 * 1000 * 1000; // 100MB
            var videoUploadButton = document.querySelector(self.selectors.uploadButton);
            var videoFileInput = document.querySelector(self.selectors.videoFileInput);

            videoUploadButton.addEventListener('click', function (e) {
                videoFileInput.click();
            });

            videoFileInput.addEventListener('change', function (e) {
                var input = this;
                var fileSize = 0;
                var fileUrl = "";

                input.files.forEach(function (file) {
                    if (videoTypes.indexOf(file.type) > -1) {
                        fileSize += file.size;
                        if (checkFileSize(fileSize) === false) {
                            input.value = "";
                            return false;
                        }
                        fileUrl = window.URL.createObjectURL(file);
                    }
                });
            });

            function checkFileSize(fileSize) {
                var maxSizeMessageElement = document.querySelector('.upload--video.upload__max-size-exceeded');

                if (fileSize > maxFileUploadSize) {
                    // show message
                    maxSizeMessageElement.classList.remove('hidden');
                    setTimeout(function () {
                        maxSizeMessageElement.classList.add('hidden');
                    }, 5000);
                    console.warn('file size exceeded');

                    return false;
                }
                return true;
            }
        }
    });
}

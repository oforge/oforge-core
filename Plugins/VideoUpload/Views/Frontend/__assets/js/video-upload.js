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
                    console.log(file);

                    if (videoTypes.indexOf(file.type) > -1) {
                        fileSize += file.size;
                        if (checkFileSize(fileSize) === false) {
                            input.value = "";
                            return false;
                        }

                        var fileReader = new FileReader();
                        //fileUrl = window.URL.createObjectURL(file);
                        fileReader.readAsDataURL(file);
                        fileReader.addEventListener('loadend', function (e) {
                            $.ajax({
                                method: "POST",
                                url: 'https://api.vimeo.com/me/videos',
                                headers: {
                                    "Authorization": "Bearer "
                                },
                                data: {
                                    upload: {
                                        approach: "tus",
                                        size: file.size
                                    }
                                },
                                dataType: "json"
                            })
                            .fail(function (e) {
                                console.log(e);
                            })
                            .done(function (response) {
                                $.ajax({
                                    method: "PATCH",
                                    url: response.upload.upload_link,
                                    headers: {
                                        "Tus-Resumable": "1.0.0",
                                        "Upload-Offset": "0",
                                        "Content-Type": "application/offset+octet-stream",
                                        "Accept": "application/vnd.vimeo.*+json;version=3.4"
                                    },
                                    data: fileReader.result
                                })
                                .fail(function (e) {
                                    console.log(e);
                                })
                                .done(function (msg) {
                                    console.log(msg);
                                });
                            });
                        });
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

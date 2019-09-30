if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'videoUpload',
        selector: '[data-upload-video]',
        classNames: {
            form: 'form--submit--loading',
            formSubmitButton: 'form__input--submit--loading'
        },
        selectors: {
            uploadButton: '[data-video-upload-button]',
            videoFileInput: '[data-file-input-video]',
            form: '.form--submit--loading',
            formSubmitButton: '.form__input--submit--loading'
        },
        init: function () {
            var self = this;
            var videoTypes = [
                'video/mp4',
            ];
            var maxFileUploadSize = 100 * 1000 * 1000; // 100MB
            var maxFileCount = 1;
            var videoUploadButton = document.querySelector(self.selectors.uploadButton);
            var videoFileInput = document.querySelector(self.selectors.videoFileInput);

            videoUploadButton.addEventListener('click', function (e) {
                videoFileInput.click();
            });

            videoFileInput.addEventListener('change', function (e) {
                var input = this;
                var fileSize = 0;
                var fileUrl = "";


                if (input.files.length <= maxFileCount) {
                    input.files.forEach(function (file) {
                        if (videoTypes.indexOf(file.type) > -1) {
                            fileSize += file.size;
                            if (checkFileSize(fileSize) === false) {
                                input.value = "";
                                return false;
                            }

                            disableSubmitButton();
                            displayPlaceholderImage();

                            var uploader = new VimeoUpload({
                                file: input.files[0],
                                token: "98cc11b80aa5107bc60f38cb31f944c9",
                                name: file.name,
                                description: 'Default description',
                                onProgress: function (data) {
                                    let progressBar = document.getElementById('upload-progress');
                                    progressBar.value = data.loaded / data.total * 100;
                                },

                                onComplete: function (data) {
                                    fileUrl = this.api_url + '/' + data;
                                    fetchVideoThumbnail(this.api_url, data, this.token);
                                    enableSubmitButton();
                                },
                                onError: function (data) {
                                    displayErrorMessage(data);
                                    enableSubmitButton();
                                }
                            });
                            uploader.upload();
                        }
                    });
                }

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

            function disableSubmitButton() {
                let form = $(self.selectors.form);
                let submitButton = form.find(self.selectors.formSubmitButton);
                if (submitButton.length > 0) {
                    submitButton.attr("disabled", true);
                    submitButton.children(".default-text").hide();
                    submitButton.children(".submit-text").show();
                }
            }

            function enableSubmitButton() {
                let form = $(self.selectors.form);
                let submitButton = form.find(self.selectors.formSubmitButton);
                if (submitButton.length > 0) {
                    submitButton.attr("disabled", false);
                    submitButton.children(".default-text").show();
                    submitButton.children(".submit-text").hide();
                }
            }

            function fetchVideoThumbnail(url, id, token) {
                var statusUrl = url + '/videos/' + id + '?fields=transcode.status';

                console.log('ring ring');


                $.ajax({
                    method: 'GET',
                    url: statusUrl,
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function (data) {
                        if (data.transcode.status === 'complete') {
                            var videoUrl = url + '/videos/' + id + '?fields=pictures';

                            $.ajax({
                                method: 'GET',
                                url: videoUrl,
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                success: function (data) {

                                    if (data.pictures) {
                                        displayVideoThumbnail(data.pictures)
                                    }
                                }
                            })
                        } else {
                            setTimeout(function () {
                                fetchVideoThumbnail(url, id, token);
                            }, 1000);
                        }
                    }
                });

            }

            function displayPlaceholderImage() {
                var thumbnailImage = $("<img>").attr({'src': '', 'id': 'vimeo-thumbnail'}).addClass("upload__image default-image processing");
                $('[data-video-upload-button]').addClass('hidden');
                $('#thumbnail-container').prepend(thumbnailImage);
            }
            function displayVideoThumbnail(pictures) {
                $("#vimeo-thumbnail").attr({'src': pictures.sizes[3].link, width: '100%', height: 'auto'}).removeClass("default-image processing");
            }

            function displayErrorMessage(data) {
                console.log(data);
                var errorMessage = document.querySelector('.upload--video.upload__error-message');
                errorMessage.classList.remove('hidden');
            }
        }
    });
}

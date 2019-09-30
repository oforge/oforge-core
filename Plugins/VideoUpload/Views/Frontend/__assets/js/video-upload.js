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
            deleteButton: '#video-delete',
            videoFileInput: '[data-file-input-video]',
            form: '.form--submit--loading',
            formSubmitButton: '.form__input--submit--loading',
            uploadMessages: '.upload__messages',
            messageUploading: '.upload__uploading-message',
            messageProcessing: '.upload__processing-message',
            messageSuccess: '.upload__success-message',
            messageDeleting: '.upload__deleting-message',
            messageDeletingSuccess: '.upload__delete-success-message'
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
            var vimeoBaseUrl = "https://api.vimeo.com/";
            var videoId;

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
                            displayMessage(self.selectors.messageUploading);
                            $("#upload-progress").val(0).parent().removeClass('hidden').show();

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
                                    videoId = data;
                                    fileUrl = this.api_url + '/' + data;
                                    enableDeleteButton();
                                    fetchVideoThumbnail(this.api_url, data, this.token);
                                    displayProcessingStatus();
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

            function enableDeleteButton() {
                $(self.selectors.deleteButton).removeClass('hidden').on("click", function () {
                    $(this).prop('disabled', true);
                    displayMessage(self.selectors.messageDeleting);
                    $.ajax({
                        method: 'DELETE',
                        url: vimeoBaseUrl + '/videos/' + videoId,
                        headers: {
                            'Authorization': 'Bearer ' + "bbccae2da119c8e83f5ff2e4f97e3c75"
                        },
                        success: function (data) {
                            displayMessage(self.selectors.messageDeletingSuccess);
                            videoId = null;
                            reset();
                        },
                        error: function (data) {
                            displayErrorMessage(data);
                        }
                    });
                });
            }

            function fetchVideoThumbnail(url, id, token) {
                var statusUrl = url + '/videos/' + id + '?fields=transcode.status';

                console.log('Are you done yet?');

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
                            console.log("       Vimeo: No I'm not done yet!");
                            setTimeout(function () {
                                fetchVideoThumbnail(url, id, token);
                            }, 1000);
                        }
                    }
                });

            }

            function reset() {
                $(self.selectors.deleteButton).addClass('hidden').off("click");
                $('#vimeo-thumbnail').remove();
                $("[data-video-upload-button]").removeClass('hidden');
                $("#thumbnail-container").removeClass("processing");
            }

            function displayPlaceholderImage() {
                var thumbnailImage = $("<img>").attr({
                    'src': '',
                    'id': 'vimeo-thumbnail'
                }).addClass("upload__image default-image");
                $("[data-video-upload-button]").addClass('hidden');
                $("#thumbnail-container").prepend(thumbnailImage);
            }

            function displayProcessingStatus() {
                displayMessage(self.selectors.messageProcessing);
                $("#thumbnail-container").addClass("processing");
                $("#upload-progress").parent().hide();
            }

            function displayVideoThumbnail(pictures) {
                displayMessage(self.selectors.messageSuccess)
                $("#vimeo-thumbnail").attr({
                    'src': pictures.sizes[3].link,
                    width: '100%',
                    height: 'auto'
                }).removeClass("default-image");
                $("#thumbnail-container").removeClass("processing");
            }

            function displayErrorMessage(data) {
                console.log(data);
                var errorMessage = document.querySelector('.upload--video.upload__error-message');
                errorMessage.classList.remove('hidden');
            }

            function displayMessage(selector) {
                $(self.selectors.uploadMessages).children('.upload--message').each(function () {
                    $(this).addClass('hidden');
                });
                $(selector).removeClass('hidden');
            }
        }
    });
}

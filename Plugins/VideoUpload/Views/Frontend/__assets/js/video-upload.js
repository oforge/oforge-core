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
            vimeoVideoIdInput: '[data-upload-video-id]',
            videoContentId: '[data-upload-video-content-id]',
            form: '.form--submit--loading',
            formSubmitButton: '.form__input--submit--loading',
            uploadMessages: '.upload__messages',
            messageError: '.upload__error-message',
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
            var maxFileUploadSize = 300 * 1000 * 1000; // 100MB
            var maxFileCount = 1;
            var videoUploadButton = document.querySelector(self.selectors.uploadButton);
            var videoFileInput = document.querySelector(self.selectors.videoFileInput);
            var vimeoVideoIdInput = document.querySelector(self.selectors.vimeoVideoIdInput);
            var credentials;
            var vimeoBaseUrl = "https://api.vimeo.com";
            var videoId;

            requestCredentials().then(function (data) {
                credentials = data;

                if (vimeoVideoIdInput.value.length > 0) {
                    videoId = vimeoVideoIdInput.value;
                    enableDeleteButton();
                    displayPlaceholderImage();
                    displayProcessingStatus();
                    fetchVideoThumbnail(vimeoBaseUrl, vimeoVideoIdInput.value, credentials.vimeo_access_token)
                }

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
                                    token: credentials.vimeo_access_token,
                                    name: file.name,
                                    description: 'Default description',
                                    onProgress: function (data) {
                                        let progressBar = document.getElementById('upload-progress');
                                        progressBar.value = data.loaded / data.total * 100;
                                    },
                                    onComplete: function (data) {
                                        fillHiddenIdInput(data);
                                        videoId = data;
                                        fileUrl = this.api_url + '/' + data;
                                        enableDeleteButton();
                                        allowEmbed(this.api_url, data, this.token);
                                        fetchVideoThumbnail(this.api_url, data, this.token);
                                        displayMessage(self.selectors.messageSuccess);
                                        displayProcessingStatus();
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
                    $(submitButton).trigger('disableSubmit');
                }
            }

            function enableSubmitButton() {
                let form = $(self.selectors.form);
                let submitButton = form.find(self.selectors.formSubmitButton);
                if (submitButton.length > 0) {
                    $(submitButton).trigger('enableSubmit');
                }
            }

            function enableDeleteButton() {
                $(self.selectors.deleteButton).removeClass('hidden').on("click", function () {
                    let deleteButton = $(this);
                    deleteButton.off("click");
                    disableSubmitButton();
                    displayMessage(self.selectors.messageDeleting);
                    $.ajax({
                        method: 'DELETE',
                        url: vimeoBaseUrl + '/videos/' + videoId,
                        headers: {
                            'Authorization': 'Bearer ' + credentials.vimeo_access_token
                        },
                        success: function (data) {
                            deleteVideoFromDatabase();
                        },
                        error: function (data) {
                            enableSubmitButton();
                            displayErrorMessage(data);
                        }
                    });
                });
            }

            function deleteVideoFromDatabase() {
                let videoContentId = document.querySelector(self.selectors.videoContentId).value;
                if (videoContentId) {
                    let getUrl = window.location;
                    let baseUrl = getUrl.protocol + "//" + getUrl.host + "/";
                    let url = baseUrl + 'account/insertions/video/' + videoContentId;
                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        success: function () {
                            displayMessage(self.selectors.messageDeletingSuccess);
                            videoId = null;
                            fillHiddenIdInput("");
                            reset();
                        },
                        error: function (data) {
                            displayErrorMessage(data);
                        },
                        complete: function () {
                            enableSubmitButton();
                        }
                    })
                } else {
                    enableSubmitButton();
                    displayMessage(self.selectors.messageDeletingSuccess);
                    videoId = null;
                    reset();
                }
            }

            function allowEmbed(url, id, token) {
                var embedUrl = url + '/videos/' + id;
                $.ajax({
                    method: 'PATCH',
                    url: embedUrl,
                    data: {
                        'privacy.embed': 'public',
                        'embed.title.name': 'hide',
                        'embed.color': '#708e2b',
                        'embed.buttons.like': false,
                        'embed.buttons.share': false,
                        'embed.buttons.watchlater': false,
                        'embed.buttons.embed': false,
                        'embed.logos.vimeo': false,
                        'embed.title.owner': 'hide',
                        'embed.title.portrait': 'hide',

                    },
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function() {
                        enableSubmitButton();
                    },
                    error: function (error) {
                        console.log(error);
                        enableSubmitButton();
                    }
                });
            }

            function requestCredentials() {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        method: 'GET',
                        url: '/vimeo-api/credentials',
                        dataType: 'json',
                        success: function (data) {
                            resolve(data);
                        },
                        error: function (error) {
                            reject(error);
                        }
                    });
                });
            }

            function fetchVideoThumbnail(url, id, token) {
                var statusUrl = url + '/videos/' + id + '?fields=transcode.status';

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
                                }, error: function (data) {
                                    displayErrorMessage();
                                    fillHiddenIdInput("");
                                    reset();
                                }
                            })
                        } else {
                            setTimeout(function () {
                                fetchVideoThumbnail(url, id, token);
                            }, 1000);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        switch (xhr.status) {
                            case 404:
                                reset();
                                fillHiddenIdInput("");
                                discardAllMessages();
                                break;
                        }

                    }
                });

            }

            function fillHiddenIdInput(data) {
                let input = document.querySelector(self.selectors.vimeoVideoIdInput);
                $(input).val(data);
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
                $("#vimeo-thumbnail").attr({
                    'src': pictures.sizes[3].link,
                    width: '100%',
                    height: 'auto'
                }).removeClass("default-image");
                $("#thumbnail-container").removeClass("processing");
                discardAllMessages();
            }

            function displayErrorMessage(data) {
                displayMessage(self.selectors.messageError);
            }

            function displayMessage(selector) {
                $(self.selectors.uploadMessages).children('.upload--message').each(function () {
                    $(this).addClass('hidden');
                });
                $(selector).removeClass('hidden');
            }

            function discardAllMessages() {
                $(self.selectors.uploadMessages).children('.upload--message').each(function () {
                    $(this).addClass('hidden');
                });
            }
        }
    });
}

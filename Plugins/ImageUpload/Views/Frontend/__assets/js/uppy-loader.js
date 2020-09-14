if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'uppyUpload',
        selector: '#drag-drop-area',
        selectors: {
            dropZone: '#drag-drop-area',
            uploader: '[data-upload]',
            uploadButton: '[data-upload-button]',
            form: 'form',
            submit: 'button[type=submit]',
        },
        init: function () {
            var self = this;
            var url = document.querySelector(self.selectors.dropZone).dataset.url;
            var required = document.querySelector(self.selectors.dropZone).dataset.required;

            var uploadItem = document.querySelector('[data-upload]');
            var uploadImageList = uploadItem.querySelector('[data-upload-images]');
            var uploadItemElement = null;

            var uppy = Uppy.Core({
                locale: Uppy.locales.de_DE,
                autoProceed: true,
                hideUploadButton: false,
                allowedFileTypes: ['image/*'],
            });
            uppy.use(Uppy.Dashboard, {
                inline: true,
                width: 1160,
                height: 164,
                target: self.selectors.dropZone,
                showSelectedFiles: false,
                proudlyDisplayPoweredByUppy: false
            }).use(Uppy.XHRUpload, {
                endpoint: url,
                method: 'post',
                formData: true,
                fieldName: 'files[]',
            });

            checkPlaceholderItem();
            uppy.on('upload', function (file) {
                disableSubmitButton();
            });

            uppy.on('upload-retry', function (file) {
                disableSubmitButton();
            });

            uppy.on('upload-error', function (file, response) {
                enableSubmitButton();
            });

            uppy.on('upload-success', function (file, response) {
                if (response.status >= 200 && response.status < 300) {
                    var res = response.body.imageData;
                    uploadItemElement = createImageListItem(res.id, res.path, uploadImageList);
                    createHiddenImageInput(uploadImageList, res);
                    checkPlaceholderItem();
                }
            });

            uppy.on('complete', function (result) {
                enableSubmitButton();
            });

            function checkPlaceholderItem() {
                var placeholderItem = document.querySelector('.upload__item--placeholder');
                var itemList = document.querySelectorAll('[data-upload-id]').length;

                if (itemList > 0) {
                    placeholderItem.classList.add('hidden');
                    if (required) {
                        placeholderItem.querySelector(".upload__file_required").removeAttribute("required");
                    }
                } else {
                    placeholderItem.classList.remove('hidden');
                    if (required) {
                        placeholderItem.querySelector(".upload__file_required").setAttribute("required", "");


                    }
                }
            }

            function createImageListItem(uploadId, imageUrl, imageList) {
                var imageListItem = document.createElement('li');
                var imageItem = document.createElement('img');
                var buttonList = document.createElement('div');

                var editIcon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                var editIconInner = document.createElementNS('http://www.w3.org/2000/svg', 'use');
                var editItem = document.createElement('div');

                var deleteIcon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                var deleteIconInner = document.createElementNS('http://www.w3.org/2000/svg', 'use');
                var deleteItem = document.createElement('div');

                editIconInner.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', '#edit2');
                editIcon.setAttribute('class', 'icon icon--edit2');
                editIcon.appendChild(editIconInner);

                deleteIconInner.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', '#delete2');
                deleteIcon.setAttribute('class', 'icon icon--delete2');
                deleteIcon.appendChild(deleteIconInner);

                editItem.setAttribute('class', 'upload__button upload__button--edit');
                editItem.setAttribute('data-upload-edit', uploadId);

                deleteItem.setAttribute('class', 'upload__button upload__button--delete');
                deleteItem.setAttribute('data-upload-delete', uploadId);
                // deleteItem.innerHTML = imageList.dataset.textSnippet;

                editItem.appendChild(editIcon);
                deleteItem.appendChild(deleteIcon);

                buttonList.setAttribute('class', 'upload__button-list');
                buttonList.appendChild(editItem);
                buttonList.appendChild(deleteItem);


                var mainItem = document.createElement('div');

                imageListItem.setAttribute('class', 'upload__item');
                imageListItem.setAttribute('data-upload-id', uploadId);

                imageItem.setAttribute('class', 'upload__image');
                imageItem.setAttribute('src', imageUrl);
                imageItem.setAttribute('data-upload-image', uploadId);

                mainItem.setAttribute('class', 'upload__choose-main');
                mainItem.setAttribute('data-upload-choose-main', uploadId);

                imageListItem.appendChild(imageItem);
                imageListItem.appendChild(buttonList);
                imageListItem.appendChild(mainItem);

                imageList.appendChild(imageListItem);

                return imageListItem;
            }

            function editListItem(uploadId) {
                var $target = $('[data-upload-image="' + uploadId + '"]');
                if (Oforge.MediaResize != undefined && Oforge.MediaResize.start != undefined) {
                    Oforge.MediaResize.start($target.attr('src'), $target.attr('data-upload-image'), { processUrl: '/image-upload' } );
                }
            }

            function deleteListItem(uploadId) {
                var itemToDelete = document.querySelector('[data-upload-id="' + uploadId + '"]');
                var uploadItem = itemToDelete.closest(self.selectors.uploader);
                var mainListItem = null;
                var mainInput = uploadItem.querySelector('[data-main]');

                if (!itemToDelete.hasAttribute('data-new-item')) {
                    createDeleteInput(uploadId, uploadItem);
                }

                itemToDelete.remove();
                mainListItem = uploadItem.querySelector('.upload__choose-main--is-main');
                if (!mainListItem && mainInput) {
                    mainInput.remove();
                }

                sortNewItems();
            }

            function createDeleteInput(deleteId, uploadItem) {
                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'images_interactions[' + deleteId + ']');
                input.setAttribute('value', 'delete');
                uploadItem.appendChild(input);
            }

            function setMainListItem(uploadId) {
                var itemToChooseMain = document.querySelector('[data-upload-id="' + uploadId + '"]');
                var isTemp = itemToChooseMain.hasAttribute('data-new-item');
                var uploadItem = itemToChooseMain.closest(self.selectors.uploader);

                createMainInput(uploadId, uploadItem, isTemp);
            }

            function createMainInput(mainId, uploadItem, isTemp) {
                var input = uploadItem.querySelector('[data-main]');
                var inputCreated = false;
                var inputName = 'images_interactions[' + mainId + ']';

                if (!input) {
                    input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', inputName);
                    input.setAttribute('value', 'main');
                    input.setAttribute('data-main', 'true');
                    inputCreated = true;
                }

                if (isTemp) {
                    inputName = 'images_temp_interactions[' + mainId + ']';
                }

                if (inputCreated) {
                    uploadItem.appendChild(input);
                }
            }

            function sortNewItems() {
                var newItems = document.querySelectorAll('[data-new-item]');
                newItems.forEach(function (item, index) {
                    item.setAttribute('data-upload-id', index);
                    var uploadImage = item.querySelector('[data-upload-image]');
                    var uploadEdit = item.querySelector('[data-upload-edit]');
                    var uploadDelete = item.querySelector('[data-upload-delete]');
                    var uploadChooseMain = item.querySelector('[data-upload-choose-main]');
                    var fileInput = item.querySelector('[data-file-input]');

                    if (uploadImage) {
                        uploadImage.setAttribute('data-upload-image', index);
                    }
                    if (uploadEdit) {
                        uploadEdit.setAttribute('data-upload-edit', index);
                    }
                    if (uploadDelete) {
                        uploadDelete.setAttribute('data-upload-delete', index);
                    }
                    if (uploadChooseMain) {
                        uploadChooseMain.setAttribute('data-upload-choose-main', index);
                    }
                    if (fileInput) {
                        fileInput.setAttribute('data-file-input', index);
                    }
                });
            }

            function createHiddenImageInput(uploadImageList, mediaObject) {
                console.log('media', mediaObject);
                var input = document.createElement('input');

                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'images[]');
                input.setAttribute('data-file-input', mediaObject.id);
                input.setAttribute('value', mediaObject.id);

                uploadImageList.appendChild(input);
            }

            function disableSubmitButton() {
                let submitButton = document.querySelector(self.selectors.form + ' ' + self.selectors.submit);
                if (submitButton) {
                    $(submitButton).trigger('disableSubmit');
                }
            }

            function enableSubmitButton() {
                let submitButton = document.querySelector(self.selectors.form + ' ' + self.selectors.submit);
                if (submitButton) {
                    $(submitButton).trigger('enableSubmit');
                }
            }

            document.addEventListener('click', function (evt) {
                if (evt.target.closest('[data-upload-edit]')) {
                    var elementToEdit = evt.target.closest('[data-upload-edit]');

                    editListItem(elementToEdit.dataset.uploadEdit);
                    checkPlaceholderItem();
                }
                if (evt.target.closest('[data-upload-delete]')) {
                    var elementToDelete = evt.target.closest('[data-upload-delete]')

                    deleteListItem(elementToDelete.dataset.uploadDelete);
                    checkPlaceholderItem();
                }

                if (evt.target.matches('[data-upload-choose-main]')) {
                    var elementToChooseMain = evt.target;
                    var chooseMainElements = document.querySelectorAll('.upload__choose-main--is-main');

                    setMainListItem(elementToChooseMain.dataset.uploadChooseMain);

                    if (chooseMainElements) {
                        chooseMainElements.forEach(function (element) {
                            element.classList.remove('upload__choose-main--is-main');
                        });
                    }
                    elementToChooseMain.classList.add('upload__choose-main--is-main');
                }
            });

            $('.upload__item.upload__item--placeholder').on('click', function (e) {
                $('.uppy-u-reset.uppy-Dashboard-browse').click();
            });
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

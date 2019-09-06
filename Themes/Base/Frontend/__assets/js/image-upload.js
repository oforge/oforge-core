if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'imageUpload',
        selector: '[data-upload]',
        selectors: {
            uploader: '[data-upload]',
            uploadButton: '[data-upload-button]'
        },
        init: function () {

            var self = this;
            var imageTypes = [
                'image/jpg',
                'image/jpeg',
                'image/gif',
                'image/png',
            ];
            checkPlaceholderItem();

            function createFileInput(uploadImageList) {
                var uploadId = document.querySelectorAll('[data-new-item]').length;
                var input = document.createElement('input');
                var uploadItemElement = null;

                input.setAttribute('type', 'file');
                input.setAttribute('name', 'images[]');
                input.setAttribute('accept', 'image/*');
                input.setAttribute('data-file-input', uploadId);
                input.setAttribute('style', 'display: none;');

                input.onchange = function (evt) {
                    var i = null,
                        file = null,
                        image = null;

                    image = new Image();

                    function setError() {
                        this.removeEventListener('error', setError);
                        console.warn('error, no image');
                        deleteListItem(uploadId);
                        checkPlaceholderItem();
                        document.querySelector('.upload__image-corrupted').classList.remove('hidden');
                        setTimeout(function () {
                            document.querySelector('.upload__image-corrupted').classList.add('hidden');
                        }, 10000);
                        return false;
                    }

                    if (input.files != null) {
                        for (i = 0; i < input.files.length; i++) {
                            file = input.files[i];

                            if (imageTypes.indexOf(file.type) < 0) {
                                console.warn('no image');
                                return false;
                            }

                            image.addEventListener('error', setError);
                            image.src = window.URL.createObjectURL(file);

                            uploadItemElement = createImageListItem(
                                uploadId,
                                window.URL.createObjectURL(file),
                                uploadImageList,
                                input.files[i].size
                            );
                        }
                        uploadItemElement.appendChild(input);
                    }
                    checkPlaceholderItem();
                    checkSizeUpload();
                    var button = uploadImageList.querySelector(self.selectors.uploadButton).closest('.upload__item--new-button');
                    button.remove();
                    uploadImageList.appendChild(button);
                };
                input.click();
            }

            function checkSizeUpload() {
                var items = document.querySelectorAll('[data-new-item]');

                var maxSize = 31 * 1024 * 1024;
                var totalSize = 0;
                items.forEach(function (item, index) {
                    enableImageItem(item);
                    var size = item.getAttribute("data-size");
                    if (size != null) {
                        var value = parseInt(size, 10);
                        if (!isNaN(value)) {
                            totalSize += parseInt(size, 10);
                            if (totalSize > maxSize) {
                                disableImageItem(item);
                            }
                        } else {
                            disableImageItem(item);
                        }
                    }
                });

                if (totalSize > maxSize) {
                    var maxSizeItem = document.querySelectorAll('.upload__max-size-exceeded');
                    if (maxSizeItem != null && maxSizeItem.length > 0) {
                        maxSizeItem[0].classList.remove("hidden");
                    }
                    var uploadListItem = document.querySelectorAll('.upload__list');
                    if (uploadListItem != null && uploadListItem.length > 0) {
                        uploadListItem[0].classList.add("upload__list_max-size-exceeded");
                    }

                }
            }

            function enableImageItem(item) {
                item.classList.remove("disabled");
                var fileInput = item.querySelector("input[type=file]");
                if (fileInput != null) {
                    fileInput.removeAttribute("disabled");
                }
            }

            function disableImageItem(item) {
                item.classList.add("disabled");
                var fileInput = item.querySelector("input[type=file]");
                if (fileInput != null) {
                    fileInput.setAttribute("disabled", "disabled");
                }
            }

            function createDeleteInput(deleteId, uploadItem) {
                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'images_interactions[' + deleteId + ']');
                input.setAttribute('value', 'delete');
                uploadItem.appendChild(input);
            }

            function createMainInput(mainId, uploadItem, isTemp) {
                var input = uploadItem.querySelector('[data-main]');
                var inputCreated = false;
                var inputName = 'images_interactions[' + mainId + ']';

                if (!input) {
                    input = document.createElement('input');
                    inputCreated = true;
                }

                if (isTemp) {
                    inputName = 'images_temp_interactions[' + mainId + ']';
                }

                input.setAttribute('type', 'hidden');
                input.setAttribute('name', inputName);
                input.setAttribute('value', 'main');
                input.setAttribute('data-main', 'true');

                if (inputCreated) {
                    uploadItem.appendChild(input);
                }
            }

            function createImageListItem(uploadId, imageUrl, imageList, size) {
                var imageListItem = document.createElement('li');
                var imageItem = document.createElement('img');
                var deleteItem = document.createElement('div');
                var mainItem = document.createElement('div');
                var sizeItem = document.createElement('span');
                var k = 1024;

                imageListItem.setAttribute('class', 'upload__item');
                imageListItem.setAttribute('data-upload-id', uploadId);
                imageListItem.setAttribute('data-new-item', 'true');
                imageListItem.setAttribute('data-size', size);

                imageItem.setAttribute('class', 'upload__image');
                imageItem.setAttribute('src', imageUrl);
                imageItem.setAttribute('data-upload-image', uploadId);

                deleteItem.setAttribute('class', 'upload__delete');
                deleteItem.setAttribute('data-upload-delete', uploadId);
                deleteItem.innerHTML = imageList.dataset.textSnippet;

                sizeItem.setAttribute('class', 'upload__size');

                sizeItem.innerHTML = size / k < k ? (Math.round(size / k) + " KB") : ((Math.round(size / k / k * 10) / 10) + " MB");

                mainItem.setAttribute('class', 'upload__choose-main');
                mainItem.setAttribute('data-upload-choose-main', uploadId);

                imageListItem.appendChild(imageItem);
                imageListItem.appendChild(deleteItem);
                imageListItem.appendChild(mainItem);
                imageListItem.appendChild(sizeItem);

                imageList.appendChild(imageListItem);

                return imageListItem;
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

            function sortNewItems() {
                var newItems = document.querySelectorAll('[data-new-item]');
                newItems.forEach(function (item, index) {
                    item.setAttribute('data-upload-id', index);
                    var uploadImage = item.querySelector('[data-upload-image]');
                    var uploadDelete = item.querySelector('[data-upload-delete]');
                    var uploadChooseMain = item.querySelector('[data-upload-choose-main]');
                    var fileInput = item.querySelector('[data-file-input]');

                    if (uploadImage) {
                        uploadImage.setAttribute('data-upload-image', index);
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

                checkSizeUpload();
            }

            function setMainListItem(uploadId) {
                var itemToChooseMain = document.querySelector('[data-upload-id="' + uploadId + '"]');
                var isTemp = itemToChooseMain.hasAttribute('data-new-item');
                var uploadItem = itemToChooseMain.closest(self.selectors.uploader);

                createMainInput(uploadId, uploadItem, isTemp);
            }

            function checkPlaceholderItem() {
                var placeholderItem = document.querySelector('.upload__item--placeholder');
                var itemList = document.querySelectorAll('[data-upload-id]').length;

                if (itemList > 0) {
                    placeholderItem.classList.add('hidden');
                } else {
                    placeholderItem.classList.remove('hidden');
                }
            }

            document.addEventListener('click', function (evt) {
                if (evt.target.matches(self.selectors.uploadButton)) {
                    var uploadItem = evt.target.closest(self.selectors.uploader);
                    var uploadImageList = uploadItem.querySelector('[data-upload-images]');

                    createFileInput(uploadImageList);
                }

                if (evt.target.matches('[data-upload-delete]')) {
                    var elementToDelete = evt.target;

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
        }
    });
}

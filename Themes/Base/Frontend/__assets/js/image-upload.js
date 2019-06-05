if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'imageUpload',
        selector: '[data-upload]',
        selectors: {
            uploader: '[data-upload]',
            uploadButton: '[data-upload-button]'
        },
        init: function() {

            var self = this;
            checkPlaceholderItem();

            function createFileInput(uploadImageList) {
                var uploadId          = document.querySelectorAll('[data-new-item]').length;
                var input             = document.createElement('input');
                var uploadItemElement = null;

                input.setAttribute('type', 'file');
                input.setAttribute('name', 'images[]');
                input.setAttribute('accept', 'image/*');
                input.setAttribute('data-file-input', uploadId);
                input.setAttribute('style', 'display: none;');

                input.onchange = function(evt) {
                    uploadItemElement = createImageListItem(
                        uploadId,
                        window.URL.createObjectURL(input.files[0]),
                        uploadImageList
                    );
                    uploadItemElement.appendChild(input);
                    checkPlaceholderItem();
                    var button = uploadImageList.querySelector(self.selectors.uploadButton).closest('.upload__item--new-button');
                    button.remove();
                    uploadImageList.appendChild(button);
                };
                input.click();
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

            function createImageListItem(uploadId, imageUrl, imageList) {
                var imageListItem = document.createElement('li');
                var imageItem     = document.createElement('img');
                var deleteItem    = document.createElement('div');
                var mainItem      = document.createElement('div');

                imageListItem.setAttribute('class', 'upload__item');
                imageListItem.setAttribute('data-upload-id', uploadId);
                imageListItem.setAttribute('data-new-item', 'true');

                imageItem.setAttribute('class', 'upload__image');
                imageItem.setAttribute('src', imageUrl);
                imageItem.setAttribute('data-upload-image', uploadId);

                deleteItem.setAttribute('class', 'upload__delete');
                deleteItem.setAttribute('data-upload-delete', uploadId);
                deleteItem.innerHTML = imageList.dataset.textSnippet;

                mainItem.setAttribute('class', 'upload__choose-main');
                mainItem.setAttribute('data-upload-choose-main', uploadId);

                imageListItem.appendChild(imageItem);
                imageListItem.appendChild(deleteItem);
                imageListItem.appendChild(mainItem);

                imageList.appendChild(imageListItem);

                return imageListItem;
            }

            function deleteListItem(uploadId) {
                var itemToDelete = document.querySelector('[data-upload-id="' + uploadId + '"]');
                var uploadItem   = itemToDelete.closest(self.selectors.uploader);
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
                newItems.forEach(function(item, index) {
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
            }

            function setMainListItem(uploadId) {
                var itemToChooseMain = document.querySelector('[data-upload-id="' + uploadId + '"]');
                var isTemp           = itemToChooseMain.hasAttribute('data-new-item');
                var uploadItem       = itemToChooseMain.closest(self.selectors.uploader);

                createMainInput(uploadId, uploadItem, isTemp);
            }

            function checkPlaceholderItem() {
                var placeholderItem = document.querySelector('.upload__item--placeholder');
                var itemList = document.querySelectorAll('[data-upload-id]').length;

                if (itemList > 0) {
                    placeholderItem.classList.add('hidden');
                }  else {
                    placeholderItem.classList.remove('hidden');
                }
            }

            document.addEventListener('click', function(evt) {
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
                        chooseMainElements.forEach(function(element) {
                            element.classList.remove ('upload__choose-main--is-main');
                        });
                    }
                    elementToChooseMain.classList.add('upload__choose-main--is-main');
                }
            });
        }
    });
}

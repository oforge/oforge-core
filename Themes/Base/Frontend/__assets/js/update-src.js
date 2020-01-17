/**
 * Updates the background image for specified elements if the file-input changes <br>
 *
 * Usage: Input elements will become triggers if they have the following attribute: "data-update = <element-class>"
 * If an image is uploaded via a trigger, every DOM element with <element-class> will get the uploaded image as <br>
 * it's background image
 */

(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'update',
            selector: '[data-update]',
            init: function () {
                var self = this;
                const triggers = document.querySelectorAll(self.selector);
                triggers.forEach(function (trigger) {
                    trigger.addEventListener('change', function (e) {
                        if (checkFileSize(e.target)) {
                            let items = document.getElementsByClassName(trigger.dataset.update);
                            items.forEach(function (item) {
                                item.style.backgroundImage = "url('" + URL.createObjectURL(trigger.files[0]) + "')";
                            });
                        }
                    })
                });

                function checkFileSize(target) {
                    if (target.matches('[data-file-size]')) {
                        var files = target.files;
                        var fileSize = 0;
                        var errorMessage = document.querySelector('.error--file-size');
                        files.forEach(function (file) {
                            fileSize += ((file.size/1024)/1024); // MB
                            if (fileSize > 12) {
                                target.value = "";
                                errorMessage.classList.remove('text-is-hidden');
                                setTimeout(function () {
                                    errorMessage.classList.add('text-is-hidden')
                                }, 5000);
                                return false;
                            }
                        });
                    }
                }
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

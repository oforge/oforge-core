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
                    trigger.addEventListener('change', function () {
                        let items = document.getElementsByClassName(trigger.dataset.update);
                        items.forEach(function (item) {
                            item.style.backgroundImage = "url('" + URL.createObjectURL(trigger.files[0]) + "')";
                        })
                    })
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

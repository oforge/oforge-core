/**
 * Updates the background image for specified elements if the file-input changes
 *
 * Usage: Input elements will become triggers for media updates if they have: data-update="<element-class>"
 * Changing such input element will result in an update for elements with <element-class>
 */

(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'update',
            selector: '[data-update]',
            init: function () {
                const triggers = document.querySelectorAll('[data-update]');
                triggers.forEach(function (trigger) {
                    trigger.addEventListener('change', function () {
                        console.log(trigger.dataset.update);
                        let items = document.getElementsByClassName(trigger.dataset.update);
                        console.log(items);
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

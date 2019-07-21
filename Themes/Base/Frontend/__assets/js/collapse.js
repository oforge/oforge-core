/**
 * Adds a simple collapse functionality.
 *
 * Usage: Collapse triggers will have the 'data-collapse' attribute. ( <element data-collapse="" > )
 * The next element on the same level as the trigger will be toggled with the class "collapsed".
 *
 * Optional: if 'data-collapse' is set to a value, triggers will only be applied if screen-width < value.
 *
 */

(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'collapse',
            selector: '[data-collapse]',
            init: function () {
                const triggers = document.querySelectorAll('[data-collapse]');
                // check if this is specified viewport
                triggers.forEach(function (trigger){
                    let clientWidth = document.documentElement.clientWidth;
                    let triggerWidth = parseInt(trigger.dataset.collapse);
                    if (Number.isNaN(triggerWidth) || clientWidth < triggerWidth ) {
                        trigger.addEventListener('click', function () {
                            this.classList.toggle("active");
                            this.nextElementSibling.classList.toggle("collapsed");
                        });
                    }
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

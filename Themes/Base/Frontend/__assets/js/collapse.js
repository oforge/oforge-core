/**
 * Adds a simple collapse functionality.
 *
 * Usage: Collapse triggers will have the 'data-collapse' attribute.
 * The next element on the same level as the trigger will be toggled with the class "collapsed".
 */

(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'collapse',
            selector: '[data-collapse]',
            init: function () {
                const triggers = document.querySelectorAll('[data-collapse]');
                triggers.forEach(function (trigger){
                    trigger.addEventListener('click', function() {
                        this.classList.toggle("active");
                        this.nextElementSibling.classList.toggle("collapsed");
                    })
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

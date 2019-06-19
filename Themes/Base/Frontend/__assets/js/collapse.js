(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'collapse',
            selector: '[data-collapse]',
            init: function () {
                var triggers = document.querySelectorAll('[data-collapse]');
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

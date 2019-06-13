(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'collapse',
            selector: '[data-collapse]',
            init: function () {
                let self = this;

                function toggleCollapse(item) {
                    item.classList.toggle("collapsed");
                }

                document.addEventListener('click', function(evt) {
                    console.log(evt);
                    if (evt.target.matches(self.selector)) {
                        evt.target.classList.toggle("active");
                        toggleCollapse(evt.target.nextElementSibling);
                    }
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

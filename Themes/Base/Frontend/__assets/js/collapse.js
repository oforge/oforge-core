(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'collapse',
            selector: '[data-collapse]',
            init: function () {
                var self = this;

                function toggleCollapse(item) {
                    if(item.style.display === "block") {
                        item.style.display = "none";
                    } else {
                        item.style.display = "block";
                    }
                }

                document.addEventListener('click', function(evt) {
                    evt.target.classList.toggle("active");
                    if (evt.target.matches(self.selector)) {
                        toggleCollapse(evt.target.nextElementSibling);
                    }
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }

})();

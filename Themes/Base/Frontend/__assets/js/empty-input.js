(function() {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'emptyInput',
            selector: '[data-remove-empty-input]',
            init: function () {
                var self = this;

                function removeEmptyInputNames(form) {
                    console.log('removing items');
                    var inputElements = form.querySelectorAll('input');
                    inputElements.forEach(function(element) {
                       if (element.value === "" || element.value === null || element.value === 'undefined') {
                           element.setAttribute('name', '');
                       }
                    });
                }

                document.addEventListener('submit', function (evt) {
                    if (evt.target.matches(self.selector)) {
                        removeEmptyInputNames(evt.target);
                    }
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

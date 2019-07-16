(function() {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'emptyInput',
            selector: '#form-search-sidebar',
            init: function () {
                var self = this;
                var allForms = document.querySelector(self.selector);

                function removeEmptyInputNames(form) {
                    console.log('removing items');
                    var inputElements = form.querySelectorAll('input');
                    inputElements.forEach(function(element) {
                       if (element.value === "" || element.value === null || element.value === 'undefined') {
                           element.setAttribute('name', '');
                       }
                    });
                }

                allForms.forEach(function(formElement) {
                    formElement.addEventListener('submit', function (evt) {
                        if (evt.target.matches(self.selector)) {
                            removeEmptyInputNames(formElement);
                        }
                    });
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

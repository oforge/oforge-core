(function() {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'emptyInput',
            selector: '#form-search',
            init: function () {
                var self = this;
                var form = document.querySelector(self.selector);

                function removeEmptyInputNames(form) {
                    console.log('removing items');
                    var inputElements = form.querySelectorAll('input');
                    inputElements.forEach(function(element) {
                       if (element.value === "" || element.value === null || element.value === 'undefined') {
                           element.setAttribute('name', '');
                       }
                    });
                }

                form.addEventListener('submit', function (evt) {
                    removeEmptyInputNames(form);
                });

            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

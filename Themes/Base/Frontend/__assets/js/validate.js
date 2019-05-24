(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'validate',
            selector: 'select[required], input[required]',
            parent: '[data-required]',
            init: function () {
                var self = this;
                var formComponents = document.querySelectorAll(self.selector);

                formComponents.forEach(function (formComponent) {
                    var parent = formComponent.parentNode;

                    // if the form component has the data-required attribute
                    if (formComponent.matches(self.parent)) {
                        parent = formComponent;
                    }

                    formComponent.onchange = function (e) {
                        if (! parent.matches(self.parent)) {
                            return;
                        }
                        if (formComponent.matches(':required:valid')) {
                            parent.classList.remove('invalid');
                            parent.classList.add('valid');
                        }
                        if (formComponent.matches(':focus:required:invalid')) {
                            parent.classList.remove('valid');
                            parent.classList.add('invalid');
                        }
                    };
                    formComponent.onfocus = function (e) {
                        if (formComponent.matches(':focus:required:invalid')) {
                            parent.classList.remove('valid');
                            parent.classList.add('invalid');
                        }
                    }
                })
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }

})();

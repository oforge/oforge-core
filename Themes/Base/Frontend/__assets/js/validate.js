(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'validate',
            selector: 'select[required]',
            parent: '[data-required]',
            init: function () {
                var self = this;
                var selectComponents = document.querySelectorAll(self.selector);

                selectComponents.forEach(function (selectComponent) {
                    var parent = selectComponent.parentNode;

                    selectComponent.onchange = function (e) {
                        if (! parent.matches(self.parent)) {
                            return;
                        }
                        if (selectComponent.matches(':required:valid')) {
                            parent.classList.remove('invalid');
                            parent.classList.add('valid');
                        }
                        if (selectComponent.matches(':focus:required:invalid')) {
                            parent.classList.remove('valid');
                            parent.classList.add('invalid');
                        }
                    };
                    selectComponent.onfocus = function (e) {
                        if (selectComponent.matches(':focus:required:invalid')) {
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

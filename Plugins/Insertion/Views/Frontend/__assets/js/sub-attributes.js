(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'subAttributes',
            selector: '[data-sub-attribute-select]',
            subAttributeSelector: '[data-sub-attribute]',

            init: function () {
                var self = this;
                var subAttributeSelectors = document.querySelectorAll(self.selector);
                subAttributeSelectors.forEach(function (item, index) {
                    var parent = item.closest('[data-attribute-id]');
                    var selector = '[data-parent="' + parent.dataset.attributeId + '"][data-value="' + item[item.selectedIndex].value + '"]';
                    var elementToUnhide = document.querySelector(selector);
                    if (elementToUnhide) {
                        elementToUnhide.classList.remove('form__control--hidden');
                    }
                });

                document.addEventListener('change', function (e) {
                    var element = e.target;
                    var elementToUnhide = null;
                    var elementsToHide = null;
                    var parent = null;

                    if (e.target.matches(self.selector)) {

                        console.log(element, element[element.selectedIndex].value);
                        elementToUnhide = document.querySelector('[data-value="' + element[element.selectedIndex].value + '"]');
                        parent = element.closest('[data-attribute-id]');
                        console.log(parent);

                        elementsToHide = document.querySelectorAll('[data-parent="' + parent.dataset.attributeId + '"]');
                        console.log(elementsToHide);
                        elementsToHide.forEach(function (item) {
                            item.classList.add('form__control--hidden');
                            var elementToReset = item.querySelector(self.selector);
                            elementToReset.selectedIndex = 0;
                            elementToReset[0].value = '';
                        });

                        if (elementToUnhide !== null) {
                            elementToUnhide.classList.remove(('form__control--hidden'));
                        }
                    }
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }

})();

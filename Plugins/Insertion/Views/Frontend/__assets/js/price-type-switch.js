if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'priceTypeSwitch',
        selector: '#price_type',
        init: function () {
            var self = this;
            var selectionElement = document.querySelector(self.selector);
            var minField = document.getElementById('price_min');
            var maxField = document.getElementById('price');

            changeHandler();

            selectionElement.addEventListener('change', function () {
                changeHandler();
            });

            function changeHandler() {
                if (selectionElement.value === 'on_demand') {
                    minField.required = true;
                    minField.setAttribute('name', 'price_min');
                    maxField.placeholder = getSelectedOption(selectionElement).getAttribute('data-placeholder');
                    minField.closest('.form__control').classList.remove('is-hidden');
                } else {
                    minField.required = false;
                    minField.removeAttribute('name');
                    maxField.placeholder = getSelectedOption(selectionElement).getAttribute('data-placeholder');
                    minField.closest('.form__control').classList.add('is-hidden');
                }
            }

            function getSelectedOption(selection) {
                var candidate;
                for (var i = 0, len = selection.options.length; i < len; i++) {
                    candidate = selection.options[i];
                    if (candidate.selected === true) {
                        break;
                    }
                }
                return candidate;
            }
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

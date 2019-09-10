(function () {
    if (typeof Oforge !== 'undefined') {
        var selectionElement = document.getElementById('price-type');
        selectionElement.addEventListener('change', function () {
            var minField = document.getElementById('price-min');
            var maxField = document.getElementById('price');

            if (selectionElement.value === 'on_demand') {
                minField.required = true;
                minField.setAttribute('name', 'price-min');
                maxField.placeholder = getSelectedOption(selectionElement).getAttribute('data-placeholder');
                minField.closest('.form__control').classList.remove('is-hidden');
            } else {
                minField.required = false;
                minField.removeAttribute('name');
                maxField.placeholder = getSelectedOption(selectionElement).getAttribute('data-placeholder');
                minField.closest('.form__control').classList.add('is-hidden');
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

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

if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'attributeEdit',
        selector: '[data-attribute-edit]',
        init: function () {
            var self = this;
            var classNames = {
                additionalInputLabel: 'additional-input__label',
                additionalInputClass: 'additional-input'
            };
            var selectors = {
                attributeType: 'attributeType',
                additionalInput: '[data-additional-input]',
                additionalInputLabel: '.' + classNames.additionalInputLabel,
                select: 'select',
                input: 'input',
                label: 'label'
            };

            function appendAdditionalInput(option, optionCount) {
                let additionalInput = document.createElement(selectors.input);
                let inputLabel = document.createElement(selectors.label);

                inputLabel.innerHTML = option.dataset.additionalInputLabel;
                inputLabel.classList.add(classNames.additionalInputLabel);
                additionalInput.setAttribute('name', option.dataset.additionalInputName);
                additionalInput.classList.add(classNames.additionalInputClass);
                additionalInput.value = option.dataset.additionalInputValue;
                removeAdditionalInput(option);
                option.closest(selectors.select).parentNode.appendChild(inputLabel);
                option.closest(selectors.select).parentNode.appendChild(additionalInput);
                return additionalInput;
            }

            function removeAdditionalInput(option) {
                    let formGroup = option.closest(selectors.select).parentElement;
                    let additionalInput = $(formGroup).find("." + classNames.additionalInputClass);
                    let inputLabel = $(formGroup).find(selectors.additionalInputLabel);
                    option.setAttribute('data-additional-input-value', $(additionalInput).val());

                    $(inputLabel).remove();
                    $(additionalInput).remove();
            }

            /** @TODO: For Some Reason the Select2 events revert changes after the event handler has been executed
             *  Doesn't need immediate Attention.
             **/
            document.getElementsByTagName('select').forEach(function (select) {
                select.getElementsByTagName('option').forEach(function (option) {
                    if (option.dataset.additionalInput) {
                        if($(select).select2('val') === option.value) {
                            appendAdditionalInput(option);
                        }
                        $(select).on('change.select2', function(event) {
                            if($(event.target).select2('val') === option.value) {
                                appendAdditionalInput(option);
                            } else {
                                removeAdditionalInput(option);
                            }
                        })
                    }
                })
            })
        }
    });
}


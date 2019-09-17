if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'attributeEdit',
        selector: '[data-attribute-edit]',
        init: function () {
            var self = this;
            var classNames = {};
            var selectors = {
                attributeType: 'attributeType',
                additionalInput: '[data-additional-input]'
            };

            function appendAdditionalInput(option) {

            }

            self.getElementsByTagName('select').forEach(function (select) {
                select.getElementsByTagName('option').forEach(function (option) {
                    if (option.dataset.additionalInput) {
                        console.log(option.value);
                    }
                })
            })
        }
    });
}


if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'rangeSlider',
        selector: '[data-range-slider]',
        init: function() {
            var self = this;

            var rangeSlider = document.querySelectorAll(self.selector);
            debugger;

            rangeSlider.forEach(function (item) {
                var firstItem = item.querySelector('[data-range-from]');
                var secondItem = item.querySelector('[data-range-to]');
                var firstItemName = firstItem.dataset.rangeName;
                var secondItemName = secondItem.dataset.rangeName;

                var priceRange = new JSR(['[data-range-name="' + firstItemName + '"]', '[data-range-name="' + secondItemName + '"]'], {
                    sliders: 2,
                    min: firstItem.getAttribute('min'),
                    max: secondItem.getAttribute('max'),
                    values: [firstItem.getAttribute('value'),
                        secondItem.getAttribute('value')],
                    limit: {
                        show: true
                    },
                    labels: false,
                    grid: false
                });

                priceRange.addEventListener('update', function(input, value) {
                    if (input.matches('[data-range-from]')) {
                        firstItem.setAttribute('value', value);
                    }
                    if (input.matches('[data-range-to]')) {
                        secondItem.setAttribute('value', value);
                    }
                });
            });
        }
    });
}

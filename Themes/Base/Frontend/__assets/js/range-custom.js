if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'rangeSlider',
        selector: '[data-range-slider]',
        init: function() {
            var self = this;
            var rangeSlider = document.querySelectorAll(self.selector);

            rangeSlider.forEach(function (item) {
                var firstItem = item.querySelector('[data-range-from]');
                var secondItem = item.querySelector('[data-range-to]');
                var firstItemName = firstItem.dataset.rangeName;
                var secondItemName = secondItem.dataset.rangeName;
                var rangeFrom = parseFloat(firstItem.getAttribute('min'));
                var rangeTo = parseFloat(firstItem.getAttribute('max'));

                var range = new JSR(['[data-range-name="' + firstItemName + '"]', '[data-range-name="' + secondItemName + '"]'], {
                    sliders: 2,
                    min: rangeFrom,
                    max: rangeTo,
                    values: [firstItem.getAttribute('value'),
                        secondItem.getAttribute('value')],
                    limit: {
                        show: false
                    },
                    modules: {
                        labels: false
                    },
                    grid: false
                }).addEventListener('update', function(input, value) {
                    console.log('Custom events test: New value set: ' + input + ' ' + value);
                });

                range.addEventListener('update', function(input, value) {
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

(function() {
    "use strict";

    if (typeof Oforge !== 'undefined') {

        Oforge.register({
            name: 'rangeSlider',
            selector: '[data-range-slider]:not(.ghost)',
            init: function () {
                let self = this;
                const rangeSliderList = document.querySelectorAll(self.selector);

                rangeSliderList.forEach(function (rangeSlider, index) {
                    const rangeSliderGhost = document.querySelector('#' + rangeSlider.id + '.ghost');
                    const rangeLow = document.querySelector('[data-range-slider-name="' + rangeSlider.id + '"][data-range-value="low"]');
                    const rangeHigh = document.querySelector('[data-range-slider-name="' + rangeSlider.id + '"][data-range-value="high"]');

                    rangeLow.value = rangeSlider.valueLow;
                    rangeHigh.value = rangeSlider.valueHigh;

                    rangeSlider.onchange = function (e) {
                        rangeLow.value = rangeSlider.valueLow;
                    };

                    rangeSliderGhost.onchange = function (e) {
                        rangeHigh.value = rangeSlider.valueHigh;
                    }
                });
            }
        });
    }
})();

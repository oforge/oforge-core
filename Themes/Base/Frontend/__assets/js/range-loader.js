(function() {
    "use strict";

    var rangeSlider = document.querySelector('#search_key3.original');
    var rangeSliderGhost = document.querySelector('#search_key3.ghost');
    var rangeLow = document.querySelector('[data-range-slider="search_key3"][data-range-value="low"]');
    var rangeHigh = document.querySelector('[data-range-slider="search_key3"][data-range-value="high"]');

    console.log(rangeSlider, rangeLow, rangeHigh);

    rangeSlider.onchange = function (e) {
        rangeLow.value = rangeSlider.valueLow;
    };

    rangeSliderGhost.onchange = function (e) {
        rangeHigh.value = rangeSlider.valueHigh;
    }
})();
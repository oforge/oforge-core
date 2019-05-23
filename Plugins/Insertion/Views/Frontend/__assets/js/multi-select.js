if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'multiSelect',
        selector: '[data-multi-select]',
        init: function () {
            var self = this;
            var multiSelectList = document.querySelectorAll(this.selector);
            var checkList = document.querySelectorAll('[data-multi-select-item]');
            var checkedValues = [];
            var checkedNames = [];

            multiSelectList.forEach(function(multi) {

                document.addEventListener('click', function(evt) {
                    console.log(multi.contains(evt.target));
                    if (multi.contains(evt.target)) {
                        multi.classList.add('select--is-open');
                    } else {
                        multi.classList.remove('select--is-open');
                    }
                });
            });

            checkList.forEach(function(check) {

                if (check.matches('.select__item--checked')) {
                    checkedValues.push(check.dataset.multiSelectItem);
                    checkedNames.push(check.innerHTML);
                }
                var search = check.closest('.select').querySelector('.select__search');
                search.innerHTML = checkedNames.join(', ');


                check.addEventListener('click', function(evt) {

                    var value = check.dataset.multiSelectItem;
                    var valueName = check.innerHTML;
                    var valueIndex = checkedValues.indexOf(value);
                    var search = check.closest(self.selector).querySelector('[data-multi-select-search]');
                    var input = check.closest(self.selector).querySelector('[data-multi-select-input]');

                    if (valueIndex > -1) {

                        check.classList.remove('select__item--checked');
                        checkedValues.splice(valueIndex, 1);
                        checkedNames.splice(valueIndex, 1);

                    } else {

                        check.classList.add('select__item--checked');
                        checkedValues.push(value);
                        checkedNames.push(valueName);

                    }
                    search.innerHTML = checkedNames.join(', ');
                    input.value = checkedValues;
                });
            });
        }
    });
}
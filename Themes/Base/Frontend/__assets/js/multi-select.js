if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'multiSelect',
        selector: '[data-multi-select]',
        init: function () {
            var self = this;
            var multiSelectList = document.querySelectorAll(self.selector);
            var checkList = document.querySelectorAll('[data-multi-select-item]');
            var checkedValues = [];
            var checkedNames = [];

            multiSelectList.forEach(function(multi) {
                if (typeof SimpleBar !== 'undefined') {
                    var selectList = multi.querySelector('[data-multi-select-list]');
                    new SimpleBar(selectList, {
                        autoHide: true,
                    });
                } else {
                    console.warn('Simplebar is not defined! MultiSelect needs Simplebar :(');
                }

                document.addEventListener('click', function(evt) {
                    if (multi.contains(evt.target)) {
                        multi.classList.add('select--is-open');
                    } else {
                        multi.classList.remove('select--is-open');
                    }
                });
            });

            checkList.forEach(function(check) {
                var input = null;

                if (check.matches('.select__item--checked')) {
                    checkedValues.push(check.dataset.multiSelectItem);
                    checkedNames.push(check.innerHTML);

                    input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', check.closest(self.selector).dataset.multiSelect);
                    input.setAttribute('data-multi-select-input', check.dataset.multiSelectItem);
                    input.setAttribute('value', check.dataset.multiSelectItem);
                    check.closest(self.selector).appendChild(input);
                }

                var search = check.closest(self.selector).querySelector('[data-multi-select-search]');
                search.innerHTML = checkedNames.join(', ');

                check.addEventListener('click', function(evt) {

                    var value = check.dataset.multiSelectItem;
                    var valueName = check.innerHTML;
                    var valueIndex = checkedValues.indexOf(value);
                    var currentSelect = check.closest(self.selector);
                    var search = currentSelect.querySelector('[data-multi-select-search]');
                    var input = null;

                    if (valueIndex > -1) {
                        check.classList.remove('select__item--checked');
                        checkedValues.splice(valueIndex, 1);
                        checkedNames.splice(valueIndex, 1);

                        input = currentSelect.querySelector('[data-multi-select-input][value="'+value+'"]');
                        input.remove();

                    } else {
                        check.classList.add('select__item--checked');
                        checkedValues.push(value);
                        checkedNames.push(valueName);

                        input = document.createElement('input');
                        input.setAttribute('type', 'hidden');
                        input.setAttribute('name', currentSelect.dataset.multiSelect);
                        input.setAttribute('data-multi-select-input', value);
                        input.setAttribute('value', value);

                        currentSelect.appendChild(input);

                    }
                    search.innerHTML = checkedNames.join(', ');
                });
            });
        }
    });
}

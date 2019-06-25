if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'multiSelect',
        selector: '[data-multi-select]',
        selectors: {
            checkList: '[data-multi-select-item]',
            selectList: '[data-multi-select-list]',
            searchBar: '[data-multi-select-search]',
            selectIsOpen: 'multi-select--is-open',
            checkIsChecked: 'multi-select__item--checked'
        },
        init: function () {
            var self            = this;
            var multiSelectList = document.querySelectorAll(self.selector);
            var checkList       = document.querySelectorAll(self.selectors.checkList);

            function multiSelectOnClick(element) {
                document.addEventListener('click', function(evt) {
                    if (element.contains(evt.target)) {
                        element.classList.add(self.selectors.selectIsOpen);
                    } else {
                        element.classList.remove(self.selectors.selectIsOpen);
                    }
                });
            }

            function getMultiSelect(childElement) {
                var multiSelect = childElement.closest(self.selector);
                var checkItems  = multiSelect.querySelectorAll(self.selectors.checkList);

                multiSelect.checkedValues = [];
                multiSelect.checkedNames  = [];

                checkItems.forEach(function(checkItem) {
                    if (checkItem.classList.contains(self.selectors.checkIsChecked)) {
                        multiSelect.checkedValues.push(checkItem.dataset.multiSelectItem);
                        multiSelect.checkedNames.push(checkItem.innerHTML);
                    }
                });

                return multiSelect;
            }

            function loadSimpleBar(element) {
                var selectList = null;

                if (typeof SimpleBar !== 'undefined') {
                    selectList = element.querySelector(self.selectors.selectList);
                    new SimpleBar(selectList, {
                        autoHide: true,
                    });
                } else {
                    console.warn('Simplebar is not defined! MultiSelect needs Simplebar :(');
                }
            }

            function addHiddenInputToCheckItem(check) {
                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', check.closest(self.selector).dataset.multiSelect);
                input.setAttribute('data-multi-select-input', check.dataset.multiSelectItem);
                input.setAttribute('value', check.dataset.multiSelectItem);
                check.closest(self.selector).appendChild(input);
            }

            function addOrRemoveCheck(check) {
                var value       = check.dataset.multiSelectItem;
                var valueName   = check.innerHTML;
                var multiSelect = getMultiSelect(check);
                var valueIndex  = multiSelect.checkedValues.indexOf(value);
                var search      = multiSelect.querySelector(self.selectors.searchBar);
                var input       = null;

                if (valueIndex > -1) {
                    check.classList.remove(self.selectors.checkIsChecked);
                    multiSelect.checkedValues.splice(valueIndex, 1);
                    multiSelect.checkedNames.splice(valueIndex, 1);

                    input = multiSelect.querySelector('[data-multi-select-input][value="'+value+'"]');
                    input.remove();

                } else {
                    check.classList.add(self.selectors.checkIsChecked);
                    multiSelect.checkedValues.push(value);
                    multiSelect.checkedNames.push(valueName);
                    addHiddenInputToCheckItem(check);
                }

                if (multiSelect.checkedNames.length < 1) {
                    search.innerHTML = search.dataset.placeholder;
                } else {
                    search.innerHTML = multiSelect.checkedNames.join(', ');
                }
            }

            multiSelectList.forEach(function(multiSelect) {
                var checkItems = multiSelect.querySelectorAll(self.selectors.checkList);
                multiSelect.checkedValues = [];
                multiSelect.checkedNames  = [];

                checkItems.forEach(function(checkItem) {
                    if (checkItem.classList.contains(self.selectors.checkIsChecked)) {
                        multiSelect.checkedValues.push(checkItem.dataset.multiSelectItem);
                        multiSelect.checkedNames.push(checkItem.innerHTML);
                    }
                });

                loadSimpleBar(multiSelect);
                multiSelectOnClick(multiSelect);
            });

            checkList.forEach(function(check) {
                var multiSelect = getMultiSelect(check);
                var search = multiSelect.querySelector(self.selectors.searchBar);

                if (check.matches(self.selectors.checkIsChecked)) {
                    multiSelect.checkedValues.push(check.dataset.multiSelectItem);
                    multiSelect.checkedNames.push(check.innerHTML);
                    addHiddenInputToCheckItem(check);
                }

                if (multiSelect.checkedNames.length < 1) {
                    search.innerHTML = search.dataset.placeholder;
                } else {
                    search.innerHTML = multiSelect.checkedNames.join(', ');
                }
            });

            document.addEventListener('click', function (evt) {
                var check = null;
                if (evt.target.matches(self.selectors.checkList)) {
                    check = evt.target;
                }
                if (check) {
                    addOrRemoveCheck(check);
                }
            });
        }
    });
}

(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'subAttributes',
            selector: '[data-select-type]',
            init: function () {
                var self = this;
                var classNames = {
                    select: 'select',
                    selectIsOpen: 'select--is-open',
                    selectText: 'select__text',
                    selectItem: 'select__item',
                    selectItemIsChecked: 'select__item--is-checked',
                    selectValue: 'select__value',
                };
                var selectors = {
                    select: '.' + classNames.select,
                    selectIsOpen: '.' + classNames.selectIsOpen,
                    selectText: '.' + classNames.selectText,
                    selectItem: '.' + classNames.selectItem,
                    selectItemIsChecked: '.' + classNames.selectItemIsChecked,
                    selectValue: '.' + classNames.selectValue,
                    subSelect: '[data-sub-select]'
                };
                var selectList = document.querySelectorAll(self.selector);

                function addHiddenInputToCheckItem(check) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', check.closest(self.selector).dataset.select);
                    input.setAttribute('data-select-input', check.dataset.valueId);
                    input.setAttribute('value', check.dataset.valueId);
                    check.closest(self.selector).appendChild(input);
                }

                function hasSubSelect(selectItem) {
                    return selectItem.querySelector(selectors.subSelect) !== null;
                }

                function resetSubSelect(selectItem) {
                    var subSelect = selectItem.querySelector(selectors.subSelect);
                    var allSubItems = null;
                    if (subSelect) {
                        allSubItems = subSelect.querySelectorAll(selectors.selectItem);
                        allSubItems.forEach(function(item) {
                            unselectItem(item);
                        });

                        subSelect.classList.remove(classNames.selectIsOpen);
                    }
                }

                function toggleOneItem(selectItem) {
                    var parentSelect = null;
                    var toggleState = false;
                    var valueId = selectItem.dataset.valueId;
                    var valueName = selectItem.querySelector(selectors.selectValue).innerHTML;
                    var valueIndex = null;

                    if (selectItem) {
                        parentSelect = selectItem.closest(self.selector);

                        if (parentSelect.dataset.selectType === 'single') {
                            unselectAllExceptCurrent(selectItem);
                        }
                        toggleState = selectItem.classList.toggle(classNames.selectItemIsChecked);
                        selectItem.dataset.selected = toggleState.toString();

                        if (toggleState) {
                            addHiddenInputToCheckItem(selectItem);
                            parentSelect.checkedValues.push(valueId);
                            parentSelect.checkedNames.push(valueName);
                        } else {
                            valueIndex = parentSelect.checkedValues.indexOf(valueId);
                            parentSelect.checkedValues.splice(valueIndex, 1);
                            parentSelect.checkedNames.splice(valueIndex, 1);
                            input = parentSelect.querySelector('[data-select-input][value="'+valueId+'"]');
                            input.remove();
                        }
                        console.log(parentSelect.checkedValues, parentSelect.checkedNames);
                    }
                }

                function unselectAllExceptCurrent(selectItem) {
                    var allItems = selectItem.parentNode.children;

                    allItems.forEach(function(item) {
                        if (item !== selectItem) {
                            item.classList.remove();
                        }
                    });
                }

                function unselectItem(item) {
                    item.classList.remove(classNames.se);
                }

                function fireClick(evt) {
                    if (evt.target.matches(selectors.selectText)) {
                        var select = evt.target.closest(self.selector);
                        if (select) {
                            select.classList.toggle(classNames.selectIsOpen);
                        }
                    }

                    if (evt.target.matches(selectors.selectItem)) {
                        toggleOneItem(evt.target);
                    }
                    if (evt.target.matches(selectors.selectValue)) {
                        var selectItem = evt.target.closest(selectors.selectItem);
                        toggleOneItem(selectItem);
                    }
                }

                selectList.forEach(function(select) {
                    select.checkedValues = [];
                    select.checkedNames  = [];

                    var selectText = select.querySelector('[data-select-text]');
                    var selectList = select.querySelector('[data-select-list]');

                    selectText.innerHTML = select.dataset.placeholder;
                });

                document.addEventListener('click', function(evt) {
                    if (evt.button === 0) {
                        fireClick(evt);
                    }
                });

                document.addEventListener('touchend', function (evt) {
                    fireClick(evt);
                });
            }
        })
    }
})();







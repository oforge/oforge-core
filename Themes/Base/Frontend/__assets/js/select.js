(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'selectTypes',
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
                    subSelect: 'form__control--is-sub'
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
                        allSubItems.forEach(function (item) {
                            unselectItem(item);
                            if (hasSubSelect(item)) {
                                resetSubSelect(item);
                            }
                        });

                        subSelect.classList.remove(classNames.selectIsOpen);
                    }
                }

                function toggleOneItem(selectItem) {
                    var parentSelect = null;
                    var toggleState = false;
                    var valueId = selectItem.dataset.valueId;
                    var valueName = selectItem.querySelector(selectors.selectValue).innerHTML;

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
                            parentSelect.querySelector(selectors.selectText).innerHTML = parentSelect.checkedNames.join(', ');
                        } else {
                            unselectItem(selectItem);
                        }
                        console.log(parentSelect.checkedValues, parentSelect.checkedNames);
                    }
                }

                function unselectAllExceptCurrent(selectItem) {
                    var allItems = selectItem.parentNode.children;

                    allItems.forEach(function (item) {
                        if (item !== selectItem) {
                            unselectItem(item)
                        }
                    });
                }

                function unselectItem(selectItem) {
                    var parentSelect = selectItem.closest(self.selector);
                    var input = null;
                    var valueIndex = null;

                    if (parentSelect) {
                       input = parentSelect.querySelector('[data-select-input][value="' + selectItem.dataset.valueId + '"]');
                       valueIndex = parentSelect.checkedValues.indexOf(selectItem.dataset.valueId);
                    }

                    selectItem.classList.remove(classNames.selectItemIsChecked);
                    if (valueIndex !== null) {
                        parentSelect.checkedValues.splice(valueIndex, 1);
                        parentSelect.checkedNames.splice(valueIndex, 1);
                    }
                    if (input) {
                        input.remove();
                    }
                    if (hasSubSelect(selectItem)) {
                        resetSubSelect(selectItem);
                    }

                    if (! parentSelect.classList.contains(classNames.subSelect)) {
                        parentSelect.querySelector(selectors.selectText).innerHTML = parentSelect.checkedNames.join(', ');
                    }
                    if (parentSelect.checkedNames.length < 1) {
                        parentSelect.querySelector(selectors.selectText).innerHTML = parentSelect.dataset.placeholder;
                    }
                }

                function fireClick(evt) {
                    if (evt.target.matches(selectors.selectText)) {
                        var select = evt.target.closest(self.selector);

                        var parentSelectList = document.querySelectorAll('.select:not(.select--is-sub)');

                        if (select) {
                            parentSelectList.forEach(function (selectElement) {
                                if (selectElement !== select && !select.classList.contains('select--is-sub')) {
                                    selectElement.classList.remove(classNames.selectIsOpen);
                                }
                            });
                            select.classList.toggle(classNames.selectIsOpen);
                        }
                    } else if (evt.target.matches(selectors.selectItem)) {
                        toggleOneItem(evt.target);
                    } else if (evt.target.matches(selectors.selectValue)) {
                        var selectItem = evt.target.closest(selectors.selectItem);
                        toggleOneItem(selectItem);
                    } else {
                        selectList.forEach(function (select) {
                            select.classList.remove(classNames.selectIsOpen);
                        });
                    }
                }

                selectList.forEach(function (select) {
                    select.checkedValues = [];
                    select.checkedNames = [];

                    var selectText = select.querySelector('[data-select-text]');
                    var selectList = select.querySelector('[data-select-list]');
                    var checkedElements = selectList.querySelectorAll(selectors.selectItemIsChecked);

                    selectText.innerHTML = select.dataset.placeholder;
                    checkedElements.forEach(function (checkedElement) {
                        select.checkedValues.push(checkedElement.dataset.valueId);
                        select.checkedNames.push(checkedElement.querySelector(selectors.selectValue).innerHTML);
                        addHiddenInputToCheckItem(checkedElement);
                    });
                    if (select.checkedNames.length > 0) {
                        selectText.innerHTML = select.checkedNames.join(', ');
                    }
                });

                document.addEventListener('click', function (evt) {
                    if (evt.button === 0) {
                        fireClick(evt);
                    }
                });
            }
        })
    }
})();







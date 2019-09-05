;(function () {
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
                    subSelect: 'form__control--is-sub',
                    selectRequireInput: 'select__require-input',
                    selectValues: []
                };
                var selectors = {
                    select: '.' + classNames.select,
                    selectIsOpen: '.' + classNames.selectIsOpen,
                    selectText: '.' + classNames.selectText,
                    selectItem: '.' + classNames.selectItem,
                    selectItemIsChecked: '.' + classNames.selectItemIsChecked,
                    selectRequireInput: '.' + classNames.selectRequireInput,
                    selectValue: '.' + classNames.selectValue,
                    subSelect: '[data-sub-select]',
                    selectFilter: '[data-select-filter]',
                    noSubSelect: '.select:not(.select--is-sub)[data-sortable]'
                };
                var selectList = document.querySelectorAll(self.selector);
                var currentOpenSelectFilterInputSelector = selectors.selectIsOpen + ' ' + selectors.selectFilter;
                var noSubSelectListSortable = document.querySelectorAll(selectors.noSubSelect);
                var selectedItems = [];

                function addHiddenInputToCheckItem(check) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', check.closest(self.selector).dataset.select);
                    input.setAttribute('data-select-input', check.dataset.valueId);
                    input.setAttribute('value', check.dataset.valueId);
                    check.closest(self.selector).appendChild(input);
                    updateRequiredInput(check);
                }

                function hasSubSelect(selectItem) {
                    return selectItem.querySelector(selectors.subSelect) !== null;
                }

                function resetSubSelect(selectItem) {
                    var subSelect = selectItem.querySelector(selectors.subSelect);
                    var allSubItems = null;
                    if (subSelect) {
                        allSubItems = subSelect.querySelectorAll(selectors.selectItem);
                        if (allSubItems.length > 0) {
                            allSubItems.forEach(function (item) {
                                unselectItem(item);
                                if (hasSubSelect(item)) {
                                    resetSubSelect(item);
                                }
                            });
                        }

                        subSelect.classList.remove(classNames.selectIsOpen);
                    }
                }

                function toggleOneItem(selectItem) {
                    var parentSelect = null;
                    var toggleState = false;
                    var valueId = selectItem.dataset.valueId;
                    var valueName = selectItem.querySelector(selectors.selectValue).innerHTML.replace('<strong>', '').replace('</strong>', '');

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
                        updateRequiredInput(selectItem);
                    }
                }

                //Fills out an invisible form element to make the select element required
                function updateRequiredInput(selectItem) {
                    let parentSelect = selectItem.closest(self.selector);
                    let requiredInput = parentSelect.querySelector(selectors.selectRequireInput);
                    if (requiredInput) {
                        if (parentSelect.checkedValues.length > 0) {
                            requiredInput.value = ' ';
                        } else {
                            requiredInput.value = '';
                        }
                    }
                }

                function unselectAllExceptCurrent(selectItem) {
                    var allItems = selectItem.parentNode.children;

                    if (allItems.length > 0) {
                        // HTMLCollection has no forEach
                        [].forEach.call(allItems, function (item) {
                            if (item !== selectItem) {
                                unselectItem(item)
                            }
                        });
                    }
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
                    var allSelects = null;
                    var currentOpenSelectFilterInput = null;

                    if (!evt.target.closest(selectors.selectIsOpen)) {
                        allSelects = document.querySelectorAll(selectors.selectIsOpen);
                        allSelects.forEach(function (selectItem) {
                            selectItem.classList.remove(classNames.selectIsOpen);
                        });
                    }

                    if (evt.target.matches(currentOpenSelectFilterInputSelector)) {
                        selectValues = evt.target.closest(selectors.select).querySelectorAll('.select__item:not(.select__item--sub) > .select__value');
                        currentOpenSelectFilterInput = document.querySelector(currentOpenSelectFilterInputSelector);

                        currentOpenSelectFilterInput.onkeyup = function (evt) {
                            var matchStart = 0;
                            var beforeMatch = null;
                            var afterMatch = null;
                            var matchText = null;
                            var matchEnd = currentOpenSelectFilterInput.value.length;

                            selectValues.forEach(function (selectedValue) {

                                selectedValue.innerHTML = selectedValue.innerHTML.replace('<strong>', '').replace('</strong>', '');

                                if (matchEnd > 0) {
                                    if (selectedValue.innerHTML.toLowerCase().indexOf(currentOpenSelectFilterInput.value.toLowerCase()) > -1) {

                                        matchStart = selectedValue.innerHTML.toLowerCase().indexOf(currentOpenSelectFilterInput.value.toLowerCase());
                                        beforeMatch = selectedValue.innerHTML.slice(0, matchStart);
                                        matchText = selectedValue.innerHTML.slice(matchStart, matchStart + matchEnd);
                                        afterMatch = selectedValue.innerHTML.slice(matchStart + matchEnd);
                                        selectedValue.innerHTML = beforeMatch + '<strong>' + matchText + '</strong>' + afterMatch;
                                        selectedValue.closest('.select__item').classList.remove('select__item--is-hidden');
                                    } else {
                                        selectedValue.closest('.select__item').classList.add('select__item--is-hidden');
                                    }
                                } else {
                                    selectedValue.closest('.select__item').classList.remove('select__item--is-hidden');
                                }
                            });
                        };
                    }

                    if (evt.target.matches(selectors.selectText)) {
                        var select = evt.target.closest(self.selector);
                        var parentSelectList = document.querySelectorAll('.select:not(.select--is-sub)');

                        if (select) {
                            if (parentSelectList.length > 0) {
                                parentSelectList.forEach(function (selectElement) {
                                    if (selectElement !== select && !select.classList.contains('select--is-sub')) {
                                        selectElement.classList.remove(classNames.selectIsOpen);
                                    }
                                });
                            }
                            select.classList.toggle(classNames.selectIsOpen);
                        }
                    } else if (evt.target.matches(selectors.selectItem)) {
                        console.log(evt.target);
                        toggleOneItem(evt.target);
                    } else if (evt.target.matches(selectors.selectValue)) {
                        var selectItem = evt.target.closest(selectors.selectItem);
                        toggleOneItem(selectItem);
                    }
                }

                function sortValues(list, items) {
                    var arr = [];
                    items.forEach(function(item) {
                        arr.push(item);
                    });
                    arr.sort(function(a, b) {
                        return a.innerHTML == b.innerHTML ? 0 : (a.innerHTML > b.innerHTML ? 1 : -1);
                    });
                    arr.forEach(function(item) {
                        list.appendChild(item);
                    });
                }

                if (noSubSelectListSortable.length > 0) {
                    noSubSelectListSortable.forEach(function (select) {
                        var list = select.querySelector('.simplebar-content');
                        var items = select.querySelectorAll('.select__item:not(.select__item--sub)');
                        sortValues(list, items);
                    });
                }

                if (selectList.length > 0) {
                    selectList.forEach(function (select) {

                        select.checkedValues = [];
                        select.checkedNames = [];

                        var selectText = select.querySelector('[data-select-text]');
                        var selectList = select.querySelector('[data-select-list]');
                        var checkedElements = selectList.querySelectorAll(selectors.selectItemIsChecked);

                        selectText.innerHTML = select.dataset.placeholder;
                        checkedElements.forEach(function (checkedElement) {
                            var checkedName = checkedElement.querySelector(selectors.selectValue).innerHTML.replace('<strong>', '').replace('</strong>', '');
                            select.checkedValues.push(checkedElement.dataset.valueId);
                            select.checkedNames.push(checkedName);
                            addHiddenInputToCheckItem(checkedElement);
                            updateRequiredInput(checkedElement);
                        });
                        if (select.checkedNames.length > 0) {
                            selectText.innerHTML = select.checkedNames.join(', ');
                        }
                    });
                }

                document.addEventListener('click', function (evt) {
                    if (evt.button === 0) {
                        fireClick(evt);
                    }
                });

                //Fixes a weird scrolling bug
                document.getElementsByClassName('simplebar-content-wrapper').forEach(function (simplebar) {
                    simplebar.setAttribute('tabindex', -1);
                });
            }
        })
    }
})();

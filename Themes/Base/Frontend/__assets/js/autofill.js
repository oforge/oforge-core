;(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'autofill',
            selector: '[data-autofill]',
            init: function () {
                let self = this;
                let classNames = {
                    autofill: 'autofill',
                    autofillItems: 'autofill__items',
                    autofillOption: 'autofill__option',
                    autofillActive: 'option--active',
                };
                let selectors = {
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

                let defaults = {
                    minCharacters: 3
                };

                let autofillIds = [];

                function autofill(autofillInput, autofillData) {
                    let currentFocus;

                    autofillInput.addEventListener("input", function (event) {
                        let value = this.value;
                        //close any already open lists of autofilled values
                        closeAllLists();

                        let minCharacters = this.dataset.autofillMinCharacters !== null ? this.dataset.autofillMinCharacters : defaults.minCharacters;
                        if (!value || value.length < minCharacters) {
                            return false;
                        }
                        currentFocus = -1;

                        //create a div element that will contain the autofill items
                        let autofillItems = document.createElement("div");
                        autofillItems.setAttribute("id", this.id + "autofill-list");
                        autofillItems.classList.add(classNames.autofillItems);
                        this.parentNode.appendChild(autofillItems);


                        for (let i = 0; i < autofillData.length; i++) {
                            if (autofillData[i].toUpperCase().includes(value.toUpperCase())) {
                                //create a div element for each autofill option
                                let autofillOption = document.createElement("div");
                                autofillOption.classList.add(classNames.autofillOption);

                                //emphasize matching characters

                                autofillOption.innerHTML = emphasizeValueInOption(autofillData[i], value);

                                //insert a input field that will hold the current array item's value
                                autofillOption.innerHTML += "<input type='hidden' value='" + autofillData[i] + "'>";
                                autofillOption.addEventListener("click", function (event) {
                                    autofillInput.value = this.getElementsByTagName("input")[0].value;
                                    closeAllLists();
                                });
                                autofillItems.appendChild(autofillOption);
                            }
                        }
                    });

                    autofillInput.addEventListener("keydown", function (event) {
                        var x = document.getElementById(this.id + "autofill-list");
                        if (x) x = x.getElementsByTagName("div");
                        if (event.keyCode === 40) {
                            /*If the arrow DOWN key is pressed,
                            increase the currentFocus variable:*/
                            currentFocus++;
                            /*and and make the current item more visible:*/
                            addActive(x);
                        } else if (event.keyCode === 38) { //up
                            /*If the arrow UP key is pressed,
                            decrease the currentFocus variable:*/
                            currentFocus--;
                            /*and and make the current item more visible:*/
                            addActive(x);
                        } else if (event.keyCode === 13) {
                            /*If the ENTER key is pressed, prevent the form from being submitted,*/
                            e.preventDefault();
                            if (currentFocus > -1) {
                                /*and simulate a click on the "active" item:*/
                                if (x) x[currentFocus].click();
                            }
                        }
                    });

                    function addActive(x) {
                        /*a function to classify an item as "active":*/
                        if (!x) return false;
                        /*start by removing the "active" class on all items:*/
                        removeActive(x);
                        if (currentFocus >= x.length) currentFocus = 0;
                        if (currentFocus < 0) currentFocus = (x.length - 1);
                        /*add class "autocomplete-active":*/
                        x[currentFocus].classList.add(classNames.autofillActive);
                    }

                    function removeActive(x) {
                        /*a function to remove the "active" class from all autocomplete items:*/
                        for (var i = 0; i < x.length; i++) {
                            x[i].classList.remove(classNames.autofillActive);
                        }
                    }

                    function closeAllLists(element) {
                        /*close all autocomplete lists in the document,
                        except the one passed as an argument:*/
                        var x = document.getElementsByClassName(classNames.autofillItems);
                        for (var i = 0; i < x.length; i++) {
                            if (element !== x[i] && element !== autofillInput) {
                                x[i].parentNode.removeChild(x[i]);
                            }
                        }
                    }

                    /*execute a function when someone clicks in the document:*/
                    document.addEventListener("click", function (e) {
                        closeAllLists(e.target);
                    });
                }

                function emphasizeValueInOption(option, value) {
                    let emphasizedHtml = "";

                    let matchStart = 0;
                    let beforeMatch = null;
                    let afterMatch = null;
                    let matchText = null;
                    let matchEnd = value.length;

                    if (option.toLowerCase().indexOf(value.toLowerCase()) > -1) {
                        matchStart = option.toLowerCase().indexOf(value.toLowerCase());
                        beforeMatch = option.slice(0, matchStart);
                        matchText = option.slice(matchStart, matchStart + matchEnd);
                        afterMatch = option.slice(matchStart + matchEnd);
                        emphasizedHtml = beforeMatch + '<strong>' + matchText + '</strong>' + afterMatch;
                    }
                    return emphasizedHtml;
                }

                //requests the available autofill-data and associates it with corresponding input
                function getAutofillData(url, attributeKeys, autofillInput) {
                    let attributeCount = 1;
                    let requestParams = '?';

                    //prepare request
                    attributeKeys.forEach(function (attributeKey) {
                        requestParams += attributeCount !== 1 ? '&' : '';
                        requestParams += 'attributekeys[' + attributeCount + ']=' + attributeKey;
                        attributeCount++;
                    });

                    let xhr = new XMLHttpRequest();
                    xhr.open("GET", url + requestParams, true);
                    xhr.onload = function (e) {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                let jsonData = JSON.parse(xhr.responseText);
                                //associate autofill-data with corresponding input field
                                autofill(autofillInput, Object.keys(jsonData).map((key) => jsonData[key]));
                            } else {
                                //don't care
                                console.error(xhr.statusText);
                            }
                        }
                    };
                    xhr.send(null);
                }

                document.querySelectorAll(self.selector).forEach(function (autofillInput) {
                    autofillInput.classList.add(classNames.autofill);

                    if(autofillInput.getAttribute('id') === null) {
                        let id = btoa(Math.floor(Math.random() * 10000).toString());
                        console.log(id);
                        while(!autofillIds.includes(id)) {
                            id = btoa(Math.floor(Math.random() * 10000).toString());
                            if (!autofillIds.includes(id)) {
                                autofillInput.setAttribute("id", id);
                                autofillIds.push(id);
                            }
                        }
                    }
                    let attributeKeys = JSON.parse(autofillInput.dataset.autofillAttributeKeys);
                    getAutofillData(autofillInput.dataset.autofillEndpoint, attributeKeys, autofillInput);
                });
            }
        });
    }
})();

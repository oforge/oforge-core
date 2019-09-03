/*Example Usage:
use any input types that you want to be autofilled
    <input type="text"

    //turn default autocomplete off
    autocomplete="off"

    //selector for the javascript plugin
    data-autofill="true"

    //define the endpoint from which the autofill-data should be fetched
    data-autofill-endpoint="url"

    //define an additional array of attribute keys from which you want the values to be fetched
    data-autofill-attribute-keys="[1, 2, 3, ...]"

    //minimum amount of characters to be typed to suggest autofill-values
    data-autofill-min-characters="2"

    //placeholder to show, when no suggestions where found
    data-autofill-placeholder='No Suggestions'
    />
*/

;(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'autofill',
            selector: '[data-autofill]',
            init: function () {
                let self = this;
                let cache = {}, processing = {}, requestAttempts = {};

                let classNames = {
                    autofill: 'autofill',
                    autofillItems: 'autofill__items',
                    autofillOption: 'autofill__option',
                    autofillPlaceholder: 'autofill__placeholder',
                    autofillActive: 'option--active',
                };

                let selectors = {
                    input: 'input',
                    div: 'div'
                };

                let defaults = {
                    minCharacters: 3,
                    maxIds: 65536,
                    maxRequestAttempts: 50,
                    requestWaitingTime: 100
                };

                let keycodes = {
                    down: 40,
                    up: 38,
                    enter: 13
                };

                let autofillIds = [];

                function autofill(autofillInput, autofillData) {
                    let currentFocus;

                    autofillInput.addEventListener(selectors.input, function (event) {
                        let value = this.value;
                        let suggestionsFound = false;
                        let minCharacters = this.dataset.autofillMinCharacters !== null ? this.dataset.autofillMinCharacters : defaults.minCharacters;

                        //close any already open lists of autofilled values
                        closeAllLists();

                        if (!value || value.length < minCharacters) {
                            return false;
                        }
                        currentFocus = -1;

                        //create a div element that will contain the autofill items
                        let autofillItems = document.createElement("div");
                        autofillItems.setAttribute("id", this.id + classNames.autofillItems);
                        autofillItems.setAttribute("data-simplebar", "");
                        autofillItems.classList.add(classNames.autofillItems);

                        for (let i = 0; i < autofillData.length; i++) {
                            if (autofillData[i].toUpperCase().includes(value.toUpperCase())) {
                                suggestionsFound = true;
                                //create a div element for each autofill option
                                let autofillOption = document.createElement("div");
                                autofillOption.classList.add(classNames.autofillOption);

                                //emphasize matching characters
                                autofillOption.innerHTML = emphasizeValueInOption(autofillData[i], value);

                                //insert an input field that will hold the current array item's value
                                autofillOption.innerHTML += "<input type='hidden' value='" + autofillData[i] + "'>";
                                autofillOption.addEventListener("click", function (event) {
                                    autofillInput.value = this.getElementsByTagName(selectors.input)[0].value;
                                    closeAllLists();
                                });
                                autofillItems.appendChild(autofillOption);
                            }
                        }

                        if (suggestionsFound) {
                            this.parentNode.appendChild(autofillItems);
                        } else if (autofillInput.dataset.autofillPlaceholder) {
                            //append a placeholder if defined
                            let autofillPlaceholder = document.createElement("div");

                            autofillPlaceholder.innerHTML = autofillInput.dataset.autofillPlaceholder;
                            autofillPlaceholder.classList.add(classNames.autofillPlaceholder);

                            this.parentNode.appendChild(autofillItems);
                            autofillItems.appendChild(autofillPlaceholder);
                        }
                    });

                    autofillInput.addEventListener("keydown", function (event) {
                        let self = document.getElementById(this.id + classNames.autofillItems);
                        if (self) {
                            self = self.getElementsByTagName(selectors.div);
                        }
                        if (event.keyCode === keycodes.down) {
                            currentFocus++;
                            addActive(self);
                        } else if (event.keyCode === keycodes.up) {
                            currentFocus--;
                            addActive(self);
                        } else if (event.keyCode === keycodes.enter) {
                            event.preventDefault();
                            if (currentFocus > -1) {
                                if (self) {
                                    self[currentFocus].click();
                                }
                            }
                        }
                    });

                    function addActive(element) {
                        //adds 'active' class to selected element and removes it from all remaining elements
                        if (!element) return false;
                        //remove 'active' class on all items
                        removeActive(element);
                        if (currentFocus >= element.length) currentFocus = 0;
                        if (currentFocus < 0) currentFocus = (element.length - 1);
                        //add 'active' class
                        element[currentFocus].classList.add(classNames.autofillActive);
                    }

                    function removeActive(element) {
                        //removes active class from element
                        for (let i = 0; i < element.length; i++) {
                            element[i].classList.remove(classNames.autofillActive);
                        }
                    }

                    function closeAllLists(element) {
                        //close all autofill lists in the document, except the one passed as an argument
                        let self = document.getElementsByClassName(classNames.autofillItems);
                        for (let i = 0; i < self.length; i++) {
                            if (element !== self[i] && element !== autofillInput) {
                                self[i].parentNode.removeChild(self[i]);
                            }
                        }
                    }

                    document.addEventListener("click", function (event) {
                        closeAllLists(event.target);
                    });
                }

                function emphasizeValueInOption(option, value) {
                    //highlights the matching phrase 'value' within a suggested 'option'
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
                function getAutofillData(endpoint, attributeKeys, autofillInput) {
                    let url = endpoint;
                    let attributeCount = 1;
                    let requestParams = '?';

                    //prepare request
                    attributeKeys.forEach(function (attributeKey) {
                        requestParams += attributeCount !== 1 ? '&' : '';
                        requestParams += 'attributekeys[' + attributeCount + ']=' + attributeKey;
                        attributeCount++;
                    });

                    url = url + requestParams;

                    //check if a similar request has already been made
                    if (cache[url]) {
                        autofill(autofillInput, cache[url]);
                    } else if (processing[url] === true) {
                        if(!requestAttempts[url]) {
                            requestAttempts[url] = defaults.maxRequestAttempts;
                        }
                        if(requestAttempts[url]-- > 0) {
                            setTimeout(function () {
                                getAutofillData(endpoint, attributeKeys, autofillInput);
                            }, defaults.requestWaitingTime);
                        }
                    } else {
                        processing[url] = true;
                        let xhr = new XMLHttpRequest();
                        xhr.open("GET", url, true);
                        xhr.onload = function (event) {
                            if (xhr.readyState === 4) {
                                if (xhr.status === 200) {
                                    let jsonData = JSON.parse(xhr.responseText);
                                    let autofillData = Object.keys(jsonData).map((key) => jsonData[key]);
                                    //associate autofill-data with corresponding input field
                                    autofill(autofillInput, autofillData);
                                    processing[url] = false;
                                    cache[url] = autofillData;
                                } else {
                                    //don't care
                                    console.error(xhr.statusText);
                                }
                            }
                        };
                        xhr.send(null);
                    }
                }

                function assignRandomId(element) {
                    //Generates a random base64 id and assigns it to the calling element
                    let id = btoa(Math.floor(Math.random() * defaults.maxIds).toString());
                    while (!autofillIds.includes(id)) {
                        id = btoa(Math.floor(Math.random() * defaults.maxIds).toString());
                        if (!autofillIds.includes(id)) {
                            element.setAttribute("id", id);
                            autofillIds.push(id);
                        }
                    }
                }

                document.querySelectorAll(self.selector).forEach(function (autofillInput) {
                    autofillInput.classList.add(classNames.autofill);
                    //id might be needed later
                    if (autofillInput.getAttribute('id') === null) {
                        assignRandomId(autofillInput);
                    }
                    let attributeKeys = JSON.parse(autofillInput.dataset.autofillAttributeKeys);
                    getAutofillData(autofillInput.dataset.autofillEndpoint, attributeKeys, autofillInput);
                });
            }
        });
    }
})();

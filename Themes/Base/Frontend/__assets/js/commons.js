// check if the Oforge namespace exists
if (typeof Oforge !== 'undefined') {

    Oforge.removeQueryString = function (key, value, url) {
        if (!url) url = window.location.href;

        key = encodeURIComponent(key.trim()).replace(/%20/g, '\\+');

        var re = new RegExp("([?&])" + key + "=" + value + "?(&|#|$)(.*)", "g"),
            hash;

        if (re.test(url)) {
            if (typeof value !== 'undefined' && value !== null) {
                return url.replace(re, '$1$3');
            }
        }

        return url;
    };

    Oforge.updateQueryString = function (key, value, url, removeEmpty) {
        key = encodeURIComponent(key.trim()).replace(/%20/g, '\\+');

        if (!url) {
            url = window.location.href;
        }
        if (removeEmpty !== true && removeEmpty !== false) {
            removeEmpty = false;
        }
        var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "g"),
            hash;

        if (re.test(url)) {
            if (typeof value !== 'undefined' && value !== null) {
                if (value === '' && removeEmpty) {
                    return url.replace(re, '$1$3');
                } else {
                    return url.replace(re, '$1' + key + "=" + value + '$2$3');
                }
            } else {
                hash = url.split('#');
                url = hash[0].replace(re, '$1$3').replace(/([&?])$/, '');
                if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                    url += '#' + hash[1];
                return url;
            }
        } else {
            if (typeof value !== 'undefined' && value !== null) {
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                hash = url.split('#');
                url = hash[0] + separator + key + '=' + value;
                if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                    url += '#' + hash[1];
                return url;
            } else
                return url;
        }
    };


    // if it exists, it should have the register function, so register your module
    // the properties "name", "selector" and "init" are required
    // name: the name of your module
    // selector: the html selector to search for. If it is found, the module can be initialized
    // init: the function to initialize the module. This function gets called automatically from the module-loader.js
    // when the DOMContentLoaded event is triggered.
    Oforge.register({
        name: 'data-loadmore',
        selector: '[data-addmore]',
        init: function () {
            var self = this;

            $(self.selector).click(function (event) {
                var $button = $(this);
                var $container = $($button.data("container"));
                var url = $button.data("url");
                var clicked = $button.data("clicked");
                if (clicked) return;

                $button.data("clicked", true);
                var page = parseInt($button.data("page"), 10);
                if (page === null || isNaN(page)) {
                    page = 1;
                }
                page += 1;

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        if (this.responseText.trim() === '') {
                            $button.hide();
                        } else {
                            $container.append($(this.responseText));
                            $button.data("page", page);
                            var triggerEvent = $button.data("trigger-event");
                            if (triggerEvent != null) {
                                $(window).trigger(triggerEvent);
                            }
                        }
                    }

                    $button.data("clicked", false);
                };
                xhttp.open("GET", Oforge.updateQueryString("page", page, url), false);
                xhttp.send();

                event.preventDefault();
                return false;
            });
        }
    });

    // if it exists, it should have the register function, so register your module
    // the properties "name", "selector" and "init" are required
    // name: the name of your module
    // selector: the html selector to search for. If it is found, the module can be initialized
    // init: the function to initialize the module. This function gets called automatically from the module-loader.js
    // when the DOMContentLoaded event is triggered.
    Oforge.register({
        name: 'form-with-loading-icon',
        selector: '.form--submit--loading',
        init: function () {
            var self = this;
            var $target = $(self.selector);

            $target.on("submit", function (event) {
                $submitButton = $target.find(".form__input--submit--loading")
                if ($submitButton.length > 0) {
                    $submitButton.attr("disabled", true);
                    $submitButton.children(".default-text").hide();
                    $submitButton.children(".submit-text").show();
                }
            });
        }
    });


}



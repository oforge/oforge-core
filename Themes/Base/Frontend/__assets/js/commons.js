// check if the Oforge namespace exists
if (typeof Oforge !== 'undefined') {


    Oforge.removeQueryString = function (key, value, url) {
        if (!url) url = window.location.href;
        var re = new RegExp("([?&])" + encodeURIComponent(key) + "=" + encodeURIComponent(value) + "?(&|#|$)(.*)", "gi"),
            hash;

        if (re.test(url)) {
            if (typeof value !== 'undefined' && value !== null) {
                return url.replace(re, '$1$3');
            }
        }

        return url;
    }


    Oforge.updateQueryString = function (key, value, url) {
        if (!url) url = window.location.href;
        var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
            hash;

        if (re.test(url)) {
            if (typeof value !== 'undefined' && value !== null)
                return url.replace(re, '$1' + key + "=" + value + '$2$3');
            else {
                hash = url.split('#');
                url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
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
    }


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

            $elements = $(self.selector);
            $elements.click(function (event) {
                var target = $(event.target).closest(self.selector);
                var url = target.attr("data-url");
                var page = parseInt(target.attr("data-page"), 10);
                if (page == null || isNaN(page)) {
                    page = 1;
                }

                page += 1;

                container = $(target.attr("data-container"));

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText.trim() == "") {
                            target.hide();
                        } else {
                            container.append($(this.responseText));
                            target.attr("data-page", page);
                        }
                    }
                };
                xhttp.open("GET", Oforge.updateQueryString("page", page, url), false);
                xhttp.send();

                event.preventDefault();
                return false;
            });
        }
    });


}



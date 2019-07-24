if (typeof Oforge !== 'undefined') {

    Oforge.removeQueryString = function (key, value, url) {
        if (!url) {
            url = window.location.href
        }
        var regex = new RegExp("([?&])" + encodeURIComponent(key) + "=" + encodeURIComponent(value) + "?(&|#|$)(.*)", "gi");
        if (regex.test(url) && typeof value !== 'undefined' && value !== null) {
            return url.replace(regex, '$1$3').replace(/([&?])$/, '');
        }

        return url;
    };

    Oforge.updateQueryString = function (key, value, url, removeEmptyKey) {
        if (!url) {
            url = window.location.href;
        }
        if (removeEmptyKey !== true && removeEmptyKey !== false) {
            removeEmptyKey = false;
        }
        var hash, regex = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi");

        if (regex.test(url)) {
            if (typeof value !== 'undefined' && value !== null) {
                if (value === '' && removeEmptyKey) {
                    url = url.replace(regex, '$1$3');
                } else {
                    url = url.replace(regex, '$1' + key + "=" + value + '$2$3');
                }
            } else {
                hash = url.split('#');
                url = hash[0].replace(regex, '$1$3').replace(/([&?])$/, '');
                if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
                    url += '#' + hash[1];
                }
            }
        } else {
            if (typeof value !== 'undefined' && value !== null) {
                if (value !== '' || !removeEmptyKey) {
                    var separator = url.indexOf('?') !== -1 ? '&' : '?';
                    hash = url.split('#');
                    url = hash[0] + separator + key + '=' + value;
                    if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
                        url += '#' + hash[1];
                    }
                }
            }
        }
        return url.replace(/([&?])$/, '');
    };

}

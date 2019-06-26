if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector ||
        Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var element = this;

        do {
            if (element.matches(s)) return element;
            element = element.parentElement || element.parentNode;
        } while (element !== null && element.nodeType === 1);
        return null;
    };
}

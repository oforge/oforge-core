if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'exampleModule',
        selector: '[data-yes-no]',
        init: function () {
            var self = this;
            var htmlElements = document.querySelectorAll(self.selector);

            htmlElements.forEach(function(element) {
                element.addEventListener('click', function(evt) {
                    var text = element.dataset.dialogText;

                    if (!window.confirm(text)) {
                        evt.preventDefault();
                    }
                })
            });
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

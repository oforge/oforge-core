if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'exampleModule',
        selector: '[data-yes-no]',
        init: function () {
            var self = this;
            var htmlElement = document.querySelectorAll(self.selector);
            var dialogText = document.querySelector('[data-dialog-text]');

            document.addEventListener('click', function(evt) {
                var text = "";
                if (evt.target.matches(self.selector) || evt.target.matches(self.selector + ' span') || evt.target.matches(self.selector + ' span svg')) {
                    if (dialogText) {
                        text = dialogText.dataset.dialogText;
                    }
                    if (!window.confirm(text)) {
                        evt.preventDefault();
                    }
                }
            });
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

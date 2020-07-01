if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'toggler',
        selector: '.form__input--checkbox',
        init: function () {
            var self = this;
            var selectionElement = document.querySelectorAll(self.selector);

            for (var i = 0; i < selectionElement.length; i++) {
                selectionElement[i].addEventListener('change', function (event) {
                    changeHandler(event);
                });
            }

            function changeHandler(event) {
                if (event != null) {
                    var name = event.target.getAttribute("data-toggle-selector");
                    var element = document.querySelector("*[data-name=" + name + "]");
                    if (event.target.checked) {
                        element.classList.remove('hide');
                    } else {
                        element.classList.add('hide');
                    }
                }
            }
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

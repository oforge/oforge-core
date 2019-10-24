(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'forms',
            selector: 'form',
            init: function () {
                var self = this;
                let selectors = {
                    submit: 'button[type=submit]'
                };


                /*
                * This code is needed to handle different scripts wanting to disable the submit button asynchronously
                * e.g. When both Photo and Video Upload disable the button,
                * the button will only be enabled once both uploads are finished.
                *
                * Usage:    $(submitButton).trigger('enableSubmit');
                *           $(submitButton).trigger('disableSubmit');
                * */

                var submitButton = document.querySelector(self.selector + ' ' + selectors.submit);
                if(submitButton) {
                    submitButton.disableCounter = 0;
                    $(submitButton).on('disableSubmit', function (target) {
                        this.disableCounter++;
                        $(this).attr("disabled", true);
                        $(this).children(".default-text").hide();
                        $(this).children(".submit-text").show();
                    });

                    $(submitButton).on('enableSubmit', function (target) {
                        if (this.disableCounter > 0) {
                            this.disableCounter--;
                            if (this.disableCounter === 0) {
                                $(this).attr("disabled", false);
                                $(this).children(".default-text").show();
                                $(this).children(".submit-text").hide();
                            }
                        }
                    });
                }
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

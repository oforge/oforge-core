if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'newsletter-tile',
        selector: '[data-newsletter-tile]',
        selectors: {
            newsletterForm: ".newsletter-tile__form",
            newsletterText: ".newsletter-tile__text",
            submitButton: ".form__input--submit--loading",
            successText: ".success-text"
        },
        init: function () {
            let self = this;

            $(self.selectors.newsletterForm).on("submit", function (event) {
                event.preventDefault();

                disableSubmitButton();
                let data = $(this).serialize();
                let url = $(this).prop("action");

                $.post(url, data).done(function () {
                    enableSubmitButton();
                    showResponse();
                });

            });

            function disableSubmitButton() {
                let form = $(self.selectors.newsletterForm);
                let submitButton = form.find(self.selectors.submitButton);
                if (submitButton.length > 0) {
                    $(submitButton).trigger('disableSubmit');
                }
            }

            function enableSubmitButton() {
                let form = $(self.selectors.newsletterForm);
                let submitButton = $(form).find(self.selectors.submitButton);
                if (submitButton.length > 0) {
                    $(submitButton).trigger('enableSubmit');
                }

            }

            function showResponse() {
                let form = $(self.selectors.newsletterForm);
                let submitButton = $(form).find(self.selectors.submitButton);
                if (submitButton.length > 0) {
                    $(submitButton).children(".default-text").hide();
                    $(submitButton).children(".submit-text").hide();
                    $(submitButton).children(self.selectors.successText).show().fadeIn(300);
                }
            }
        }
    });
}

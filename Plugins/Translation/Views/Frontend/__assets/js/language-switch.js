if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'languageSwitch',
        selector: '.language-switch',
        init: function () {
            let self = this;
            this.$target = $(this.target);
            let allTranslations = {};
            let numberAvailableLanguages = this.$target.data('numberAvailableLanguages');
            let classList = {
                collapsed: 'language-switch--collapsed',
                expanded: 'language-switch--expanded',
                currentLanguage: 'language--current',
                languageOption: 'language--option',
                languageSwitchContent: 'language-switch__content',
                userGeneratedContent: 'user-generated',
                autoTranslatable: 'auto-translatable',
                languageLabel: 'language__label',
            };

            let selectors = {
                currentLanguage: '.' + classList.currentLanguage,
                languageOption: '.' + classList.languageOption,
                languageSwitchContent: '.' + classList.languageSwitchContent,
                languageLabel: '.' + classList.languageLabel,
                datasetLanguage: function (language) {
                    return '[data-language="' + language + '"]';
                },
                insertionId: '[data-insertionid]',
                autoTranslatableContent: '.' + classList.userGeneratedContent + '.' + classList.autoTranslatable
            };


            this.$target.on('mouseenter', function () {
                expand(this);
            });

            this.$target.on('mouseleave', function () {
                collapse(this)
            });

            $(self.selector).on('click', selectors.languageOption, function (evt) {
                switchLanguage(this);
                collapse(this);
            });

            function switchLanguage(languageOption) {
                let selectedLanguage = languageOption.dataset.language;
                let allSwitches = document.querySelectorAll(self.selector);

                allSwitches.forEach(function (singleSwitch) {
                    singleSwitch.dataset.selectedLanguage = selectedLanguage;

                    let $currentLanguageButton = $(singleSwitch).children(selectors.currentLanguage);
                    let $selectedLanguageButton = $(singleSwitch).find(selectors.languageOption + selectors.datasetLanguage(selectedLanguage));
                    $selectedLanguageButton.attr('data-language', $currentLanguageButton.attr('data-language'));
                    $currentLanguageButton.attr('data-language', selectedLanguage);

                    let $currentLanguageLabel = $currentLanguageButton.find(selectors.languageLabel).remove();
                    let $selectedLanguageLabel = $selectedLanguageButton.find(selectors.languageLabel).remove();
                    $(singleSwitch).find($currentLanguageButton).prepend($selectedLanguageLabel);
                    $(singleSwitch).find($selectedLanguageButton).prepend($currentLanguageLabel);
                });

                fetchTranslatedInsertionContent().then(function (data) {
                    allTranslations = data;
                    document.querySelectorAll(selectors.autoTranslatableContent).forEach(function (content) {
                        if (allTranslations[selectedLanguage][content.dataset.contentIdentifier]) {
                            content.dataset.language = selectedLanguage;
                            content.innerHTML = allTranslations[selectedLanguage][content.dataset.contentIdentifier];
                        }
                    });
                });
            }

            function fetchTranslatedInsertionContent() {
                let insertionId = document.querySelector(selectors.insertionId).dataset.insertionid;

                return new Promise(function (resolve, reject) {
                    if (Object.keys(allTranslations).length < numberAvailableLanguages) {
                        $.ajax({
                            method: 'GET',
                            url: '/translate/insertion/' + insertionId,
                            dataType: 'json',
                            success: function (data) {
                                resolve(data);
                            },
                            error: function (error) {
                                reject(error);
                            }
                        });
                    } else {
                        resolve(allTranslations);
                    }
                });
            }

            function expand(languageSwitch) {
                $(languageSwitch).addClass(classList.expanded);
                $(languageSwitch).removeClass(classList.collapsed);
            }

            function collapse(languageSwitch) {
                $(languageSwitch).addClass(classList.collapsed);
                $(languageSwitch).removeClass(classList.expanded);
            }


            document.querySelectorAll(selectors.autoTranslatableContent).forEach(function (content) {
                let language = content.dataset.language, identifier = content.dataset.contentIdentifier;
                if (!(language in allTranslations)) {
                    allTranslations[language] = {};
                }
                if (!(identifier in allTranslations[language])) {
                    allTranslations[language][identifier] = content.innerHTML;
                }
            });
        }
    });
} else {
    console.warn("Oforge is not defined. Module 'languageSwitch' cannot be registered.");
}

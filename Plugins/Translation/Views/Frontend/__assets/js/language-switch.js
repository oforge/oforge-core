if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'languageSwitch',
        selector: '.language-switch',
        init: function () {
            let self = this;
            this.$target = $(this.target);
            let classList = {
                collapsed: 'language-switch--collapsed',
                expanded: 'language-switch--expanded',
                currentLanguage: 'language--current',
                languageOption: 'language--option'
            };

            let selectors = {
                currentLanguage: '.' + classList.currentLanguage,
                languageOption: '.' + classList.languageOption,
            };

            this.$target.on('mouseenter', function () {
                $(this).addClass(classList.expanded);
                $(this).removeClass(classList.collapsed);
            });

            this.$target.on('mouseleave', function () {
                $(this).addClass(classList.collapsed);
                $(this).removeClass(classList.expanded);
            });

            $(selectors.languageOption).on('click', function(){
                switchLanguage(this.dataset.language);
            });

            function switchLanguage(language){
                console.log(self.$target.dataset);
            }
        }
    });
} else {
    console.warn("Oforge is not defined. Module 'languageSwitch' cannot be registered.");
}

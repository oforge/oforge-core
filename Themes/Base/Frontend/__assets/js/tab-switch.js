if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'tabSwitch',
        selector: '[data-tab]',
        contents: '[data-tab-content]',
        init: function () {
            var self = this;
            var tabs = document.querySelectorAll(self.selector);
            var tabContents = document.querySelectorAll(self.contents);

            tabs.forEach(function (tab) {
                var currentTab = tab.dataset.tab;
                var currentContent = document.querySelector('[data-tab-content="' + currentTab + '"]');

                tab.addEventListener('click', function (e) {
                    tabs.forEach(function (tabToDeactivate) {
                        tabToDeactivate.classList.remove('tab--active');
                    });

                    tabContents.forEach(function (tabContentToDeactivate) {
                        tabContentToDeactivate.classList.remove('tab__content--visible');
                    });

                    tab.classList.add('tab--active');
                    currentContent.classList.add('tab__content--visible');
                });
            });
            console.info("Module " + self.name + " initialized");
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

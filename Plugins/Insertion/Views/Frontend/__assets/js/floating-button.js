if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'floatingButton',
        selector: '#floating-button',
        init: function () {
            var self = this;
            var floatingButton = document.querySelector(self.selector);
            var insertionSidebar = floatingButton.closest('.insertion__sidebar');
            var searchForm = document.querySelector('#form-search');
            var raf = window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.msRequestAnimationFrame ||
                window.oRequestAnimationFrame;
            var floatingButtonPos = 0;
            var insertionSidebarPos = 0;
            var lastInsertionSidebarPos = 0;
            var ticking = false;

            function requestTick() {
                if (!ticking) {
                    raf(update);
                }
                ticking = true;
            }

            //Check if any input has been filled out
            function isFormFilled() {
                var inputs = searchForm.getElementsByTagName('input');
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].value !== "" && inputs[i].type !== 'submit') {
                        if (inputs[i].type === 'checkbox' && !inputs[i].checked) {
                            continue;
                        }
                        return true;
                    }
                }
                return false;
            }

            function onScroll() {
                floatingButtonPos = floatingButton.getBoundingClientRect().bottom;
                lastInsertionSidebarPos = insertionSidebarPos;
                insertionSidebarPos = insertionSidebar.getBoundingClientRect().bottom;
                requestTick();
            }

            function onFormChange() {
                requestTick();
            }

            function update() {
                if (isFormFilled()) {
                    floatingButton.classList.add('is-visible')
                } else {
                    floatingButton.classList.remove('is-visible');
                }
                if (floatingButtonPos > insertionSidebarPos && (lastInsertionSidebarPos === 0 || lastInsertionSidebarPos > insertionSidebarPos)) {
                    floatingButton.classList.add('position-static');

                } else if (floatingButtonPos < insertionSidebarPos && lastInsertionSidebarPos < insertionSidebarPos && insertionSidebarPos > window.innerHeight) {
                    floatingButton.classList.remove('position-static');
                }
                ticking = false;
            }

            window.addEventListener('scroll', function (evt) {
                onScroll();
            });

            $(searchForm).on('change', function(evt) {
                onFormChange();
            })
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

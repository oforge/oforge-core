if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'floatingButton',
        selector: '#floating-button',
        init: function () {
            var self = this;
            var floatingButton = document.querySelector(self.selector);
            var insertionSidebar = floatingButton.closest('.insertion__sidebar');
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

            function onScroll() {
                floatingButtonPos = floatingButton.getBoundingClientRect().bottom;
                lastInsertionSidebarPos = insertionSidebarPos;
                insertionSidebarPos = insertionSidebar.getBoundingClientRect().bottom;
                requestTick();
            }

            function update() {
                if (floatingButtonPos > insertionSidebarPos && lastInsertionSidebarPos > insertionSidebarPos) {
                    floatingButton.classList.add('position-static');

                } else if (floatingButtonPos < insertionSidebarPos && lastInsertionSidebarPos < insertionSidebarPos && insertionSidebarPos > window.innerHeight) {
                    floatingButton.classList.remove('position-static');
                }
                ticking = false;
            }

            window.addEventListener('scroll', function (evt) {
                onScroll();
            });

            update();
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

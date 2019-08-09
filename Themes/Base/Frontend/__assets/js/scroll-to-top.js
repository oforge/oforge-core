;(function () {
    var me = this;
    me.lastScrollPos = 0;
    me.ticking = false;
    me.scrollToTopElem = document.createElement('div');
    me.scrollToTopElem.id = "scroll_to_top";
    document.body.appendChild(me.scrollToTopElem);

    me.rAF = (function () {
        return window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            function (callback) {
                window.setTimeout(callback, 1000 / 60);
            };
    })();

    me.scrollToTopElem.addEventListener('click', function () {
        scrollUp();
    });

    function toggleScrollElement(scroll_pos) {
        if (scroll_pos > 200) {
            me.scrollToTopElem.classList.add('is--visible');
        } else {
            if (me.scrollToTopElem.classList.contains('is--visible')) {
                me.scrollToTopElem.classList.remove('is--visible');
            }
        }
    }

    function scrollUp() {
        var currentPos = window.scrollY || document.documentElement.scrollTop,
            destPos = 0;

        function tick() {
            if (currentPos > destPos) {
                currentPos = currentPos - 120;
                if (currentPos < destPos) {
                    currentPos = destPos;
                }
                me.rAF(tick);
                window.scrollTo(0, currentPos);
            }
        }

        tick();
    }

    window.addEventListener('scroll', function (e) {
        me.lastScrollPos = window.scrollY;
        if (!me.ticking) {
            me.rAF(function () {
                toggleScrollElement(me.lastScrollPos);
                me.ticking = false;
            });
            me.ticking = true;
        }
    });
})();

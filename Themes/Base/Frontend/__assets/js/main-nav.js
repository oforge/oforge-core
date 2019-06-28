(function() {
    /*
     * Hide the main navigation when resizing to mobile menu size
     */
    var resizeTimer = null;
    var elem = document.querySelector('.main-nav');
    var hidden = false;

    window.addEventListener('resize', function(evt) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            elem.style.display = 'block';
            hidden = false;
        }, 250);

        if (!hidden) {
            if (!window.matchMedia('(min-width: 1280px').matches) {
                elem.style.display = 'none';
                hidden = true;
            } else {
                elem.style.display = 'block';
            }
        }
    });
})();

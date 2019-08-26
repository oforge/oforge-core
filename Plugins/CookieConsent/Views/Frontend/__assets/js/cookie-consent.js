/**
 * This code handles the cookie consent banner.
 */

(function() {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'cookie-consent',
            selector: '#cookie__consent',
            init: function () {
                let container = document.getElementById('cookie__consent');
                const consent = localStorage.getItem('cookie_consent');

                if (consent === null) {
                    setTimeout(() => {
                        container.classList.toggle('visible');

                    }, 1000);
                    let accept_btn = document.getElementById('cookie__accept');
                    let decline_btn = document.getElementById('cookie__decline');

                    try {
                        accept_btn.addEventListener('click', function() {
                            localStorage.setItem('cookie_consent', 'accepted');
                            fadeOut(container);

                        });
                        decline_btn.addEventListener('click', function() {
                            localStorage.setItem('cookie_consent', 'declined');
                            fadeOut(container);
                        })

                    } catch(e) {
                        console.error(e);
                    }
                }
                // consent has been set and we don't want the container to block underlying elements
                else {
                    container.style.display = 'none';
                }
                function fadeOut(element) {
                    element.classList.toggle('visible');  // run css animation
                    setTimeout(() => {
                        element.style.display = 'none';
                    }, 1000);
                }
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

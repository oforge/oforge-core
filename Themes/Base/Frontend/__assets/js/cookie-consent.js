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
                            localStorage.setItem('cookie_consent', 'true');
                            container.classList.toggle('visible');
                        });
                        decline_btn.addEventListener('click', function() {
                            localStorage.setItem('cookie_consent', 'false');
                            container.classList.toggle('visible');
                        })

                    } catch(e) {
                        console.error(e);
                    }
                }
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

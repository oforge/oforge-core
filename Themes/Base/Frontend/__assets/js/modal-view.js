(function() {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'modalView',
            selector: '[data-modal-view]',
            selectors: {
                closeButton: '[data-modal-close]'
            },
            init: function () {
                var self = this;
                var modal = document.querySelector(self.selector);
                if (modal.hasAttribute('data-auto-open') && !getAutoModalSeen(modal.dataset.modalView)) {
                    openModalView(modal.dataset.modalView);
                }

                function openModalView(id) {
                    var modalViewContent = document.querySelector('[data-modal-content="' + id + '"]');
                    modalViewContent.classList.add('is--visible');
                }

                function closeModalView(id) {
                    var modal = document.querySelector('[data-modal-view="' + id + '"]');
                    var modalViewContent = document.querySelector('[data-modal-content="' + id + '"]');
                    modalViewContent.classList.remove('is--visible');
                    if (modal.hasAttribute('data-auto-open')) {
                        setAutoModalSeen(id);
                    }
                }

                function setAutoModalSeen(id) {
                    sessionStorage.setItem('oforge_' + id, '1');
                }

                function getAutoModalSeen(id) {
                    return sessionStorage.getItem('oforge_' + id);
                }

                document.addEventListener('click', function (evt) {
                    if (evt.target.matches(self.selector)) {
                        openModalView(evt.target.dataset.modalView);
                    }
                    if (evt.target.matches('.modal-view')) {
                        closeModalView(evt.target.dataset.modalContent);
                    }
                    if (evt.target.matches(self.selectors.closeButton)) {
                        closeModalView(evt.target.dataset.modalClose)
                    }
                });
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();

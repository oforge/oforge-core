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

                function openModalView(id) {
                    var modalViewContent = document.querySelector('[data-modal-content="' + id + '"]');
                    modalViewContent.classList.add('is--visible');
                }

                function closeModalView(id) {
                    var modalViewContent = document.querySelector('[data-modal-content="' + id + '"]');
                    modalViewContent.classList.remove('is--visible');
                }

                document.addEventListener('click', function (evt) {
                    if (evt.target.matches(self.selector)) {
                        openModalView(evt.target.dataset.modalView);
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

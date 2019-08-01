(function ($) {
    // Make the dashboard widgets sortable Using jquery UI
    $('.connectedSortable').sortable({
        containment: $('section.content'),
        placeholder: 'sort-highlight',
        connectWith: '.connectedSortable',
        handle: '.box-header, .nav-tabs',
        forcePlaceholderSize: true,
        zIndex: 999999,
        stop: function (event, ui) {
            var data = {};
            var container = $('.connectedSortable');
            window.setTimeout(function () {
                container.each(function () {
                    var c = $(this);
                    var site = c.data('site');
                    if (site !== null) {
                        var children = c.children('.box');
                        children.each(function (index) {
                            var child = $(this);
                            var id = child.data('id');
                            if (id !== null) {
                                data[id] = {'order': index, 'position': site};
                            }
                        });
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/backend/dashboard/widgets',
                    async: false,
                    data: {'data': data},
                    success: function () {
                    }
                });
            }, 0);
        }
    });

    $('.connectedSortable .box-header, .connectedSortable .nav-tabs-custom').css('cursor', 'move');
})(jQuery);

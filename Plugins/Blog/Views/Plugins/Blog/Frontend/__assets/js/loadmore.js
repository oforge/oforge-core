// TODO load more ajax script
(function ($) {
    $('#blog-load-more').click(function () {
        var page = $(this).data('page') || 1;
        var config = $(this).data('config');
        var url = config.url;
        var container = config.container;
        var insertionPosition = config.insertionPosition;
        console.log(config);
        // $.get(url, )
        //     .done(function() {
        //         alert( "second success" );
        //     })
        //     .fail(function() {
        //         alert( "error" );
        //     });



        console.log(page);
        $(this).data('page', page + 1);
    });
})(jQuery);

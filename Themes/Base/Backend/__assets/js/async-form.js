if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'asyncForm',
        selector: 'form[data-async]',
        init: function () {
            var $target = $(this.target);
            $target.each(function () {
                $(this).on("submit", function (event) {

                    var form = $(this);
                    var url = form.attr('action');

                    event.preventDefault();

                    var replace = form.data("replace");

                    $.ajax({
                        type: form.attr('method'),
                        url: url,
                        data: new FormData(this),
                        async: true,
                        cache: false,
                        contentType: false,
                        processData: false,

                        success: function (data) {
                            if (replace != null) {
                                $(replace).html(data);
                            }
                        }
                    });


                    return false;
                });
            });
        }
    });
}

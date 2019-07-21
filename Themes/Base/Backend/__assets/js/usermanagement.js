(function ($) {
    $('.modal--delete').on('show.bs.modal', function (event) {
        // Values to fill
        var button = $(event.relatedTarget); // Button that triggered the modal
        var target = button.data('target');
        var dataId = button.data('query-id');
        var dataName = button.data('name');
        var dataTitle = button.data('title');
        var dataLabel = button.data('label');
        var dataUrl = button.data('url');


        // Elements to be filled
        var titleElem = $(target + ' [data-element=title]');
        var labelElem = $(target + ' [data-element=label]');
        var nameElem = $(target + ' [data-element=name]');
        var queryIdElem = $(target + ' [data-element=query-id]');
        var deleteForm = $('#form_delete');

        titleElem.text(dataTitle);
        labelElem.text(dataLabel);
        nameElem.text(dataName);
        deleteForm.attr("action", dataUrl);
        queryIdElem.attr("value", dataId);

        //console.log(userEmail);
        //console.log($('#delete_user'));

        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this);
    });
})(jQuery);

/*
data-url="{{ url("backend_users_delete") }}"
data-cancel-url="{{ url("backend_users") }}"
data-query-id="{{ user.id }}"
data-name="{{ user.email }}">
*/

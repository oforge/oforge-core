(function ($) {
    $('.modal--delete').on('show.bs.modal', function (event) {
        // Values to fill
        let button = $(event.relatedTarget); // Button that triggered the modal
        let target = button.data('target');
        let dataId = button.data('query-id');
        let dataName = button.data('name');
        let dataTitle = button.data('title');
        let dataLabel = button.data('label');
        let dataUrl = button.data('url');


        // Elements to be filled
        let titleElem = $(target + ' [data-element=title]');
        let labelElem = $(target + ' [data-element=label]');
        let nameElem = $(target + ' [data-element=name]');
        let queryIdElem = $(target + ' [data-element=query-id]');
        let deleteForm = $('#form_delete');

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
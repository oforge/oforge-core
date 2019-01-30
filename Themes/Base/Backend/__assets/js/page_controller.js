$('#page_controller_jstree').on("changed.jstree", function (e, data) {
    console.log(data.selected);
});

$(document).ready(function() {
    $('#page_controller_jstree').jstree(page_controller_tree);
});

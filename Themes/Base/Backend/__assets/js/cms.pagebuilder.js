// variable that holds the content type id that should be transmitted with the dnd operation
var pbDndContentTypeId = undefined;

// variable that holds the placeholder order index that should be transmitted with the dnd operation
var pbDndPlaceholderOrderIndex = undefined;

// status variable to check if a dnd operation is in progress
var pbDndActive = false;

// status variable to check if foreign object was dragged to jsTree
var pbIsForeignDND = false;

// variable that holds the selected parent for content type dragged to jsTree
var pbContentTypeParentForeignDND = false;

$(function () {
    $('#page_builder_container_wrapper [data-toggle="tooltip"]').tooltip();

    $('#cms_content_type_list_filter').keyup(function() {
        var value = $(this).val().trim().toLowerCase();
        $('#cms_content_type_list_open_all').click();
        $('#cms_content_type_list_container .content-type-selector').each(function() {
            $(this)[value === '' || this.title.toLowerCase().includes(value) ? 'show' : 'hide']();
        });
    });

    $('#cms_content_type_list_collapse_all').click(function() {
        $('#cms_content_type_list_container .row.collapse.in').collapse('hide');
    });

    $('#cms_content_type_list_open_all').click(function() {
        $('#cms_content_type_list_container .row.collapse').collapse('show');
    });
});




// drag 'n drop event listeners for jsTree foreign objects
$(document).bind("dnd_start.vakata", function (event, data) {
    console.log("jsTree - Start dnd");
    console.log("Data:");
    console.log(jsonStringify(data.data.jstree));
    console.log(jsonStringify(data.data.obj));
    console.log(jsonStringify(data.data.nodes));
    console.log("------------------");
})
    .bind("dnd_stop.vakata", function (event, data) {
        console.log("jsTree - Stop dnd");
        console.log("Data:");
        console.log(jsonStringify(data.data.jstree));
        console.log(jsonStringify(data.data.obj));
        console.log(jsonStringify(data.data.nodes));
        console.log("this was a foreign operation: " + pbIsForeignDND);
        console.log("------------------");

        var pbDndContentTypeId = data.data;

        if (pbIsForeignDND && pbContentTypeParentForeignDND) {
            if (data && data.data && data.data.nodes && data.data.nodes.length > 0) {
                var node = data.data.nodes[0];

                if (node && node.data_ct_id) {
                    $('#cms_edit_element_id').val(node.data_ct_id);
                    $('#cms_edit_element_parent_id').val(pbContentTypeParentForeignDND);
                    $('#cms_edit_element_action').val('dnd');
                    $('#cms_element_jstree_form').submit();
                }
            }
        }
    });

// make foreign objects draggable to jsTree
$('.jstree_draggable').on('mousedown', function (event) {
    console.log("pagebuilder - starting to drag .jstree-draggable");

    $(this).wrap("<div id='jstree-drag-element'></div>");
    var dragHelper = '<div id="jstree-dnd" class="jstree-default"><i class="jstree-icon jstree-er"></i>' + $('#jstree-drag-element').html() + '<ins class="jstree-copy" style="display:none;">+</ins></div>';
    $(this).unwrap();

    return $.vakata.dnd.start(
        event,
        {
            'jstree': true,
            'obj': $(this),
            'nodes': [{
                'icon': 'jstree-file',
                'text': 'New Content Element',
                'data_ct_id': $(this).attr('data-ct-id')
            }]
        },
        dragHelper
    );
});

// content type drag 'n drop functionality
$('.content-type-selector').on('mousedown', function (event) {
    if (pbDndActive === false && event.which === 1) {
        pbDndContentTypeId = $(this).attr('data-ct-id');
        pbDndPlaceholderOrderIndex = undefined;
        pbDndActive = true;
    }
    console.log("pagebuilder - Dragging content type id: " + pbDndContentTypeId);
});

$('.content-type-edit-placeholder').on('mouseover', function (event) {
    if (pbDndActive === true) {
        $(this).addClass("content-type-edit-placeholder-drag-over");
        pbDndPlaceholderOrderIndex = $(this).attr('data-pb-order');
        console.log("pagebuilder - Dragged over placeholder with order: " + pbDndPlaceholderOrderIndex);
    }
});

$('.content-type-edit-placeholder').on('mouseleave', function (event) {
    if (pbDndActive === true) {
        $(this).removeClass("content-type-edit-placeholder-drag-over");
        pbDndPlaceholderOrderIndex = undefined;
        console.log("pagebuilder - Drag leaving placeholder with order: " + $(this).attr('data-pb-order'));
    }
});

$(document).on('mouseup', function (event) {
    if (pbDndActive === true && event.which === 1) {
        $(this).removeClass("content-type-edit-placeholder-drag-over");

        if (pbDndContentTypeId && pbDndPlaceholderOrderIndex) {
            $('#cms_page_create_content_with_type_id').val(pbDndContentTypeId);
            $('#cms_page_create_content_at_order_index').val(pbDndPlaceholderOrderIndex);
            $('#cms_page_selected_action').val('create');
            $('#cms_page_builder_form').submit();

            console.log("pagebuilder - Dropped on placeholder with order: " + pbDndPlaceholderOrderIndex + " and data: " + pbDndContentTypeId);
        } else {
            console.log("pagebuilder - Dropped outside of any placeholder!");

        }
    }

    pbDndContentTypeId = undefined;
    pbDndPlaceholderOrderIndex = undefined;
    pbDndActive = false;
});


// on edit cancel button event
$('#cms-page-builder-cancel').click(
    function () {
        var lastElementIdPosition = $(this).attr('data-pb-se').lastIndexOf('-');
        var newSelectedElementId = '';

        if (lastElementIdPosition > 0) {
            newSelectedElementId = $(this).attr('data-pb-se').substring(0, lastElementIdPosition);
        }

        $('#cms_page_selected_element').val(newSelectedElementId);
        $('#cms_page_builder_form').submit();
    }
);

// on edit submit button event
$('#cms-page-builder-submit').click(
    function () {
        var lastElementIdPosition = $(this).attr('data-pb-se').lastIndexOf('-');
        var newSelectedElementId = '';

        if (lastElementIdPosition > 0) {
            newSelectedElementId = $(this).attr('data-pb-se').substring(0, lastElementIdPosition);
        }

        $('#cms_page_selected_action').val('submit');
        $('#cms_page_builder_form').submit();
    }
);

function deleteContentType(event, element) {
    if (event.stopPropagation) {
        event.stopPropagation();
    } else {
        event.cancelBubble = true;
    }

    $('#cms_page_delete_content_with_id').val($(element).parent().attr('data-pb-id'));
    $('#cms_page_delete_content_at_order_index').val($(element).parent().attr('data-pb-order'));
    $('#cms_page_selected_action').val('delete');
    $('#cms_page_builder_form').submit();
}


// Make the dashboard widgets sortable Using jquery UI
$('.cms-content-sortable').sortable({
    containment: $('.cms-content-sortable'),
    placeholder: 'sort-highlight',
    handle: '.sort-handle',
    forcePlaceholderSize: true,
    zIndex: 999999,
    stop: function (event, ui) {

        var container = $(this);

        var placeholder = container.find(".content-type-edit-placeholder");
        var elements = container.find(".content-type-edit-selector");

        var elementId = $('#cms_page_selected_element').val();
        var pageId = $('#cms_page_jstree_selected_page').val();


        var languageId = $('#cms_page_selected_language').val();

        var data = {"element": elementId, "page": pageId, "language": languageId, "order": []};

        if (elements.length == placeholder.length - 1) {
            elements.each(function (index) {
                var element = $(this);
                element.data("pb-order", index + 1);
                if (index == 0) {
                    placeholder.eq(0).data("pb-order", index + 1);
                    placeholder.eq(0).insertBefore(element);
                }
                data.order.push({"order": index + 1, "id": element.data("pb-id"), 'se': element.data("pb-se")})

                placeholder.eq(index + 1).data("pb-order", index + 2);
                placeholder.eq(index + 1).insertAfter(element);
            });
        }

        window.setTimeout(function () {
            $.ajax
            ({
                type: "POST",
                url: "/backend/cms/ajax/order",
                async: false,
                data: {"data": JSON.stringify(data)},
                success: function () {
                }
            })
        }, 500);


        placeholder.slideDown();

        window.setTimeout(function () {
            ui.item.data("click-false", false);
        }, 0);
    },
    start: function (event, ui) {
        ui.item.data("click-false", true);
        $(this).find(".content-type-edit-placeholder").slideUp();
    }
});

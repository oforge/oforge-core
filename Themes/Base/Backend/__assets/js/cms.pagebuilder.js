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

// drag 'n drop event listeners for jsTree foreign objects
$(document).bind("dnd_start.vakata", function(event, data) {
    console.log("jsTree - Start dnd");
    console.log("Data:");
    console.log(jsonStringify(data.data.jstree));
    console.log(jsonStringify(data.data.obj));
    console.log(jsonStringify(data.data.nodes));
    console.log("------------------");
})
.bind("dnd_stop.vakata", function(event, data) {
    console.log("jsTree - Stop dnd");
    console.log("Data:");
    console.log(jsonStringify(data.data.jstree));
    console.log(jsonStringify(data.data.obj));
    console.log(jsonStringify(data.data.nodes));
    console.log("this was a foreign operation: " + pbIsForeignDND);
    console.log("------------------");

    var pbDndContentTypeId = data.data;

    if (pbIsForeignDND && pbContentTypeParentForeignDND) {
        if (data && data.data && data.data.nodes &&  data.data.nodes.length > 0) {
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

    $(this).wrap( "<div id='jstree-drag-element'></div>" );
    var dragHelper = '<div id="jstree-dnd" class="jstree-default"><i class="jstree-icon jstree-er"></i>' + $('#jstree-drag-element').html() + '<ins class="jstree-copy" style="display:none;">+</ins></div>';
    $(this).unwrap();

    return $.vakata.dnd.start(
        event,
        {
            'jstree' : true,
            'obj' 	 : $(this),
            'nodes'  : [{
                'icon'		 : 'jstree-file',
                'text'		 : 'New Content Element',
                'data_ct_id' : $(this).attr('data-ct-id')
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

// bind quill richtext editor
if ($('#cms_page_builder_form').length && $('#cms_page_richtext_editor').length) {
    $('#cms_page_builder_form').submit(
        function() {
            $('#cms_page_richtext_text').val(quill.root.innerHTML);
        }
    );
    
    const quill = new Quill('#cms_page_richtext_editor', {
        theme: 'snow'
    });
}

// on edit cancel button event
$('#cms-page-builder-cancel').click(
    function() {
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
    function() {
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

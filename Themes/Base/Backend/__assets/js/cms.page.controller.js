// jsTree callback functions
$('#cms_page_controller_jstree').on('loaded.jstree', function (event, data) {
	$('#cms_page_controller_jstree').jstree('open_all');
});

$('#cms_page_controller_jstree').on('select_node.jstree', function (event, data) {
	// switch page on page select
	$('#cms_page_jstree_selected_page').val($('#cms_page_controller_jstree').jstree('get_selected'));
	$('#cms_page_selected_element').val('');
	$('#cms_page_builder_form').submit();
});

// called after creating the node in jstree. afterwards rename_node.jstree-callback
// will be called when user finished editing the node's name
$('#cms_page_controller_jstree').on('create_node.jstree', function (event, data) {
	var node = data.node;
	var parent = data.parent;
	var position = data.position;
	
	$('#cms_edit_page_parent_id').val(node.parent);
	$('#cms_edit_page_action').val('create');
});

// called after user finished editing the node's name
$('#cms_page_controller_jstree').on('rename_node.jstree', function (event, data) {
	var node = data.node;
	var text = data.text;
	var old = data.old;
	
	if ($('#cms_edit_page_action').val() != 'create') {
		$('#cms_edit_page_id').val(node.id);
		$('#cms_edit_page_action').val('rename');
	}
	
	$('#cms_edit_page_name').val(node.text);
	$('#cms_page_jstree_form').submit();
});

// called after deleting a jstree node
$('#cms_page_controller_jstree').on('delete_node.jstree', function (event, data) {
	var node = data.node;
	var parent = data.parent;
	
	$('#cms_edit_page_id').val(node.id);
	$('#cms_edit_page_action').val('delete');
	$('#cms_page_jstree_form').submit();
});

// mark and select selectable elements in page builder
$('[data-pb-id]').each(
	function() {
		var selectedElement = '^(' + $(this).attr('data-pb-se') + '\-)';
		var regularExpression = new RegExp(selectedElement);
		
		if (
			$(this).attr('data-pb-id') != $(this).attr('data-pb-se')
			&& $(this).attr('data-pb-id').startsWith($(this).attr('data-pb-se'))
			&& $(this).attr('data-pb-id').replace(regularExpression, '').indexOf('-') === -1
		) {
			// mark selectable elements in page builder on mouse hover
			$(this).hover(
				function() {
					$(this).addClass("cms-page-builder-selected-element");
				},
				function() {
					$(this).removeClass("cms-page-builder-selected-element");
				}		
			);
					
			// select element in page builder on mouse click
			$(this).click(
				function() {
					$('#cms_page_selected_element').val($(this).attr('data-pb-id'));
					$('#cms_page_builder_form').submit();
				}
			);
		}
	}
);

// on click create new root page
$('#cms-page-builder-create-new-root-page').click(
	function() {
	    var tree = $('#cms_page_controller_jstree').jstree(true);
	    var node = tree.get_node("#");
    	
	    node = tree.create_node(node, {"type":"folder"});
	    tree.edit(node);
	}
);

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

// adopt cms content builder containers to parents height on window resize event
function resizePageBuilder() {
	var calculatedHeight = window.innerHeight - $('#page_builder_container_wrapper').position().top - $('.main-footer').outerHeight(true) - $('.content').css('padding-top').replace('px', '') - $('.content').css('padding-bottom').replace('px', '');
	
	$('#page_builder_container_wrapper').height(calculatedHeight);
	
	$('#cms_page_jstree_container').height(calculatedHeight);
	$('#cms_page_builder_container').height(calculatedHeight);
	$('#cms_content_type_list_container').height(calculatedHeight);
	
	$('#cms_content_type_editor_wrapper').height(calculatedHeight - $('#cms_content_type_editor_wrapper').position().top);
}

// bind functions to window resize event
$(window).resize(function() {
    resizePageBuilder();
});

// bind functions to document load event
$(document).ready(function() {
	// create jstree configs
	var jsTreeCoreConfig   = cms_page_controller_jstree_config;
	var jsTreeCustomConfig = {
		"plugins" : ["contextmenu"],
		"contextmenu" : {
		    "select_node" : false,
		    "show_at_node" : true,
		    "items" : {
		        "createItem": {
		            "label": "Create",
		            "action": function (obj) {
		        	    var tree = $('#cms_page_controller_jstree').jstree(true);
		        	    var node = tree.get_node(obj.reference);
		            	
		        	    node = tree.create_node(node, {"type":"folder"});
		        	    tree.edit(node);
		            }
		        },
		        "renameItem": {
		            "label": "Rename",
		            "action": function (obj) {
		        	    var tree = $('#cms_page_controller_jstree').jstree(true);
		        	    var node = tree.get_node(obj.reference);
		        	    
		        	    tree.edit(node);
		            }
		        },
		        "deleteItem": {
		            "label": "Delete",
		            "action": function (obj) {
		        	    var tree = $('#cms_page_controller_jstree').jstree(true);
		        	    var node = tree.get_node(obj.reference);
		        	    
		        	    tree.delete_node(node);
		            }
		        }
		    }
		}
	};
	
	// merge jstree configs
	var jsTreeConfig = Object.assign(jsTreeCoreConfig, jsTreeCustomConfig);
	
	// create jstree object
    $('#cms_page_controller_jstree').jstree(jsTreeConfig);
    
    $('#cms_page_builder_language_selector').change(
    	function() {
    		$('#cms_page_selected_language').val($('#cms_page_builder_language_selector option:selected').val());
    		$('#cms_page_builder_form').submit();
    	}
    );
    
    // TODO: move to own function that is triggered after document loaded by RichText-PageBuilderForm.twig
    if ($('#cms_page_builder_form').length && $('#cms_page_richtext_editor').length) {
    	$('#cms_page_builder_form').submit(
	        function() {
	            $('#cms_page_richtext_text').val($('#cms_page_richtext_editor').val());
	        }
		);
		$('#cms_page_richtext_editor').wysihtml5();
    }
    
    resizePageBuilder();
});

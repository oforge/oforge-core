// jsTree callback functions
$('#cms_elements_controller_jstree').on('loaded.jstree', function (event, data) {
	$('#cms_elements_controller_jstree').jstree('open_all');
});

$('#cms_elements_controller_jstree').on('select_node.jstree', function (event, data) {
	// switch element on element select
	$('#cms_element_jstree_selected_element').val($('#cms_elements_controller_jstree').jstree('get_selected'));
	$('#cms_element_selected_element').val('');
	$('#cms_element_editor_form').submit();
});

// called after creating the node in jstree. afterwards rename_node.jstree-callback
// will be called when user finished editing the node's name
$('#cms_elements_controller_jstree').on('create_node.jstree', function (event, data) {
	var node = data.node;
	var parent = data.parent;
	var position = data.position;

	$('#cms_edit_element_parent_id').val(node.parent);
	$('#cms_edit_element_action').val('create');
});

// called after user finished editing the node's name
$('#cms_elements_controller_jstree').on('rename_node.jstree', function (event, data) {
	var node = data.node;
	var text = data.text;
	var old = data.old;
	
	if ($('#cms_edit_element_action').val() != 'create') {
		$('#cms_edit_element_id').val(node.id);
		$('#cms_edit_element_action').val('rename');
	}

	$('#cms_edit_element_description').val(node.text);
	$('#cms_element_jstree_form').submit();
});

// called after deleting a jstree node
$('#cms_elements_controller_jstree').on('delete_node.jstree', function (event, data) {
	var node = data.node;
	var parent = data.parent;

	$('#cms_edit_element_id').val(node.id);
	$('#cms_edit_element_action').val('delete');
	$('#cms_element_jstree_form').submit();
});

// on click create new root element
$('#cms-element-editor-create-new-root-element').click(
	function() {
	    var tree = $('#cms_elements_controller_jstree').jstree(true);
		var node = tree.get_node("#");

		if (node.id === "#" || node.id.startsWith("_parent")) {
			node = tree.create_node(node, {"type":"folder"});
			tree.edit(node);
		} else {
			alert("New folders can only be created as root folders or as sub-folders in user created folders!");
		}

	}
);

// adopt cms content editor containers to parents height on window resize event
function resizeContentEditor() {
	if (typeof $('#element_editor_container_wrapper') !== typeof undefined && typeof $('#element_editor_container_wrapper').position() !== typeof undefined) {
		var calculatedHeight = window.innerHeight - $('#element_editor_container_wrapper').position().top - $('.main-footer').outerHeight(true) - $('.content').css('padding-top').replace('px', '') - $('.content').css('padding-bottom').replace('px', '');
	
		$('#element_editor_container_wrapper').height(calculatedHeight);
		
		$('#cms_element_jstree_container').height(calculatedHeight);
		$('#cms_element_editor_container').height(calculatedHeight);
		$('#cms_content_type_list_container').height(calculatedHeight);
		
		if (typeof $('#cms_content_type_editor_wrapper') !== typeof undefined && typeof $('#cms_content_type_editor_wrapper').position() !== typeof undefined) {
			$('#cms_content_type_editor_wrapper').height(calculatedHeight - $('#cms_content_type_editor_wrapper').position().top);
		}
	}
}

// bind functions to window resize event
$(window).resize(function() {
    resizeContentEditor();
});

// bind functions to document load event
$(document).ready(function() {
	if (typeof cms_elements_controller_jstree_config !== typeof undefined && cms_elements_controller_jstree_config) {
		// create jstree configs
		var jsTreeCoreConfig   = cms_elements_controller_jstree_config;
		var jsTreeCustomConfig = {
			"plugins" : ["contextmenu"],
			"contextmenu" : {
				"select_node" : false,
				"show_at_node" : true,
				"items" : {
					"createItem": {
						"label": "Create",
						"action": function (obj) {
							var tree = $('#cms_elements_controller_jstree').jstree(true);
							var node = tree.get_node(obj.reference);

							if (node.id === "#" || node.id.startsWith("_parent")) {
								node = tree.create_node(node, {"type":"folder"});
								tree.edit(node);
							} else {
								alert("New folders can only be created as root folders or as sub-folders in user created folders!");
							}
						}
					},
					"renameItem": {
						"label": "Rename",
						"action": function (obj) {
							var tree = $('#cms_elements_controller_jstree').jstree(true);
							var node = tree.get_node(obj.reference);

							if (node.id.startsWith("_parent")) {
								tree.edit(node);
							} else {
								alert("Only user created folders can be renamed!");
							}
						}
					},
					"deleteItem": {
						"label": "Delete",
						"action": function (obj) {
							var tree = $('#cms_elements_controller_jstree').jstree(true);
							var node = tree.get_node(obj.reference);
							
							if (node.id.startsWith("_parent")) {
								tree.delete_node(node);
							} else {
								alert("Only user created folders can be deleted!");
							}
						}
					}
				}
			}
		};
		
		// merge jstree configs
		var jsTreeConfig = Object.assign(jsTreeCoreConfig, jsTreeCustomConfig);
	}

	// create jstree object
	$('#cms_elements_controller_jstree').jstree(jsTreeConfig);
	
    resizeContentEditor();
});

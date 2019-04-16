// CMS Pages Controller Module
// will be initialized on load of pages/index.twig
var cmsPagesControllerModule = (function() {
	// bind functions to window resize event
	$(window).resize(function() {
		cpcm.resizePageBuilder();
	});

	// jsTree callback functions
	$('#cms_pages_controller_jstree').on('loaded.jstree', function (event, data) {
		$('#cms_pages_controller_jstree').jstree('open_all');
	});

	$('#cms_pages_controller_jstree').on('select_node.jstree', function (event, data) {
		// switch page on page select
		$('#cms_page_jstree_selected_page').val($('#cms_pages_controller_jstree').jstree('get_selected'));
		$('#cms_page_selected_element').val('');
		$('#cms_page_builder_form').submit();
	});

	// called after creating the node in jstree. afterwards rename_node.jstree-callback
	// will be called when user finished editing the node's name
	$('#cms_pages_controller_jstree').on('create_node.jstree', function (event, data) {
		var node = data.node;
		var parent = data.parent;
		var position = data.position;
		
		$('#cms_edit_page_parent_id').val(node.parent);
		$('#cms_edit_page_action').val('create');
	});

	// called after user finished editing the node's name
	$('#cms_pages_controller_jstree').on('rename_node.jstree', function (event, data) {
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
	$('#cms_pages_controller_jstree').on('delete_node.jstree', function (event, data) {
		var node = data.node;
		var parent = data.parent;
		
		$('#cms_edit_page_id').val(node.id);
		$('#cms_edit_page_action').val('delete');
		$('#cms_page_jstree_form').submit();
	});

	// on click create new root page
	$('#cms-page-builder-create-new-root-page').click(
		function() {
			var tree = $('#cms_pages_controller_jstree').jstree(true);
			var node = tree.get_node("#");
			
			node = tree.create_node(node, {"type":"folder"});
			tree.edit(node);
		}
	);

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
				// add delete button to element
				$(this).append('<div class="content-type-delete-button" onclick="deleteContentType(event, this)"><img src="/Themes/Base/Backend/__assets/img/pagebuilder/delete.svg"></div>');

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
	            $('#cms_page_richtext_text').val(quill.root.innerHTML);
	        }
		);
    	
    	const quill = new Quill('#cms_page_richtext_editor', {
			theme: 'snow'
		});
	}

	if (typeof cms_pages_controller_jstree_config !== typeof undefined && cms_pages_controller_jstree_config) {
		// create jstree configs
		var jsTreeConfig = {
            "core" : {
                "multiple"       : false,
                "animation"      : 0,
                "check_callback" : true,
                "force_text"     : true,
                "themes"         : {"stripes" : false},
                "data"           : cms_pages_controller_jstree_config
			},
			"plugins" : ["contextmenu"],
			"contextmenu" : {
				"select_node" : false,
				"show_at_node" : true,
				"items" : {
					"createItem": {
						"label": "Create",
						"action": function (obj) {
							var tree = $('#cms_pages_controller_jstree').jstree(true);
							var node = tree.get_node(obj.reference);
							
							node = tree.create_node(node, {"type":"folder"});
							tree.edit(node);
						}
					},
					"renameItem": {
						"label": "Rename",
						"action": function (obj) {
							var tree = $('#cms_pages_controller_jstree').jstree(true);
							var node = tree.get_node(obj.reference);
							
							tree.edit(node);
						}
					},
					"deleteItem": {
						"label": "Delete",
						"action": function (obj) {
							var tree = $('#cms_pages_controller_jstree').jstree(true);
							var node = tree.get_node(obj.reference);
							
							tree.delete_node(node);
						}
					}
				}
			}
		};

		// create jstree object
		$('#cms_pages_controller_jstree').jstree(jsTreeConfig);
	}

	console.log("-= CMS Pages Controller Module has been initialized! =-");

	return {
		// adopt cms content builder containers to parents height on window resize event
		resizePageBuilder : function () {
			if (typeof $('#page_builder_container_wrapper') !== typeof undefined && typeof $('#page_builder_container_wrapper').position() !== typeof undefined) {
				var calculatedHeight = window.innerHeight - $('#page_builder_container_wrapper').position().top - $('.main-footer').outerHeight(true) - $('.content').css('padding-top').replace('px', '') - $('.content').css('padding-bottom').replace('px', '');
			
				$('#page_builder_container_wrapper').height(calculatedHeight);
				
				$('#cms_page_jstree_container').height(calculatedHeight);
				$('#cms_page_builder_container').height(calculatedHeight);
				$('#cms_content_type_list_container').height(calculatedHeight);
				
				if (typeof $('#cms_content_type_editor_wrapper') !== typeof undefined && typeof $('#cms_content_type_editor_wrapper').position() !== typeof undefined) {
					$('#cms_content_type_editor_wrapper').height(calculatedHeight - $('#cms_content_type_editor_wrapper').position().top);
				}
			}
		},
		// content type drag 'n drop functionality
		dragContentType : function (event) {
			console.log("Dragging content type id: " + $(event.target).attr('data-ct-id'));
			event.dataTransfer.setData('text', $(event.target).attr('data-ct-id'));
		},
		dragOverPlaceholder : function (event) {
			event.preventDefault();
			console.log("Dragged over placeholder with order: " + $(event.target).attr('data-pb-order'));
			$(event.target).addClass("content-type-edit-placeholder-drag-over");
		},
		dragLeavePlaceholder : function (event) {
			event.preventDefault();
			console.log("Drag leaving placeholder with order: " + $(event.target).attr('data-pb-order'));
			$(event.target).removeClass("content-type-edit-placeholder-drag-over");
		},
		dropOverPlaceholder : function (event) {
			event.preventDefault();
			console.log("Dropped on placeholder with order: " + $(event.target).attr('data-pb-order'));
			$(event.target).removeClass("content-type-edit-placeholder-drag-over");
			
			var data = event.dataTransfer.getData("text");
			
			$('#cms_page_create_content_with_type_id').val(data);
			$('#cms_page_create_content_at_order_index').val($(event.target).attr('data-pb-order'));
			$('#cms_page_selected_action').val('create');
			$('#cms_page_builder_form').submit();
		},
		deleteContentType : function (event, element) {
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
	}
});

// check if the Oforge namespace exists
if (typeof Oforge !== 'undefined') {
    // if it exists, it should have the register function, so register your module
    // the properties "name", "selector" and "init" are required
    // name: the name of your module
    // selector: the html selector to search for. If it is found, the module can be initialized
    // init: the function to initialize the module. This function gets called automatically from the module-loader.js
    // when the DOMContentLoaded event is triggered.
    Oforge.register({
        name: 'cmsPagesControllerModule',
        selector: '#page_builder_container_wrapper',
        init: function () {
			window.cpcm = cmsPagesControllerModule();
			cpcm.resizePageBuilder();
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}

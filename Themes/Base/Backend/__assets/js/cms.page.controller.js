// jsTree callback function
$('#cms_page_controller_jstree').on('changed.jstree', function (e, data) {
	// switch page on page select
	$('#cms_page_jstree_selected_page').val($('#cms_page_controller_jstree').jstree('get_selected'));
	$('#cms_page_selected_element').val('');
	$('#cms_page_builder_form').submit();
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

//on edit cancel button event
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

//on edit submit button event
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

//adopt cms content builder containers to parents height on window resize event
function resizePageBuilder() {
	var calculatedHeight = $('.main-footer').position().top - $('#page_builder_container_wrapper').position().top;
	
	$('#cms_page_jstree_container').height(calculatedHeight);
	$('#page_builder_container').height(calculatedHeight);
	$('#cms_content_type_list_container').height(calculatedHeight);
}

// bind functions to window resize event
$(window).resize(function() {
    resizePageBuilder();
});

// bind functions to document load event
$(document).ready(function() {
    $('#cms_page_controller_jstree').jstree(cms_page_controller_jstree_config);
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

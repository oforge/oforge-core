$('#cms_page_controller_jstree').on('changed.jstree', function (e, data) {
	/*
	console.log(data.selected);
	console.log("Selected Id:"+$('#cms_page_controller_jstree').jstree('get_selected'));
	console.log("Selected Page:"+$('#cms_page_controller_jstree').jstree('get_selected', true)[0].text);
	*/

	$('#cms_page_jstree_selected_page').val($('#cms_page_controller_jstree').jstree('get_selected'));
	$('#cms_page_jstree_form').submit();
});

function resizePageBuilder() {
	var calculatedHeight = $('.main-footer').position().top - $('#page_builder_container_wrapper').position().top;
	
	$('#cms_page_jstree_container').height(calculatedHeight);
	$('#page_builder_container').height(calculatedHeight);
	$('#cms_content_type_list_container').height(calculatedHeight);
}

$(window).resize(function() {
    resizePageBuilder();
});

$(document).ready(function() {
    $('#cms_page_controller_jstree').jstree(cms_page_controller_jstree_config);
    $('#cms_page_builder_language_selector').change(
    	function() {
    		$('#cms_page_selected_language').val($('#cms_page_builder_language_selector option:selected').val());
    		$('#cms_page_jstree_form').submit();
    	}
    );
    
    resizePageBuilder();
});

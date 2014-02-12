jQuery(function($){
	var iframe_div = $('div.xpress_xeditor_editing_area_container');
	if(iframe_div.length>0){
		iframe_div.height('400px');
		iframe_div.children().height('400px');
	}
});
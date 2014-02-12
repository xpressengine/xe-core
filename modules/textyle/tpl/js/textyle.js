function completeInsertTextyle(ret_obj, response_tags) {
	alert(ret_obj['message']);
	location.href=current_url.setQuery('act','dispTextyleAdminList');
}

function completeInsertGrant(ret_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var page = ret_obj['page'];
	var module_srl = ret_obj['module_srl'];
	alert(message);
}

function completeInsertConfig(ret_obj, response_tags) {
	alert(ret_obj['message']);
	location.reload();
}

function completeDeleteTextyle(ret_obj) {
	alert(ret_obj['message']);
	location.href=current_url.setQuery('act','dispTextyleAdminList').setQuery('module_srl','');
}

function completeInsertBlogApiService(ret_obj, response_tags) {
	alert(ret_obj['message']);
	location.href=current_url.setQuery('act','dispTextyleAdminBlogApiConfig').setQuery('textyle_blogapi_services_srl','');
}

function deleteBlogApiService(srl) {
    var params = new Array();
    params['textyle_blogapi_services_srl'] = srl;
    exec_xml('textyle', 'procTextyleAdminDeleteBlogApiServices', params, completeReload);
}



function toggleAccessType(target) {
	switch(target) {
		case 'domain' :
				xGetElementById('textyleFo').domain.value = '';
				xGetElementById('accessDomain').style.display = 'block';
				xGetElementById('accessVid').style.display = 'none';
			break;
		case 'vid' :
				xGetElementById('textyleFo').vid.value = '';
				xGetElementById('accessDomain').style.display = 'none';
				xGetElementById('accessVid').style.display = 'block';
			break;
	}
}

function completeReload() {
    location.reload();
}

function doApplySubChecked(obj, id) {
    jQuery('div.menu_box_'+id).find('input[type=checkbox]').each(function() { this.checked = obj.checked; });

}


function exportTextyle(site_srl,export_type){
    var params = new Array();
    params['site_srl'] = site_srl;
    params['export_type'] = export_type;
    exec_xml('textyle', 'procTextyleAdminExport', params, completeReload);
}

function deleteExportTextyle(site_srl){
    var params = new Array();
    params['site_srl'] = site_srl;
    exec_xml('textyle', 'procTextyleAdminDeleteExportTextyle', params, completeReload);
}

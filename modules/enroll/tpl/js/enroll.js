function completeInsertEnrollItem(ret_obj) {
    alert(ret_obj['message']);

    var url = request_uri.setQuery('mid', current_mid).setQuery('enroll_srl', ret_obj['enroll_srl']).setQuery('act','dispEnrollInsertComplete');
    if(typeof(xeVid)!='undefined') url = url.setQuery('vid', xeVid);

    location.href = url;
}


function deleteEnrollItem(enroll_srl){
    var params = new Array();
    params['enroll_srl'] = enroll_srl;
    params['mid'] = current_mid;
	
	var response_tags = new Array('error','message');
    exec_xml('enroll', 'procEnrollAdminItemDelete', params, completeReload, response_tags);
}

function updateItemStatus(obj,enroll_srl,module_srl) {
    var params = new Array();
    params['module_srl'] = module_srl;
    params['enroll_srl'] = enroll_srl;
    params['status'] = jQuery(obj).parent().children('select[name=status]').val();
	var response_tags = new Array('error','message');
    exec_xml('enroll', 'procEnrollItemUpdateStatus', params, completeReload, response_tags);
}

function completeReload(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    location.reload();
}

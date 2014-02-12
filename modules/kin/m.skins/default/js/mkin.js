function completeWriteDocument(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var document_srl = ret_obj['document_srl'];
    var url;
    if(!document_srl)
    {
        url = current_url.setQuery('act','');
    }
    else
    {
        url = current_url.setQuery('document_srl',document_srl).setQuery('act','');
    }
    location.href = url;
}

function completeWriteReply(ret_obj) {
    alert(ret_obj['message']);

    var url = request_uri.setQuery('mid', current_mid).setQuery('document_srl', ret_obj['document_srl']).setQuery('act','dispKinView');
    if(typeof(xeVid)!='undefined') url = url.setQuery('vid', xeVid);
    location.href = url;
}

function doDeleteDocument(document_srl) {
    var params = new Array();
    params['document_srl'] = document_srl; 
    var url = request_uri.setQuery('mid', current_mid).setQuery('document_srl', '').setQuery('act','');
    if(typeof(xeVid)!='undefined') url = url.setQuery('vid', xeVid);
    exec_xml('kin','procKinDeleteDocument', params, function() { location.href=url; });
}

function loadPage(document_srl, page) {
	var params = {};
	params["cpage"] = page; 
	params["document_srl"] = document_srl;
	params["mid"] = current_mid;
	exec_xml("board", "getKinCommentPage", params, completeGetPage, ['html','error','message'], params);
}

function completeGetPage(ret_val) {
	jQuery("#cl").remove();
	jQuery("#clpn").remove();
	jQuery("#clb").after(ret_val['html']);
}

function doDeleteReply(comment_srl) {
    var params = new Array();
    params['comment_srl'] = comment_srl; 
    exec_xml('kin','procKinDeleteReply', params, function() { location.reload(); });
}

function doSelectReply(comment_srl) {
    var params = new Array();
    params['comment_srl'] = comment_srl; 
    exec_xml('kin','procKinSelectReply', params, function() { location.reload(); });
}

function doGetComments(parent_srl, page) {
    var o = jQuery('#replies_'+parent_srl);
    var o = jQuery('#replies_content_'+parent_srl);
    if(o.css('display')=='block' && typeof(page)=='undefined') o.css('display','none');
    else {
        var params = new Array();
        params['mid'] = current_mid;
        params['parent_srl'] = parent_srl;
        if(typeof(page)=='undefined') page = 1;
        params['page'] = page;
        exec_xml('kin','getKinComments', params, displayComments, new Array('error','message','parent_srl','html'));
    }
}

function displayComments(ret_obj) {
    var parent_srl = ret_obj['parent_srl'];
    var html = ret_obj['html'];
    var o = jQuery('#replies_'+parent_srl);
    var o = jQuery('#replies_content_'+parent_srl);
    o.html(html).css('display','block');
}

function doDeleteComment(parent_srl,reply_srl) {
    var params = new Array();
    params['parent_srl'] = parent_srl; 
    params['reply_srl'] = reply_srl; 
    params['mid'] = current_mid;
    exec_xml('kin','procKinDeleteComment', params, displayComments,  new Array('error','message','parent_srl','html'));
}

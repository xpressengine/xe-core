function completeInsertComment(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var mid = ret_obj['mid'];
    var document_srl = ret_obj['document_srl'];
    var comment_srl = ret_obj['comment_srl'];

    var url = current_url.setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','');
    if(comment_srl) url = url.setQuery('rnd',comment_srl)+"#comment_"+comment_srl;

    //alert(message);
    location.href = url;
}

function completeDocumentInserted(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var mid = ret_obj['mid'];
    var document_srl = ret_obj['document_srl'];
    var category_srl = ret_obj['category_srl'];

    //alert(message);

    var url;
    if(!document_srl)
    {
        url = current_url.setQuery('mid',mid).setQuery('act','');
    }
    else
    {
        url = current_url.setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','');
    }
    if(category_srl) url = url.setQuery('category',category_srl);
    location.href = url;
}

function completeGetPage(ret_val) {
	jQuery("#cl").remove();
	jQuery("#clpn").remove();
	jQuery("#clb").after(ret_val['html']);
}

function loadPage(document_srl, page) {
	var params = {};
	params["cpage"] = page; 
	params["document_srl"] = document_srl 
	exec_xml("textyle", "getTextyleCommentPage", params, completeGetPage, ['html','error','message'], params);
}



function insertGuestbookItem(obj,filter){
	jQuery(':text,:password',obj).each(function(){
		var jthis = jQuery(this);
		if(jthis.attr('title') && jthis.val() == jthis.attr('title')) jthis.val('');
	});
	var email = jQuery('[name=email_address].request',obj);
	if(email.length>0 && !jQuery.trim(email.val())){
		alert(jQuery('[name=msg_input_email_address]',obj).val());
		email.eq(0).focus();
		return false;
	}
	var homepage = jQuery('[name=homepage].request',obj);
	if(homepage.length>0 && !jQuery.trim(homepage.val())){
		alert(jQuery('[name=msg_input_homepage]',obj).val());
		homepage.eq(0).focus();
		return false;
	}

	return procFilter(obj,filter);
}

function completeInsertGuestbook(ret_obj){
    var page = ret_obj.page;

	location.href=current_url.setQuery('act','dispTextyleGuestbook').setQuery('mid',current_mid).setQuery('page',page).setQuery('reply','').setQuery('modify','');
}

function insertCommentItem(obj,filter){
	jQuery(':text,:password',obj).each(function(){
		var jthis = jQuery(this);
		if(jthis.attr('title') && jthis.val() == jthis.attr('title')) jthis.val('');
	});
	var email = jQuery('[name=email_address].request',obj);
	if(email.length>0 && !jQuery.trim(email.val())){
		alert(jQuery('[name=msg_input_email_address]',obj).val());
		email.eq(0).focus();
		return false;
	}
	var homepage = jQuery('[name=homepage].request',obj);
	if(homepage.length>0 && !jQuery.trim(homepage.val())){
		alert(jQuery('[name=msg_input_homepage]',obj).val());
		homepage.eq(0).focus();
		return false;
	}

	return procFilter(obj,filter);
}

function deleteGuestbookItem(srl){
    var params = new Array();
    params['textyle_guestbook_srl'] = srl;
	params['mid'] = current_mid;

	var response_tags = new Array('error','message','page','mid');
    exec_xml('textyle', 'procTextyleGuestbookItemDelete', params, function(){
		location.reload();	
	}, response_tags);

	return false;
}

function deleteCommentItem(srl){
    var params = new Array();
    params['comment_srl'] = srl;
	params['mid'] = current_mid;

	var response_tags = new Array('error','message','document_srl');
    exec_xml('textyle', 'procTextyleCommentItemDelete', params, function(){
		location.reload();	
	}, response_tags);

	return false;
}


function checkPasswordForDeleteGuestbookItem(f){
    var params = new Array();
    params['textyle_guestbook_srl'] = f.textyle_guestbook_srl.value;
	params['mid'] = current_mid;

	var response_tags = new Array('error','message','page','mid');
    exec_xml('textyle', 'procTextyleGuestbookItemDelete', params, function(){
		if(f.callback_url.value) location.href = f.callback_url.value;
	}, response_tags);

	return false;
}


function checkPasswordForDeleteComment(f){
    var params = new Array();
    params['comment_srl'] = f.comment_srl.value;
    params['document_srl'] = f.document_srl.value;
    params['mid'] = current_mid;
	
	var response_tags = new Array('error','message');
    exec_xml('textyle', 'procTextyleDeleteComment', params, function(){
		if(f.callback_url.value) location.href = f.callback_url.value;
	}, response_tags);

	return false;
}
function checkPasswordForModifyComment(f){
	procFilter(f,input_password_for_modify_comment);
	return false;
}
function completeCheckPasswordForModifyComment(ret_obj,a,b,f){
	var url = current_url.setQuery('comment_srl',f.comment_srl.value).setQuery('document_srl',f.document_srl.value).setQuery('act','dispTextyleCommentModify');
	location.href = url;
}

function completeInsertMpost(ret_obj){
	var url = current_url.setQuery('act','');
	location.href = url;
}

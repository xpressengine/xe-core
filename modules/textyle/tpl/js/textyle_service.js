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

function checkPasswordGuestbook(f){
	procFilter(f,input_password_for_guestbook);
	return false;
}
function checkPasswordForDeleteGuestbook(f){
	procFilter(f,input_password_for_delete_guestbook);
	return false;
}
function checkPasswordForModifyGuestbook(f){
	procFilter(f,input_password_for_modify_guestbook);
	return false;
}
function completeCheckPasswordForModifyGuestbook(ret_obj,a,b,f){
	location.href=current_url.setQuery('modify',f.textyle_guestbook_srl.value);
}
function completeCheckPasswordForDeleteGuestbook(ret_obj,a,b,f){
	var params = new Array();
    params['textyle_guestbook_srl'] = f.textyle_guestbook_srl.value;
    params['mid'] = current_mid;
	
	var response_tags = new Array('error','message');
    exec_xml('textyle', 'procTextyleGuestbookItemDelete', params, completeReload, response_tags);
}



function deleteGuestbookItem(textyle_guestbook_srl,page){
    var params = new Array();
    params['textyle_guestbook_srl'] = textyle_guestbook_srl;
	
	var response_tags = new Array('error','message','page','mid');
    exec_xml('textyle', 'procTextyleGuestbookItemDelete', params, completeReload, response_tags);
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
function checkPasswordForModifyComment(f){
	procFilter(f,input_password_for_modify_comment);
	return false;
}

function completeCheckPasswordForModifyComment(ret_obj,a,b,f){
	var url = current_url.setQuery('comment_srl',f.comment_srl.value).setQuery('document_srl',f.document_srl.value).setQuery('act','dispTextyleCommentModify');
	location.href = url;
}

function checkPasswordForDeleteComment(f){
    var params = new Array();
    params['comment_srl'] = f.comment_srl.value;
    params['document_srl'] = f.document_srl.value;
    params['password'] = f.password.value;
    params['mid'] = current_mid;
	
	var response_tags = new Array('error','message');
    exec_xml('textyle', 'procTextyleDeleteComment', params, completeReload, response_tags);
	return false;
}

function deleteCommentItem(comment_srl) {
    var params = new Array();
    params['comment_srl'] = comment_srl;
    params['mid'] = current_mid;
	
	var response_tags = new Array('error','message');
    exec_xml('textyle', 'procTextyleDeleteComment', params, completeReload, response_tags);
}

function completeDeleteTrackback(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var mid = ret_obj['mid'];
    var document_srl = ret_obj['document_srl'];
    var page = ret_obj['page'];

    var url = current_url.setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','');
    if(page) url = url.setQuery('page',page);

    //alert(message);

    location.href = url;
}


function completeReload(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    location.reload();
}

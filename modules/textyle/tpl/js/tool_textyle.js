function unique(t) {
	var a = [];
	var l = t.length;
	for(var i=0; i<l; i++) {
		for(var j=i+1; j<l; j++) {
			if (t[i] === t[j])
				j = ++i;
		}
		a.push(t[i]);
	}
	return a;
}

function appendTag(tag){
	var area = jQuery('textarea[name=tags]');
	if(area.attr('title') && area.val() == area.attr('title')) area.val('');

	var val = area.val();
	var tags = val ? val.split(',') : [];
	
	tags.push(tag);
	jQuery.each(tags,function(i){
		tags[i] = tags[i].replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	});

	tags = unique(tags);
	area.val(tags.join(', '));
}

function publishPost(obj,filter){
	jQuery('input,textarea',obj).each(function(){
		var j = jQuery(this);
		if(j.val() && j.val() == j.attr('title')) j.val('');
	});
    return procFilter(obj,filter);
}

function deleteTrackbackItem(srl,page){
	var params = new Array();
	params['trackback_srl'] = srl;
	params['page'] = page;
	_deleteTrackbackItem(params);
}
function deleteTrackbackItems(page){
	var val,srls = [];
	jQuery("input[name=trackback_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['trackback_srl'] = srls.join(',');
	params['page'] = page;
	_deleteTrackbackItem(params);
}

function _deleteTrackbackItem(params){
	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextyleTrackbackItemDelete', params, completeReload, response_tags);
}

function insertCommentItem(obj,filter){
	jQuery(':text,:password',obj).each(function(){
		var jthis = jQuery(this);
		if(jthis.attr('title') && jthis.val() == jthis.attr('title')) jthis.val('');
	});

	return procFilter(obj,filter);
}

function completeInsertComment(ret_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var mid = ret_obj['mid'];
	var document_srl = ret_obj['document_srl'];
	var comment_srl = ret_obj['comment_srl'];

	var url = current_url.setQuery('mid',mid).setQuery('act','dispTextyleToolCommunicationComment');
	location.href = url;
}


function deleteCommentItem(srl,page){
	var params = new Array();
	params['comment_srl'] = srl;
	params['page'] = page;
	_deleteCommentItem(params);
}
function deleteCommentItems(page){
	var val,srls = [];
	jQuery("input[name=comment_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['comment_srl'] = srls.join(',');
	params['page'] = page;
	_deleteCommentItem(params);
}

function _deleteCommentItem(params){
	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextyleCommentItemDelete', params, completeReload, response_tags);
}

function deleteNotifyItems(page){
	var val,srls = [],srls2 = [];
	jQuery("input[name=notified_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	jQuery("input[name=child_notified_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls2.push(val);
	});

	if(srls.length<1 && srls2.length<1) return;
	var params = new Array();
	params['notified_srl'] = srls.join(',');
    params['child_notified_srl'] = srls2.join(',');
	params['page'] = page;
	_deleteNotifyItem(params);
}

function _deleteNotifyItem(params){
	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextyleNotifyItemDelete', params, completeReload, response_tags);
}

function updateCommentItemSetSecret(srl,is_secret,page,module_srl){
	var params = new Array();
	params['comment_srl'] = srl;
	params['page'] = page;
	params['is_secret'] = is_secret;
	params['module_srl'] = module_srl;

	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextyleCommentItemSetSecret', params, completeReload, response_tags);
}

function updateGuestbookItemChangeSecret(srl,page){
	var params = new Array();
	params['textyle_guestbook_srl'] = srl;
	params['page'] = page;
	_updateGuestbookItemChangeSecret(params);
}


function updateGuestbookItemsChangeSecret(page){
	var val,srls = [];
	jQuery("input[name=textyle_guestbook_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['textyle_guestbook_srl'] = srls.join(',');
	params['page'] = page;
	_updateGuestbookItemChangeSecret(params);
}

function _updateGuestbookItemChangeSecret(params){
	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextyleGuestbookItemsChangeSecret', params, completeGuestbookItemDelete, response_tags);
}
function completeInsertGuestbook(ret_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var page = ret_obj['page'];
	var mid = ret_obj['mid'];

	location.reload();
}
function completeInsertGuestbookReply(ret_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var page = ret_obj['page'];
	var mid = ret_obj['mid'];
	location.href=current_url.setQuery('act','dispTextyleToolCommunicationGuestbook').setQuery('mid',current_mid);
}

function deleteGuestbookItem(srl,page){
	var params = new Array();
	params['textyle_guestbook_srl'] = srl;
	params['page'] = page;
	_deleteGuestbookItem(params);
}
function deleteGuestbookItems(page){
	var val,srls = [];
	jQuery("input[name=textyle_guestbook_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['textyle_guestbook_srl'] = srls.join(',');
	params['page'] = page;
	_deleteGuestbookItem(params);
}

function _deleteGuestbookItem(params){
	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextyleGuestbookItemsDelete', params, completeGuestbookItemDelete, response_tags);

}

function completeGuestbookItemDelete(ret_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var page = ret_obj['page'];
	var mid = ret_obj['mid'];
	location.reload();
}

function completeInsertCategory(){
	jQuery('#category_info').html("");
	Tree(xml_url);
}

function completeInsertDeny(ret_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	location.reload();
}
function completeInsertConfigCommunication(ret_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	location.reload();
}
function deleteDenyItem(mid,srl){
	var params = new Array();
	params['textyle_deny_srl'] = srl;
	params['mid'] = mid;
	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextyleDenyDelete', params, completeInsertDeny, response_tags);
}

function completeModifyPassword(ret_obj, response_tags, args, fo_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	alert(message);
	location.reload();
}

function completeInsertConfig(ret_obj, response_tags, args, fo_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var mid = ret_obj['mid'];

	location.reload();

}


function addCategory(){
	var category_title= jQuery('[name=add_category]').val();
	var parent_srl = jQuery('#category').val();
	if(!category_title) return;
	var response_tags = new Array('error','message','xml_file','category_srl');
	exec_xml('document','procDocumentInsertCategory',{'mid':current_mid,'title':category_title,'parent_srl':parent_srl},completeAddCategory,response_tags);
}

function completeAddCategory(ret_obj, response_tags, args, fo_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var xml_file = ret_obj['xml_file'];
	var category_srl = ret_obj['category_srl'];

	var sel = jQuery('#category').get(0);	
	var n = sel.options[0].text[sel.options[0].text.length-1];
	n+=n;
	for(i=0,c=sel.length;i<c;i++) sel.options[1] = null;

	jQuery.get(xml_file,function(data){
		var c = '';
			jQuery(data).find("node").each(function(j){
				var node_srl = jQuery(this).attr("node_srl");
				var document_count = jQuery(this).attr("document_count");
				var text = jQuery(this).attr("text") +'('+document_count+')';

				for(i=0,c=jQuery(this).parents('node').size();i<c;i++) text = n +text;
				sel.options[sel.options.length] = new Option(text,node_srl, false,false);
				if(node_srl == category_srl) sel.selectedIndex = j;
			});
	});
	jQuery('[name=add_category]').val('');
	jQuery('#add_category').removeClass('open');
}


function savePost(obj){
	jQuery('input[name=publish]',obj).val('N');
	if(editorRelKeys[1]) editorRelKeys[1].content.value = editorRelKeys[1].func();
	jQuery('input,textarea',obj).each(function(){
		var j = jQuery(this);
		if(j.val() && j.val() == j.attr('title')) j.val('');
	});
	return procFilter(obj,save_post);

}

function savePostPublish(obj){
	jQuery('input[name=publish]',obj.form).val('Y');
	jQuery('input,textarea',obj).each(function(){
		var j = jQuery(this);
		if(j.val() && j.val() == j.attr('title')) j.val('');
	});
	window.onbeforeunload = function(){};
	return procFilter(obj,save_post);
}

function completePostsave(ret_obj, response_tags, args, fo_obj) {
	fo_obj.document_srl.value = ret_obj.document_srl;
	alert(ret_obj.message);
}

function completePostwrite(ret_obj, response_tags, args, fo_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var page = ret_obj['page'];
	var mid = ret_obj['mid'];

	var url = current_url.setQuery('act','dispTextyleToolPostManageList');
	document.location.href = url;
}

function checkAlias(value,alias){
	if(value && value == alias) return;

	var response_tags = new Array('error','message','document_srl');
	exec_xml('textyle','dispTextylePostCheckAlias',{'mid':current_mid,'alias':value},completeCheckAlias,response_tags);
}
function completeCheckAlias(ret_obj,response_tags,args,fo_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var document_srl = ret_obj['document_srl'];

	if(document_srl){
		jQuery('em.msg_used_alias').show();
		jQuery('input[name=use_alias]').val('N');
	}else{
		jQuery('em.msg_used_alias').hide();
		jQuery('input[name=use_alias]').val('Y');
	}
}




function restorePostItem(srl,page){
	var params = new Array();
	params['document_srl'] = srl;
	params['mid'] = current_mid;

	_restorePostItem(params);
}
function restorePostItems(page){
	var val,srls = [];
	jQuery("input[name=document_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['document_srl'] = srls.join(',');
	params['mid'] = current_mid;
	params['page'] = page;
	_restorePostItem(params);
}

function _restorePostItem(params){
	var response_tags = new Array('error','message','page','mid','vid');
	exec_xml('textyle', 'procTextylePostTrashRestore', params, completeReload, response_tags);

}
function confirmDeletePostItem(srl,page,msg){
	if(confirm(msg)) deletePostItem(srl,page);
}
function deletePostItem(srl,page){
	var params = new Array();
	params['document_srl'] = srl;
	params['page'] = page;
	_deletePostItem(params);
}
function deletePostItems(page){
	var val,srls = [];
	jQuery("input[name=document_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['document_srl'] = srls.join(',');
	params['page'] = page;
	_deletePostItem(params);
}

function _deletePostItem(params){
	 var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextylePostDelete', params, completeGuestbookItemDelete, response_tags);

}

function trashPostItem(srl, page){
	var params = new Array();
	params['document_srl'] = srl;
	params['page'] = page;
	_trashPostItem(params);
}

function trashPostItems(page){
	if(!confirm(xe.lang.msg_confirm_delete_post)) return false;

	var val, srls = [];
	jQuery("input[name=document_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['document_srl'] = srls.join(',');
	params['page'] = page;
	_trashPostItem(params);
}

function _trashPostItem(params){
	 var response_tags = new Array('error','message','page','mid');
	 exec_xml('textyle', 'procTextylePostTrash', params, completeReload, response_tags);
}

function updatePostItemsSecret(set_secret,page){
	var val,srls = [];
	jQuery("input[name=document_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['document_srl'] = srls.join(',');
	params['page'] = page;
	params['set_secret'] = set_secret;

	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextylePostItemsSetSecret', params, completeGuestbookItemDelete, response_tags);
}


function updatePostItemsCategory(vid,category_srl,page){
	var val,srls = [];
	jQuery("input[name=document_srl]:checked").each(function(){
		val = jQuery(this).val();
		if(val) srls.push(val);
	});
	if(srls.length<1) return;
	var params = new Array();
	params['document_srl'] = srls.join(',');
	params['mid'] = current_mid;
	params['vid'] = vid;
	params['page'] = page;
	params['category_srl'] = category_srl;

	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextylePostItemsCategoryMove', params, completeGuestbookItemDelete, response_tags);
}

function deleteTag(obj){
	var selected_tag = jQuery("input[name=selected_tag]",obj.form).val();

	var params = new Array();
	params['mid'] = current_mid;
	params['selected_tag'] = selected_tag;

	var response_tags = new Array('error','message','page','mid');
	exec_xml('textyle', 'procTextyleTagDelete', params, completeDeleteTag, response_tags);
}

function completeDeleteTag(ret_obj, response_tags, args, fo_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var mid = ret_obj['mid'];
	document.location.href=current_url.setQuery('selected_tag','');
}

function updateTag(obj){
	var tag = jQuery("input[name=tag]",obj.form).val();
	var selected_tag = jQuery("input[name=selected_tag]",obj.form).val();

	if(tag == '' || tag == selected_tag) return false;
	var params = new Array();
	params['mid'] = current_mid;
	params['tag'] = tag;
	params['selected_tag'] = selected_tag;

	var response_tags = new Array('error','message','page','mid','selected_tag');
	exec_xml('textyle', 'procTextyleTagUpdate', params, completeUpdateTag, response_tags);
}

function completeUpdateTag(ret_obj, response_tags, args, fo_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var mid = ret_obj['mid'];
	var tag = ret_obj['selected_tag'];
	document.location.href=current_url.setQuery('selected_tag',tag);
}

function completeReload(ret_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	location.href = location.href;
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

function completeInsertProfile(ret_obj) {
	var fo = jQuery('#foProfile');
	var photo = jQuery('#photo');
	var src = photo.get(0).value;
	if(!photo.get(0).value || !/\.(jpg|jpeg|gif|png)$/i.test(src)) {
		location.reload();
		return;
	}
	fo.append('<input type="hidden" name="act" value="procTextyleProfileImageUpload" />');
	fo.get(0).submit();
}

function deleteMaterialItem(material_srl){
	var params = new Array();
	params['mid'] = current_mid;
    if(material_srl) {
        params['material_srl'] = material_srl;
    } else {
        var selectedEl = jQuery('#content .subjectList input:checked.materialCart');
        var material_srls = [];
        selectedEl.each(function() {
            material_srls.push(this.value);
        });
        params['material_srl'] = material_srls.join(',');
    }

	var response_tags = new Array('error','message','page','mid');
	exec_xml('material', 'procMaterialDelete', params, completeReload, response_tags);
}

function deleteAllMaterials(module_srl) {
    var params = {};
    params.module_srl = module_srl;

    jQuery.exec_json('material.procMaterialsDelete', params, function(ret) {
        alert(ret.message);
        reloadDocument();
    });
}

function getEditorSkinColorList(skin_name,selected_colorset,type){
	if(skin_name.length>0){
		type = type || 'comment';
		var response_tags = new Array('error','message','colorset');
		exec_xml('editor','dispEditorSkinColorset',{skin:skin_name},resultGetEditorSkinColorList,response_tags,{'selected_colorset':selected_colorset,'type':type});
	}
}

function resultGetEditorSkinColorList(ret_obj,response_tags, params) {

	var selectbox = null;
	if(params.type == 'comment'){
		selectbox = xGetElementById("sel_editor_comment_colorset");
	}else{
		selectbox = xGetElementById("sel_editor_guestbook_colorset");
	}

	if(ret_obj['error'] == 0 && ret_obj.colorset){
		var it = new Array();
		var items = ret_obj['colorset']['item'];
		if(typeof(items[0]) == 'undefined'){
			it[0] = items;
		}else{
			it = items;
		}
		var sel = 0;
		for(var i=0,c=it.length;i<c;i++){
			selectbox.options[i]=new Option(it[i].title,it[i].name);
			if(params.selected_colorset && params.selected_colorset == it[i].name) sel = i;
		}
		selectbox.options[sel].selected = true;
		selectbox.style.display="";
	}else{
		selectbox.style.display="none";
		selectbox.innerHTML="";
	}
}

function moveDate() {
	location.href = current_url.setQuery('selected_date',jQuery('#str_selected_date').text().replace(/\./g,''));
}

function doSelectSkin(skin) {
	var params = new Array();
	var response_tags = new Array('error','message');
	params['skin'] = skin;
	params['mid'] = current_mid;
	exec_xml('textyle', 'procTextyleToolLayoutConfigSkin', params, completeReload, response_tags);
}

function doSelectMobileSkin(mskin) {
	var params = new Array();
	var response_tags = new Array('error','message');
	params['mskin'] = mskin;
	params['mid'] = current_mid;
	exec_xml('textyle', 'procTextyleToolLayoutConfigMobileSkin', params, completeReload, response_tags);
}

function doResetLayoutConfig() {
	var params = new Array();
	var response_tags = new Array('error','message');
	params['mid'] = current_mid;
	params['vid'] = xeVid;
	exec_xml('textyle', 'procTextyleToolLayoutResetConfigSkin', params, completeReload, response_tags);
}

function completeUpdateAllow(ret_obj) {
	jQuery('.layerCommunicationConfig').removeClass('open');
	location.href=location.href;
}

function openLayerCommuicationConfig(){
	jQuery('input[name=document_srl]','.layerCommunicationConfig').val('');
	var v,srls = [];
	jQuery("input[name=document_srl]:checked").each(function(){
		v = jQuery(this).val();
		if(v) srls.push(v);
	});
	if(srls.length<1) return;
	jQuery('input[name=document_srl]','.layerCommunicationConfig').val(srls.join(','));
	jQuery('.layerCommunicationConfig').addClass('open');
}


function hideLayerCommuicationConfig(){
	jQuery('.layerCommunicationConfig').removeClass('open');
	jQuery('input[name=document_srl]','.layerCommunicationConfig').val('');
}

function openLayerAddDenyItem(srl,f){
	jQuery("input.boxlist").each(function(){
		if(jQuery(this).val() == srl) this.checked=true;
		else this.checked=false; 
	});
	openLayerAddDeny(f);
}

function openLayerAddDeny(f){
	jQuery('input[type=text]','.layerAddDeny fieldset').val('');
	if(jQuery("input.boxlist:checked").size() == 0) return;

	var r = eval(f+'()');
	if((r.homepage.length + r.email.length + r.user_name.length + r.ipaddress.length) ==0) return;

	jQuery(':input[name=homepage]','.layerAddDeny fieldset').parent().show();
	if(r.homepage.length == 0) jQuery(':input[name=homepage]','.layerAddDeny fieldset').parent().hide();
	if(r.email.length == 0) jQuery(':input[name=email_address]','.layerAddDeny fieldset').parent().hide();
	if(r.user_name.length == 0) jQuery(':input[name=user_name]','.layerAddDeny fieldset').parent().hide();
	if(r.ipaddress.length == 0) jQuery(':input[name=ipaddress]','.layerAddDeny fieldset').parent().hide();

	jQuery(':input[name=homepage]','.layerAddDeny fieldset').val(r.homepage.join('|')).get(0).checked=true;
	jQuery(':input[name=email_address]','.layerAddDeny fieldset').val(r.email.join('|')).get(0).checked=true;
	jQuery(':input[name=ipaddress]','.layerAddDeny fieldset').val(r.ipaddress.join('|')).get(0).checked=false;
	jQuery(':input[name=user_name]','.layerAddDeny fieldset').val(r.user_name.join('|')).checked=false;
	
	jQuery('.layerAddDeny').addClass('open');
}

function _addDenyGuestbookList(){
	return _addDeny(':input[name=textyle_guestbook_srl]:checked');
}
function _addDenyCommentList(){
	return _addDeny(':input[name=comment_srl]:checked');
}
function _addDenyTrackbackList(){
	return _addDenyTrackback(':input[name=trackback_srl]:checked');
}
function _addDeny(selected){
	var tr,e,ip,n,s;
	var r={email:[],ipaddress:[],user_name:[],homepage:[]};
	jQuery(selected).each(function(i){
		tr = jQuery(this).parents('tr').get(0);

		e = jQuery('>td.email',tr).html();
		ip = jQuery('>td.ipAddress',tr).html();
		n = jQuery('>td.replyArea dt strong',tr).html();
		s = jQuery('>td.replyArea dt a',tr).html();

		if(e && jQuery.inArray(e,r.email)<0) r.email.push(e);
		if(ip && jQuery.inArray(ip,r.ipaddress)<0) r.ipaddress.push(ip);
		if(n && jQuery.inArray(n,r.user_name)<0) r.user_name.push(n);
		if(s && jQuery.inArray(s,r.homepage)<0) r.homepage.push(n);
	});
	return r;
}

function _addDenyTrackback(selected){
	var tr,e,ip,n,s;
	var r={email:[],ipaddress:[],user_name:[],homepage:[]};
	jQuery(selected).each(function(i){
		tr = jQuery(this).parents('tr').get(0);

		//e = jQuery('>td.email',tr).html();
		ip = jQuery('>td span.ipAddress',tr).html();
		n = jQuery('>td p.trackbackAddress',tr).clone();
        n.children().text('');
        n = n.text();
//		n = jQuery('>td.replyArea dt strong',tr).html();
//		s = jQuery('>td.replyArea dt a',tr).html();

		if(e && jQuery.inArray(e,r.email)<0) r.email.push(e);
		if(ip && jQuery.inArray(ip,r.ipaddress)<0) r.ipaddress.push(ip);
		if(n && jQuery.inArray(n,r.user_name)<0) r.user_name.push(n);
		if(s && jQuery.inArray(s,r.homepage)<0) r.homepage.push(n);
	});
	return r;
}
function hideLayerAddDeny(){
	jQuery('.layerAddDeny').removeClass('open');
	
	jQuery("input.boxlist:checked").each(function(){
		this.checked=false;
			});
}

function completeInsertDenyList(ret_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	hideLayerAddDeny();
}


function insertDeny(obj,filter){
	jQuery('input,textarea',obj).each(function(){
		var j = jQuery(this);
		if(j.val() && j.val() == j.attr('title')) j.val('');
	});
	return procFilter(obj,filter);
}


function toggleLnb() {
	if(xGetCookie('tclnb')) {
		xDeleteCookie('tclnb','/');
		jQuery(document.body).addClass('lnbToggleOpen');
		jQuery(document.body).removeClass('lnbClose');
	} else {
		var d = new Date();
		d.setDate(31);
		d.setMonth(12);
		d.setFullYear(2999);
		xSetCookie('tclnb',1,d,'/');
		jQuery(document.body).removeClass('lnbToggleOpen');
		jQuery(document.body).addClass('lnbClose');
	}
}

jQuery(function(){
	
	var saved_st_menu = xGetCookie('tclnb_menu');
	if(saved_st_menu) saved_st_menu = saved_st_menu.split(',');
	else saved_st_menu = [];

	jQuery("div#tool_navigation > ul > li:has(ul) > a").click(function(evt){
		jQuery(this).parent('li').toggleClass('open');
		jQuery(document.body).addClass('lnbToggleOpen');
		jQuery(document.body).removeClass('lnbClose');

		st_menu = [];
		jQuery("div#tool_navigation > ul > li:has(ul) > a").each(function(i){
			if(jQuery(this).parent('li').hasClass('open')) st_menu.push(i);
		});

		var d = new Date();
		d.setDate(31);
		d.setMonth(12);
		d.setFullYear(2999);
		st_menu = jQuery.unique(st_menu);
		xSetCookie('tclnb_menu',st_menu.join(','),d,'/');

		return false;
	}).each(function(i){
		if(jQuery.inArray(i+'',saved_st_menu)>-1) jQuery(this).parent('li').addClass('open');
	});

jQuery("div#tool_navigation > ul > li").hover(
	function(e){
		jQuery(this).addClass('hover');
	},function(e){
		jQuery(this).removeClass('hover');
	});

	jQuery("div.dashboardNotice>button").click(function(){
		jQuery("div.dashboardNotice").toggleClass('open','');
	});
});

function completeTextyleLogin(ret_obj, response_tags, params, fo_obj) {
    var stat = ret_obj['stat'];
    var msg = ret_obj['message'];
    if(stat == -1) {
        alert(msg);
        return;
    }
    if(fo_obj.remember_user_id && fo_obj.remember_user_id.checked) {
        var expire = new Date();
        expire.setTime(expire.getTime()+ (7000 * 24 * 3600000));
        xSetCookie('user_id', fo_obj.user_id.value, expire);
    }

    location.href = current_url.setQuery('act','dispTextyleToolDashboard');
}

var prepared = false;
function doPreProcessing(fo_obj) {
    var xml_file = fo_obj.xml_file.value;
    if(!xml_file || xml_file == 'http://') return false;

    var type = 'module';
    if(fo_obj.type[1].checked) type = 'ttxml';

    jQuery('dl.prepare').addClass('open');
    prepared = false;
    setTimeout(doPrepareDot, 50);

    var params = new Array();
    params['xml_file'] = xml_file;
    params['type'] = type;

    var response_tags = new Array('error','message','type','total','cur','key','status');
    exec_xml('textyle','procTextyleToolImportPrepare', params, completePreProcessing, response_tags);

    return false;
}

function doPrepareDot() {
    if(prepared) return;

    var w = parseInt(jQuery('span.preProgress').css('width').replace(/%$/,''),10);
    w++;
    if(w>100) w = 1;
    jQuery('span.preProgress').css('width',w+'%');
    setTimeout(doPrepareDot, 50);
}

function completePreProcessing(ret_obj, response_tags) {
    prepared = true;
    jQuery('dl.prepare').removeClass('open');

    var status = ret_obj['status'];
    var message = ret_obj['message'];
    var type = ret_obj['type'];
    var total = parseInt(ret_obj['total'],10);
    var cur = parseInt(ret_obj['cur'],10);
    var key = ret_obj['key'];

    if(status == -1) {
        alert(message);
        return;
    }

    var fo_obj = jQuery('#fo_process').get(0);
    fo_obj.total.value = total;
    fo_obj.cur.value = cur;
    fo_obj.key.value = key;
    
    doImport();
}


function doImport() {
    var fo_obj = jQuery('#fo_process').get(0);

    var params = new Array();
    if(fo_obj.type[0].checked) params['type'] = 'module';
    else params['type'] = 'ttxml';
    params['total'] = fo_obj.total.value;
    params['cur'] = fo_obj.cur.value;
    params['key'] = fo_obj.key.value;
    params['target_module'] = fo_obj.target_module.value;
    params['guestbook_target_module'] = fo_obj.target_module.value;
    params['unit_count'] = fo_obj.unit_count.value;
    params['user_id'] = fo_obj.user_id.value;

    jQuery('dl.doing').addClass('open');
    displayProgress(params['total'], params['cur']);

    var response_tags = new Array('error','message','type','total','cur','key');

    show_waiting_message = false;
    exec_xml('textyle','procTextyleToolImport', params, completeImport, response_tags);
    show_waiting_message = true;

    return false;
}

function completeImport(ret_obj, response_tags) {
    var message = ret_obj['message'];
    var type = ret_obj['type'];
    var total = parseInt(ret_obj['total'], 10);
    var cur = parseInt(ret_obj['cur'], 10);
    var key = ret_obj['key'];

    displayProgress(total, cur);

    var fo_obj = jQuery('#fo_process').get(0);
    fo_obj.type.value = type;
    fo_obj.total.value = total;
    fo_obj.cur.value = cur;
    fo_obj.key.value = key;
    
    if(total > cur) doImport();
    else {
        alert(message);
        fo_obj.reset();
        jQuery('dl.doing').removeClass('open');
    }
}

function displayProgress(total, cur) {
    var per = 0;
    if(total > 0) per = Math.round(cur / total * 100);
    else per = 100;
    if(!per) per = 1;

    jQuery('dl.progress').find('span.fill').css('width',per+'%');
    jQuery('dl.progress').find('em').html(per+'%');
}

function doCheckMe2day() {
    var params = new Array();
    params['me2day_userid'] = jQuery('#me2userid').val();
    params['me2day_userkey'] = jQuery('#me2userkey').val();
	exec_xml('textyle', 'procTextyleCheckMe2day', params, function() {});
}

/* check twitter account */
function doCheckTwitter() {
    var params = new Array();
    params['twitter_consumer_key'] = jQuery('#twitterconsumerkey').val();
    params['twitter_consumer_secret'] = jQuery('#twitterconsumersecret').val();
    params['twitter_oauth_token'] = jQuery('#twitteroauthtoken').val();
    params['twitter_oauth_token_secret'] = jQuery('#twitteroauthtokensecret').val();
	exec_xml('textyle', 'procTextyleCheckTwitter', params, function() {});
}

addNode = function(node,e) {
	var $ = jQuery;
	var $w = $('#__category_info');

	clearValue();

	// set value
	$w.find('input[name="category_srl"]').val(0);
	$w.find('input[name="parent_srl"]').val(node);
	$w.find('.category_groups').css('display','none');
	if(node){
		$('#__parent_category_info').show().next('.x_control-group').css('borderTop','1px dotted #ddd');
		$('#__parent_category_title').text($('#tree_' + node + ' > span').text());
	}else{
		$('#__parent_category_info').hide().next('.x_control-group').css('borderTop','0');
	}

}
modifyNode = function(node,e) {
	var $ = jQuery;
	var $w = $('#__category_info');

	clearValue();

	// set value
	$w.find('input[name="category_srl"]').val(node);
	$w.find('.category_groups').css('display','none');

	var module_srl = $w.find('input[name="module_srl"]').val();

	$.exec_json('document.getDocumentCategoryTplInfo', {'module_srl': module_srl, 'category_srl': node}, function(data){
		if(!data || !data.category_info) return;

		if(data.error){
			alert(data.message);
			return;
		}

		$w.find('input[name="parent_srl"]').val(data.category_info.parent_srl);
		$w.find('input[name="category_title"]').val(data.category_info.title).trigger('reload-multilingual');
		$w.find('input[name="category_color"]').val(data.category_info.color).trigger('keyup');
		$w.find('textarea[name="category_description"]').val(data.category_info.description).trigger('reload-multilingual');
		for(var i in data.category_info.group_srls){
			var group_srl = data.category_info.group_srls[i];
			$w.find('input[name="group_srls[]"][value="' + group_srl + '"]').attr('checked', 'checked');
		}
		if(data.category_info.expand == 'Y'){
			$w.find('input[name="expand"]').attr('checked', 'checked');
		}
	});

	$('#__parent_category_info').hide().next('.x_control-group').css('borderTop','0');
}

function doBlogApiTest() {
    var fo = jQuery('#foBlogApi');

    var params = new Array();
    fo.find('input,select').each( function() {
            if(this.type=='radio' && !this.checked) return;
            params[this.name] = jQuery(this).val();
    } );
    if(params['blogapi_service']=='custom' && !params['blogapi_url']) {
        alert(_msg[2]);
        fo.find('input[name=blogapi_url]').get(0).focus();
        return;
    }
    if(!params['blogapi_url']) {
        alert(_msg[2]);
        fo.find('input[name=blogapi_url]').get(0).focus();
        return;
    }
    if(!params['blogapi_user_id']) {
        alert(_msg[3]);
        fo.find('input[name=blogapi_user_id]').get(0).focus();
        return;
    }
    if(!params['blogapi_password']) {
        alert(_msg[4]);
        fo.find('input[name=blogapi_password]').get(0).focus();
        return;
    }

    exec_xml('textyle','getTextyleAPITest', params, completeBlogApiTest, new Array('error','message','site_url','title'));
}

function completeBlogApiTest(ret_obj) {
    var site_url = ret_obj['site_url'];
    var title = ret_obj['title'];
    var fo = jQuery('#foBlogApi');
    fo.find('input[name=blogapi_site_url]').val(site_url);
    fo.find('input[name=blogapi_site_title]').val(title);
    if(site_url) {
        jQuery('.submitButton').css('display','block');
        jQuery('#response_info').show(); 
	}else{
        alert(ret_obj['message']);
    }
}

function doCheckApiConnect() {
    var fo = jQuery('#foBlogApi');
    var target = new Array('blogapi_site_url','blogapi_site_title','blogapi_url','blogapi_user_id','blogapi_password');
    var params = {}
    for(var i=0;i<target.length;i++) {
        params[target[i]] = jQuery('[name='+target[i]+']').val();
        if(!params[target[i]]) {
            alert(_msg[i]);
            fo.find('[name='+target[i]+']').get(0).focus();
            return;
        }
    }
    exec_xml('textyle','getTextyleConnectInfo', params);
}

function completeInsertBlogApi() {
    location.href = current_url.setQuery('type','');
}

function doToggleAPI(obj, api_srl) {
    exec_xml('textyle','procTextyleToggleEnableAPI', {api_srl:api_srl}, function(ret_obj) { var o = jQuery(obj); if(ret_obj['enable']=='Y') o.attr('class','buttonSet buttonActive'); else o.attr('class','buttonSet buttonDisable'); }, new Array('error','message','enable'));
}

function doRemoveApi(api_srl) {
    exec_xml('textyle','procTextyleDeleteBlogApi', {api_srl:api_srl}, completeReload, new Array('error','message'));
}

function appendTrackbackForm() {
    var o = jQuery(jQuery('div.item').get(0)).clone();
    var l = jQuery('div.item').length;
    o.find('input[name=trackback_url]').val('');
    o.find('select[name=trackback_charset]');
    o.html(o.html().replace(/trackback_(url|charset)/g, 'trackback_$1'+l));
    o.appendTo(jQuery('.trackbackOption'));
}



function doSetupComponent(component_name) {
    popopen(request_uri.setQuery('module','editor').setQuery('act','dispEditorAdminSetupComponent').setQuery('component_name',component_name), 'SetupComponent');
}

function doEnableComponent(component_name) {
    var params = new Array();
    params['component_name'] = component_name;

    exec_xml('editor', 'procEditorAdminEnableComponent', params, completeUpdate);
}
function doDisableComponent(component_name) {
    var params = new Array();
    params['component_name'] = component_name;

    exec_xml('editor', 'procEditorAdminDisableComponent', params, completeUpdate);
}

function doMoveListOrder(component_name, mode) {
    var params = new Array();
    params['component_name'] = component_name;
    params['mode'] = mode;

    exec_xml('editor', 'procEditorAdminMoveListOrder', params, completeUpdate);
}
function getBlogApiServiceInfo(srl) {
	if(!srl){
		jQuery('#api_url_label').html('');
		jQuery('#blogapi_userid_label').html('');
		jQuery('#blogapi_password_label').html('');
	
		return;
	}
    var params = new Array();
    params['textyle_blogapi_services_srl'] = srl;
    exec_xml('textyle', 'getBlogApiService', params, function(data){
		var s = data['services'].item;
		jQuery('#api_url_label').html(s.url_description);
		jQuery('#blogapi_userid_label').html(s.id_description);
		jQuery('#blogapi_password_label').html(s.password_description);
		jQuery(':input[name=blogapi_host_provider_type]').val(s.api_type);
	},new Array('error','message','services'));
}


function completeUpdate(ret_obj) {
    location.reload();
}

function initTextyle() {
	var params = new Array();
	params['mid'] = current_mid;
	params['vid'] = xeVid;

    exec_xml('textyle','procTextyleToolInit', params, 
		function(ret_obj){
			alert(ret_obj['message']);
			location.href = current_url.setQuery('act','dispTextyleToolDashboard');
		}, new Array('error','message'));
}
function checkUserImage(f,msg){
    var filename = jQuery('[name=user_image]',f).val();
    if(/\.(gif|jpg|jpeg|gif|png|swf|flv)$/i.test(filename)){
        return true;
    }else{
        alert(msg);
        return false;
    }
}
function deleteUserImage(filename){
    var params ={
            "mid":current_mid
			,"vid":xeVid
            ,"filename":filename
            };
    jQuery.exec_json('textyle.procTextyleToolUserImageDelete', params, function(data){
        document.location.reload();
    });
}


function completeRequestExportTextyle(ret_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];

	location.reload();
}
function completeExportTextyle(ret_obj){
	var error = ret_obj['error'];
	var message = ret_obj['message'];

	location.reload();
}


(function($){

var inputPublish, submitButtons;
var validator = xe.getApp('Validator')[0];

validator.cast('ADD_CALLBACK', ['save_post', function callback(form) {
	var params={}, responses=[], elms=form.elements, data=jQuery(form).serializeArray();
	$.each(data, function(i, field) {
		var val = $.trim(field.value);
		if(!val) return true;
		if(/\[\]$/.test(field.name)) field.name = field.name.replace(/\[\]$/, '');
		if(params[field.name]) params[field.name] += '|@|'+val;
		else params[field.name] = field.value;
	});
	responses = ['error','message','mid','document_srl','category_srl', 'redirect_url'];
	exec_xml('textyle','procTextylePostsave', params, completePostsave, responses, params, form);

	inputPublish.val('N');
}]);

$(function(){
	inputPublish  = $('input[name=publish]');
	inputPreview  = $('input[name=preview]');
	submitButtons = $('#wPublishButtonContainer button');

	submitButtons.click(function(){
		inputPublish.val( $(this).parent().hasClass('_publish')?'Y':'N' );
		inputPreview.val( $(this).parent().hasClass('_preview')?'Y':'N' );
		$('input:text,textarea', this.form).each(function(){
			var t = $(this);
			var v = $.trim(t.val());
			if (v && v == t.attr('title')) t.val('');
		});

		if(editorRelKeys[1]) editorRelKeys[1].content.value = editorRelKeys[1].func();
	});
});

})(jQuery);



function changeMenuType(obj) {
    var sel = obj.options[obj.selectedIndex].value;
    if(sel == 'url') {
        jQuery('#urlForm').css("display","block");
    } else {
        jQuery('#urlForm').css("display","none");
    }
}

function deleteExtraMenu(menu_mid,confirm_msg){
	if(confirm(confirm_msg)){
	var response_tags = new Array('error','message');
	var params = {'menu_mid':menu_mid}
	exec_xml('textyle', 'procTextyleToolExtraMenuDelete', params, completeReload, response_tags);
	}
}


function completeDeleteExtraMenu(ret_obj, response_tags, params, fo_obj) {
    var error = ret_obj['error'];
    var msg = ret_obj['message'];

    location.href = current_url.setQuery('act','dispTextyleToolExtraMenuList');
}

function completeModifyExtraMenu(ret_obj, response_tags, params, fo_obj) {
    var error = ret_obj['error'];
    var msg = ret_obj['message'];

    location.href = current_url.setQuery('act','dispTextyleToolExtraMenuList');
}

function sortExtraMenu(menu_mids){
	var response_tags = new Array('error','message');
	var params = {'menu_mids':menu_mids.join(',')};
	exec_xml('textyle', 'procTextyleToolExtraMenuSort', params, function(){}, response_tags);
}

function isLive(){
	exec_xml('textyle', 'procTextyleToolLive', []);
}

jQuery(function($){
	// Label Text Clear
	var iText = $('.fItem>.iLabel').next('.iText');
	$('.fItem>.iLabel').css('position','absolute');
	iText
		.focus(function(){
			$(this).prev('.iLabel').css('visibility','hidden');
		})
		.blur(function(){
			if($(this).val() == ''){
				$(this).prev('.iLabel').css('visibility','visible');
			} else {
				$(this).prev('.iLabel').css('visibility','hidden');
			}
		})
		.change(function(){
			if($(this).val() == ''){
				$(this).prev('.iLabel').css('visibility','visible');
			} else {
				$(this).prev('.iLabel').css('visibility','hidden');
			}
		})
		.blur();
});

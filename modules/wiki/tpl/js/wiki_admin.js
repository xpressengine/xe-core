/**
 * @file   modules/wiki/js/wiki_admin.js
 * @author NHN(developers@xpressengine.com)
 * @brief  wiki 모듈의 관리자용 javascript
 **/


/* after creation wiki Module */
function completeInsertWiki(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    var page = ret_obj['page'];
    var module_srl = ret_obj['module_srl'];

    alert(message);

    var url = current_url.setQuery('act','dispWikiAdminInsertWiki');
    if(module_srl) url = url.setQuery('module_srl',module_srl);
    if(page) url.setQuery('page',page);
    location.href = url;
}

/*  after deleting wiki Module */
function completeDeleteWiki(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var page = ret_obj['page'];
    alert(message);

    var url = current_url.setQuery('act','dispWikiAdminContent').setQuery('module_srl','');
    if(page) url = url.setQuery('page',page);
    location.href = url;
}


/* Set Batch for bulk operations */
function doCartSetup(url) {
    var module_srl = new Array();
    jQuery('#fo_list input[name=cart]:checked').each(function() {
        module_srl[module_srl.length] = jQuery(this).val();
    });

    if(module_srl.length<1) return;

    url += "&module_srls="+module_srl.join(',');
    popopen(url,'modulesSetup');
}

function doArrangeWikiList(module_srl) {
    exec_xml('wiki','procWikiAdminArrangeList',{module_srl:module_srl},function() {location.reload();});
}

function doChangeCategory(fo_obj) {
    var module_category_srl = fo_list.module_category_srl.options[fo_list.module_category_srl.selectedIndex].value;
    if(module_category_srl==-1) {
        location.href = current_url.setQuery('act','dispModuleAdminCategory');
        return false;
    }
    return true;
}
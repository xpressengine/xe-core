/**
 * @file   modules/dfoward/tpl/dfoward.js
 * @author NHN (developer@xpressengine.com)
 * @brief  dfoward 모듈의 관리자용 javascript
 **/

/* 목록 출력 */
function doDisplayDfowardListUp() {
	jQuery('#dfoward_listup,#dfoward_update_form,#dfoward_insert_form').hide();
	jQuery('#dfoward_listup').show();
}

/* 입력 폼 노출 */
function doDisplayInsertDfoward() {
	jQuery('#dfoward_listup,#dfoward_update_form,#dfoward_insert_form').hide();
	jQuery('#dfoward_insert_form').show();
}

/* 수정폼 노출 */
function doDisplayUpdateDfoward(dfoward_srl) {
    var params = {dfoward_srl:dfoward_srl}, response_tags=['error','message','dfoward_srl','hostname','title','target_url'];
    exec_xml("dfoward", "getDfowardHostInfo", params, completeDisplayUpdateDfoward, response_tags);
}

function completeDisplayUpdateDfoward(ret_obj) {
    var $form = jQuery('#update_form');

    $form[0].dfoward_srl.value = ret_obj["dfoward_srl"];
    $form[0].hostname.value = ret_obj["hostname"];
    $form[0].target_url.value = ret_obj["target_url"];
    $form[0].title.value = ret_obj["title"];

	jQuery('#dfoward_listup,#dfoward_update_form,#dfoward_insert_form').hide();
	jQuery('#dfoward_update_form').show();
}

/* 삭제 */
function doDeleteDfoward(dfoward_srl, msg) {
    if(!confirm(msg)) return;

    var params = {dfoward_srl:dfoward_srl};

    exec_xml("dfoward","procDfowardDelete",params, completeDeleteDfoward);
}

function completeDeleteDfoward(ret_obj) {
    var url = current_url.setQuery('act','dispDfowardContent');
    location.href = url;
}

/* 포워딩 정보 입력후 */
function completeInsert(ret_obj) {
    var url = current_url.setQuery('act','dispDfowardContent');
    location.href = url;
}

/* 포워딩 정보 수정후 */
function completeUpdate(ret_obj) {
    var url = current_url.setQuery('act','dispDfowardContent');
    location.href = url;
}

/* 포워딩 정보 삭제 후 */
function completeDelete(ret_obj) {
    var url = current_url.setQuery('act','dispDfowardContent');
    location.href = url;
}


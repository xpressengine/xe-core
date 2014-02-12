/**
 * @file   modules/wiki/js/wiki.js
 * @author NHN(developers@xpressengine.com)
 * @brief  wiki 모듈의 javascript
 **/

/* Delete wiki document */
function doDeleteWiki(document_srl) {
    var params = new Array();
    params['mid'] = current_mid;
    params['document_srl'] = document_srl;
    exec_xml('wiki','procWikiDeleteDocument', params, function(){
    	document.location.reload()
    });
}

/* insert the comment and than return to view document's page */
function completeInsertComment(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var mid = ret_obj['mid'];
    var document_srl = ret_obj['document_srl'];
    var comment_srl = ret_obj['comment_srl'];

    var url = current_url.setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','');
    if(comment_srl) url = url.setQuery('rnd',comment_srl)+"#comment_"+comment_srl;

    location.href = url;
}

/* Delete comment */
function completeDeleteComment(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var mid = ret_obj['mid'];
    var document_srl = ret_obj['document_srl'];
    var page = ret_obj['page'];

    var url = current_url.setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','');
    if(page) url = url.setQuery('page',page);

    location.href = url;
}

/* Recreate the hierarchy */
function doRecompileTree() {
    var params = new Array();
    params['mid'] = current_mid;
    exec_xml('wiki','procWikiRecompileTree', params, function(){
    	document.location.reload();
    });
}

jQuery(document).ready(function($){
    $('<span style="cursor: pointer; margin-left: 10px">[hide]</span>')
        .click(function(){
            var subject = $(this).parents('#wikiToc').children('ul').not(':animated');
            if (subject.is(':visible')) {
                subject.slideUp();
                $(this).text('[show]');
            }
            else {
                subject.slideDown();
                $(this).text('[hide]');
            }
        })
        .appendTo('#wikiTocTitle');
});
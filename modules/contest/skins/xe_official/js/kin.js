jQuery(function($) { 

    if($('.askTitle').length) $('.askTitle').get(0).focus();
	
	var tabs  = $('.kinAsk>.item');
	var boxes = $('.formBox');
	
	tabs.find('>h3>a').each(function(i){
		$(this).attr('_index', i);
	});
	
	function setTab(e) {
		var currentIdx = $(this).attr('_index');
		
		tabs.removeClass('selected');
		$(this).parent().parent().addClass('selected');
		
		boxes.addClass('hide').eq(currentIdx).removeClass('hide')
		
		if (e.type != 'keyup') {
			boxes.eq(currentIdx).find('.askTitle')[0].focus();
		}
	}
	
    $('.kinAsk>.item>h3>a').click(setTab).mouseover(setTab).keyup(setTab);

    $('#kinTab>li').each( function(i) {
        $(this).click(function() {
            $('#kinTab>li').removeClass('selected');
            $(this).addClass('selected');
            $('.listBox').each( function(j) {
                if(i==j) $(this).removeClass('hide');
                else $(this).addClass('hide');
            });
            return false;
        });
    });
/*
	
	var tabs  = $('.roundTabBox>li');
	var boxes = $('.listBox');
	
	tabs.find('>a').each(function(i){
		$(this).attr('_index', i);
	});
	
	function setTab(e) {
		var currentIdx = $(this).attr('_index');
		
		tabs.removeClass('selected');
		$(this).parent().addClass('selected');
		
		boxes.addClass('hide').eq(currentIdx).removeClass('hide')
		}
	
    $('.roundTabBox>li>a').click(setTab).mouseover(setTab);
*/

});

function completeWriteDocument(ret_obj) {
    alert(ret_obj['message']);

    var url = request_uri.setQuery('mid', current_mid).setQuery('document_srl', ret_obj['document_srl']).setQuery('act','dispKinView');
    if(typeof(xeVid)!='undefined') url = url.setQuery('vid', xeVid);
    location.href = url;
}

function doDeleteDocument(document_srl) {
    var params = new Array();
    params['document_srl'] = document_srl; 

	var url = request_uri.setQuery('mid',current_mid);
    exec_xml('kin','procKinDeleteDocument', params, function() { location.href = url; });
}

function doSelectReply(comment_srl) {
    var params = new Array();
    params['comment_srl'] = comment_srl; 
    exec_xml('kin','procKinSelectReply', params, function() { location.reload(); });
}

function doDeleteReply(comment_srl) {
    var params = new Array();
    params['comment_srl'] = comment_srl; 
    exec_xml('kin','procKinDeleteReply', params, function() { location.reload(); });
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

function doDeleteComment(parent_srl,reply_srl,page) {
    var params = new Array();
    params['parent_srl'] = parent_srl; 
    params['reply_srl'] = reply_srl; 
    params['mid'] = current_mid;
    exec_xml('kin','procKinDeleteComment', params, displayComments,  new Array('error','message','parent_srl','html'));
}

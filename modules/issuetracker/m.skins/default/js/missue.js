function loadPage(document_srl, page) {
	var params = {};
	params["cpage"] = page; 
	params["document_srl"] = document_srl;
	params["mid"] = current_mid;
	exec_xml("issuetracker", "getIssuetrackerCommentPage", params, completeGetPage, ['html','error','message'], params);
}

function completeGetPage(ret_val) {
	jQuery("#cl").remove();
	jQuery("#clpn").remove();
	jQuery("#clb").parent().after(ret_val['html']);
}

function completeIssueInserted(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var mid = ret_obj['mid'];
    var document_srl = ret_obj['document_srl'];

    //alert(message);

    var url = current_url.setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','dispIssuetrackerViewIssue');
    location.href = url;
}
function completeGetMoreChangesets(ret_val)
{
	if(ret_val['lastitems'])
	{
		jQuery(".lt:last").append(ret_val['lastitems']);
	}
	if(ret_val['changesets'])
	{
		jQuery(".lt:last").after(ret_val['changesets']);
	}
	if(ret_val['lastdate'])
	{
		jQuery("#hiddenForm").get(0).lastdatetime.value = ret_val['lastdate'];
	}
	else
	{
		jQuery(".pn").remove();		
	}


}

function getMoreTimeLine()
{
	var params = {}, data = jQuery("#hiddenForm").serializeArray();
	jQuery.each(data, function(i, field){ params[field.name] = field.value });
	exec_xml('issuetracker', 'getIssuetrackerMoreChangesetsM', params, completeGetMoreChangesets, ['lastdate','lastitems', 'changesets', 'error', 'message'], params);
}

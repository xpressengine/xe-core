jQuery(document).ready(function($)
{
		$('#package_search_button').click(function()
		{
			searchResultPage(1, $('#package_search_keyword').val());
			return false;
		});
});

function afterSearchPackage(ret_obj)
{
	jQuery('#package_search_result').html(ret_obj['output']).show();
}

function selectPackage(package_srl, title)
{
	jQuery('#package_search_result').hide();
	jQuery('#package_select_result').html(title).show();
	jQuery('input[name=package_srl]').val(package_srl);
	return false;
}

function searchResultPage(page, keyword)
{
	jQuery('#package_select_result').hide();

	var params = new Array();
	params['keyword'] = keyword;
	params['page'] = page;
	var response_tags = new Array('error', 'message', 'output');
	exec_xml('moumi', 'searchPackageList', params, afterSearchPackage, response_tags);

	return false;
}

function deletePackage(package_srl)
{
	if (!package_srl) return;

	var params = new Array();
	params['package_srl'] = package_srl;
	var response_tags = new Array('error', 'message');
	exec_xml('moumi', 'procMoumiAdminDeletePackage', params, afterDeletePackage, response_tags);

	return false;
}

function afterDeletePackage(ret_obj)
{
	document.location.reload();
}

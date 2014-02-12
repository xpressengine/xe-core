function checkAPIKey(apiKey){
    var response_tags = new Array('error','message','result_status');
	exec_xml('analytics','procAnalyticsAdminCheckAPIKey',{'api_key':apiKey},function(ret_obj,response_tags){
		var error = ret_obj['error'];
		var message = ret_obj['message'];
		var result = ret_obj['result_status'];
		alert(result);
	},response_tags);

}

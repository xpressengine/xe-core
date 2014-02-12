jQuery(function($){
	
	// GNB
	var gnb = $('div.gnb');
	var gnb_ul = gnb.find('>ul');
	var gnb_list = gnb.find('>ul>li');
	
	// Show GNB
	function show_gnb(){
		gnb_list.removeClass('active');
		$(this).parent('li').addClass('active');
	}
	gnb_list.find('>a').mouseover(show_gnb).focus(show_gnb);
	
	// Hide GNB
	function hide_gnb(){
		gnb_list.removeClass('active');
	}
	gnb.mouseleave(hide_gnb);
	$('*:not(".gnb a")').focus(hide_gnb);
	
	// Menu
	var menu = $('div.menu');
	var major = $('.issuetracker div.major');
	var li_list = major.find('>ul>li');
	
	// Selected
	function onselectmenu(){
		var myclass = [];
		
		$(this).parent('li').each(function(){
			myclass.push( $(this).attr('class') );
		});
		
		myclass = myclass.join(' ');
		if (!major.hasClass(myclass)) major.attr('class','major').addClass(myclass);
	}
	
	// Show Menu
	function show_menu(){
		li_list.removeClass('active');
		$(this).parent('li').addClass('active');
		
		// IE7 or IE7 documentMode bug fix
		if($.browser.msie) {
			var v = document.documentMode || parseInt($.browser.version);

			if (v == 7) {
				var sub = $(this).next('div.sub').eq(-1);
				sub.css('width', '').css('width', sub.width()+'px');
			}
		}
	}
	li_list.find('>a').click(onselectmenu).mouseover(show_menu).focus(show_menu);
	
	// Hide Menu
	function hide_menu(){
		li_list.removeClass('active');
	}
	menu.mouseleave(hide_menu);
	li_list.find('div.sub>ul').mouseleave(hide_menu);
	
	//icon
	major.find('div.sub').prev('a').find('>span').append('<span class="i"></span>');
	
	// Aside
	var aside_li = $('.menu>.inset>.aside>ul>li');
	var aside_a = $('.menu>.inset>.aside>ul>li>a');

	// Show Aside
	function show_aside(){
		li_list.removeClass('active');
		aside_li.removeClass('active');
		$(this).parent('li').addClass('active');
	}	
	aside_a.mouseover(show_aside).focus(show_aside);
	
	// Hide Aside
	function hide_aside(){
		aside_li.removeClass('active');
	}	
	menu.mouseleave(hide_aside);
	aside_li.find('div.sub>ul').mouseleave(hide_aside);

	// Hide Menu & Aside
	$('*:not(".menu *")').focus(hide_menu).focus(hide_aside);
	
});


function completeGetMoreLog(ret_obj)
{
	var listBody = jQuery("#logList");
	if(!jQuery.isArray(ret_obj['logs']['item']))
	{
		ret_obj['logs']['item'] = [ret_obj['logs']['item']];
	}
	var rev = 0;
	if(ret_obj['logs']['item'].length > 20)
	{
		rev = ret_obj['logs']['item'][20]['revision'];
	}
	for(var i=0;i<ret_obj['logs']['item'].length && i<20;i++)
	{
		var v = ret_obj['logs']['item'][i];
		var item = '<tr><td class="num aRight"><a href="'+request_uri.setQuery('type','file').setQuery('path',v['p']).setQuery('revs',v['revision'])+'">r'+v['revision']+'</a></td>';
		item += '<td><input name="erev" type="radio" value="'+v['revision']+'" class="iRadio" title="r'+v['revision']+'" /></td>';
		item += '<td><input name="brev" type="radio" value="'+v['revision']+'" class="iRadio" title="r'+v['revision']+'" /></td>';
		item += '<td class="title">'+v['msg']+'&nbsp;</td><td>'+v['author']+'</td><td class="time">'+v['date']+' <em>('+v['gap']+')</em></td></tr>';
		listBody.append(item);
	}
	if(rev)
	{
		jQuery("#logForm").get(0).lastrev.value = rev;
	}
	else
	{
		jQuery("#moreButton").css({"display" : "none"});
	}
}

function getMoreLog()
{
	var params = {}
	var form = jQuery("#logForm").get(0);
	params["mid"] = form.mid.value; 
	params["vid"] = form.vid.value; 
	params["lastRev"] = form.lastrev.value;
	params["path"] = form.path.value; 
	exec_xml('issuetracker', 'getIssuetrackerMoreLog', params, completeGetMoreLog, ['logs', 'error', 'message'], params, form);
}

function completeGetMoreChangesets(ret_val)
{
	if(ret_val['lastitems'])
	{
		jQuery(".pxeT3:last").append(ret_val['lastitems']);
	}
	if(ret_val['changesets'])
	{
		jQuery(".pxeT3:last").after(ret_val['changesets']);
	}
	if(ret_val['lastdate'])
	{
		jQuery("#hiddenForm").get(0).lastdatetime.value = ret_val['lastdate'];
	}
	else
	{
		jQuery(".buttonArea").remove();		
	}


}

function getMoreTimeline()
{
	var params = {}, data = jQuery("#hiddenForm").serializeArray();
	jQuery.each(data, function(i, field){ params[field.name] = field.value });
	exec_xml('issuetracker', 'getIssuetrackerMoreChangesets', params, completeGetMoreChangesets, ['lastdate','lastitems', 'changesets', 'error', 'message'], params);
}

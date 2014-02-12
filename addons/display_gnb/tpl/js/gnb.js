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
	var major = $('div.major');
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

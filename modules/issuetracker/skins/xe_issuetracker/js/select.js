jQuery(function($){
	
	// Drop Down Emulator
	// Common
	var select_root = $('div.select');
	var select_value = $('.myValue');
	var select_a = $('div.select>ul>li>a');
	var select_input = $('div.select>ul>li>input[type=radio]');
	var select_label = $('div.select>ul>li>label');
	
	// Radio Default Value
	$('div.myValue').each(function(){
		var default_value = $(this).next('.iList').find('input[checked]').next('label').text();
		$(this).append(default_value);
	});
	
	// Line
	select_value.bind('focusin',function(){$(this).addClass('outLine');});
	select_value.bind('focusout',function(){$(this).removeClass('outLine');});
	select_input.bind('focusin',function(){$(this).parents('div.select').children('div.myValue').addClass('outLine');});
	select_input.bind('focusout',function(){$(this).parents('div.select').children('div.myValue').removeClass('outLine');});
	
	// Show
	function show_option(){
		$(this).parents('div.select:first').toggleClass('open');
	}
	
	// Hover
	function i_hover(){
		$(this).parents('ul:first').children('li').removeClass('hover');
		$(this).parents('li:first').toggleClass('hover');
	}
	
	// Hide
	function hide_option(){
		var t = $(this);
		setTimeout(function(){
			t.parents('div.select:first').removeClass('open');
		}, 1);
	}
	
	// Set Input
	function set_label(){
		var v = $(this).next('label').text();
		$(this).parents('ul:first').prev('.myValue').text('').append(v);
		$(this).parents('ul:first').prev('.myValue').addClass('selected');
	}
	
	// Set Anchor
	function set_anchor(){
		var v = $(this).text();
		$(this).parents('ul:first').prev('.myValue').text('').append(v);
		$(this).parents('ul:first').prev('.myValue').addClass('selected');
	}

	// Anchor Focus Out
	$('*:not("div.select a")').focus(function(){
		$('.aList').parent('.select').removeClass('open');
	});
			
	select_value.click(show_option);
	select_root.removeClass('open');
	select_root.mouseleave(function(){$(this).removeClass('open');});
	select_a.click(set_anchor).click(hide_option).focus(i_hover).hover(i_hover);
	select_input.change(set_label).focus(set_label);
	select_label.hover(i_hover).click(hide_option);
		
	// Multi Select Emulator
	var option_ui = $('ul.sOption');

	// Single Select
	function single_select(){
		var ul = $(this).parents('ul.sOption:first');

		ul.find('li>label').removeClass('selected');
		ul.prev('h4').find('input[type=checkbox]').attr('checked', 'checked');
		$(this).next('label').toggleClass('selected');
	}

	option_ui.find('>li>input.iRadio')
		.change(single_select)
		.filter('[checked]')
		.each(function(){
			var t = $(this);
			var chk = t.parents('div.item:first').find('>h4>input[type=checkbox]');
			
			if (chk.attr('checked')) {
				t.change();
			} else {
				t.removeAttr('checked');
			}
		});
		
	option_ui.find('>li>.iRadio').next('label').click(function(){
		var t = $(this);
		t.toggleClass('selected');
		
		if(t.prev('.iRadio[checked]').length){
			t.prev('.iRadio[checked]').removeAttr('checked');
			t.parents('.item:first').find('h4>input[type=checkbox]').removeAttr('checked');
			return false;
		}
	});

	window.package_releases = package_releases;
	function package_releases(element, package_srl)
	{
		var cur_package = $("#current_package").get(0);
		var list = $("#releases").empty();
		cur_package.value = package_srl;
		var target = jQuery("#release_"+package_srl);	
		target.find('li').clone(true).appendTo(list);

		/*list.find('>li>.iRadio').change(single_select)
			.filter('[checked]')
			.each(function(){
				var t = $(this);
				var chk = t.parents('div.item:first').find('>h4>input[type=checkbox]');
			
				if (chk.attr('checked')) {
					t.change();
				} else {
					t.removeAttr('checked');
				}
			});
		list.find('>li>.iRadio').next('label').click(function(){
				var t = $(this);
				t.toggleClass('selected');

				if(t.prev('.iRadio[checked]').length){
				t.prev('.iRadio[checked]').removeAttr('checked');
				t.parents('.item:first').find('h4>input[type=checkbox]').removeAttr('checked');
				return false;
				}
			});*/
	}

		
	// Multi Select
	function multi_select(){
		var ul = $(this).parents('ul.sOption:first');

		ul.prev('h4').find('input[type=checkbox]').attr('checked', 'checked');
		$(this).next('label').toggleClass('selected');
	}

	option_ui.find('>li>input.iCheck')
		.change(multi_select)
		.filter('[checked]')
		.each(function(){
			var t = $(this);
			var chk = t.parents('div.item:first').find('>h4>input[type=checkbox]');
			
			if (chk.attr('checked')) {
				t.change();
			} else {
				t.removeAttr('checked');
			}
		});
		
	option_ui.prev('h4').find('input[type=checkbox]')
		.click(function(){
			var t = $(this);
			var ul = t.parents('h4:first').next('ul.sOption');

			ul.find('input[checked]').removeAttr('checked').next('label').removeClass('selected');
		});
		
	
});

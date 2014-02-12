jQuery(function($){
	
	// 검색창 글꼴 색
	var input_search = $('form.search>fieldset.basic>input[type=text]');
	input_search.focus(function(){$(this).attr('style','color:#767676')});
	input_search.bind('focusout',function(){$(this).removeAttr('style')});
	
	// 이슈목록 상세검색
	$('form.search').removeClass('openSrch');
	$('button.toggleSrch').click(function(){
		$('form.search').toggleClass('openSrch');
	});
	
	// 다운로드
	$('.pxeDownload>.dBody').removeClass('show');
	$('.pxeDownload').each(function(){ 
		$(this).find('>.dBody:first').addClass('show'); 
	});
	$('.pxeDownload>.dFooter button').click(function(){
		$(this).toggleClass('hidden');
		
		if($(this).hasClass('hidden')){
			//alert('1');
			$(this).parents('.pxeDownload').find('.dBody').removeClass('show');
			$(this).parents('.pxeDownload').find('.dBody:first').addClass('show');
		} else {
			//alert('2');
			$(this).parents('.pxeDownload').find('.dBody').addClass('show');
		}
	});
	
	// 다운로드 - 배포기록 및 변경사항
	$('.vInfo').removeClass('openInfo');
	$('.vInfo>button').click(function(){
		$(this).parents('.vInfo:first').toggleClass('openInfo');
	});
	
	// 필터
	$('.tFilter>button').click(function(){
		$(this).parent('.tFilter').toggleClass('fOpen');
	});
	$('.tFilter').mouseleave(function(){
		$(this).removeClass('fOpen');
	});

	// Input Clear
	var iText = $('.item>.iLabel').next('.iText');
	$('.item>.iLabel').css('position','absolute');
	iText
		.focus(function(){
			$(this).prev('.iLabel').css('visibility','hidden');
		})
		.blur(function(){
			if($(this).val() == ''){
				$(this).prev('.iLabel').css('visibility','visible');
			} else {
				$(this).prev('.iLabel').css('visibility','hidden');
			}
		})
		.change(function(){
			if($(this).val() == ''){
				$(this).prev('.iLabel').css('visibility','visible');
			} else {
				$(this).prev('.iLabel').css('visibility','hidden');
			}
		})
		.blur();

	// 개발계획 및 타임라인 토글
	window.button_tr_click = button_tr_click;
	window.button_table_click = button_table_click;
	window.button_table_click_t2 = button_table_click_t2;

	var button_tr = $('button.toggleTr');
	var button_table = $('button.toggleTable');
	var pxeT2_tr = $('table.pxeT2>tbody>tr');
	var pxeT3_tr = $('table.pxeT3>tbody>tr');

	function button_tr_click(element)
	{
		jQuery(element).parents('tr:first').toggleClass('open');	
	}
	
	// 타임라인
	function button_table_click(element){
		if(jQuery(element).toggleClass('all').hasClass('all')){
			jQuery(element).parents('.pxeT3').find('tr').addClass('open');
		} else {
			jQuery(element).parents('.pxeT3').find('tr').removeClass('open');
		}	
	}
	
	// 개발계획
	if($('.pxeT2>tbody>tr').length == $('.pxeT2>tbody>tr.open').length){
		$('.pxeT2 .toggleTable').addClass('all');
	}
	
	function button_table_click_t2(element){
		var pxeT2_tr = jQuery('table.pxeT2>tbody>tr');
		if(jQuery(element).toggleClass('all').hasClass('all')){
			pxeT2_tr.addClass("open");
		} else {
			pxeT2_tr.removeClass("open");
		}
	}

});

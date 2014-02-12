
(function($){
jQuery(function($){

	var NIcon = $('span.HB_Icon');
	var NIconImg = NIcon.find('img[title|="new"]');

	NIconImg.parent().parent().addClass('HBdateAreaA_New');
	NIconImg.parent().parent().children('span').css('display','none');

	$('ul.FBCwidgetTabB').children('li').addClass('FBCTabLiC');
	$('ul.FBCwidgetTabB>li:last-child').addClass('FBCTabLi_last');


});
})(jQuery);

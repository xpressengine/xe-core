jQuery(function($){
	// 목록 마지막 행에 보더 지우기
	$('.board_list tr:last-child>td').css('border','0');
	// 태그 마지막 쉼표 지우기
	$('.read_footer .tags span:last-child').hide();
	// 검색창 토글
	var bs = $('.board_search');
	bs.hide().addClass('off');
	$('.bsToggle').click(function(){
		if(bs.hasClass('off')){
			bs.show().removeClass('off').find('.iText').focus();
		} else {
			bs.hide().addClass('off');
		};
	});
	// 글쓴이 입력창 레이블 토글
	var iText = $('.item .iLabel').next('.iText');
	$('.item .iLabel').css('position','absolute');
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
	// 하위 카테고리 선택시 상위 카테고리 선택 클래스 추가
	$('.cTab>li>ul>li.on_').parents('li:first').addClass('on');
	// 댓글 첫 번째 요소의 margin-top 지우기
	$('.feedback .xe_content>*:first-child').css('margin-top','0');
});

// SNS에 글쓰기
(function($){
	$.fn.snspost = function(opts) {
		var loc = '';
		opts = $.extend({}, {type:'twitter', event:'click', content:''}, opts);
		opts.content = encodeURIComponent(opts.content);
		switch(opts.type) {
			case 'me2day':
				loc = 'http://me2day.net/posts/new?new_post[body]='+opts.content;
				if (opts.tag) loc += '&new_post[tags]='+encodeURIComponent(opts.tag);
				break;
			case 'facebook':
				loc = 'http://www.facebook.com/share.php?t='+opts.content+'&u='+encodeURIComponent(opts.url||location.href);
				break;
			case 'delicious':
				loc = 'http://www.delicious.com/save?v=5&noui&jump=close&url='+encodeURIComponent(opts.url||location.href)+'&title='+opts.content;
				break;
			case 'twitter':
			default:
				loc = 'http://twitter.com/home?status='+opts.content;
				break;
		}
		this.bind(opts.event, function(){
			window.open(loc);
			return false;
		});
	};
	$.snspost = function(selectors, action) {
		$.each(selectors, function(key,val) {
			$(val).snspost( $.extend({}, action, {type:key}) );
		});
	};
})(jQuery);

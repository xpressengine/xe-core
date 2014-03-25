// <![CDATA[
jQuery(function($){
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
	// 패키지 정보 테이블 마지막 라인 지우기
	$('.meta tr:first-child').addClass('first');
	// 표 짝수행 배경색 바꾸기
	$('.rsTable tr:odd').addClass('even');
	
});
// ]]>

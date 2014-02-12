
// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.Orange_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#ffa500"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.Orange_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#ffa500"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.Orange_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#ffa500"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#dadada"
	  }, 500 )
		
	  }
	);
});
})(jQuery);
// ]]> 
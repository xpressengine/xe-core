// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.Blue_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#7e78fd"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.Blue_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#7e78fd"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.Blue_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#7e78fd"
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
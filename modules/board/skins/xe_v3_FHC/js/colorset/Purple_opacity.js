

// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.Purple_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#d790d0"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.Purple_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#d790d0"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.Purple_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#d790d0"
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
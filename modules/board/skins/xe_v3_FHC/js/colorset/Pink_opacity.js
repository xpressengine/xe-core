

// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.Pink_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#ec7386"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.Pink_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#ec7386"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.Pink_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#ec7386"
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
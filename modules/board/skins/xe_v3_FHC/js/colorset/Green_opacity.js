

// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.Green_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#b7d970"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.Green_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#b7d970"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.Green_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#b7d970"
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
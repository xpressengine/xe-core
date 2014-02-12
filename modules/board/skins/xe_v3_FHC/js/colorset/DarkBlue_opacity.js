
// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.DarkBlue_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#98aebd"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.DarkBlue_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#98aebd"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.DarkBlue_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#98aebd"
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
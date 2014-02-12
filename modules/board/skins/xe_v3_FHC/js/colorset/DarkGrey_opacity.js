
// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.DarkGrey_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#dadada"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.DarkGrey_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#dadada"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.DarkGrey_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#bebebe"
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


// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.Dark_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#515151"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.Dark_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#515151"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.Dark_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#515151"
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


// <![CDATA[
(function($){
jQuery(function($){
	 $(".round_colorA>.Teal_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#7edcdf"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#f6f6f6"
	  }, 500 )
		
	  }
	);

	 $(".round_colorB>.Teal_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#7edcdf"
	  }, 800 )
		
	}, 
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#eee"
	  }, 500 )
		
	  }
	);

	 $(".round_colorC>.Teal_li_board").hover(
	  function () {
		$(this).stop(true,true).animate({
		backgroundColor: "#7edcdf"
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
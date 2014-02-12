// <![CDATA[
(function($){
jQuery(function($){
	 $(".HBWrapImg_A").hover(
	  function () {
		$(this).stop(true,true).animate({
		opacity: 0.5
	  }, 800 );
	}, 
	  function () {
		$(this).stop(true,true).animate({
		opacity: 1
	  }, 800 );
	  }
	);
});
})(jQuery);
// ]]>
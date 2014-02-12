// <![CDATA[
jQuery(function($){
	$('.class .widgetA')
		.find('li:eq(1),li:eq(2)')
			.addClass('next')
			.find('.summary').remove();

	$('.widgetNavigator button')
		.hover(
			function(){$(this).addClass('hover')},
			function(){$(this).removeClass('hover')}
		)
		.focus(function(){$(this).mouseover()})
		.blur(function(){$(this).mouseout()});
});
// ]]>

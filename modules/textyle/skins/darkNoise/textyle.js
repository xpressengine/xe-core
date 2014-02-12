/**
 * SNS post jQuery plugin - supports 'twitter', 'me2day', 'facebook', 'del.icio.us'
 * @author NHN (developers@xpressengine.com)
 * @example
 * Twitter
 * $('button.twitter').snspost({
 *    type    : 'twitter',
 *    event   : 'click',
 *    content : 'Hello, world via @xe_team'
 * });
 *
 * Me2day
 * $('a.me2day').snspost({
 *    type    : 'me2day',
 *    event   : 'click',
 *    content : 'Let me to be your friend via @xe_team',
 *    tag     : 'Me2day friend'
 * });
 *
 * Facebook or Delicious
 * $('a.facebook').snspost({
 *    type    : 'facebook',
 *    content : 'Facebook Sharing via @xe_team'
 *    url     : 'http://www.facebook.com/share/'
 * });
 * 
 *
 * Twitter and Facebook
 * $.snspost(
 *    {'twitter':'button.twitter', 'facebook':'input[type=button]'},
 *    { event : 'click', content : 'Multiple buttons and one action via @xe_team', url : 'http://xpressengine.com' }
 */
(function($){

$.fn.snspost = function(opts) {
	var loc = '';

	opts = $.extend({}, {type:'twitter', event:'click', content:''}, opts);
	opts.content = encodeURIComponent(opts.content);

	switch(opts.type) {
		case 'me2day':
			loc = 'http://me2day.net/posts/new?new_post[body]='+opts.content;
			if (opts.tag) loc += '&new_post[tags]='+encodeURIComponent(opts.tag);
			break;
		case 'facebook':
			loc = 'http://www.facebook.com/share.php?t='+opts.content+'&u='+encodeURIComponent(opts.url||location.href);
			break;
		case 'delicious':
			loc = 'http://www.delicious.com/save?v=5&noui&jump=close&url='+encodeURIComponent(opts.url||location.href)+'&title='+opts.content;
			break;
		case 'twitter':
		default:
			loc = 'http://twitter.com/home?status='+opts.content;
			break;
	}

	this.bind(opts.event, function(){
		window.open(loc);
		return false;
	});
};

$.snspost = function(selectors, action) {
	$.each(selectors, function(key,val) {
		$(val).snspost( $.extend({}, action, {type:key}) );
	});
};

})(jQuery);

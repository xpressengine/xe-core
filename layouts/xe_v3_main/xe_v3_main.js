/* jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/ */

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) {
		//alert(jQuery.easing.default);
		return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
	},
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});

// <![CDATA[
jQuery(function($){

	// Global Navigation Bar
	var gMenu = $('.header>div.gnb');
	var gItem = gMenu.find('>ul>li');
	var ggItem = gMenu.find('>ul>li>ul>li');
	var lastEvent = null;
	gItem.find('>ul').hide();
	function gMenuToggle(){
		var t = $(this);
		if (t.next('ul').is(':hidden') || t.next('ul').length == 0) {
			gItem.find('>ul').slideUp(200);
			gItem.find('a').removeClass('hover');
			t.next('ul').slideDown(200);
			t.addClass('hover');			
		}; 
	};
	function gMenuOut(){
		gItem.find('ul').slideUp(200);
		gItem.find('a').removeClass('hover');
	};
	gItem.find('>a').mouseover(gMenuToggle).focus(gMenuToggle);
	gItem.mouseleave(gMenuOut);

	// Notice Anchor Remover
	$('.xev3main .notice .author').removeAttr('href').removeAttr('onclick').attr('class','author');
	
	// Text Rotation
	var $rot = $('div.tRotation'), $btns, $prev, $next, $box, $items, timer, stopped=0, animating=0, current=0, INTERVAL=5000;

	// items
	$box   = $rot.find('>.itembox');
	$items = $box.find('>.item');

	// buttons
	$btns = $rot.prev('.nav').find('button').focus(function(){$box.mouseenter()}).blur(function(){$box.mouseleave()});
	$prev = $btns.filter('.prev')
		.click(function(){
			var _stopped = stopped;
			if (animating || $items.length < 2) return;
			stop();
			animating = true;
			stopped = _stopped;
			var $tmp;
			if (!current) {
				$items.eq(0).before($tmp=$items.eq(-1).clone());
				$box.css('top', parseInt($box.css('top'))-$tmp.height());
			}
			$box.animate(
				{top : '+='+$items.eq(current).height()},
				{
					duration : 'slow',
					complete : function() {				
						animating = false;
						current--;
						if ($tmp) {
							current = $items.length-1;
							$tmp.remove();
							$box.css('top', -$items.get(-1).offsetTop);
						}
						if (!stopped) timer = setTimeout(start, INTERVAL);
					}
				}
			);
		});
	$next = $btns.filter('.next')
		.click(function(){	
			var _stopped = stopped;
			if (animating || $items.length < 2) return;
			stop();
			animating = true;
			stopped = _stopped;
			var $tmp;
			if (current == $items.length-1) $items.eq(-1).after($tmp=$items.eq(0).clone());
			$box.animate(
				{top : '-='+$items.eq(current).height()},
				{
					duration : 'slow',
					complete : function() {
						animating = false;
						current++;
						if ($tmp) {
							current = 0;
							$box.css('top', 0);
							$tmp.remove();
						}
						if (!stopped) timer = setTimeout(start, INTERVAL);
					}
				}
			);
		});
	$box.mouseenter(function(){ stop() }).mouseleave(function(){ stop();stopped=false;timer=setTimeout(start, INTERVAL) });
	$box.find('a').focus(function(){ $box.mouseenter() }).blur(function(){ $box.mouseleave() });
	function stop() {
		stopped = true;
		clearTimeout(timer);
		timer = null;
	}
	function start() {
		stopped = false;
		$next.click();
	}
	// auto start
	timer = setTimeout(start, INTERVAL);
	//

	// Text Rotation Button
	var trBtnPrev = $('.xev3main .mVisual>.nav>.prev');
	function trPrevToggle(){
		var t = $(this);
		if(t.hasClass('up')){
			trBtnPrev.removeClass('up');
		} else if(!t.hasClass('up')){
			trBtnPrev.removeClass('up');
			t.addClass('up');
		}
	};
	var trBtnNext = $('.xev3main .mVisual>.nav>.next');
	function trNextToggle(){
		var t = $(this);
		if(t.hasClass('down')){
			trBtnNext.removeClass('down');
		} else if(!t.hasClass('down')){
			trBtnNext.removeClass('down');
			t.addClass('down');
		}
	};
	trBtnPrev.mouseover(trPrevToggle).mouseout(trPrevToggle);
	trBtnNext.mouseover(trNextToggle).mouseout(trNextToggle);
	trBtnPrev.focus(function(){$(this).addClass('up')});
	trBtnPrev.blur(function(){$(this).removeClass('up')});
	trBtnNext.focus(function(){$(this).addClass('down')});
	trBtnNext.blur(function(){$(this).removeClass('down')});
	
	// Download Button
	var download = $('.download');
	var downCore = $('.download li.core a');
	var downTextyle = $('.download li.textyle a');
	var downModule = $('.download li.module a');
	var downSkin = $('.download li.skin a');
	var downManual = $('.download li.manual a');
	var downBg = $('.download span.bg');
	var downCoreBg = $('.download span.core');
	var downTextyleBg = $('.download span.textyle');
	var downModuleBg = $('.download span.module');
	var downSkinBg = $('.download span.skin');
	var downManualBg = $('.download span.manual');
	function core(){downBg.fadeOut();downCoreBg.fadeIn();$(this).children().animate({color:'#fff',top:'20px',fontSize:'16px'},240,$.easing.easeInOutQuad);};
	function coreOut(){downBg.fadeOut();$(this).children().animate({color:'#bbb',top:'35px',fontSize:'13px'},240,$.easing.easeInOutQuad);};
	function textyle(){downBg.fadeOut(); downTextyleBg.fadeIn();$(this).children().animate({color:'#fff',top:'24px',fontSize:'16px'},240,$.easing.easeInOutQuad);};
	function textyleOut(){downBg.fadeOut();$(this).children().animate({color:'#bbb',top:'35px',fontSize:'13px'},240,$.easing.easeInOutQuad);};
	function module(){downBg.fadeOut(); downModuleBg.fadeIn();$(this).children().animate({color:'#fff',top:'35px',fontSize:'16px'},240,$.easing.easeInOutQuad);};
	function moduleOut(){downBg.fadeOut();$(this).children().animate({color:'#bbb',top:'43px',fontSize:'13px'},240,$.easing.easeInOutQuad);};
	function skin(){downBg.fadeOut(); downSkinBg.fadeIn();$(this).children().animate({color:'#fff',top:'35px',fontSize:'16px'},240,$.easing.easeInOutQuad);};
	function skinOut(){downBg.fadeOut();$(this).children().animate({color:'#bbb',top:'43px',fontSize:'13px'},240,$.easing.easeInOutQuad);};
	function manual(){downBg.fadeOut(); downManualBg.fadeIn();$(this).children().animate({color:'#fff',top:'18px',fontSize:'16px'},240,$.easing.easeInOutQuad);};
	function manualOut(){downBg.fadeOut();$(this).children().animate({color:'#bbb',top:'35px',fontSize:'13px'},240,$.easing.easeInOutQuad);};
	function downOff(){downBg.fadeOut();};
	downCore.mouseover(core).focus(core).mouseout(coreOut).blur(coreOut);
	downTextyle.mouseover(textyle).focus(textyle).mouseout(textyleOut).blur(textyleOut);
	downModule.mouseover(module).focus(module).mouseout(moduleOut).blur(moduleOut);
	downSkin.mouseover(skin).focus(skin).mouseout(skinOut).blur(skinOut);
	downManual.mouseover(manual).focus(manual).mouseout(manualOut).blur(manualOut);
	download.mouseleave(downOff).focusout(downOff);
	
});
// ]]>



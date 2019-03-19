/**
 * @file   common/js/xml_handler.js
 * @brief  XE에서 ajax기능을 이용함에 있어 module, act를 잘 사용하기 위한 자바스크립트
 **/

// xml handler을 이용하는 user function
var show_waiting_message = true;

(function($){
	var x2js = new X2JS();

	/**
	* @brief exec_xml
	* @author NAVER (developers@xpressengine.com)
	**/
	$.exec_xml = window.exec_xml = function(module, act, params, callback_func, response_tags, callback_func_arg, fo_obj) {
		var xml_path = request_uri+"index.php";
		if(!params) params = {};

		// {{{ set parameters
		if($.isArray(params)) params = arr2obj(params);
		params.module = module;
		params.act    = act;

		if(typeof(xeVid)!='undefined') params.vid = xeVid;
		if(typeof(response_tags) == "undefined" || response_tags.length<1) {
			response_tags = ['error','message'];
		} else {
			response_tags.push('error', 'message');
		}
		// }}} set parameters

		// use ssl?
		if ($.isArray(ssl_actions) && params.act && $.inArray(params.act, ssl_actions) >= 0) {
			var url    = default_url || request_uri;
			var port   = window.https_port || 443;
			var _ul    = $('<a>').attr('href', url)[0];
			var target = 'https://' + _ul.hostname.replace(/:\d+$/, '');

			if(port != 443) target += ':'+port;
			if(_ul.pathname[0] != '/') target += '/';

			target += _ul.pathname;
			xml_path = target.replace(/\/$/, '')+'/index.php';
		}

		var _u1 = $('<a>').attr('href', location.href)[0];
		var _u2 = $('<a>').attr('href', xml_path)[0];

		// 현 url과 ajax call 대상 url의 schema 또는 port가 다르면 직접 form 전송
		if(_u1.protocol != _u2.protocol || _u1.port != _u2.port) return send_by_form(xml_path, params);

		var xml = [];
		var xmlHelper = function(params) {
			var stack = [];

			if ($.isArray(params)) {
				$.each(params, function(key, val) {
					stack.push('<value type="array">' + xmlHelper(val) + '</value>');
				});
			}
			else if ($.isPlainObject(params)) {
				$.each(params, function(key, val) {
					stack.push('<' + key + '>' + xmlHelper(val) + '</' + key + '>');
				});
			}
			else if (!$.isFunction(params)) {
					stack.push(String(params).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;'));
			}

			return stack.join('\n');
		};

		xml.push('<?xml version="1.0" encoding="utf-8" ?>');
		xml.push('<methodCall>');
		xml.push('<params>');
		xml.push(xmlHelper(params));
		xml.push('</params>');
		xml.push('</methodCall>');

		var _xhr = null;
		if (_xhr && _xhr.readyState !== 0) _xhr.abort();

		// 전송 성공시
		function onsuccess(data, textStatus, xhr) {
			var resp_xml = $(data).find('response')[0];
			var resp_obj;
			var txt = '';
			var ret = {};
			var tags = {};

			waiting_obj.css('display', 'none').trigger('cancel_confirm');

			if(!resp_xml) {
				alert(_xhr.responseText);
				return null;
			}

			resp_obj = x2js.xml2json(data).response;

			if (typeof(resp_obj)=='undefined') {
				ret.error = -1;
				ret.message = 'Unexpected error occured.';
				try {
					if(typeof(txt=resp_xml.childNodes[0].firstChild.data)!='undefined') {
						ret.message += '\r\n'+txt;
					}
				} catch(e){}

				return ret;
			}

			$.each(response_tags, function(key, val){
				tags[val] = true;
			});
			tags.redirect_url = true;
			tags.act = true;
			$.each(resp_obj, function(key, val){ 
				if(tags[key]) ret[key] = val;
			});

			if(ret.error != '0') {
				if ($.isFunction($.exec_xml.onerror)) {
					return $.exec_xml.onerror(module, act, ret, callback_func, response_tags, callback_func_arg, fo_obj);
				}

				alert( (ret.message || 'An unknown error occured while loading ['+module+'.'+act+']').replace(/\\n/g, '\n') );

				return null;
			}

			if(ret.redirect_url) {
				location.href = ret.redirect_url.replace(/&amp;/g, '&');
				return null;
			}

			if($.isFunction(callback_func)) callback_func(ret, response_tags, callback_func_arg, fo_obj);
		}

		// 모든 xml데이터는 POST방식으로 전송. try-catch문으로 오류 발생시 대처
		try {
			$.ajax({
				url         : xml_path,
				type        : 'POST',
				dataType    : 'xml',
				data        : xml.join('\n'),
				contentType : 'text/plain',
				beforeSend  : function(xhr){ _xhr = xhr; },
				success     : onsuccess,
				error       : function(xhr, textStatus) {
					waiting_obj.css('display', 'none');

					var msg = '';

					if (textStatus == 'parsererror') {
						msg  = 'The result is not valid XML :\n-------------------------------------\n';

						if(xhr.responseText === "") return;

						msg += xhr.responseText.replace(/<[^>]+>/g, '');
					} else {
						msg = textStatus;
					}

					try{
						console.log(msg);
					} catch(ee){}
				}
			});
		} catch(e) {
			alert(e);
			return;
		}

		// ajax 통신중 대기 메세지 출력 (show_waiting_message값을 false로 세팅시 보이지 않음)
		var waiting_obj = $('.wfsr');
		if(show_waiting_message && waiting_obj.length) {

			var timeoutId = $(".wfsr").data('timeout_id');
			if(timeoutId) clearTimeout(timeoutId);
			$(".wfsr").css('opacity', 0.0);
			$(".wfsr").data('timeout_id', setTimeout(function(){
				$(".wfsr").css('opacity', '');
			}, 1000));

			waiting_obj.html(waiting_message).show();
		}
	};

	function send_by_form(url, params) {
		var frame_id = 'xeTmpIframe';
		var form_id  = 'xeVirtualForm';

		if (!$('#'+frame_id).length) {
			$('<iframe name="%id%" id="%id%" style="position:absolute;left:-1px;top:1px;width:1px;height:1px"></iframe>'.replace(/%id%/g, frame_id)).appendTo(document.body);
		}

		$('#'+form_id).remove();
		var form = $('<form id="%id%"></form>'.replace(/%id%/g, form_id)).attr({
			'id'     : form_id,
			'method' : 'post',
			'action' : url,
			'target' : frame_id
		});

		params.xeVirtualRequestMethod = 'xml';
		params.xeRequestURI           = location.href.replace(/#(.*)$/i,'');
		params.xeVirtualRequestUrl    = request_uri;

		$.each(params, function(key, value){
			$('<input type="hidden">').attr('name', key).attr('value', value).appendTo(form);
		});

		form.appendTo(document.body).submit();
	}

	function arr2obj(arr) {
		var ret = {};
		for(var key in arr) {
			if(arr.hasOwnProperty(key)) ret[key] = arr[key];
		}

		return ret;
	}


	/**
	* @brief exec_json (exec_xml와 같은 용도)
	**/
	$.exec_json = window.exec_json = function(action, data, callback_sucess, callback_error){
		if(typeof(data) == 'undefined') data = {};

		action = action.split('.');

		if(action.length == 2) {
			// The cover can be disturbing if it consistently blinks (because ajax call usually takes very short time). So make it invisible for the 1st 0.5 sec and then make it visible.
			var timeoutId = $(".wfsr").data('timeout_id');

			if(timeoutId) clearTimeout(timeoutId);

			$(".wfsr").css('opacity', 0.0);
			$(".wfsr").data('timeout_id', setTimeout(function(){
				$(".wfsr").css('opacity', '');
			}, 1000));

			if(show_waiting_message) $(".wfsr").html(waiting_message).show();

			$.extend(data,{module:action[0],act:action[1]});

			if(typeof(xeVid)!='undefined') $.extend(data,{vid:xeVid});

			try {
				$.ajax({
					type: "POST",
					dataType: "json",
					url: request_uri,
					contentType: "application/json",
					data: $.param(data),
					success: function(data) {
						$(".wfsr").hide().trigger('cancel_confirm');
						if(data.error != '0' && data.error > -1000) {
							if(data.error == -1 && data.message == 'msg_is_not_administrator') {
								alert('You are not logged in as an administrator');
								if($.isFunction(callback_error)) callback_error(data);

								return;
							} else {
								alert(data.message);
								if($.isFunction(callback_error)) callback_error(data);

								return;
							}
						}

						if($.isFunction(callback_sucess)) callback_sucess(data);
					},
					error: function(xhr, textStatus) {
						$(".wfsr").hide();

						var msg = '';

						if (textStatus == 'parsererror') {
							msg  = 'The result is not valid JSON :\n-------------------------------------\n';

							if(xhr.responseText === "") return;

							msg += xhr.responseText.replace(/<[^>]+>/g, '');
						} else {
							msg = textStatus;
						}

						try{
							console.log(msg);
						} catch(ee){}
					}
				});
			} catch(e) {
				alert(e);
				return;
			}
		}
	};

	$.fn.exec_html = function(action,data,type,func,args){
		if(typeof(data) == 'undefined') data = {};
		if(!$.inArray(type, ['html','append','prepend'])) type = 'html';

		var self = $(this);
		action = action.split(".");
		if(action.length == 2){
			var timeoutId = $(".wfsr").data('timeout_id');
			if(timeoutId) clearTimeout(timeoutId);
			$(".wfsr").css('opacity', 0.0);
			$(".wfsr").data('timeout_id', setTimeout(function(){
				$(".wfsr").css('opacity', '');
			}, 1000));
			if(show_waiting_message) $(".wfsr").html(waiting_message).show();

			$.extend(data,{module:action[0],act:action[1]});
			try {
				$.ajax({
					type:"POST",
					dataType:"html",
					url:request_uri,
					data:$.param(data),
					success : function(html){
						$(".wfsr").hide().trigger('cancel_confirm');
						self[type](html);
						if($.isFunction(func)) func(args);
					},
					error: function(xhr, textStatus) {
						$(".wfsr").hide();

						var msg = '';

						if (textStatus == 'parsererror') {
							msg  = 'The result is not valid page :\n-------------------------------------\n';

							if(xhr.responseText === "") return;

							msg += xhr.responseText.replace(/<[^>]+>/g, '');
						} else {
							msg = textStatus;
						}

						try{
							console.log(msg);
						} catch(ee){}
					}

				});

			} catch(e) {
				alert(e);
				return;
			}
		}
	};

	function beforeUnloadHandler(){
	}

	$(function($){
		$(document)
			.ajaxStart(function(){
				$(window).on('beforeunload', beforeUnloadHandler);
			})
			.bind('ajaxStop cancel_confirm', function(){
				$(window).off('beforeunload', beforeUnloadHandler);
			});
	});

})(jQuery);

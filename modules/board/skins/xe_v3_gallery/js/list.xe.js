jQuery(function($){
	// get template
	var $list_body = $('form.list-body'), $tpl = $list_body.find('.container:first').remove(), $paging = $('form.pagination ul'), animating=false;

	function load(page, params) {
		page = parseInt(page, 10) || xe_v3.page;

		if (page == xe_v3.page || animating) return;
		if (typeof params == 'undefined') params = {};

		params.page = page;
		params.mid  = current_mid;
		params.content_cut_size = xe_v3.content_cut_size;
		params.thumbnail_width  = xe_v3.thumbnail_width;
		params.thumbnail_height = xe_v3.thumbnail_height;
		params.thumbnail_type   = xe_v3.thumbnail_type || 'crop';
		params.api_type         = 'summary';

		if (xe_v3.search_keyword && xe_v3.search_target) {
			params.search_target = xe_v3.search_target;
			params.search_keyword = xe_v3.search_keyword;
		}

		show_waiting_message = false;
		$.exec_json('board.dispBoardContentList', params, draw);
		show_waiting_message = true;
	}

	function draw(data) {
		var list, nav, error, i, c, d, e, box_h, current_h, link, item, $fs, $current, $box, $ul, $item, $item_tpl, $fs, member_srls=[], used={};

		if (data.error) {
			alert(data.message);
			return false;
		}

		list = data.document_list;
		nav  = data.page_navigation;
		animating = true;

		// draw list
		$box = $tpl.clone(true).removeClass('hide');
		$ul  = $box.find('>ul');
		$item_tpl = $ul.find('>li:first').remove();
		$current  = $list_body.find('div.container');

		for(i=0,c=xe_v3.list_count; i < c; i++) {
			item  = list[i];
			$item = $item_tpl.clone(true).addClass(i%2?'even':'odd').appendTo($ul);

			if (!item) {
				$item.find('>*').css('visibility', 'hidden');
				continue;
			}

			if (!used['@'+item.member_srl]) {
				member_srls[member_srls.length] = item.member_srl;
                used['@'+item.member_srl] = 1;
			}

			link = request_uri+'index.php?mid='+current_mid+'&document_srl='+item.document_srl+'&page='+nav.cur_page;

			$item
				.find('>.title')
					.prepend(document.createTextNode(item.title))
					.attr('href', link)
					.end()
				.find('>.meta')
					.find('>.reading>em').text(item.readed_count).end()
					.find('>.recommend>em').text(item.voted_count).end()
					.find('>.author>a')
						.attr('class', 'member_'+item.member_srl)
						.text(item.nick_name)
						.end()
					.find('>.reply').text(item.comment_count).end()
					.find('>.date').text(item.regdate.replace(/^([0-9]{4})([0-9]{2})([0-9]{2}).+$/, '$1.$2.$3')).end()
					.find('>.summary').text(item.content);

			if (item.thumbnail_src) $item.find('.thumb>img').attr('src', item.thumbnail_src);
		}

		if (nav.cur_page < xe_v3.page) {
			$list_body.find('legend').after($box);
		} else {
			$list_body.find('>fieldset').append($box);
		}

		d         = 500;
		e         = 'easeOutQuad';
		box_h     = $box.height();
		current_h = $current.height();
		$fs       = $list_body.find('>fieldset');

		if (isNaN(parseInt($fs[0].style.height))) $fs.css('height', current_h);

		$fs.animate({'height':box_h}, {duration:d,easing:e,queue:false});
		$box.css({position:'absolute',width:$current.width()});
		$current.css({position:'absolute',width:$current.width(),top:0});

		if (nav.cur_page < xe_v3.page) {
			$current.animate({'top':'+='+current_h},{duration:d,easing:e,queue:false,complete:function(){ $(this).remove(); animating = false; }});
			$box.css('top', -current_h).animate({'top':'+='+current_h},{duration:d,easing:e,queue:false,complete:function(){ $(this).css({position:'',width:''}); }});
		} else {
			$current.animate({'top':'-='+current_h},{duration:d,easing:e,queue:false,complete:function(){ $(this).remove(); animating = false; }});
			$box.css('top', current_h).animate({'top':'-='+current_h},{duration:d,easing:e,queue:false,complete:function(){ $(this).css({position:'',width:''}); }});
		}

		// get members point to draw level icons
		show_waiting_message = false;
		$.exec_json(
			'point.getMembersPointInfo',
			{member_srls:member_srls.join(',')},
			function(data){
				var i, c, info;

				if (!data || data.error) return;

				for(i=0,c=data.point_info.length; i < c; i++) {
					info = data.point_info[i];

					$ul.find('a.member_'+info.member_srl)
						.prepend('<img src="%request_uri%/modules/point/icons/xe_v3/%level%.gif" alt="[레벨:%level%]" title="포인트:%point%point, 레벨:%level%/30" style="vertical-align:middle;margin-right:3px" />'.replace(/%point%/g, info.point).replace(/%level%/g, info.level).replace(/%request_uri%/g, request_uri));
				}
			}
		);
		show_waiting_message = true;

		// paging
		xe_v3.page = nav.cur_page - 0;
		xe_v3.last_page = nav.total_page;
		$paging.find('>li.pages')
			.find('em').text(nav.total_page).end()
			.find(':text').val(nav.cur_page).end()
			.prev('li.prev')
				.find('>a')
					.each(function(){
						this.href = this.href.replace(/page=[0-9]+/, 'page='+Math.max(xe_v3.page-1, 1));
						this.className = (xe_v3.page < 2)?'':'active';
					})
					.end()
				.prev('li.first')
					.find('>a').each(function(){ this.className = (xe_v3.page < 2)?'':'active' }).end()
					.end()
				.end()
			.next('li.next')
				.find('>a')
					.each(function(){
						this.href = this.href.replace(/page=[0-9]+/, 'page='+Math.min(xe_v3.page+1, nav.total_page));
						this.className = (xe_v3.page >= nav.total_page)?'':'active';
					})
					.end()
				.next('li.last')
					.find('>a')
						.each(function(){
							this.href = this.href.replace(/page=[0-9]+/, 'page='+nav.total_page)
							this.className = (xe_v3.page >= nav.total_page)?'':'active';
						})

		location.hash = '#'+location.hash.substr(1).replace(/&?page=[0-9]+/, '')+'page='+xe_v3.page;
		watch_hash.start(hash_onchange);
	}

	function strip_tags(str) {
		var tt = {nbsp:' ',lt:'<',gt:'>',quot:'"'};

		str = str.replace(/<[^>]+>/g, '');
		str = str.replace(/&([a-z]+);/g, function(all,m1){ return tt[m1]||'' });

		return str;
	}

	function cut_string(str, size) {
		var s, i, c, l;

		for(i=0,l=0,c=str.length; i < c; i++) {
			l += (str.charCodeAt(i) > 127)?2:1;
			if (l >= size) break;
		}

		s = (i<c)?str.substr(0, i):str;
		if (s.length < str.length) s += '...';

		return s;
	}

	function hash_onchange(hash) {
		var vals = location.hash.substr(1).split('&'), i, c, params={};
		for(i=0,c=vals.length; i < c; i++) {
			if (/^([^=]+)=(.+)$/.test(vals[i])) params[RegExp.$1] = RegExp.$2;
		}
		if (!location.hash.length) params.page = 1;
		if (params.page) load(params.page);
	}

	$paging
		.find('>li >a')
			.click(function(){
				var $this = $(this), page = 1;

				if (!$this.hasClass('active')) return false;
				if (/[\?&]page=([0-9]+)/.test($this.attr('href'))) page = RegExp.$1;

				load(page);

				return false;
			})
			.end()
		.find('>.pages :text')
			.keypress(function(event){
				var page;

				if (event.keyCode != 13) return true;

				page = parseInt(this.value, 10) || xe_v3.page;
				page = Math.min(Math.max(page, 1), xe_v3.last_page);

				load(this.value=page);
				return false;
			});

	// toggle search ui
	var $seform = $('#board_search');
	$seform.prev('button').click(function(){
		$seform.toggleClass('_off');
		$seform.hasClass('_off')?$seform.hide():$seform.show().find('.iText').focus();
	});
	if (!$seform.find('input[name=search_keyword]').val()) $seform.prev('button').click();

	// has page?
	hash_onchange(location.hash);

	// watching hash
	var watch_hash = {
		_timer : null,
		_last_hash : '',
		start : function(callback) {
			this.stop();
			this._last_hash = location.hash;
			this.exec(callback);
		},
		exec : function(callback){
			var self = this;

			if (location.hash != this._last_hash) {
				callback(location.hash);
				this._last_hash = location.hash;
			}

			this._timer = setTimeout(function(){ self.exec(callback) }, 200);
		},
		stop : function() {
			clearTimeout(this._timer);
		}
	}

	watch_hash.start();
});

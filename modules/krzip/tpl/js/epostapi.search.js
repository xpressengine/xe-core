/* Copyright (C) XEHub <https://www.xehub.io> */

(function ($) {
	"use strict";

	$.fn.Krzip = function () {
		var $this = $(this);

		var ui = {
			input       : $this.find(".krzip-input"),
			search      : $this.find(".krzip-search"),
			addressList : $this.find(".krzip-addressList")
		};

		/* 자식 요소 이벤트 등록 */
		ui.search.on("click", function (e) {
			exec_json(
				"krzip.getKrzipCodeList",
				{query: ui.input.val()},
				function (response) {
					var address_list = response.address_list;
					$this.data("address_list", address_list);
					var $html = $('ul');
					for(var i = 0; i < address_list.length; i++) {
						var val = address_list[i];
						var $li = $("<li>").data("index", i);
						var text = [];
						text.push('<strong>(' + val[0] + ')</strong>');
						text.push('<strong>' + val[1] + '(' + val[4] + ')</strong>');
						text.push('<br>' + val[2]);
						text.push('<br>' + val[3]);
						$li.html(text.join(' '));

						$html.append($li);
					}
					ui.addressList.append($html.children());
				},
				function (response) {
					$this.data("address_list", "");
					ui.addressList.html("");
				}
			);
		});
		ui.addressList.on("click", "li", function (e) {
			var address_list = $this.data("address_list"),
				address = address_list[$(this).data("index")];
			opener.jQuery(window.name).Krzip("query", address);
			window.close();
		});
		ui.input.on("keydown", function (e) {
			(e.keyCode == 13 && ui.search.trigger("click"));
		});
	};
})(jQuery);

/* End of file epostapi.search.js */
/* Location: ./modules/krzip/tpl/js/epostapi.search.js */

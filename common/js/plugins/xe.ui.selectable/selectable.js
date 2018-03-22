(function($){
	"use strict";

	$.widget( "xe.selectable", $.ui.mouse, {
		version: "1.0.0",
		options: {
		},
		_create: function() {
			var that = this;

			_debug = this.options.debug;
		},
	});

	var _debug = false;
	function dd() {
		if(!_debug || typeof console.debug !== 'function') return;

		arguments[0] = '[$.xe.selectable] ' + arguments[0];

		console.debug.apply(this, arguments);
	}
})(jQuery);

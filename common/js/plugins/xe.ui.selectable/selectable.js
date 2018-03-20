(function($){
	'use strict';

	var _selecting = {};

	$.widget('xe.selectable', $.ui.mouse, {
		version: '1.0.0',
		options: {
			appendTo: 'body',
			autoRefresh: true,
			distance: 0,
			filter: '*',
			tolerance: 'touch',
			toggle: false,

			// Callbacks
			selected: null,
			selecting: null,
			start: null,
			stop: null,
			unselected: null,
			unselecting: null
		},
		_create: function() {
			var that = this;

			_debug = this.options.debug;

			this._addClass('xe-ui-selectable');

			this.dragged = false;

			this.refresh();
			this._mouseInit();
		},
		_init: function() {
		},
		_enable: function() {
			this.refresh();
			this._mouseInit();
		},
		_destroy: function() {
			this.selectees.removeData('xe-selectable-item');
			this._mouseDestroy();
		},
		refresh: function() {
			this.selectees = $(this.options.filter, this.element[0]);
			this._addClass(this.selectees, 'ui-selectee');

			this.selectees.each(function() {
				var $this = $(this);
				$.data(this, 'xe-selectable-item', {
					element: this,
					$element: $this,
					startselected: false,
					selected: $this.hasClass('ui-selected'),
					selecting: $this.hasClass('ui-selecting'),
					unselecting: $this.hasClass('ui-unselecting')
				});
			});
		},
		_mouseStart: function(event) {
			var that = this
			var options = this.options;

			if(this.options.disabled) {
				return;
			}

			dd('_mouseStart()', event);

			this.selectees = $(options.filter, this.element[0]);

			this._trigger('start', event);

			if(options.autoRefresh) {
				this.refresh();
			}

			this.selectees.filter('.ui-selected').each(function() {
				var selectee = $.data(this, 'xe-selectable-item');

				selectee.startselected = true;
				if(!that.options.toggle && !event.metaKey && !event.ctrlKey) {
					that._removeClass(selectee.$element, 'ui-selected');
					selectee.selected = false;
					that._addClass(selectee.$element, 'ui-unselecting');
					selectee.unselecting = true;

					// selectable UNSELECTING callback
					that._trigger('unselecting', event, {
						unselecting: selectee.element
					});
				}
			});

			$(event.target).parents().addBack().each(function() {
				var doSelect = that.options.toggle;
				var selectee = $.data(this, 'xe-selectable-item');

				if(selectee) {
					if(that.options.toggle) {
						doSelect = !selectee.$element.hasClass('ui-selected');
					} else {
						doSelect = (!event.metaKey && !event.ctrlKey) || !selectee.$element.hasClass('ui-selected');
					}

					that._removeClass(selectee.$element, doSelect ? 'ui-unselecting' : 'ui-selected')
						._addClass(selectee.$element, doSelect ? 'ui-selecting' : 'ui-unselecting');
					selectee.unselecting = !doSelect;
					selectee.selecting = doSelect;
					selectee.selected = doSelect;

					// selectable (UN)SELECTING callback
					if(doSelect) {
						that._trigger('selecting', event, {
							selecting: selectee.element
						});
					} else {
						that._trigger('unselecting', event, {
							unselecting: selectee.element
						});
					}
					_selecting.start = selectee.element;

					return false;
				}
			});
		},
		_mouseDrag: function(event) {
			var that = this;
			var selecting = {};

			this.dragged = true;

			if(this.options.disabled) {
				return;
			}

			selecting.start = this.selectees.index($(_selecting.start, this.element[0]));
			selecting.end = this.selectees.index($(event.target).closest('.selectable-item'));

			if(selecting.end < 0) return;
			if(selecting.start > selecting.end) {
				selecting = {
					start: selecting.end,
					end: selecting.start
				};
			}
dd('_mouseDrag()', selecting);
			this.selectees.each(function(idx, el) {
				var selectee = $.data(this, "xe-selectable-item");
				var hit = (idx >= selecting.start && idx <= selecting.end);

				if(hit) {
					// SELECT
					if(selectee.selected) {
						that._removeClass(selectee.$element, "ui-selected");
						selectee.selected = false;
					}
					if(selectee.unselecting) {
						that._removeClass(selectee.$element, "ui-unselecting");
						selectee.unselecting = false;
					}
					if(!selectee.selecting) {
						that._addClass(selectee.$element, "ui-selecting");
						selectee.selecting = true;

						// selectable SELECTING callback
						that._trigger("selecting", event, {
							selecting: selectee.element
						});
					}
				} else {
					// UNSELECT
					if(selectee.selecting) {
						if((that.options.toggle || event.metaKey || event.ctrlKey) && selectee.startselected) {
							that._removeClass(selectee.$element, "ui-selecting");
							selectee.selecting = false;
							that._addClass(selectee.$element, "ui-selected");
							selectee.selected = true;
						} else {
							that._removeClass(selectee.$element, "ui-selecting");
							selectee.selecting = false;

							if(selectee.startselected) {
								that._addClass(selectee.$element, "ui-unselecting");
								selectee.unselecting = true;
							}

							// selectable UNSELECTING callback
							that._trigger("unselecting", event, {
								unselecting: selectee.element
							});
						}
					}
					if(selectee.selected) {
						if(!that.options.toggle && !event.metaKey && !event.ctrlKey && !selectee.startselected) {
							that._removeClass(selectee.$element, "ui-selected");
							selectee.selected = false;

							that._addClass(selectee.$element, "ui-unselecting");
							selectee.unselecting = true;

							// selectable UNSELECTING callback
							that._trigger("unselecting", event, {
								unselecting: selectee.element
							});
						}
					}
				}
			});

			return false;
		},
		_mouseStop: function(event) {
			var that = this;
dd('_mouseStop()');
			this.dragged = false;

			$('.ui-unselecting', this.element[0]).each(function() {
				var selectee = $.data(this, 'xe-selectable-item');

				that._removeClass(selectee.$element, 'ui-unselecting');
				selectee.unselecting = false;
				selectee.startselected = false;

				that._trigger('unselected', event, {
					unselected: selectee.element
				});
			});

			$('.ui-selecting', this.element[0]).each(function() {
				var selectee = $.data(this, 'xe-selectable-item');

				that._removeClass(selectee.$element, 'ui-selecting')
					._addClass(selectee.$element, 'ui-selected');
				selectee.selecting = false;
				selectee.selected = true;
				selectee.startselected = true;

				that._trigger('selected', event, {
					selected: selectee.element
				});
			});

			this._trigger('stop', event);

			return false;
		},
		select: function(targets) {
			var that = this;

			dd('select()', targets);

			targets.each(function(){
				var selectee = $.data(this, "xe-selectable-item");
				dd('selectee', selectee);

				that._addClass(selectee.$element, "ui-selected");
				that._removeClass(selectee.$element, "ui-selecting");
				that._removeClass(selectee.$element, "ui-unselecting");
				selectee.selected = true;
				selectee.selecting = false;
				selectee.unselecting = false;
				selectee.startselected = false;
			});
			that._trigger('selected', null, targets);
		},
		selectAll: function() {
			var that = this;
			this.selectees.each(function() {
				var selectee = $.data(this, "xe-selectable-item");

				that._addClass(selectee.$element, "ui-selected");
				that._removeClass(selectee.$element, "ui-selecting");
				that._removeClass(selectee.$element, "ui-unselecting");
				selectee.selected = true;
				selectee.selecting = false;
				selectee.unselecting = false;
				selectee.startselected = false;
			});
			that._trigger('selected', null, this.selectees);
		},
		getSelected: function() {
			return $('.ui-selected', this.element[0]);
		},
		getSelectedNodes: function() {
			return this.getSelected().toArray();
		},
		unselect: function(targets) {
			var that = this;
			dd('unselect()', targets);

			targets.each(function() {
				var selectee = $.data(this, "xe-selectable-item");

				that._removeClass(selectee.$element, "ui-selected");
				that._removeClass(selectee.$element, "ui-selecting");
				that._removeClass(selectee.$element, "ui-unselecting");
				selectee.selected = false;
				selectee.selecting = false;
				selectee.unselecting = false;
				selectee.startselected = false;

			});
			that._trigger('unselected', null, targets);
		},
		unselectAll: function() {
			var that = this;
			this.selectees.each(function() {
				var selectee = $.data(this, "xe-selectable-item");

				that._removeClass(selectee.$element, "ui-selected");
				that._removeClass(selectee.$element, "ui-selecting");
				that._removeClass(selectee.$element, "ui-unselecting");
				selectee.selected = false;
				selectee.selecting = false;
				selectee.unselecting = false;
				selectee.startselected = false;
			});
			that._trigger('unselected', null, this.selectees);
		}
	});

	var _debug = false;
	function dd() {
		if(!_debug || typeof console.debug !== 'function') return;

		arguments[0] = '[$.xe.selectable] ' + arguments[0];

		console.debug.apply(this, arguments);
	}
})(jQuery);

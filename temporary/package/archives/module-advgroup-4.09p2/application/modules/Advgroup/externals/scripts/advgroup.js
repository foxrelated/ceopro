
	(function($,$$){
		var events;
		var check = function(e){
			var target = $(e.target);
			var parents = target.getParents();
			events.each(function(item){
				var element = item.element;
				if (element != target && !parents.contains(element))
					item.fn.call(element, e);
			});
		};
		Element.Events.outerClick = {
			onAdd: function(fn){
				if(!events) {
					document.addEvent('click', check);
					events = [];
				}
				events.push({element: this, fn: fn});
			},
			onRemove: function(fn){
				events = events.filter(function(item){
					return item.element != this || item.fn != fn;
				}, this);
				if (!events.length) {
					document.removeEvent('click', check);
					events = null;
				}
			}
		};
	})(document.id,$$);

	function ynadvgroupOptions() {
		$$('.ynadvgroup-options-button').removeEvents('click').addEvent('click', function (event) {
			var parent = this.getParent(),
				items = this.getSiblings('.ynadvgroup_dropdown_items');
			this.toggleClass('ynadvgroup_explained');
			parent.toggleClass('ynadvgroup_show');
			items.setStyle('display', items.getStyle('display') == 'block' ? 'none' : 'block');

			var popup = this.getParent('.ynadvgroup-options');
			window.setTimeout(function(){
				var layout_parent = popup.getParent('.ynadvgroup_photo_view') || popup.getParent('.layout_middle');
			var y_position = popup.getPosition(layout_parent).y;
			var p_height = layout_parent.getHeight();
			var c_height = popup.getElement('.ynadvgroup_dropdown_items').getHeight();
			if (parent.hasClass('ynadvgroup_show')) {
				if (p_height - y_position < (c_height + 60)) {
					layout_parent.addClass('popup-padding-bottom');
					var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace(/\D+/g, ''));
					layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 60 + y_position - p_height) + 'px');
				}
			} else {
				layout_parent.setStyle('padding-bottom', '0');
			}
			}, 20);
		});

		$$('.ynadvgroup-options-button').addEvent('outerClick', function () {
			if (!this.hasClass('ynadvgroup_explained'))
				return;
			var popup = this.getParent('.ynadvgroup-options');
			var items = this.getSiblings('.ynadvgroup_dropdown_items');
			this.removeClass('ynadvgroup_explained');
			popup.removeClass('ynadvgroup_show');
			items.setStyle('display', 'none');
		});
	}


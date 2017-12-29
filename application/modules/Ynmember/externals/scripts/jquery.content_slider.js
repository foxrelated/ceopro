/*
All Around Slider 1.0.4

Copyright (c) 2013 Br0 (www.shindiristudio.com)

jQuery project site: http://codecanyon.net/item/all-around-jquery-content-slider-carousel/4809047
WordPress project site: http://codecanyon.net/item/all-around-wordpress-content-slider-carousel/5266981
*/

// ------------------- slider items -----------------------
(function($) {

	function content_slider_child(element, options) {
		this._constructor(element, options, 0);
	};
	content_slider_child.prototype = {
		$: function (selector) {
			return this.$element.find(selector);
		},
		_constructor: function (element, options, dinamic) {
			var self = this;
			this.$element = $(element);
			this.$base = this.$element;
			this.$parent = options.$parent;
			this.options = options;
			this.n = options.n;
			this.parent_this = options.parent_this;
			this.have_element=1;
			this.$image = $('img',this.$element);
			this.$border_div = $('div.'+this.options.border_class,this.$element);
			this.image_src = this.$image.attr ('src');
			this.turn_counter = 0;
			this.last_mouse_x = 0;
			this.show_mouse_move = 0;
			this.sum_movement = 0;
			this.mouse_in_animation=0;
			this.hover_status=0;
			this.mouse_out_animation=0;
			this.positions=0;
			this.max = this.parent_this.max_show;

			this.marg_left = Math.floor((this.options.big_pic_width - this.options.small_pic_width)/2);
			this.marg_top = Math.floor((this.options.big_pic_height - this.options.small_pic_height)/2);

			this.$element.mousedown ($.proxy(this._mouse_down, this));
			this.$element.mouseup ($.proxy(this._mouse_up, this));
			this.$element.mouseleave ($.proxy(this._mouse_leave, this));
			this.$element.mousemove ($.proxy(this._mouse_move, this));
			this.$image.mousedown ($.proxy(this._mouse_down, this));
			this.$image.mouseup ($.proxy(this._mouse_up, this));
		},
		_set_img: function (img) {
			this.image_src = img;
			this.$image.attr('src', this.options.no_image_path );

			// set background image
			this.$image.attr('style', 'background-image: url('+img+' );');
		},
		_set_pos_size : function (new_position,pos_speed,new_top,new_width,new_height,border,method,call_back)
		{
			var border_radius;
			var border_color=this.options.border_color;
			if (this.options.border_on_off==0) border=0;
			this.current_border=border;
			if (!method)
			{
				if (this.options.border_radius==-1) border_radius=new_width;
				else {
					//var percent=(new_width-this.options.small_pic_width)/((this.options.big_pic_width-this.options.small_pic_width)/100);
					if (this.options.radius_proportion) {
						var radius_proportion=this.options.big_pic_width/this.options.border_radius;
						var lower_radius=new_width/radius_proportion;
						border_radius=lower_radius;
					} else {
						border_radius=this.options.border_radius;
					}
				}
				if (this.parent_this.options.hv_switch) {
					if (this.options.activate_border_div) {
						this.$element.css ({"left": new_top,'top': new_position,'width' : new_width ,'height' : new_height,'border-radius':border_radius,'border': border_color+' solid 0px'});
						this.$border_div.css ({'width' : (new_width+2), 'height': (new_height+2), 'border-radius':border_radius,'border':border_color+' solid '+border+'px'});
					} else {
						this.$element.css ({"left": new_top,'top': new_position,'width' : new_width ,'height' : new_height,'border-radius':border_radius,'border':border_color+' solid '+border+'px'});
					}
				} else {
					if (this.options.activate_border_div) {
						this.$element.css ({"left": new_position,'top': new_top, 'width': new_width ,'height' : new_height, 'border-radius':border_radius,'border':border_color+' solid 0px'});
						this.$border_div.css ({'width': (new_width+2), 'height': (new_height+2), 'border-radius':border_radius,'border':border_color+' solid '+border+'px'});
					} else {
						this.$element.css ({"left": new_position,'top': new_top,'width' : new_width ,'height' : new_height,'border-radius':border_radius,'border':border_color+' solid '+border+'px'});
					}
				}
				this.$image.css ({'width' : new_width ,'height' : new_height,'border-radius':border_radius});
			} else {
				if (this.options.border_radius==-1) border_radius=this.parent_this.options.big_pic_width;
				else {
					if (this.options.radius_proportion) {
						var radius_proportion=this.options.big_pic_width/this.options.border_radius;
						var lower_radius=new_width/radius_proportion;
						border_radius=lower_radius;
					} else {
						border_radius=this.options.border_radius;
					}
				}
				if (this.options.activate_border_div) {
					this.$element.css ({'border-radius':border_radius+'px'});
					this.$border_div.css ({'border-radius':border_radius+'px'});
				} else {
					this.$element.css ({'border-radius':border_radius+'px'});
				}
				this.$image.css ({'border-radius':border_radius+'px'});
				if (this.parent_this.options.hv_switch)
				{
					if (this.options.activate_border_div) {
						this.$element.animate ({"left": new_top,'top': new_position, 'width': new_width, 'height': new_height, 'border-width':'0px'},pos_speed,this.options.moving_easing, call_back);
						this.$border_div.animate ({'width': (new_width+2), 'height': (new_height+2), 'border-width':border+'px'},pos_speed,this.options.moving_easing);
					} else {
						this.$element.animate ({"left": new_top,'top': new_position,'width' : new_width ,'height' : new_height, 'border-width':border+'px'},pos_speed,this.options.moving_easing, call_back);
					}
					this.$image.animate ({'width' : new_height ,'height' : new_width},pos_speed,this.options.arrow_easing,call_back);
				}
				else
				{
					if (this.options.activate_border_div) {
						this.$element.animate ({"left": new_position, 'top': new_top, 'width': new_width, 'height': new_height, 'border-width':'0px'}, pos_speed,this.options.moving_easing, call_back);
						this.$border_div.animate ({'width': (new_width+2), 'height': (new_height+2), 'border-width':border+'px'}, pos_speed,this.options.moving_easing);
					} else {
						this.$element.animate ({"left": new_position,'top': new_top,'width' : new_width ,'height' : new_height, 'border-width':border+'px'}, pos_speed,this.options.moving_easing, call_back);					
					}
					this.$image.animate ({'width' : new_width ,'height' : new_height},pos_speed,this.options.arrow_easing,call_back);
				}
			}
		},
		_mouse_down : function (e) {
			e.preventDefault ();
			if (this.options.hv_switch)
				var current_x = e.pageY-this.parent_this.y_offset;
			else
				var current_x = (e.pageX-this.parent_this.x_offset)+this.parent_this.minus;
			var c=this.parent_this.math._convert_x_position_to_n (current_x);
			if (c.master_click==1) return;
			this._mouse_leave(e);
		},
		_mouse_leave : function (e) {
			e.preventDefault ();
			if (this.options.hover_movement==0 || this.parent_this.show_mouse_move==1 || this.parent_this.slider_state==1) return;
			if (this.mouse_out_animation==1 || this.hover_status==0) return;

			if (this.mouse_in_animation==1) {
				this.$element.stop();
				this.$image.stop();
				if (this.options.activate_border_div) this.$border_div.stop();
				this.mouse_in_animation=0;
			}
			if (this.element_top<1) {
				this.hover_status=0;
				this.mouse_in_animation=0;
				this.mouse_out_animation=0;
				return;
			}
			this.hover_status=1;
			this.mouse_out_animation=1;
			this._end_hover2();
		},
		_end_hover2: function() {
					this.$element.animate ({
				'left': this.element_left+"px", 
				'top': this.element_top+"px",
				'width': this.element_width+"px", 
				'height': this.element_height+"px"},
				this.options.hover_speed, this.options.hover_easing, $.proxy(this._hover_ended2, this));
			if (this.options.activate_border_div) {
				this.$border_div.animate ({
					'width': (this.element_width+2)+"px", 
					'height': (this.element_height+2)+"px"},
					this.options.hover_speed, this.options.hover_easing);
			}
			this.$image.animate ({
				'width': this.image_width+"px", 
				'height': this.image_height+"px"},
				this.options.hover_speed, this.options.hover_easing);
		},
		_hover_ended2: function() {
			this.hover_status=0;
			this.mouse_out_animation=0;
		},
		_mouse_move : function (e)
		{
			e.preventDefault ();
			if (this.options.hover_movement==0 || this.parent_this.show_mouse_move==1 || this.parent_this.slider_state==1) return;
			if (this.mouse_in_animation==1 || this.hover_status==2) return;

			if (this.mouse_out_animation==1) {
				this.$element.stop();
				this.$image.stop();
				if (this.options.activate_border_div) this.$border_div.stop();
				this.mouse_out_animation=0;
			}

			if (this.options.hv_switch)
				var current_x = e.pageY-this.parent_this.y_offset;
			else
				var current_x = (e.pageX-this.parent_this.x_offset)+this.parent_this.minus;

			var c=this.parent_this.math._convert_x_position_to_n (current_x);
			if (c.master_click==1) return;
			this.hover_status=1;
			this.mouse_in_animation=1;
			this._start_hover();
		},
		_calculate_hovers: function() {
			this.positions=1;
			hover_movement_middle=Math.floor(this.options.hover_movement/2);
			hover_movement=this.options.hover_movement;
			hover_movement2=hover_movement*2;
			var pos=this.$element.position();
			pos2=this.$image.position();
			this.element_top=pos.top;
			this.element_left=pos.left;
			this.element_width=this.$element.width();
			this.element_height=this.$element.height();
			this.image_top=pos2.top;
			this.image_left=pos2.left;
			this.image_height=this.$image.height();
			this.image_width=this.$image.width();
			this.element_top_middle=this.element_top-hover_movement_middle;
			this.element_left_middle=this.element_left-hover_movement_middle;
			this.element_width_middle=this.element_width+hover_movement;
			this.element_height_middle=this.element_height+hover_movement;
			this.image_width_middle=this.image_width+hover_movement;
			this.image_height_middle=this.image_height+hover_movement;
			this.element_top_end=this.element_top-hover_movement;
			this.element_left_end=this.element_left-hover_movement;
			this.element_width_end=this.element_width+hover_movement2;
			this.element_height_end=this.element_height+hover_movement2;
			this.image_width_end=this.image_width+hover_movement2;
			this.image_height_end=this.image_height+hover_movement2;
		},
		_start_hover: function() {
			if (this.positions==0) {
				this._calculate_hovers();
			}
			if (this.element_top<3) {
				this.hover_status=0;
				this.mouse_in_animation=0;
				this.mouse_out_animation=0;
				return;
			}
			this.$element.animate ({
				'left': this.element_left_end+"px", 
				'top': this.element_top_end+"px",
				'width': this.element_width_end+"px", 
				'height': this.element_height_end+"px"},
				this.options.hover_speed, this.options.hover_easing, $.proxy(this._end_hover, this));
			if (this.options.activate_border_div) {
				this.$border_div.animate ({
					'width': (this.element_width_end+2)+"px", 
					'height': (this.element_height_end+2)+"px"},
					this.options.hover_speed, this.options.hover_easing);
			}
			this.$image.animate ({
				'width': this.image_width_end+"px", 
				'height': this.image_height_end+"px"},
				this.options.hover_speed, this.options.hover_easing);
		},
		_end_hover: function() {
			this.$element.animate ({
				'left': this.element_left_middle+"px", 
				'top': this.element_top_middle+"px",
				'width': this.element_width_middle+"px", 
				'height': this.element_height_middle+"px"},
				this.options.hover_speed, this.options.hover_easing, $.proxy(this._hover_ended, this));
			if (this.options.activate_border_div) {
				this.$border_div.animate ({
					'width': (this.element_width_middle+2)+"px", 
					'height': (this.element_height_middle+2)+"px"},
					this.options.hover_speed, this.options.hover_easing);
			}
			this.$image.animate ({
				'width': this.image_width_middle+"px", 
				'height': this.image_height_middle+"px"},
				this.options.hover_speed, this.options.hover_easing);
		},
		_hover_ended: function() {
			this.hover_status=2;
			this.mouse_in_animation=0;
		},
		reset_positions: function() {
			if (this.positions==0) return;
			if (this.mouse_in_animation==1 || this.mouse_out_animation==1) {
				this.$element.stop();
				this.$image.stop();
				if (this.options.activate_border_div) this.$border_div.stop();
			}
			if (this.parent_this.mouse_moved==0) {
				this.$element.css ({
					'left': this.element_left+"px", 
					'top': this.element_top+"px",
					'width': this.element_width+"px", 
					'height': this.element_height+"px"}
				);
				if (this.options.activate_border_div) {
					this.$border_div.css ({
						'width': (this.element_width+2)+"px", 
						'height': (this.element_height+2)+"px"}
					);
				}
				this.$image.css ({
					'width': this.image_width+"px", 
					'height': this.image_height+"px"}
				);
			}
			this.positions=0;
			this.mouse_in_animation=0;
			this.hover_status=0;
			this.mouse_out_animation=0;
		}
	};
// ----------------- Content Slider main class ---------------------------
	function content_slider(element, options) {
		var self = this;
		this.$element = $(element);
		this.$base = this.$element;
		this.$element.wrap('<div class="main_content_slider_wrapper">');
		this.$parent_wrapper=this.$element.parent();
		
		this.options = $.extend({}, $.fn.content_slider.defaults, options);
		
		
		if (this.options.border_on_off==0) {
			this.options.arrow_width=this.options.small_arrow_width;
			this.options.arrow_height=this.options.small_arrow_height;
			this.options.activate_border_div=0;
			this.options.use_thin_arrows=0;
		}
		if (this.options.use_thin_arrows==1) {
			this.options.arrow_width=this.options.small_arrow_width;
			this.options.arrow_height=this.options.small_arrow_height;
		}
		if (this.options.activate_border_div==1) {
			this.options.small_pic_width+=this.options.small_border*2;
			this.options.small_pic_height+=this.options.small_border*2;
			this.options.big_pic_width+=this.options.big_border*2;
			this.options.big_pic_height+=this.options.big_border*2;
			this.options.small_border+=1;
			this.options.big_border+=1;
		}

		this.lock = 0;
		this.lock2 = 0;
		this.click = 0;
		this.keep_going = 0;
		this.going_counter = 0;
		this.sum_movement = 0;
		this.is_auto_play=0;
		this.dismiss_auto_play=0;
		if (this.options.hv_switch)
			this.last_mouse_x = this.options.y_offset;
		else
			this.last_mouse_x = 0;			
		this.show_mouse_move = 0;
		this.max_show = this.options.max_shown_items+2;
		this.anim_counter = 0;
		this.func = this.go_right;
		this.arrow_hidden_counter = 0;
		this.clicked = 0;
		this.speed = this.options.moving_speed;
		this.mid_elem = Math.floor (this.options.max_shown_items/2);
		this.max_pos = 3;
		this.opration = 0;
		this.offset = 0;
		this.was_gone = 0;
		this.number_of_items=0;
		this.slider_state=0;
		this.prettyPhoto_status=0;
		this.mouse_in_animation=0;
		this.hover_status=0;
		this.mouse_out_animation=0;
		this.minus=0;
		this.real_width=0;
		this.last_resolution_mode=0;
		this.last_resolution=0;
		this.under_600=0;
		this.mouse_state=0;
		this.mouse_moved=0;
		this.ignore_click_up=0;
		this.ignore_click_down=0;
		var poss = this.$element.offset();
		this.x_offset=poss.left;
		this.y_offset=poss.top;
		poss = this.$parent_wrapper.offset();
		this.parent_x_offset=poss.left;
		this.last_c={'pos': 0, 'master_click': 1};
		this.touch_event=0;
		this.is_touch_device = 'ontouchstart' in document.documentElement;
		this.last_height=this.options.wrapper_text_max_height;

		if (!this.options.top_offset)
			this.options.top_offset = Math.floor (this.options.big_pic_height/2) + this.options.big_border+1;

		this.math = new position (	this.options.map.length,
								this.options.max_shown_items,
								this.mid_elem,
								this.options.active_item - this.mid_elem - 1,
								0,
								this.options.child_div_width,
								this.options.big_pic_width,
								this.options.small_pic_width,
								this.options.small_pic_height,
								this.options.big_pic_width,
								this.options.big_pic_height,
								this.options.top_offset,
								this.options.small_border,
								this.options.big_border,
								this.options.arrow_width,
								this.options.arrow_height,
								this.options.container_class_padding,
								this.options.mode,
								this,
								this.options.left_offset);

		if (this.options.hv_switch==0) {
			var max_pos_general = this.math._calculate_child_coordinates_by_n (this.max_show-1,0);
			this.max_width=max_pos_general.new_pos+this.options.left_offset;
		} else {
			this.max_width=this.options.wrapper_text_max_height;
		}
		this.$parent_wrapper.css({'max-width': this.max_width+'px'});
		this.create_html ();

		this.$prettyPhoto_div = $('div.image_more_info', this.$base);
		this.$prettyPhoto_a = $('a', this.$prettyPhoto_div);
		this.$prettyPhoto_a.on('mousedown',$.proxy(this._prettyPhoto_clicked, this));
		this.$prettyPhoto_a.on('touchstart',$.proxy(this._prettyPhoto_clicked, this));
		this.$prettyPhoto_img = $('img', this.$prettyPhoto_div);
		this.$prettyPhoto_img.css('padding', '0px');
		if (this.options.allow_shadow==0) {
			this.$prettyPhoto_div.css('box-shadow', '0px 0px 0px #fff');
		}

		this.$items = $('div.'+this.options.picture_class, this.$base);
		if (this.options.allow_shadow==0) {
			this.$items.css ({
				'-moz-box-shadow': '0px 0px 0px #fff',
				'-webkit-box-shadow': '0px 0px 0px #fff',
				'box-shadow': '0px 0px 0px #fff'
			});
		}
		this.$left_arrow_class = $(this.options.left_arrow_class,this.$element);
		this.$right_arrow_class = $(this.options.right_arrow_class,this.$element);
		if (this.options.border_on_off==0 || this.options.use_thin_arrows==1) {
			this.$left_arrow_class.addClass('circle_slider_no_border');
			this.$right_arrow_class.addClass('circle_slider_no_border');
		}
		if (this.options.use_thin_arrows==1) {
			this.$left_arrow_class.addClass('circle_slider_no_border2_left');
		}
		this.$left_arrow = $(this.options.left_arrow_class+' img',this.$element);
		this.$right_arrow = $(this.options.right_arrow_class+' img',this.$element);
		if (this.options.border_on_off==1) {
			this.$left_arrow.css('background', this.options.arrow_color);
			this.$right_arrow.css('background', this.options.arrow_color);
		}
		if (this.options.border_on_off==0 || this.options.use_thin_arrows==1) {
			if (this.options.hv_switch==0) {
				this.$left_arrow.css({
					'z-index': '1000',
					'margin-top': '15px'
					});
				this.$right_arrow.css({
					'z-index': '1000',
					'margin-top': '15px'			
				});
			} else {
				this.$left_arrow.css({
					'z-index': '1000',
					'margin-left': '15px'
					});
				this.$right_arrow.css({
					'z-index': '1000',
					'margin-left': '15px'			
				});			
			}
		}
		this._set_arrows_events ();

		var n=0;
		this.items=new Array();
		$.each( this.$items, function(i, element) {
			self.items[n] = new content_slider_child(element, $.extend(self.options, {$parent: self.$element, parent_this: self, n:n }) );
			n++;
		});
		this.number_of_items=n;
		this._preset_all_children_parameters (0);
		
		this._align_arrows ();
		this.last_middle=this.math._convert_position_to_image_array(0,this.mid_elem);
		this._set_parent_window_size ();
		this._return_container_width_height ();
		this.mid = this._return_middle_position_of_content ();
		this.slider_text = $('.'+this.options.left_text_class, this.$element);
		this.max_size = Math.floor((this.options.wrapper_text_max_height - this.ret_values.height - 45)/2);
		this.orig_max_size = this.max_size;

		if (this.options.max_shown_items>1 && this.options.hv_switch==0) {
			if (this.options.border_on_off==1) $(this.options.text_object, this.$element).css('width', (this.max_width-16)+'px');
			else $(this.options.text_object, this.$element).css('width', (this.max_width-22)+'px');
		}

		$(window).resize($.proxy(this._resize, this));
		this._resize();

		var ofst = this.$container.offset ();
		if (this.options.hv_switch)
			this.offset = ofst.top;
		else
			this.offset = ofst.left+this.minus;

		if (this.options.hv_switch)
			this._set_text_div_width_ver();
		else
			this._set_text_div_width_hor();

		this.show_text (this.math._convert_position_to_image_array(0,this.mid_elem));
		

		this._set_prettyPhoto_div_position();

		if (this.options.enable_mousewheel==1) {
			this.$container.bind('mousewheel', function(event, delta, deltaX, deltaY) {
				event.preventDefault();
				if (delta==-1) self.public_go_left();
				else self.public_go_right();
			});
		}
		if (this.options.auto_play) this.start_auto_play();
		if (this.is_touch_device) this._start_main_hover();
		
		$(window).on('keydown', $.proxy(this.keypress, this));
		
		if (this.options.max_shown_items==1 && this.options.hv_switch==0) {
			this.$container.css('left', '3px');
		}
		if (this.options.hv_switch==0 && this.options.border_on_off==1 && this.options.use_thin_arrows==1) {
			this.$left_arrow.css('margin-left', '0px');
		}
	};

	content_slider.prototype = {
		$: function (selector) {
			return this.$element.find(selector);
		},
		keypress: function(e) {
			if (this.options.bind_arrow_keys) {
				if (e.keyCode==39) this.public_go_left();
				if (e.keyCode==37) this.public_go_right();
			}
		},
		_prettyPhoto_clicked: function(e) {
			this.ignore_click_up=1;
			this.ignore_click_down=1;
		},
		public_go_left: function(check_autoplay) {
			if (typeof check_autoplay=='undefined') check_autoplay=0;
			if (check_autoplay==1 && this.is_auto_play==1 && this.dismiss_auto_play==1) return;
			if (this.slider_state==0) {
				this._stop_children();
				this.slider_state=1;
				this._arrow_mouse_down();
				this._arrow_mouse_up();
				this.left_clicked();
			}
		},
		public_go_right: function(check_autoplay) {
			if (typeof check_autoplay=='undefined') check_autoplay=0;
			if (check_autoplay==1 && this.is_auto_play==1 && this.dismiss_auto_play==1) return;
			if (this.slider_state==0) {
				this._stop_children();
				this.slider_state=1;
				this._arrow_mouse_down();
				this._arrow_mouse_up();
				this.right_clicked();
			}
		},
		check_under_600: function(ww) {
			if (this.under_600==0 && ww<600) {
				this.under_600=1;
				this.height_backup=this.$element.height();
				this.$element.css({'height': ''});
				$(this.options.text_object, this.$element).css({'float': '', 'top': '0px', 'left': '0px', 'clear': 'both'});
				if (this.options.small_resolution_max_height) this.$parent_wrapper.css({'height': this.options.small_resolution_max_height});
			}
			if (this.under_600==1 && ww>=600) {
				this.under_600=0;
				this.$element.css({'height': this.height_backup});
				$(this.options.text_object, this.$element).css({'float': 'left', 'clear': ''});
				if (this.options.small_resolution_max_height) this.$parent_wrapper.css({'height': ''});
			}		
		},
		get_window_width: function() {
			if (this.options.responsive_by_available_space==1) {
				var wd=this.$parent_wrapper.parent().width();
				return wd;
			} else {
				// return $(window).width();
				// get width of widget element
				return $('.layout_ynmember_featured_members').width();
			}
		},
		_resize: function() {
			var ww=this.get_window_width();
			if (this.last_resolution==ww) return;
			var poss = $(this.$element).offset();
			this.x_offset=poss.left;
			this.y_offset=poss.top;
			poss = this.$parent_wrapper.offset();
			this.parent_x_offset=poss.left;
			var ofst = this.$container.offset ();
			if (this.options.hv_switch) {
				this.offset = ofst.top;
			} else {
				this.offset = ofst.left+this.minus;
			}
			if (ww<this.options.big_pic_width+59) {
				var backup_ww=ww;
				x_ww=this.options.big_pic_width+59;
				var ww_r=x_ww-backup_ww;
				var ww_percent=(backup_ww/(x_ww/100));
				if (backup_ww<195) ww_percent-=4;
				ww_percent=ww_percent/100;
				this.$container.css('transform', 'scale('+ww_percent+', '+ww_percent+')');
				if (this.options.hv_switch==1) this.$container.css('left', '-'+(ww_r/3)+'px');
				$('.circle_slider_text_wrapper', this.$element).css('top', '-'+(ww_r/2)+'px');
				//console.log('percent='+ww_percent);
			} else {
				this.$container.css('transform', 'scale(1,1)');			
				if (this.options.hv_switch==1) this.$container.css('left', '0px');
				$('.circle_slider_text_wrapper', this.$element).css('top', '0px');
			}
			//if (this.options.max_shown_items==1) ww=this.options.big_pic_width+59; // 296
			this.last_resolution=ww;
			if (this.options.hv_switch) {
				this.check_under_600(ww);
				if (this.options.wrapper_text_max_height+13>=ww) {
					this.max_size = Math.floor((ww - this.ret_values.height - 45)/2)-5;
					if (this.under_600==1) {
						if (this.options.vert_text_mode==1) this.max_size=Math.floor(this.options.big_pic_width/2); 
						else this.max_size=this.options.child_div_width;
					}
					this._set_parent_window_size(1, ww);
					this._set_text_div_width_ver();
					this.show_text (this.last_middle, 1);
					this.last_resolution_mode=2;
				} else {
					if (this.last_resolution_mode==2) {
						this.max_size = this.orig_max_size;
						this._set_parent_window_size(1, this.options.wrapper_text_max_height);
						this._set_text_div_width_ver();
						this.show_text (this.last_middle, 1, 1);
					}
					this.last_resolution_mode=1;
				}
				return;
			} else {
				var sw=this.real_width;
				var $text_element = $(this.options.text_field_id_prefix + this.last_middle,this.$element);
				if (sw+13>=ww) {
					if (this.options.small_resolution_max_height) this.$parent_wrapper.css({'height': this.options.small_resolution_max_height});
					var r = (sw+13)-ww;
					var minus2=r;
					r=Math.floor(r/2)-8;
					this.minus=r;
					this.$element.css({'left': '-'+r+'px'});
					$block=$('div.content_slider_text_block_wrap', $text_element);
					if ($block.length) {
						if (typeof this.last_text_width == 'undefined') this.last_text_width=$text_element.width();
						$text_element.css('width', (ww-10)+'px');
					}
					this._set_text_div_width_hor();
					this._set_parent_window_size(1,ww-10);
					this.last_resolution_mode=2;
				} else {
					if (this.last_resolution_mode==2) {
						if (this.options.small_resolution_max_height) this.$parent_wrapper.css({'height': ''});
						this.minus=0;
						this.$element.css({'left': '0px'});				
						this._set_text_div_width_hor();
						this._set_parent_window_size(1, this.real_width);
						if (typeof this.last_text_width == 'undefined') this.last_text_width=this.real_width-5;
						$text_element.css('width', this.last_text_width+'px');
					}
					this.last_resolution_mode=1;
				}
			}
		},
		_set_text_div_width_hor: function() {
			$text_element = $(this.options.text_object,this.$element);
			var minus=0;
			var mid=this.mid;
			var ww=this.get_window_width();
			//if (this.options.max_shown_items==1) ww=this.options.big_pic_width+59;
			if (this.minus>0) mid=Math.floor(ww/2)-5;
			if (this.options.activate_border_div==1) minus=Math.floor(this.options.big_border/2);
			var minus2=0;
			if (this.options.max_shown_items>1 && this.options.hv_switch==0) {
				if (this.options.border_on_off==1) minus2=8;
				else minus2=11;
			}
			this.slider_text.css ({'width' : (mid - this.options.left_text_class_padding-minus-minus2) + 'px'});
			if (this.minus>0) {
				$text_element.css ({'left': this.minus+'px'});
			} else {
				$text_element.css ({'left': '0px'});			
			}
		},
		_set_text_div_width_ver: function() {
			if (!this.options.vert_text_mode)
			{
				this.slider_text.css ({'width' : this.max_size + 'px'});
			} else {
				if (this.under_600==0) this.slider_text.css ({'left' : this.ret_values.height + 'px'});
			}
		},
		create_html: function () {
			this.items_counts = this.options.map.length;
			var i, img, real_i;
			var html_code = '<div class="'+this.options.container_class+'"><div class="image_more_info"><a href="#"><img alt="" src="'+this.options.plugin_url+'application/modules/Ynmember/externals/images/more.png"></a></div>';
			this._start = -1;
			this._end = this.max_show-1;
			var pos_size;
			for (i=this._start;i<this._end;i++) {
				img = this.options.map [this.math._convert_position_to_image_array(0,i)].image;
				pos_size = this.math._calculate_child_coordinates_by_n (i);
				html_code += this._create_a_html_for_a_child (img,pos_size.new_pos);
			}
			var max_position = this.options.max_shown_items * this.options.child_div_width;
			if (this.options.preload_all_images) {
				for (i=0;i<this.items_counts;i++) {
					 $('<img/>')[0].src=this.options.map[i].image;
				}
			}
			html_code += this._create_arrows ();
			if (this.options.hv_switch)
				html_code += '<div class="clear"></div></div>';
			else
				html_code += '</div>';

			this.$element.prepend (html_code);
			this.$container = $('div.'+this.options.container_class, this.$element);
			// -------------- for computer ------------------
			this.$container.mousedown ($.proxy(this._mouse_down, this));
			this.$container.mouseup ($.proxy(this._mouse_up, this));
			this.$element.mouseenter ($.proxy(this._mouse_enter_widget, this));
			this.$element.mouseleave ($.proxy(this._mouse_leave_widget, this));
			this.$container.mouseleave ($.proxy(this._mouse_leave, this));
			this.$container.mousemove ($.proxy(this._mouse_move, this));
			// ---------------- for smart devices and touch panels ---------------
			this.$container.on ('touchstart',$.proxy(function (e) {
				e.preventDefault ();
				var next_e = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
				if (this.touch_event) {
					this.touch_event=0;
					return;
				}
				this._mouse_down (next_e, 1);
			}, this));
			this.$container.on ('touchend',$.proxy(function (e) {
				e.preventDefault ();
				var next_e = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
				this._mouse_up (next_e);
			}, this));
			this.$container.on ('touchmove',$.proxy(function (e) {
				e.preventDefault ();
				var new_e = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
				new_e.touched=1;
				var elm = $(this.$container).offset();
				var x = (new_e.pageX - elm.left)+this.minus;
				var y = new_e.pageY - elm.top;
				if(x < $(this.$container).width() && x > 0 && y < $(this.$container).height() && y > 0) 
					this._mouse_move (new_e);
				else 
					this._mouse_leave (new_e);
			}, this));
		},
		_set_prettyPhoto_div_position : function() {
			this.prettyPhoto_left=(this._return_middle_position_of_content()-Math.floor(this.options.big_pic_width/2))+Math.floor(this.options.big_pic_width*this.options.prettyPhoto_start);
			var offset=0;
			if (this.options.top_offset>0) offset=this.options.top_offset-Math.floor(this.options.big_pic_height/2);
			this.prettyPhoto_top=offset+Math.floor(this.options.big_pic_height*this.options.prettyPhoto_start);
			if (this.options.hv_switch)
			{
				this.$prettyPhoto_div.css({'top': this.prettyPhoto_left+'px', 'left': this.prettyPhoto_top+'px'});
			}
			else
			{
				this.$prettyPhoto_div.css({'left': this.prettyPhoto_left+'px', 'top': this.prettyPhoto_top+'px'});
			}
		},
		_set_parent_window_size : function (only_height, width) {
			if (typeof only_height=='undefined') only_height=0;
			if (typeof width=='undefined') width=0;
			this.ret_values = {'height' : 0, 'width' : 0};
			this.ret_values.height = ((2*this.options.top_offset)+this.options.shadow_offset); 
			var size = this.math._calculate_child_coordinates_by_n (this.max_show-1,0);
			size.new_pos2=size.new_pos+this.options.left_offset;

			wrapper_text_max_height=this.options.wrapper_text_max_height;
			var $text_element = $(this.options.text_field_id_prefix + this.last_middle,this.$element);
			if (this.minus>0 && this.last_middle>-1) {
				$block=$('div.content_slider_text_block_wrap', $text_element);
				if ($block.length) {
					if (typeof this.last_text_width == 'undefined') this.last_text_width=$text_element.width();
					var ww=this.get_window_width();
					//if (this.options.max_shown_items==1) ww=this.options.big_pic_width+59;
					$text_element.css('width', (ww-10)+'px');
				}
			}
			var element_height=$text_element.height();
			var parent_wrapper_height=this.$parent_wrapper.height();
			var temp=this.ret_values.height+element_height+10;
			if (temp>wrapper_text_max_height) wrapper_text_max_height=temp;

			if (only_height) {
				if (!this.options.hv_switch) {
					if (width!=0) this.$parent_wrapper.css ({'width' : width+'px'});
					this.$element.css ({'height' : wrapper_text_max_height+'px'});
					return;
				} else {
					this.$element.css ({'width' : width+'px'});
					return;
				}
			}
			if (size.new_pos<=0) return;
			this.container_height=this.ret_values.height;
			if (this.options.hv_switch) {
				this.$container.css ({'height' : size.new_pos + 'px','width': this.ret_values.height+'px'});
				this.$element.css ({'height' : size.new_pos2 + 'px','width' : this.options.wrapper_text_max_height+'px'});
			} else {
				this.$container.css ({'width' : size.new_pos + 'px','height': this.ret_values.height+'px'});
				this.$element.css ({'width' : size.new_pos2 + 'px','height' : wrapper_text_max_height+'px'});
				if (this.real_width==0) this.real_width=size.new_pos2;
			}
			this.ret_values.width = size.new_pos;
		},
		_return_container_width_height : function () {
			return this.ret_values;
		},
		_return_middle_position_of_content : function () {
			var middle = this.math._calculate_child_coordinates_by_n (this.mid_elem+1,0);
			middle.new_pos += Math.floor (this.options.big_pic_width/2) + this.options.big_border;
			return middle.new_pos;
		},
		_create_arrows : function () {
			var html;
			if (this.options.hv_switch) {
				if (this.options.border_on_off==0 || this.options.use_thin_arrows==1) {
					html = '<div class="circle_slider_nav_left"><img src="'+this.options.plugin_url+'application/modules/Ynmember/externals/images/left_vertical2.png" alt="left" /></div>';
					html += '<div class="circle_slider_nav_right"><img src="'+this.options.plugin_url+'images/right_vertical2.png" alt="right" /></div>';
				} else {
					html = '<div class="circle_slider_nav_left"><img src="'+this.options.plugin_url+'application/modules/Ynmember/externals/images/left_vertical.png" alt="left" /></div>';
					html += '<div class="circle_slider_nav_right"><img src="'+this.options.plugin_url+'application/modules/Ynmember/externals/images/right_vertical.png" alt="right" /></div>';
				}
			} else {
				if (this.options.border_on_off==0 || this.options.use_thin_arrows==1) {
					html = '<div class="circle_slider_nav_left"><img src="'+this.options.plugin_url+'application/modules/Ynmember/externals/images/left2.png" alt="left" /></div>';
					html += '<div class="circle_slider_nav_right"><img src="'+this.options.plugin_url+'application/modules/Ynmember/externals/images/right2.png" alt="right" /></div>';
				} else {
					html = '<div class="circle_slider_nav_left"><img src="'+this.options.plugin_url+'application/modules/Ynmember/externals/images/left.png" alt="left" /></div>';
					html += '<div class="circle_slider_nav_right"><img src="'+this.options.plugin_url+'application/modules/Ynmember/externals/images/right.png" alt="right" /></div>';
				}
			}
			return html;
		},
		_hide_arrows : function (method) {
			if (this.options.border_on_off==0 || this.options.use_thin_arrows==1) move_more=4;
			else move_more=0;
			if (method) {
				this.hide_text (this.math._convert_position_to_image_array(0,this.mid_elem),1);
				this.arrow_hidden_counter = 0;
				if (this.options.hv_switch) {
					this.$left_arrow.animate ({'top' : this.options.arrow_width+move_more},this.options.arrow_speed,this.options.arrow_easing,$.proxy(this._arrows_hidden, this));
					this.$right_arrow.animate ({'top' : -this.options.arrow_width},this.options.arrow_speed,this.options.arrow_easing,$.proxy(this._arrows_hidden, this));
				} else {
					this.$left_arrow.animate ({'left' : this.options.arrow_width+move_more},this.options.arrow_speed,this.options.arrow_easing,$.proxy(this._arrows_hidden, this));
					this.$right_arrow.animate ({'left' : -this.options.arrow_width},this.options.arrow_speed,this.options.arrow_easing,$.proxy(this._arrows_hidden, this));
				}
			} else {
				this.hide_text (this.math._convert_position_to_n(this.mid_elem-2),0);
				if (this.options.hv_switch) {
					this.$left_arrow.css ({'top' : this.options.arrow_width+move_more});
					this.$right_arrow.css ({'top' : -this.options.arrow_width});
				} else {
					this.$left_arrow.css ({'left' : this.options.arrow_width+move_more});
					this.$right_arrow.css ({'left' : -this.options.arrow_width});
				}
			}
		},
		_arrows_hidden : function () {
			if (this.arrow_hidden_counter >= 1) {
				this.func ();
				this.arrow_hidden_counter = 0;
			}
			else
				this.arrow_hidden_counter ++;
		},
		_arrows_shown : function () {
			this.clicked = 0;
		},
		
		_show_arrows : function () {
			this.slider_state=0;
			var right_end=0;
			if (this.options.hv_switch) {
				if (this.options.border_on_off==0 || this.options.use_thin_arrows==1) right_end=34;
				this.$left_arrow.animate ({'top' : 0},this.options.arrow_speed,this.options.arrow_easing,$.proxy(this._arrows_shown, this));
				this.$right_arrow.animate ({'top' : right_end+'px'},this.options.arrow_speed,this.options.arrow_easing,$.proxy(this._arrows_shown, this));
			} else {
				if (this.options.border_on_off==0 || this.options.use_thin_arrows==1) right_end=4;
				this.$left_arrow.animate ({'left' : 0},this.options.arrow_speed,this.options.arrow_easing,$.proxy(this._arrows_shown, this));
				this.$right_arrow.animate ({'left' : right_end+'px'},this.options.arrow_speed,this.options.arrow_easing,$.proxy(this._arrows_shown, this));
			}
			this.show_text (this.math._convert_position_to_image_array(0,this.mid_elem));
			if (this.last_c.master_click==1 || this.is_touch_device) this._start_main_hover();
			this.$element.trigger('open', [this.last_middle]);
		},
		_align_arrows: function () {
			var arrow_pos = this.math._calculate_arrows_positions ();
			if (this.options.hv_switch) {
				this.$left_arrow_class.css ({ top: arrow_pos.first_arrow_x , left: arrow_pos.arrow_y});
				this.$right_arrow_class.css ({ top: arrow_pos.second_arrow_x , left: arrow_pos.arrow_y});
			} else {
				this.$left_arrow_class.css ({ left: arrow_pos.first_arrow_x , top: arrow_pos.arrow_y});
				this.$right_arrow_class.css ({ left: arrow_pos.second_arrow_x , top: arrow_pos.arrow_y});
			}
		},
		_set_arrows_events : function () {
		this.$prettyPhoto_img.on ('touchstart', function (e){
				e.preventDefault(); 
				e.stopPropagation();
				$(this).click();
			});
			this.$prettyPhoto_img.on ('touchend', function (e){
				e.preventDefault(); 
				e.stopPropagation();
			});
			this.$prettyPhoto_img.mouseup (function (e){
				e.preventDefault(); 
				e.stopPropagation();
			});
			this.$prettyPhoto_img.mousedown (function (e){
				e.preventDefault(); 
				e.stopPropagation();
			});

			this.$left_arrow_class.click ($.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
				this.public_go_right();
			}, this));
			this.$right_arrow_class.click ($.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
				this.public_go_left();
			}, this));
			this.$left_arrow_class.on ('touchstart', $.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
				this.public_go_right();
			}, this));
			this.$left_arrow_class.on ('touchend', $.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
			}, this));
			this.$right_arrow_class.on ('touchstart', $.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
				this.public_go_left();
			}, this));
			this.$right_arrow_class.on ('touchend', $.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
			}, this));
			this.$left_arrow_class.mouseup ($.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
			}, this));
			this.$right_arrow_class.mousedown ($.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
			}, this));
			this.$left_arrow_class.mousedown ($.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
			}, this));
			this.$right_arrow_class.mousedown ($.proxy(function (e){
				e.preventDefault(); 
				e.stopPropagation();
			}, this));
		},
		hide_text : function (middle,method) {
			$text_element = $(this.options.text_object,this.$element);
			this.last_parent_height=this.$parent_wrapper.height();
			if (this.options.small_resolution_max_height==0 && this.options.hv_switch && this.under_600) this.$parent_wrapper.css('height', this.last_parent_height+'px');
			if (method)
				$text_element.fadeOut (); 
			else
				$text_element.hide ();
		},
		show_text: function (middle, dont_fadein, set_width) {
			if (typeof dont_fadein == 'undefined') dont_fadein=0;
			if (typeof set_width == 'undefined') set_width=0;
			this.last_middle=middle;
			var $text_element = $(this.options.text_field_id_prefix + middle,this.$element);

			var v2=$('div.circle_slider_text_wrapper_v2', $text_element);
			if (v2.length) this.options.vert_text_mode=1;
			else this.options.vert_text_mode=0;

			if (typeof this.options.map[middle].link_url != 'undefined') {
				this.$prettyPhoto_a.attr('href', this.options.map[middle].link_url);
			} else {
				this.$prettyPhoto_a.attr('href', '');
			}
			if (typeof this.options.map[middle].link_rel != 'undefined') {
				this.$prettyPhoto_a.attr('rel', this.options.map[middle].link_rel);
			} else {
				this.$prettyPhoto_a.attr('rel', '');
			}

			if (typeof this.options.map[middle].link_class != 'undefined') {
				this.$prettyPhoto_a.attr('class', this.options.map[middle].link_class);
			} else {
				this.$prettyPhoto_a.attr('class', '');
			}

			var text_object = $('.'+this.options.left_text_class,$text_element);
			if (this.options.small_resolution_max_height==0) this.$parent_wrapper.css('height', '');

			if (dont_fadein==0) $text_element.fadeIn ();
			if (this.options.hv_switch==0) {
				if (this.minus>0) this._set_parent_window_size(1);
				else {
					if (this.options.automatic_height_resize) {
						this.ret_values = {'height' : 0, 'width' : 0};
						this.ret_values.height = ((2*this.options.top_offset)+this.options.shadow_offset);
						var element_height=$text_element.height();
						var parent_wrapper_height=this.$parent_wrapper.height();
						var temp=this.ret_values.height+element_height+10;
						if (temp!=this.last_height) { 
							if (temp<this.options.wrapper_text_max_height) temp=this.options.wrapper_text_max_height;
							this.last_height=temp;
							this.$element.animate ({'height' : temp+'px'}, 300, 'linear');
						}
					}
				}
			}
			if (this.options.hv_switch) {
				if (this.options.vert_text_mode) {
					var width = $text_element.width ();
					if (width >= this.max_size || set_width) {
						text_object.css ({'width' : (this.max_size*2) + 'px'});
					}
					var height = text_object.height ();
					if (this.under_600==0) $text_element.css ({'top' : (this.mid - height - this.options.left_text_class_padding) + 'px'});
				} else {
					$block=$('div.content_slider_text_block_wrap', $text_element);
					if ($block.length) {
						var w;
						if (this.under_600==0) {
							var ww=this.get_window_width();
							//if (this.options.max_shown_items==1) ww=this.options.big_pic_width+59;
							if (ww>this.options.wrapper_text_max_height) w=this.options.wrapper_text_max_height-this.container_height-2;
							else w=ww-this.container_height-20;
						}
						else w=this.options.big_pic_width;
						$text_element.css ({'width' : w+'px'});	
					}
					else $text_element.css ({'width' : ''});

					var height = $text_element.height ();
					var top=this.mid - Math.floor(height/2);
					if (top<0) top=0;
					if (this.under_600==0) $text_element.css ({'top' : top + 'px'});
				}
			} else {
				if (this.minus>0) {
					var ww=this.last_resolution;
					$block=$('div.content_slider_text_block_wrap', $text_element);
					if ($block.length) {
						if (typeof this.last_text_width == 'undefined') this.last_text_width=$text_element.width();
						$text_element.css('width', (ww-10)+'px');
					}
				}
			}
		},
		_preset_all_children_parameters : function (animate,direction) {
			set_img = 0;
			var new_pos_size;
			var animation;
			this.do_animate = animate;
			for (i=0; i<this.max_show; i++) {
				new_pos_size = this.math._calculate_child_coordinates_by_n(i,this.operation);	
				if (animate) animation = this.math._calculate_method_for_child_by_n(i,direction);
				else animation = 0;
				this.items[i]._set_pos_size (new_pos_size.new_pos,this.speed,new_pos_size.new_y_pos,new_pos_size.new_siz.width,new_pos_size.new_siz.height,new_pos_size.new_border,animation,$.proxy(this._animation_done, this));
			}
			if (set_img)
			{
				for (i=-1; i<this.max_show-1; i++) {
					var pos_to_n = this.math._convert_position_to_n (i);
					var img = this.options.map [this.math._convert_position_to_image_array(0,i)].image;
					this.items[pos_to_n]._set_img (img);
				}
			}
		},	
		_stop_children: function(){
			for (i=0; i<this.number_of_items; i++) {
				this.items[i].reset_positions();
			}
			if (this.prettyPhoto_status) {
				if (this.prettyPhoto_status==1) {
					this.$prettyPhoto_div.stop();
					this.$prettyPhoto_img.stop();
				}
				this._end_main_hover2();
				this.prettyPhoto_status=0;
				this.hover_status=0;
				this.mouse_in_animation=0;
				this.mouse_out_animation=0;
			}
		},
		_mouse_down : function (e, skip_prevent)
		{
			if (this.ignore_click_down) {
				this.ignore_click_down=0;
				return;
			}
			if (typeof skip_prevent=='undefined') skip_prevent=0;
			if (skip_prevent==0) if (typeof e != 'undefined') e.preventDefault ();
			this.mouse_moved=0;
			if (this.hover_status!=0) this._end_main_hover2();

			if (typeof e != 'undefined') if (typeof e.pageX != 'undefined') {
				if (this.options.hv_switch)
					var current_x = e.pageY - this.offset;
				else
					var current_x = (e.pageX - this.offset)+this.minus;
			}

			var offset = current_x - this.last_mouse_x;
			this.last_mouse_x = current_x;
			this.slider_state=1;
			this.mouse_state=1;

			if (!this.clicked )
			{
				this.show_mouse_move = 1;
				this.clicked = 1;
			}
			var ofst = this.$container.offset ();

			if (this.options.hv_switch) {
				this.offset = ofst.top;
			} else {
				this.offset = ofst.left+this.minus;
			}
			this.was_gone = 0;
		},
		_mouse_up : function (e) {
			if (this.ignore_click_up) {
				this.ignore_click_up=0;
				return;
			}
			this.mouse_state=0;
			this._stop_children();
			if (this.show_mouse_move) {	
				var help_a1 = 1;
				this.show_mouse_move = 0;
				if (Math.abs(this.math.sum_movement) == 0 && !this.was_gone) {
					this.func = 0;
					help_a1 = 0;
					
					if (this.options.hv_switch)
						var pos = this.math._change_master_position_by_x (e.pageY - this.offset);
					else
						var pos = this.math._change_master_position_by_x (((e.pageX - this.offset)+this.minus)-0);

					this.last_c=pos;

					if (pos.master_click) {
						var right_limit=38;
						var left_limit=20;
						if (this.options.border_on_off==1 && this.options.use_thin_arrows==0) {
							right_limit=48;
							left_limit=30;
						}
						if (pos.dist_right>right_limit && pos.dist_left>left_limit) {
							if (this.options.middle_click == 1) {
								help_a1 = 1;
								this.going_counter = -1;
								pos.pos = 1;
							}
							if (this.options.middle_click == 2) {
								help_a1 = 1;
								this.going_counter = 1;
								pos.pos = -1;
							}	
							if (this.options.middle_click == 0) {
								this.slider_state=0;
								this.clicked=0;
							}
						} else {
							this.slider_state=0;
							this.clicked=0;						
						}
					}	
					this.speed = ((this.mid_elem - Math.abs (pos.pos)+1)*this.options.moving_speed)+this.options.moving_speed_offset;
					if (!help_a1) this.going_counter = -pos.pos;
					this.keep_going = 1;
					if (pos.pos < 0 ) {
						this.click = 2;
						if (pos.pos < -1) this.operation = 1;
						else this.operation = 0;
						this.func = this.go_right;
						this._hide_arrows (1);
					}
					if (pos.pos > 0) {
						this.click = 1;
						if (pos.pos > 1) this.operation = 1;
						else this.operation = 0;
						this.func = this.go_left;
						this._hide_arrows (1);
					}
					if (pos.pos == 0) {
						this.keep_going = 0;
					}
					return;
				} else {
					this._reorder ();
					this.click = 0;
				}
			}
		},
		_arrow_mouse_up : function () {
			this.keep_going = 1;
			this.click = 0;
			this.armd = 0;
		},
		_arrow_mouse_down : function () {
			this.armd = 1;
			this.clicked = 1;
		},
		_arrow_mouse_leave : function () {
			if (this.armd) {
				this.clicked = 0;
				this.armd = 0;
			}
		},
		_mouse_move : function (e)
		{
			this.mouse_moved=1;
			if (typeof e.touched=='undefined') e.preventDefault ();
			
			var ofst = this.$container.offset ();
			if (this.options.hv_switch) {
				this.offset = ofst.top;
			} else {
				this.offset = ofst.left+this.minus;
			}

			var poss = $(this.$element).offset();
			this.y_offset=poss.top;
			var current_x, offset;
			if (typeof e != 'undefined') if (typeof e.pageX != 'undefined') {
				if (this.options.hv_switch)
					current_x = e.pageY - this.offset;
				else
					current_x = (e.pageX - this.offset)+this.minus;
				offset = current_x - this.last_mouse_x;
			}

			if (this.show_mouse_move && this.clicked) {
				this._move_all (offset);
				if (Math.abs (this.sum_movement) >= 1 && !this.was_gone) {
					this.was_gone = 1;
					this._hide_arrows (0);
				}
			}
			
			this.last_mouse_x = current_x;

			if (this.show_mouse_move==1 || this.slider_state==1) return;
	
			var c={'pos': 0, 'master_click': 0};

			if (typeof e != 'undefined') if (typeof e.pageX != 'undefined') {
				if (this.options.hv_switch) {
					current_x = e.pageY-this.y_offset;
				} else {
					if (this.minus==0) {
						current_x = (e.pageX-this.x_offset)+this.minus;
					} else {
						current_x=(e.pageX-this.parent_x_offset)+this.minus;
					}
				}
				c=this.math._convert_x_position_to_n (current_x);
			}
			if (c.master_click==1)
			{
				if (this.hover_status==2 || this.mouse_in_animation==1) return;
				if (this.mouse_out_animation==1) {
					this.$prettyPhoto_div.stop();
					this.$prettyPhoto_img.stop();
					this.mouse_out_animation=0;
				}
				this.hover_status=1;
				this.mouse_in_animation=1;
				this._start_main_hover();
			} else {
				if (this.hover_status==2 || (this.hover_status==1 && this.mouse_out_animation==0)) this._fake_mouse_leave();
			}
		},
		_start_main_hover: function() {
			if (this.$prettyPhoto_a.attr('href')=='') return;
			var left_end=this.prettyPhoto_left-this.options.prettyPhoto_movement-10;
			var top_end=this.prettyPhoto_top-this.options.prettyPhoto_movement-10;
			var width=this.options.prettyPhoto_width+3;
			this.prettyPhoto_status=1;
			if (this.options.hv_switch==0) {
				this.$prettyPhoto_div.animate({'left': left_end+'px', 'top': top_end+'px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing, $.proxy(this._ending_main_hover, this));
			} else {
				this.$prettyPhoto_div.animate({'left': top_end+'px', 'top': left_end+'px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing, $.proxy(this._ending_main_hover, this));
			}
			this.$prettyPhoto_img.animate({'width': width+'px', 'padding': '11px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing);			
		},
		_ending_main_hover: function() {
			var left_end=this.prettyPhoto_left-this.options.prettyPhoto_movement;
			var top_end=this.prettyPhoto_top-this.options.prettyPhoto_movement;
			var width=this.options.prettyPhoto_width;
			if (this.options.hv_switch==0) {
				this.$prettyPhoto_div.animate({'left': left_end+'px', 'top': top_end+'px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing, $.proxy(this._end_main_hover, this));
			} else {
				this.$prettyPhoto_div.animate({'left': top_end+'px', 'top': left_end+'px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing, $.proxy(this._end_main_hover, this));
			}
			this.$prettyPhoto_img.animate({'width': width+'px', 'padding': '10px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing);
		},
		_end_main_hover: function() {	
			this.prettyPhoto_status=2;
			this.hover_status=2;
			this.mouse_in_animation=0;
		},
		_fake_mouse_leave: function() {
			if (this.$prettyPhoto_a.attr('href')=='') return;
			if (this.mouse_in_animation==1) {
				this.$prettyPhoto_div.stop();
				this.$prettyPhoto_img.stop();
				this.mouse_in_animation=0;
			}
			this.hover_status=1;
			this.mouse_out_animation=1;
			this._end_main_hover2();
		},
		_end_main_hover2: function() {
			var left_end=this.prettyPhoto_left;
			var top_end=this.prettyPhoto_top;
			this.prettyPhoto_status=1;
			if (this.options.hv_switch==0) {
				this.$prettyPhoto_div.animate({'left': left_end+'px', 'top': top_end+'px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing, $.proxy(this._main_hover_ended, this));
			} else {
				this.$prettyPhoto_div.animate({'left': top_end+'px', 'top': left_end+'px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing, $.proxy(this._main_hover_ended, this));
			}
			this.$prettyPhoto_img.animate({'width': '0px', 'padding': '0px'}, this.options.prettyPhoto_speed, this.options.prettyPhoto_easing);
		},
		_main_hover_ended: function() {
			this.prettyPhoto_status=0;
			this.hover_status=0;
			this.mouse_out_animation=0;		
		},
		_mouse_enter_widget : function (e) {
			if (this.is_auto_play==1) this.dismiss_auto_play=1;
		},
		_mouse_leave_widget : function (e) {
			this.dismiss_auto_play=0;
		},
		_mouse_leave : function (e) {
			if (this.show_mouse_move && !this.click) {
				this.show_mouse_move = 0;
				this._reorder ();
			}
			if (this.hover_status==2 || (this.hover_status==1 && this.mouse_out_animation==0)) this._fake_mouse_leave();
		},
		_reorder : function () {
			var movement = this.math.sum_movement;
			this.speed = ((this.mid_elem + 1) * this.options.moving_speed)+this.options.moving_speed_offset;
			if (Math.abs(this.math.sum_movement) < (this.options.child_div_width / 2)) {
				this.math._clear_movement ();
				this._preset_all_children_parameters (1,0,1);
			} else {
				this.math._clear_movement ();
				if (movement < 0) {
					this.math._rotate_right (1);
					this._preset_all_children_parameters (1,0,1);
				}
				if (movement > 0) {
					this.math._rotate_left (1);
					this._preset_all_children_parameters (1,1,1);
				}
			}
			this.sum_movement = 0;
		},
		_create_a_html_for_a_child : function (img,left) {
			var border_div='';
			if (this.options.activate_border_div) border_div='<div class="'+this.options.border_class+'"></div>';
			return '<div class="'+this.options.picture_class+'" style="left: '+left+'px;">'+border_div+'<img src="' + this.options.no_image_path + '" style=" background-image:url(' + img + ');" /></div>';
		},
		left_clicked : function (e) {
			this.speed = ((this.mid_elem + 1) * this.options.moving_speed)+this.options.moving_speed_offset;
			if (typeof e != 'undefined') e.preventDefault ();
			this.func = this.go_left;
			this.click = 1;
			this.going_counter = -1;
			this.$element.trigger('next');
			this._animation_begin();
		},
		right_clicked : function (e) {
			this.speed = ((this.mid_elem + 1) * this.options.moving_speed)+this.options.moving_speed_offset;
			if (typeof e != 'undefined') e.preventDefault ();
			this.func = this.go_right;
			this.click = 2;
			this.going_counter = 1;
			this.$element.trigger('prev');
			this._animation_begin();
		},
		go_right : function () {
			if (this.lock == 1) return;
			this.lock = 1;
			this.math.sum_movement = this.sum_movement = 0;
			if (this.keep_going == 1 && this.going_counter > 0) this.going_counter --;	
			this.anim_counter = 0;
			this._set_first_left ();
			this.math._rotate_left (1);
			this._preset_all_children_parameters (1,1);
		},
		go_left : function () {
			if (this.lock == 1) return;
			this.lock = 1;
			this.math.sum_movement = this.sum_movement = 0;
			if (this.keep_going == 1 && this.going_counter < 0) this.going_counter ++;
			this.anim_counter = 0;
			this._set_first_right ();
			this.math._rotate_right (1);
			this._preset_all_children_parameters (1,0);
		},
		_animation_begin : function () {
			this.show_mouse_move = 0;
			this.anim_counter = 0;
			this.keep_going = 1;
			this.do_animate = 1;
			this._hide_arrows (1);
		},
		_animation_done : function () {
			var quantity;
			if (this.do_animate) quantity = (this.max_show)+(this.max_show-3);
			else quantity = (this.max_show)+(this.max_show-2);
			if (this.anim_counter >= quantity) {
				this.anim_counter = 0;
				this.lock = 0;
				if (this.click == 1) {
					if (this.keep_going !=0) {
						if (this.going_counter != 0) {
							this.operation = 0;
							if (this.going_counter < -1) this.operation = 1;
							this.go_left();
						} else {
							this.keep_going = 0;
							this.click = 0;
						}
					} else {
						this.go_left ();
					}
				}
				if (this.click == 2) {
					if (this.keep_going !=0) {
						if (this.going_counter != 0) {
							this.operation = 0;
							if (this.going_counter > 1) this.operation = 1;
							this.go_right();
						} else {
							this.keep_going = 0;
							this.click = 0;
						}
					} else {
						this.go_right();
					}
				}
				if (this.click == 0) {
					this._show_arrows ();
					this.operation = 0;
				}
				return;
			}
			this.anim_counter++;
		},
		_move_all : function (n) {
			var dir = 0;
			this._set_first_left ();
			this._set_first_right ();
			while (Math.abs(n) >= this.options.child_div_width) {
				if (n > 0) {
					this.math._add_movement (this.options.child_div_width);
					this._set_first_left ();
					n -= this.options.child_div_width;
				} else {
					this.math._add_movement (-this.options.child_div_width);
					this._set_first_right ();
					n += this.options.child_div_width;
				}
			}
			this.math._add_movement (n);
			if (n > 0) {
				this._set_first_left ();
				dir = 1;
			} else {
				this._set_first_right ();
				dir = 0;
			}
			this._preset_all_children_parameters (0,dir);
			this.sum_movement = this.math.sum_movement;
		},
		_set_first_right : function () {	
			this.items[this.math._next_right_n ()]._set_img (this.options.map[this.math._next_right_image()].image);
		},
		_set_first_left : function () {
			this.items[this.math._next_left_n ()]._set_img (this.options.map[this.math._next_left_image()].image);
		},
		start_auto_play: function() {
			var self=this;
			this.dismiss_auto_play=0;
			this.is_auto_play=1;
			if (this.options.auto_play_direction==1) this.timeout_autoplay_handler=setInterval(function(){self.public_go_left(1);}, self.options.auto_play_pause_time);
			else this.timeout_autoplay_handler=setInterval(function(){self.public_go_right(1);}, self.options.auto_play_pause_time);
		},
		stop_auto_play: function() {
			this.dismiss_auto_play=1;
			if (this.is_auto_play==1) {
				clearInterval(this.timeout_autoplay_handler);
			}
			this.is_auto_play=0;
		},
		get_auto_play_status: function() {
			return this.is_auto_play;
		},
		get_number_of_current_slide: function() {
			return this.last_middle;
		}
	};

	function position (image_array_len,visible_window_len,mid_n_no,n_img_ofst,pos_n_ofst,elem_width,master_elem_width,spw,sph,bpw,bph,top_offset,small_border,big_border,arw,arh,cpad,mode, parent_this, left_offset) {
		var self = this;
		this.parent_this=parent_this;
		this.image_array_lenght = image_array_len;
		this.visible_window_lenght = visible_window_len;
		this.div_window_lenght = this.visible_window_lenght + 2;
		this.beginning_position_number = -1;
		this.n_img_offset = n_img_ofst;
		this.begining_n_img_offset2 = n_img_ofst;
		this.position_n_offset = pos_n_ofst;
		this.element_width = elem_width;
		this.master_element_width = master_elem_width;
		this.master_element_height = bph;
		this.current_mid_after_ratio = 1;
		this.max_show = this.visible_window_lenght;
		this.sum_movement = 0;
		this.mid_elem = mid_n_no;
		this.left_offset=left_offset;

		this.small_pic_width = spw;
		this.small_pic_height = sph;	
		this.big_pic_width = bpw;
		this.big_pic_height = bph;

		this.top_offset = top_offset;
		this.small_border = small_border;
		this.big_border = big_border;

		this.arrow_width = arw;
		this.arrow_height = arh;

		this.container_padding = cpad;
		
		this.mode = mode;
	};
	position.prototype = {
		//Conversions
		_convert_n_to_position : function (n) {
			return ((this._windowing (this.div_window_lenght,n-this.position_n_offset))+this.beginning_position_number);
		},
		_convert_position_to_n : function (position) {
			return this._windowing (this.div_window_lenght,(position-this.beginning_position_number) + this.position_n_offset);
		},
		_convert_position_to_image_array : function (k,position) {
			return this._windowing (this.image_array_lenght,position-this.beginning_position_number + this.n_img_offset + this.position_n_offset + (k*this.div_window_lenght));
		},
		//Functionality
		_next_left_image : function () {
			return this._convert_position_to_image_array (0,this.beginning_position_number);
		},
		_next_right_image : function () {
			return this._convert_position_to_image_array (0,this.visible_window_lenght);
		},
		_next_left_n : function () {
			return this._convert_position_to_n (this.beginning_position_number);
		},
		_next_right_n : function () {
			return this._convert_position_to_n (this.visible_window_lenght);
		},
		_rotate_left : function (places) {
			var last_offset = this.position_n_offset;
			this.position_n_offset = this._windowing (this.div_window_lenght,this.position_n_offset-places);
			if (last_offset < this.position_n_offset) {
				this.n_img_offset = this._windowing (this.image_array_lenght,this.n_img_offset - (Math.floor((Math.abs(places)+this.div_window_lenght)/this.div_window_lenght)*this.div_window_lenght));
			}	
		},
		_rotate_right : function (places) {
			var last_offset = this.position_n_offset;
			this.position_n_offset = this._windowing (this.div_window_lenght,this.position_n_offset+places);
			if (last_offset > this.position_n_offset) {
				this.n_img_offset = this._windowing (this.image_array_lenght,this.n_img_offset+(Math.floor((Math.abs(places)+this.div_window_lenght)/this.div_window_lenght)*this.div_window_lenght));
			}
		},
		_change_master_position_by_x : function (x) {
			this.sum_movement = 0;
			var before_mid = (this.mid_elem) * this.element_width;
			var after_mid = (before_mid + this.master_element_width + (2*this.big_border) + (2*this.arrow_width));
			var pos;
			if (x <= before_mid) {
				pos = Math.floor (x/this.element_width);
				pos = this.mid_elem - pos;
				return ({
					'pos': (-(pos)),
					'master_click' : 0
				});
			}
			if (x < after_mid) {
				return ({
					'pos': 0,
					'master_click': 1,
					'dist_left': x-before_mid,
					'dist_right': after_mid-x
				});
			}
			x=x-after_mid + this.element_width;
			pos = Math.floor (x/this.element_width);
			return ({'pos' : pos , 'master_click' : 0});
		},
		_convert_x_position_to_n: function (x) {
			var before_mid = (this.mid_elem) * this.element_width;
			var after_mid = (before_mid + this.master_element_width + (2*this.big_border) + (2*this.arrow_width));
			var pos;
			if (x <= before_mid) {
				pos = Math.floor (x/this.element_width);
				pos = this.mid_elem - pos;
				return ({'pos' : (-(pos)) , 'master_click' : 0});
			}
			if (x < after_mid) {
				return ({'pos' : 0 , 'master_click' : 1});
			}
			x=x-after_mid + this.element_width;
			pos = Math.floor (x/this.element_width);
			return ({'pos' : pos , 'master_click' : 0});
		},
		_calculate_child_size_by_ratio : function (new_ratio) {
			var new_width = this.big_pic_width - this.small_pic_width;
			var new_height = this.big_pic_height - this.small_pic_height;
			var new_margin_left;
			var new_margin_top;
			new_width = new_width * new_ratio;
			new_width += this.small_pic_width;
			new_height = new_height * new_ratio;
			new_height += this.small_pic_height;
			new_margin_left = Math.floor ((this.big_pic_width - this.small_pic_width)/2) - Math.floor(new_width/2);
			new_margin_top = Math.floor ((this.big_pic_height - this.small_pic_height)/2) - Math.floor(new_height/2);
			return {width: new_width,height: new_height,margin_left: new_margin_left,margin_top: new_margin_top};
		},
		_calculate_arrows_positions : function () {
			var big_border=this.big_border;
			if (this.parent_this.options.activate_border_div) big_border=0;
			var first_arrow_x_position = ((this.mid_elem) * this.element_width) + Math.ceil(big_border) + this.left_offset-1;
			var y_minus=0;
			if ((this.parent_this.options.border_on_off==0 || this.parent_this.options.use_thin_arrows==1)) y_minus=15;
			
			var arrow_y_position = this.top_offset - Math.ceil (this.arrow_height/2)-y_minus;
			if (this.parent_this.options.activate_border_div) {
				first_arrow_x_position+=this.big_border;
				arrow_y_position-=this.big_border;
			}
			if (this.parent_this.options.border_on_off==0 || this.parent_this.options.use_thin_arrows==1) {
				//if (this.parent_this.options.max_shown_items!=1) first_arrow_x_position-=big_border+5;		// was 5
				//else 
				first_arrow_x_position-=big_border+7;
				arrow_y_position-=Math.ceil(big_border/2)+1;
			}
			//if (this.parent_this.options.border_on_off==1 && this.parent_this.options.use_thin_arrows==1) {
			var second_arrow_x_position = ((this.mid_elem) * this.element_width) + this.master_element_width + this.arrow_width + big_border  + this.left_offset+1;
			if (this.parent_this.options.border_on_off==0 && this.parent_this.options.use_thin_arrows==0) {
				second_arrow_x_position-=Math.ceil(big_border)+2;
			}
			if (this.parent_this.options.use_thin_arrows==1) {
				second_arrow_x_position-=Math.ceil(big_border)-1;
			}
			if (this.parent_this.options.border_on_off==0 || this.parent_this.options.use_thin_arrows==1) {
				second_arrow_x_position+=1;
			}
			if (this.parent_this.options.activate_border_div) second_arrow_x_position-=this.big_border;
			return {first_arrow_x : first_arrow_x_position, second_arrow_x: second_arrow_x_position, arrow_y : arrow_y_position};
		},
		_calculate_child_coordinates_by_n : function (n,operation) {
			var new_n = this._convert_n_to_position(n);
			if (typeof operation == 'undefined') operation = 0;
			var movement = Math.abs (this.sum_movement);
			var left_ratio = movement / this.element_width;
			var right_ratio = 1 - left_ratio;
			var new_x_pos = 0;
			var new_y_pos = this.top_offset;
			var new_size;
			var new_return_size;
			var border = this.small_border;
			
			var small_element_width = this.element_width;
			var big_element_width = this.master_element_width;
			var big_small_diff = this.master_element_width - this.element_width;
			var n_position = (new_n * small_element_width)+this.sum_movement;
			var left_one_pos = (left_ratio*big_small_diff);
			var right_one_pos = (right_ratio*big_small_diff);
			var pic_div_diff = (this.element_width - this.small_pic_width);
			//---------------------
			var master_plus_arrows = this.master_element_width + (2*this.arrow_width);
			var master_arows_small_diff = master_plus_arrows - this.element_width;
			var left_elem_size = this.element_width + (left_ratio*master_arows_small_diff);
			var right_elem_size = this.element_width + (right_ratio*master_arows_small_diff);
			//---------------------
			var left_one_pos_with_arrow = (left_ratio*(this.arrow_width));
			var right_one_pos_with_arrows = right_one_pos + (right_ratio*(this.arrow_width + pic_div_diff + 2*this.big_border));
			var right_one_pos_with_arrows_static = big_small_diff + (2*this.arrow_width);
			var left_arrow_ratio = right_ratio * this.arrow_width;
			var right_border_ratio = left_ratio * pic_div_diff;

			if (this.sum_movement <= 0)
			{
				if (new_n < this.mid_elem) {
					new_x_pos = n_position;
					
					new_size = 0;
					new_y_pos = this.top_offset - (Math.floor(this.small_pic_height/2)) - this.small_border;
					
				}
				if (new_n == this.mid_elem) {
					border = this.small_border + ((this.big_border - this.small_border)*right_ratio);
					new_x_pos = n_position + left_arrow_ratio;
					if (!operation || this.mode == 2) {
						new_size = right_ratio;
						new_y_pos = this.top_offset - Math.floor(this.small_pic_height/2) - (((this.master_element_height - this.small_pic_height)/2)*right_ratio) - this.small_border - (this.big_border - this.small_border)*right_ratio;
					} else {
						new_size = 0;
						new_y_pos = this.top_offset - (Math.floor(this.small_pic_height/2)) - this.small_border;
					}
				}
				if (new_n == this.mid_elem+1) {
					border = this.small_border + ((this.big_border - this.small_border)*left_ratio);
					new_x_pos = n_position + right_elem_size - this.element_width + ((pic_div_diff + this.small_border)*right_ratio) + (this.arrow_width*left_ratio);
					if (!operation || this.mode == 2) {
						new_size = left_ratio;
						new_y_pos = this.top_offset - Math.floor(this.small_pic_height/2) - (((this.master_element_height - this.small_pic_height)/2)*left_ratio) - this.small_border;
					} else {
						new_size = 0;
						new_y_pos = this.top_offset - (Math.floor(this.small_pic_height/2)) - this.small_border; 
					}
				}
				if (new_n > this.mid_elem+1) {
					new_x_pos = n_position + right_one_pos_with_arrows_static + pic_div_diff + this.small_border;
					new_size = 0;
					new_y_pos = this.top_offset - (Math.floor(this.small_pic_height/2)) - this.small_border;					
				}
			} else {
				if (new_n < this.mid_elem) {
					new_x_pos = n_position;
					new_size = 0;
					new_y_pos = this.top_offset - (Math.floor(this.small_pic_height/2)) - this.small_border;
					
				}
				if (new_n == this.mid_elem) {
					border = this.small_border + ((this.big_border - this.small_border)*right_ratio);
					new_x_pos = n_position + ((2*this.arrow_width)+this.master_element_width + pic_div_diff + this.small_border - this.element_width) - (right_ratio*((1*this.arrow_width)+this.master_element_width + pic_div_diff + this.big_border - this.small_border - this.element_width));
					if (!operation || this.mode == 2) {
						new_size = right_ratio;
						new_y_pos = this.top_offset - Math.floor(this.small_pic_height/2) - (((this.master_element_height - this.small_pic_height)/2)*right_ratio) - this.small_border - (this.big_border - this.small_border)*right_ratio;
					} else {
						new_size = 0;
						new_y_pos = this.top_offset - (Math.floor(this.small_pic_height/2)) - this.small_border;
					}
				}
				if (new_n == this.mid_elem-1) {
					border = this.small_border + ((this.big_border - this.small_border)*left_ratio);
					new_x_pos = n_position + left_one_pos_with_arrow;	
					if (!operation || this.mode == 2) {
						new_size = left_ratio;
						new_y_pos = this.top_offset - Math.floor(this.small_pic_height/2) - (((this.master_element_height - this.small_pic_height)/2)*left_ratio) - this.small_border;
					} else {
						new_size = 0;
						new_y_pos = this.top_offset - (Math.floor(this.small_pic_height/2)) - this.small_border;
					}
				}
				if (new_n > this.mid_elem) {
					new_x_pos = n_position + right_one_pos_with_arrows_static + pic_div_diff + this.small_border;
					new_size = 0;
					new_y_pos = this.top_offset - (Math.floor(this.small_pic_height/2)) - this.small_border;
				}
			}
			new_return_size = this._calculate_child_size_by_ratio (new_size);
			return {new_pos: new_x_pos+this.left_offset,new_y_pos: new_y_pos,new_border: border, new_siz: new_return_size};
		},
		_calculate_method_for_child_by_n : function (n,direction) {
			var pos = this._convert_n_to_position(n);
			if (pos == -1 && direction == 1) return 0;
			if (pos == this.visible_window_lenght && direction == 0) return 0;
			return 1;
		},
		_add_movement : function (movement) {
			this.sum_movement += movement;
			if (Math.abs(this.sum_movement) >= this.element_width) {
				if (this.sum_movement >= 0) {
					this._rotate_left(Math.floor(Math.abs(this.sum_movement) / this.element_width));
					this.sum_movement = this.sum_movement % this.element_width;
				} else {
					this._rotate_right(Math.floor(Math.abs(this.sum_movement) / this.element_width));
					this.sum_movement = this.sum_movement % this.element_width;
				}
			}
		},
		_clear_movement : function () {
			this.sum_movement = 0;
		},
		_windowing : function (a,b) {
			return (a + (b % a)) % a;
		}
	};

	$.fn.content_slider = function ( option, scnd ) {
		var $this = $(this),
			data = $this.data('tooltip'),
			options = typeof option == 'object' && option;
		data || $this.data('tooltip', (data = new content_slider(this, options)));
		if (typeof scnd !== 'undefined') return data[option](scnd);
		else {
			if (typeof option == 'string') {
				return data[option]();
			}
		}
	};
	$.fn.content_slider.Constructor = content_slider;
	$.fn.content_slider.defaults = {
		map:						false,
		max_shown_items:			3,
		active_item:				0,
		top_offset:					0,
		left_offset:				0,
		child_div_width:			104,
		child_div_height:			104,
		small_pic_width:			100,
		small_pic_height:			100,
		big_pic_width:				200,
		big_pic_height:				200,
		small_border:				5,
		big_border:					8,
		arrow_width:				28,
		arrow_height:				57,
		small_arrow_width:			20,
		small_arrow_height:			20,		
		moving_speed:				70,
		moving_speed_offset:		100,
		moving_easing:				'linear',
		arrow_speed:				300,
		arrow_easing:				'linear',
		mode:						2,
		left_arrow_class:			'.circle_slider_nav_left',
		right_arrow_class:			'.circle_slider_nav_right',
		container_class:			'circle_slider',
		container_class_padding: 	24,
		picture_class:				'circle_slider_thumb',
		border_class:				'circle_item_border',
		child_final_z_index:		100,
		text_field_id_prefix:		'#sw',
		text_object:				'.circle_slider_text_wrapper',
		hv_switch:					0,
		shadow_offset:				10,
		wrapper_text_max_height:	810,
		left_text_class:			'circle_slider_text',
		right_text_class_sufix:		'right',
		left_text_class_padding:	20,
		vert_text_mode:				0,
		middle_click:				2,
		border_on_off:				1,
		activate_border_div:		1,
		hover_movement:				6,
		hover_speed:				100,
		hover_easing:				'linear',
		prettyPhoto_speed:			200,
		prettyPhoto_easing:			'linear',
		prettyPhoto_width:			21,
		prettyPhoto_start:			0.93,
		prettyPhoto_movement:		45,
		auto_play:					0,
		auto_play_direction:		1,
		auto_play_pause_time:		3000,
		allow_shadow:				1,
		small_resolution_max_height:0,
		preload_all_images:			0,
		border_radius:				-1,
		border_color:				'#282828',
		arrow_color:				'#282828',
		automatic_height_resize:	1,
		bind_arrow_keys:			1,
		use_thin_arrows:			0,
		enable_mousewheel:			1,
		radius_proportion:			1,
		plugin_url:					'',
		responsive_by_available_space:	0,

		// blank image
		no_image_path: 'slide_profile_blank.png',
	};
})(jQuery);
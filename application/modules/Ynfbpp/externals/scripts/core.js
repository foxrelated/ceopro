/**
 * @package Ynfbpp
 * @category Extenstion
 * @author YouNet Company
 */

/**
 * extends Mootools component
 */

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

Element.implement({
	addLiveEvent : function(event, selector, fn) {
		this.addEvent(event, function(e) {
			var t = $(e.target);
			if(!t.match(selector))
				return ;
			fn.apply(t, [e]);
		}.bindWithEvent(this, selector, fn));
	}
});

var ynfbpp = {
	pt : [],
	ele : 0,
	href : 0,
	data : {},
	timeoutId : 0,
	isShowing: 0,
	cached: {},
	dir: {cx:0,cy:0, x:0, y:0},
	isShowing: 0,
	ignoreClasses: ['uiContextualDialogContent','uiYnfbppHovercardFooter','layout_page_header','layout_page_footer'],
	enableThumb: false,
	isMouseOver: 1,
	mouseOverTimeoutId: 0,
	box:0,
	timeoutOpen: 300,
	timeoutClose: 300,
	enabledAdmin: false,
	data: {match_type: '', match_id: 0},
	boxContent: 0,
	bussinessRoute: 'business-page',
	enabledItems: {},

	setTimeoutOpen: function(time){
		ynfbpp.timeoutOpen = time;
		return ynfbpp;
	},
	clearCached: function(){
		ynfbpp.cached = {};
	},
	openSmoothBox: function(href){
		// create an element then bind to object
		var a = new Element('a', {
			href : href,
			'style' : 'display:none'
		});
		var body = document.getElementsByTagName('body')[0];
		a.inject(body);
		Smoothbox.open(a);
	},
	setTimeoutClose: function(time){
		ynfbpp.timeoutClose = time;
		return ynfbpp;
	},
	setEnabledAdmin: function(flag){
		ynfbpp.enabledAdmin = flag;
		return ynfbpp;
	},
	setIgnoreClasses: function(s){
		var ar = s.replace(/\./g,' ').replace(/,/g,' ').split(/\s+/);
		for(var i=0; i<ar.length; ++ i){
			if(ar[i]!= null && ar[i]!= undefined && ar[i]){
				ynfbpp.ignoreClasses.push(ar[i]);
			}
		}
		return ynfbpp;
	},
	setEnableThumb: function(flag){
		ynfbpp.enableThumb =  flag;
		return ynfbpp;
	},
	// set the business route that can be changed in business acp
	setBusinessRoute: function(business_route){
		ynfbpp.bussinessRoute =  business_route;
		return ynfbpp;
	},
	// set enabled profile popup item
	setEnabledItems: function(enable_items_str){
		ynfbpp.enabledItems = JSON.parse(enable_items_str);
		return ynfbpp;
	},
	removeIgnoreClass: function(k){
		for(var i=0; i<ynfbpp.ignoreClasses.length; ++i){
			if(ynfbpp.ignoreClasses[i] == k){
				ynfbpp.ignoreClasses[i] = '';
			}
		}
		return ynfbpp;
	},
	boot : function() {

		if(window.parent != window){return;}

		if (typeof jQuery != 'undefined')
			jQuery.noConflict();
		if(document.location.href.search(en4.core.baseUrl+'admin')>0){
            if(document.location.href.search(en4.core.baseUrl+'admin/ynfbpp/settings')>0) {
				Asset.css(en4.core.basePath + 'application/css.php?f=application/modules/Ynfbpp/externals/styles/main.css');
            }
			if(ynfbpp.enabledAdmin == false){
				return ;
			}else{
				Asset.css(en4.core.basePath + 'application/css.php?f=application/modules/Ynfbpp/externals/styles/main.css');
			}
		}

		$(document.body).addLiveEvent('mouseover', 'a', ynfbpp.check);

		if(ynfbpp.enableThumb){
			$(document.body).addLiveEvent('mouseover', 'img', ynfbpp.check);
			$(document.body).addLiveEvent('mouseover', 'span', ynfbpp.check);
		}
	},
	// check if the item profile popup is enabled in acp setting
	checkEnableItem: function(item){
		return (ynfbpp.enabledItems.hasOwnProperty(item) && ynfbpp.enabledItems[item]);
	},
	checkIngoreClasses: function (a){
		var p = a;
		var len = ynfbpp.ignoreClasses.length;
		while(p.parentNode != null && p.parentNode != undefined && p.tagName != null && p.tagName != undefined){
			for(var i=0; i<len; ++i){
				if(ynfbpp.ignoreClasses[i] && $(p).hasClass(ynfbpp.ignoreClasses[i])){
					return false;
				}
			}
			p  = p.parentNode;
		}
		return true;
	},
	check : function(e) {
		if(e.target == null && e.target == undefined){
			return;
		}

		var a = e.target;
		var ele = e.target;

		if(a.getAttribute == null || a.getAttribute == undefined){
			return;
		}

		if((ele.tagName.toUpperCase() == 'IMG' || ele.tagName.toUpperCase() == 'SPAN') && ($(ele).hasClass('thumb_icon') || $(ele).hasClass('thumb_normal') || $(ele).hasClass('image-thumb'))){
			var found=false;
			while(a.parentNode != null && a.parentNode!= undefined && a.parentNode.tagName != null && a.parentNode.tagName != undefined &&  a.tagName.toUpperCase() != 'A'){
				a = a.parentNode;
				found=  true;
				break;
			};
			if(!found){return;}
		}

		if($(a).hasClass('buttonlink') || $(a).hasClass('menu_core_mini')){
			return ;
		}

		var href = a.getAttribute('href');
		if(href == null && href == undefined){
			return;
		}

		var p = a;

		if(ynfbpp.checkIngoreClasses(p) == false){
			return ;
		}

		for(var i =0; i<ynfbpp.pt.length; ++i) {
			var data = ynfbpp.pt[i](href);

			if(data != null && data != undefined && data != false) {
				ynfbpp.ele = $(ele);
				ynfbpp.href = href;
				ynfbpp.data = data;
				if(ynfbpp.timeoutId) {
					try {
						window.clearTimeout(ynfbpp.timeoutId);
					} catch(e) {

					}
				}
				$(a).addEvent('mouseleave',function(){ynfbpp.resetTimeout(0);});
				var eventDoc = (e.target && e.target.ownerDocument) || document;
				var doc = eventDoc.documentElement;
				var body = eventDoc.body;
				ynfbpp.timeoutId = 0;
				ynfbpp.isRunning = 0;
				ynfbpp.dir.cx = e.event.clientX;
				ynfbpp.dir.cy = e.event.clientY;
				ynfbpp.dir.x = e.event.clientX +
					(doc && doc.scrollLeft || body && body.scrollLeft || 0) -
					(doc && doc.clientLeft || body && body.clientLeft || 0);
				ynfbpp.dir.y = e.event.clientY +
					(doc && doc.scrollTop  || body && body.scrollTop  || 0) -
					(doc && doc.clientTop  || body && body.clientTop  || 0 );
				ynfbpp.timeoutId = window.setTimeout('ynfbpp.requestPopup()', ynfbpp.timeoutOpen);
				return ;
			}
		}

	},
	updateBoxContent: function(html){
		ynfbpp.boxContent.innerHTML = html;
		return ynfbpp;
	},
	startSending: function(html){
		ynfbpp.boxContent.innerHTML = '<div class="uiContextualDialogContentLoading"> \
                                <div class="uiYnfbppHovercardStageLoading"> \
                                    <div class="uiYnfbppHovercardContentLoading"> \
                                    ' +html+ ' \
                                    </div> \
                                </div> \
                            </div> \
                            ';
		return ynfbpp;

	},
	requestPopup : function() {
		ynfbpp.timeoutId = 0;
		var box = ynfbpp.getBox();
		box.style.display = 'none';

		if(!ynfbpp.data.match_type || !ynfbpp.data.match_id){
			return ;
		}

		if (!ynfbpp.checkEnableItem(ynfbpp.data.match_type)) {
			return;
		}

		var key = ynfbpp.data.match_type + '-' + ynfbpp.data.match_id;
		if(ynfbpp.cached[key] != undefined){
			ynfbpp.showPopup(ynfbpp.cached[key]);
			return;
		}
		var jsonRequest = new Request.JSON({
			url : en4.core.baseUrl + '?m=lite&module=ynfbpp&name=popup',
			onSuccess : function(json, text) {
				ynfbpp.cached[key] = json;
				ynfbpp.showPopup(json);
			}
		}).get({match_type:ynfbpp.data.match_type,match_id: ynfbpp.data.match_id});
		ynfbpp.startSending(en4.core.language.translate('Loading...'));
		ynfbpp.resetPosition(1);
		return ynfbpp;

	},
	resetTimeout: function($flag){
		ynfbpp.isMouseOver = $flag;
		if(ynfbpp.mouseOverTimeoutId){
			try{
				window.clearTimeout(ynfbpp.mouseOverTimeoutId);
				ynfbpp.mouseOverTimeoutId = 0;
				if(ynfbpp.timeoutId){
					try{
						window.clearTimeout(ynfbpp.timeoutId);
						ynfbpp.timeoutId = 0;
					}catch(e){
					}
				}
			}catch(e){
			}
		}
		if($flag ==0){
			ynfbpp.data.match_id = 0;
			ynfbpp.mouseOverTimeoutId = window.setTimeout('ynfbpp.closePopup()',ynfbpp.timeoutClose);
		}
		return ynfbpp;

	},
	closePopup: function(){
		box = ynfbpp.getBox();
		box.style.display = 'none';
		ynfbpp.isShowing = 0;
		return ynfbpp;
	},
	resetPosition: function(flag){
		ynfbpp.isShowing = 1;
		var box = ynfbpp.getBox();
		var ele =  ynfbpp.ele;

		if(!ele){
			return ;
		}
		var pos = ele.getPosition();
		var size = ele.getSize();

		if(pos == null || pos == undefined){
			return ;
		}

		if(ynfbpp.dir.cy > 510){
			box.style.top =  ynfbpp.dir.y - 20 + 'px';
			box.removeClass('uiYnfbppDialogDirDown').addClass('uiYnfbppDialogDirUp');
		}else{
			box.style.top =  ynfbpp.dir.y + 16 + 'px';
			box.removeClass('uiYnfbppDialogDirUp').addClass('uiYnfbppDialogDirDown');
		}

		// check the position of the content
		var px = ynfbpp.dir.x;
		if(window.getSize().x - ynfbpp.dir.x > 600){
			box.style.left =  px - 20 + 'px';
			box.removeClass('uiYnfbppDialogDirLeft').addClass('uiYnfbppDialogDirRight');
		}else{
			box.style.left =  px + 20 + 'px';
			box.removeClass('uiYnfbppDialogDirRight').addClass('uiYnfbppDialogDirLeft');
		}

		if(flag){
			box.style.display = 'block';
		}
	},
	showPopup : function(json) {
		if(json == null || json == undefined){
			return ;
		}
		if(json.match_type != ynfbpp.data.match_type || json.match_id != ynfbpp.data.match_id){
			ynfbpp.closePopup();
			return ;
		}
		ynfbpp.resetPosition(1);
		var box = ynfbpp.getBox();
		ynfbpp.updateBoxContent(json.html);
		box.style.display='block';
		ynfbpp.bindEvent();
		return ynfbpp;
	},
	getBox: function(){
		if(ynfbpp.box){
			return ynfbpp.box;
		}
		var ct = document.createElement('DIV');
		ct.setAttribute('id','uiYnfbppDialog');
		var html = '<div class="uiYnfbppDialogOverlay" id="ynfbppUiOverlay" onmouseover="ynfbpp.resetTimeout(1)" onmouseout="ynfbpp.resetTimeout(0)">\
						<div class="uiYnfbppOverlayContent" id="ynfbppUiOverlayContent">\
						</div> \
						<i class="uiYnfbppContextualDialogArrow"><i class="uiYnfbppContextualDialogArrow_child"></i></i> \
					</div> \
		';
		ct.innerHTML = html;
		var body = document.getElementsByTagName('body')[0];
		body.appendChild(ct);
		$(ct).addClass('uiYnfbppDialog');
		ynfbpp.box = $('uiYnfbppDialog');
		ynfbpp.boxContent = $('ynfbppUiOverlayContent');
		return ynfbpp.box;
	},
	bindEvent: function(){
		Smoothbox.bind();
		$$('.uiYnfbpp_option_button').removeEvent('click').addEvent('click', function(){
			var parent = this.getParent('.uiYnfbpp_option');
			parent.toggleClass('uYnfbpp_show_option');
		});
		$$('.uiYnfbpp_option_button').addEvent('outerClick', function(){
			var parent = this.getParent('.uiYnfbpp_option');
			parent.removeClass('uYnfbpp_show_option');
		});
	}
};

ynfbpp.pt = [
	function(href) {
		reg = new RegExp("profile/([^\/]+)$", "i");
		match = href.match(reg);
		if(match != null && match != undefined) {
			return {
				match_id : decodeURIComponent(match[1]),
				match_type : 'user'
			}
		}
		return false;
	},function(href) {
		var match = href.match(/\/group\/(\d+)(\/)?/i);
		if(match != null && match != undefined) {
			return {
				match_id : decodeURIComponent(match[1]),
				match_type : 'group'
			}
		}
		return false;
	},function(href) {
		var match = href.match(/\/event\/(\d+)(\/)?/i);
		if(match != null && match != undefined) {
			return {
				match_id : decodeURIComponent(match[1]),
				match_type : 'event'
			}
		}
		return false;
	},function(href) {
		// match business route
		var regex = new RegExp("\\/" + ynfbpp.bussinessRoute + "\\/(\\d+)(\\/)?", "i");
		var match = href.match(regex);
		if(match != null && match != undefined) {
			return {
				match_id : decodeURIComponent(match[1]),
				match_type : 'business'
			}
		}
		return false;
	},function(href) {
		// match business route
		var regex = new RegExp("\\/" + "job-posting/company/detail/id" + "\\/(\\d+)(\\/)?", "i");
		var match = href.match(regex);
		if(match != null && match != undefined) {
			return {
				match_id : decodeURIComponent(match[1]),
				match_type : 'company'
			}
		}
		return false;
	},function(href) {
		// match business route
		var regex = new RegExp("\\/" + "listings" + "\\/(\\d+)(\\/)?", "i");
		var match = href.match(regex);
		if(match != null && match != undefined) {
			return {
				match_id : decodeURIComponent(match[1]),
				match_type : 'listing'
			}
		}
		return false;
	},function(href) {
		// match business route
		var regex = new RegExp("\\/" + "multi-listing" + "\\/(\\d+)(\\/)?", "i");
		var match = href.match(regex);
		if(match != null && match != undefined) {
			return {
				match_id : decodeURIComponent(match[1]),
				match_type : 'multilisting'
			}
		}
		return false;
	},function(href) {
		// match business route
		var regex = new RegExp("\\/" + "socialstore/store-front" + "\\/(\\d+)(\\/)?", "i");
		var match = href.match(regex);
		if(match != null && match != undefined) {
			return {
				match_id : decodeURIComponent(match[1]),
				match_type : 'store'
			}
		}
		return false;
	}
];

window.addEvent('domready', ynfbpp.boot);

function ynfbppLike(el) {
	var action = $(el).get('action'),
		id = $(el).get('listing_id'),
		likeText = $(el).get('like_text'),
		likedText = $(el).get('liked_text'),
		type = $(el).get('type'),
		like_icon = new Element('span', {
			class: 'ynicon yn-thumb-o-up'
		});
	new Request.JSON({
		url: en4.core.baseUrl + 'core/comment/' + action,
		method: 'post',
		data : {
			format: 'json',
			type : type,
			id : id,
			comment_id : 0
		},
		onSuccess: function(responseJSON, responseText) {
			if (responseJSON.status == true)
			{
				el.set('action', (action=='like') ? 'unlike' : 'like');
				el.set('html', (action=='like') ? likedText : likeText);
				like_icon.inject(el, 'top');
				if (action=='like') {
					el.removeClass('active');
				} else {
					el.addClass('active');
				}
			}
			location.reload();
		},
		onComplete: function(responseJSON, responseText) {
		}
	}).send();
}

function ynfbppAdminPreview(el) {
	// collect data
	var el = $(el),
		backgroundColor = $('popup_backgroundcolor').get('value'),
		footerColor = $('popup_footercolor').get('value'),
		borderColor = $('popup_bordercolor').get('value'),
		// borderWeight = $('ynfbpp_popup_borderweight').get('value'),
		// borderDash = $('ynfbpp_popup_borderdash').get('value'),
		previewContainer = $('ynfbppUiOverlay'),
		previewPopup = previewContainer.getChildren('#ynfbppUiOverlayContent')[0];
    previewContainer.setStyle('top', (el.offsetTop - 300 + 'px'));
	previewPopup.setStyle('border-color', borderColor);
    $$('.uiContextualDialogContent').setStyle('background-color', backgroundColor);
    $$('.uiYnfbppHovercardFooter').setStyle('background-color', footerColor);

    previewContainer.toggle();
    if (previewContainer.isDisplayed()) {
        setTimeout(function(){
            previewContainer.addEvent('outerClick', function(){
                this.hide();
                this.removeEvents('outerClick');
            });
        }, 20);
    }
}

function ynfbppFollowStore(store_id, user_id, text_url) {
	var request = new Request.JSON(
		{
			'format' : 'json',
			'url' : en4.core.baseUrl
			+ 'socialstore/my-follow-store/follow',
			'data' : {
				'user_id' : user_id,
				'store_id' : store_id
			},
			'onComplete' : function(response) {
				if (response.signin == 0) {
					window.location = en4.core.baseUrl + 'login/return_url/64-' + text_url;
					return;
				}
				var ele_array = $$('.store_follow_' + store_id);
				var length = ele_array.length;
				for (i = 0; i < length; i++) {
					var followText = "Follow";
					if (response.text != "Follow")
						followText = "Following";
					if(response.follow){
						$(ele_array[i]).removeClass("active");
					}else{
						$(ele_array[i]).addClass("active");
					}
					ele_array[i].innerHTML = followText;
				}
			}
		});
	request.send();
}
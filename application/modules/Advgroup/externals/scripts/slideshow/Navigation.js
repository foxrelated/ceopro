var navSlideShow;
document.addEvent('domready', function(){
	// cache the navigation elements
	var navs = $('advgroup_pagination').getElements('a');

	// create a basic slideshow
	navSlideShow = new SlideShow('advgroup_navigation-slideshow', {
		autoplay: true,
		delay: 5000,
		transition: 'blindLeft',
		selector: 'span',
		onShow: function(data){
			// update navigation elements' class depending upon the current slide
			navs[data.previous.index].removeClass('current');
			navs[data.next.index].addClass('current');
		}
	});

	navs.each(function(item, index){
		// click a nav item ...
		item.addEvent('click', function(event){
			event.stop();
			// pushLeft or pushRight, depending upon where
			// the slideshow already is, and where it's going
			var transition = (navSlideShow.index < index) ? 'blindLeft' : 'blindRight';
			// call show method, index of the navigation element matches the slide index
			// on-the-fly transition option
			navSlideShow.show(index, {transition: transition});
		});
	});
	var list_middle = $$('.layout_main .layout_middle .generic_layout_container');
	var position = list_middle[0].getCoordinates();
	var slideWidth = position.width;
	
	if($$('.advgroup_newsalbums').length > 0) {
		var itemWidth =  parseInt($$('.advgroup_newsalbums')[0].getStyle('width'));
		var containerWidth = $$('.layout_main .layout_middle .generic_layout_container')[0].getCoordinates().width;
		var padding = Math.floor((containerWidth - itemWidth*4)/5);
		
		$('advgroup_navigation-slideshow').style.width = slideWidth + "px";
		console.log(itemWidth);
		if($$('.advgroup_newsalbums').length >= 4) {
			$$('.advgroup_newsalbums').each(function(item, index){
				if(index == 0 || (index + 1) % 4 == 1) {
					item.setStyle("padding-left", padding);
					item.setStyle("padding-right", padding);
				} 
				else {
					item.setStyle("padding-right", padding);
				}
			});
		}
	}
});
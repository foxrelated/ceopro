window.addEvent('domready', function() {
	var elements = document.body.getElementsByTagName("a");
	<?php $listingtype_id = $this -> listingtype_id ;?>
	for (var i = 0; i < elements.length; i++) {
	    var href = elements[i].getAttribute("href");
	    if(href && (href.indexOf("listingtype_id") < 0)){
	    	if(href.indexOf("void(0)") < 0 && href.indexOf("multi-listing") >= 0){
	    		var myURI = new URI(href);
	    		myURI.setData('listingtype_id', '<?php echo $listingtype_id;?>');
	    		href = myURI.toString();
	    		elements[i].setAttribute("href", href);
			}
		}
	}
	
	$$('form').addEvent('submit', function() {
		var input = new Element('input', {
			'type': 'hidden',
			'name': 'listingtype_id',
			'value': '<?php echo $listingtype_id;?>'
		});
		this.grab(input);
		return true;
	});
});
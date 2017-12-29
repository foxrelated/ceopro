function changeListingType(loadNew) {
	var listingtype_id = $('listingtype').get('value');
	if (listingtype_id != '0') {
        new Request.HTML({
            method: 'post',
            url: en4.core.baseUrl + 'admin/ynmultilisting/listingtype/get-quicklink-options',
            data: {
                format: 'html',
                listingtype_id : listingtype_id,
            },
            onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript) {
            	$('quicklinks').set('html', responseHTML);
            	if (loadNew == true) {
            		resetQuicklinkIds();
            	}
            	else {
            		setQuicklinkIds();
            	}
            	$('quicklinks-wrapper').show();
            }
        }).send();
    } else {
    	$('quicklinks').empty();
    	$('quicklinks-wrapper').hide();
    }
}

function resetQuicklinkIds() {
	var ids = [];
	$$('#quicklinks option:selected').each(function(el) {
		ids.push(el.get('value'));
	});
	$('quicklink_ids').set('value', ids.join());
}

function setQuicklinkIds() {
	var ids = $('quicklink_ids').get('value');
	ids = ids.split(',');
	$$('#quicklinks option').each(function(el) {
		var id = el.get('value');
		if (ids.indexOf(id) == -1) {
			el.set('selected', false);
		}
		else {
			el.set('selected', true);
		}
	});
	
}

window.addEvent('load', function() {
	changeListingType(false);
	$$('#listingtype').addEvent('change', function() {
		changeListingType(true);
	});
	
	$$('#quicklinks').addEvent('change', function() {
		resetQuicklinkIds();
	});
});
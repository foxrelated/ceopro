<script>
window.addEvent('domready', function() {
	$$('#listingtype').addEvent('change', function() {
		changeListingType(true);
	});
});

function changeListingType(loadNew) {
	var listingtype_id = $('listingtype').get('value');
	if (listingtype_id != '0') {
        new Request.HTML({
            method: 'post',
            url: '<?php echo $this->url(Array('action'=>'filter-category', 'controller' => 'index'), 'ynmultilisting_general', true);?>',
            data: {
                format: 'html',
                type_id : type_id
            },
            onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript)
            {
                $("category_id").set('html', responseHTML);
            }
        }).send();
    } else {
    	$("category_id").set('html', '<option value="all"><?php echo $this->translate('All')?></option>');
    }
}
</script>

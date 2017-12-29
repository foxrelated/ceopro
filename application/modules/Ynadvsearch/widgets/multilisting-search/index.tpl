<?php
  /* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
))
?>
<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>
<div class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<?php echo $this->form->render($this); ?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		if ($('listingtype_id')) {
			$('listingtype_id').addEvent('change', function() {
				var form = this.getParent('#filter_form');
				var loading = form.getPrevious('.ynadvsearch_loading');
				loading.show();
				var params = form.toQueryString().parseQueryString();
				params.format = 'html';
				var request = new Request.HTML({
		            url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
		            data : params,
		            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
		                elements = Elements.from(responseHTML);
		                if (elements.length > 0) {
		                	var layout = form.getParent('.generic_layout_container');
		                	(elements[0]).replaces(layout);
		                	eval(responseJavaScript);
		                	en4.activity.updateNotifications();
		                }
		            }
		        });
		        request.send();
			});
		}
	});
</script>

<script type="text/javascript">
    <?php $session = new Zend_Session_Namespace('mobile'); ?>
    <?php if($session -> mobile): ?>
    window.addEvent('domready', function()
    {
        $$('#listing_title').addEvent('change', function()
        {
            $('filter_form').submit();
        });
    });
    <?php endif; ?>
    var pageAction =function(page){
        $('page').value = page;
        $('filter_form').submit();
    }
    window.addEvent('domready', function()
    {
        if($('text'))
        {
            new OverText($('text'), {
                poll: true,
                pollInterval: 500,
                positionOptions: {
                    position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
                    edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
                    offset: {
                        x: ( en4.orientation == 'rtl' ? -4 : 4 ),
                        y: 2
                    }
                }
            });
        }
        if($('within'))
        {
            new OverText($('within'), {
                poll: true,
                pollInterval: 500,
                positionOptions: {
                    position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
                    edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
                    offset: {
                        x: ( en4.orientation == 'rtl' ? -4 : 4 ),
                        y: 2
                    }
                }
            });
        }
    });

    function initialize() {
        var input = /** @type {HTMLInputElement} */(
                document.getElementById('location'));

        var autocomplete = new google.maps.places.Autocomplete(input);

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            document.getElementById('lat').value = place.geometry.location.lat();
            document.getElementById('long').value = place.geometry.location.lng();
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);

    var getCurrentLocation = function(obj)
    {
        if(navigator.geolocation) {

            navigator.geolocation.getCurrentPosition(function(position) {

                var pos = new google.maps.LatLng(position.coords.latitude,
                        position.coords.longitude);

                if(pos)
                {

                    current_posstion = new Request.JSON({
                        'format' : 'json',
                        'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynmultilisting_general') ?>',
                        'data' : {
                    latitude : pos.lat(),
                            longitude : pos.lng(),
                },
                    'onSuccess' : function(json, text) {

                    if(json.status == 'OK')
                    {
                        document.getElementById('location').value = json.results[0].formatted_address;
                        document.getElementById('lat').value = json.results[0].geometry.location.lat;
                        document.getElementById('long').value = json.results[0].geometry.location.lng;
                    }
                    else{
                        handleNoGeolocation(true);
                    }
                }
                });
                current_posstion.send();

            }

        }, function() {
        handleNoGeolocation(true);
    });
    }
    else {
        // Browser doesn't support Geolocation
        handleNoGeolocation(false);
    }
    return false;
    }

    function handleNoGeolocation(errorFlag) {
        if (errorFlag) {
            document.getElementById('location').value = 'Error: The Geolocation service failed.';
        }
        else {
            document.getElementById('location').value = 'Error: Your browser doesn\'t support geolocation.';
        }
    }
</script>
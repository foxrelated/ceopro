<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
  ))
?>

<?php
$googleApi = Engine_Api::_()->getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyBUNcrjiTBFrc5NS9b_yrmH6TNRRUkSQzM');
$this->headScript()->appendFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=' . $googleApi);
?>
<?php echo $this->form->render($this) ?>
<script type="text/javascript">
<?php
$session = new Zend_Session_Namespace('mobile');
if($session -> mobile): ?>
	$$('#displayname').addEvent('change', function()
	{
	     $('filter_form').submit();
	});
	$$('#within').addEvent('change', function()
	{
	     $('filter_form').submit();
	});
	$$('#location').addEvent('change', function()
	{
	     $('filter_form').submit();
	});
<?php
endif;
?>
 var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
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
	      	var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			if(pos)
			{
				var current_posstion = new Request.JSON({
					'format' : 'json',
					'url' : '<?php echo $this->url(array('action'=>'get-my-location', 'controller' => 'edit'), 'ynmember_extended') ?>',
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
<style type="text/css">
	@media (max-width: 992px) {
		#global_page_advgroup-index-listing #global_content {
			min-height: 475px;
		}
	
	}
</style>

<?php if( $session -> mobile) { ?>
	<script type="text/javascript">
		var myGrabSearch = $$('.layout_ynmember_browse_search')[0];
  		$$('.layout_ynmember_browse_menu')[0].grab( myGrabSearch );
	</script>
<?php } ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Place Autocomplete</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        min-height: 100%;
        margin: 0px;
        padding: 0px
      }
      .controls {
        margin-top: 16px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        padding: 0 11px 0 13px;
        width: 400px;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        text-overflow: ellipsis;
      }

      #pac-input:focus {
        border-color: #4d90fe;
        margin-left: -1px;
        padding-left: 14px;  /* Regular padding-left + 1. */
        width: 401px;
      }

      .pac-container {
        font-family: Roboto;
      }

	</style>
    
  </head>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$latitude = $request->getParam('latitude',10.771299);
$longtitude = $request->getParam('longtitude',106.65335800000003);
$reference = $request->getParam('reference',"CnRoAAAAibnWNEq4yDnvH1fQV7IyiIaUEp9zGnLQHLfhNTfElk6HyLKWh0LNa-4LUE7BzpygtjR7yAXiMuQVSlFO00WZAN4WoYhcR6TTgxt6hMS9IaPuvC3s_fIbVbVQpuQXYN7HnWDd6cbpQMaFiwy9ogdozBIQg4t9gvS4pO_ud2_0H0jI8RoUDUqXRWyPxfC14LI3g_hjwvC1CUc");

?> 
<body>
    <input id="pac-input" class="controls" type="text" value="<?php echo $this->params['location']?>"
        placeholder="<?php echo $this->translate('Enter a location') ?>">
        
    <input type="text" name="latitude" id="latitude" value="<?php echo $latitude?>" style="display:none">
    <input type="text" name="longtitude" id="longtitude" value="<?php echo $longtitude?>" style="display:none">
    <input type="text" name="reference" id="reference" value="<?php echo $reference?>" style="display:none">
    
    <div id="map-canvas" class="map-canvas" style="max-width: 100%; max-height: 300px;"></div>
    <div id="markerStatus"></div>    
    <br />
    
    <div class="form-wrapper" id="buttons-wrapper"><fieldset id="fieldset-buttons">
	
    <div class="advgroup_location">    
		<button><a href="javascript:;" onclick="action_add()"><?php echo $this->translate("Add this location");?></a></button>
 		<?php echo $this->translate("or");?>
			<a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="parent.Smoothbox.close();">cancel</a>
	</div>
  </body>
</html>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc') ?>&v=3.exp&libraries=places"></script>

<script type="text/javascript">
    var action_add = function()	{		
		
		if(document.getElementById('pac-input').value == '')
			return;
		var location = document.getElementById('pac-input').value;
		parent.$('location').value = location;	
			
		var latitude = document.getElementById('latitude').value;
		parent.$('latitude').value = latitude;
		
		var longitude = document.getElementById('longitude').value;
		parent.$('longtitude').value = longtitude;
		
		var reference = document.getElementById('reference').value;
		parent.$('reference').value = reference;
		
		var html = '<?php echo $this->params['location'];?>' + '/location/'+location+'/latitude/'+latitude+'/longtitude/'+longitude+'/reference/'+reference;
				
		parent.$$('#location-element .description a')[0].set('href',html) ;
		parent.$$('#location-element .description a')[0].set('html','<?php echo $this->translate("Edit location")?>');
		parent.Smoothbox.close();
	}  
    
    function geocodePosition(pos) {
	 	geocoder.geocode({
	    	latLng: pos
	  	}, function(responses) {
	    	if (responses && responses.length > 0) 
	    	{
	      		updateMarkerAddress(responses[0].formatted_address);
	    	} 
	    	else 
	    	{
	      		updateMarkerAddress('Cannot determine address at this location.');
	    	}
	  	});
	}
	function initialize() {
		var center = new google.maps.LatLng(10.771299, 106.65335800000003);
		var mapOptions = {
	    	center: center,
	    	zoom: 17
  	 	};  	 	
  	 	
	  	var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
		<?php if(isset($this->params['location']) && $this->params['location'] != ''):?>
			var center = new google.maps.LatLng(<?php echo $this->params['latitude']?>, <?php echo $this->params['longitude']?>);
			var map = new google.maps.Map(document.getElementById('map-canvas'), {
		    	center: center,
		    	zoom: 17
		  	});
	
			
	  
		<?php endif;?>	
		
		<?php if($this->view == 'view'):?>
			marker = new google.maps.Marker({
	    	map:map,
	    	draggable:false,
	    	animation: google.maps.Animation.DROP,
	    	position: center
		});
		<?php endif?>
		
		<?php if($this->view == 'edit'):?>
		marker = new google.maps.Marker({
	    	map:map,
	    	draggable:true,
	    	animation: google.maps.Animation.DROP,
	    	position: center
		});
		google.maps.event.addListener(marker, 'dragend', toggleBounce);
		  
		<?php endif;?>	
		
	  	var input = /** @type {HTMLInputElement} */(document.getElementById('pac-input'));
	
	  	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);  
	
	  	var autocomplete = new google.maps.places.Autocomplete(input);
	  	autocomplete.bindTo('bounds', map);
	
	  	var infowindow = new google.maps.InfoWindow();
	  	  	
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
		    infowindow.close();
		    //marker.setVisible(false);
		    var place = autocomplete.getPlace();
		    if (!place.geometry) {
		      return;
		    }	    
		    
		    document.getElementById('latitude').value = place.geometry.location.d;		
			document.getElementById('longtitude').value = place.geometry.location.e;
			document.getElementById('reference').value = place.reference;
			
		    // present it on a map.
		    map.setCenter(place.geometry.location);
		    map.setZoom(17);  // Why 17? Because it looks good.
		    
		    
		    marker.setPosition(place.geometry.location);
		    marker.setVisible(true);
		    
		    google.maps.event.addListener(marker, 'dragend', toggleBounce);
		
		    var address = '';
		    if (place.address_components) {
		      address = [
		        (place.address_components[0] && place.address_components[0].short_name || ''),
		        (place.address_components[1] && place.address_components[1].short_name || ''),
		        (place.address_components[2] && place.address_components[2].short_name || '')
		      ].join(' ');
		    }
		
		    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
		    infowindow.open(map, marker);
	    });
	    
		function toggleBounce() {
	  		if (marker.getAnimation() != null) {
	    		marker.setAnimation(null);
	  		} 
	  		else {
	    		marker.setAnimation(google.maps.Animation.BOUNCE);
	  		}
	  		var point = marker.getPosition();
		    document.getElementById('latitude').value = point.d;		
			document.getElementById('longtitude').value = point.e;
		}
	}
	
	google.maps.event.addDomListener(window, 'load', initialize);

</script>
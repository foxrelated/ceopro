<?php if (!$this->listing->longitude || !$this->listing->latitude) : ?>
<div class="tip" style="margin: 10px">
    <span><?php echo $this->translate('Location not found.')?></span>
</div>
<?php else: ?>
<?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?key=". Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc')."&v=3.exp&sensor=false&libraries=places"); ?>
<div id="ynmultilisting_direction">
  <h5>
	 <?php echo $this->listing->location; ?>
  </h5>
<div style="display:none" class="ynmultilisting_widget_location_direction">
	<label for="starting_location"><?php echo $this->translate("Starting location");?></label>
	<input id="pac-input" class="controls" placeholder="<?php echo $this->translate("Enter a location"); ?>" type="text" name="starting_location" id="starting_location" />
    <span class="close-btn" id="ynmultilisting_location_direction_close_btn">x</span>
</div>
<div id="map-canvas" style="height: 220px; margin-top: 20px;"></div>
<br/>
<span class="ynicon yn-map-marker"></span>
<span class="ynmultilisting_get_location">
    <?php echo $this->htmlLink(
    array('route' => 'ynmultilisting_specific', 'action' => 'direction', 'listing_id' => $this->listing->getIdentity()), 
    $this->translate('Get Direction'),
    array('class' => 'smoothbox')) ?>
</span>
</div>
<script>

var fromPoint;
var endPoint = new  google.maps.LatLng(<?php echo $this->listing->latitude;?>, <?php echo $this->listing->longitude;?>);
var directionsService = new google.maps.DirectionsService();
var directionsDisplay;
var map;
var marker;

function initialize() {
  var center =  new google.maps.LatLng(<?php echo $this->listing->latitude;?>, <?php echo $this->listing->longitude;?>);
  var mapOptions = {
    center: center,
    zoom: 13
  };

  directionsDisplay = new google.maps.DirectionsRenderer();
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  directionsDisplay.setMap(map);
  
  var input = /** @type {HTMLInputElement} */(
      document.getElementById('pac-input'));

  var types = document.getElementById('type-selector');
  //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  //map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);

  var infowindow = new google.maps.InfoWindow();

  marker = new google.maps.Marker({
  	map:map,
  	draggable:true,
  	animation: google.maps.Animation.DROP,
  	position: center
  });
	
  google.maps.event.addListener(marker, 'dragend', toggleBounce);

  google.maps.event.addListener(autocomplete, 'place_changed', function() {
    infowindow.close();
    marker.setVisible(false);
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);  // Why 17? Because it looks good.
    }
    marker.setIcon(/** @type {google.maps.Icon} */({
      url: place.icon,
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(35, 35)
    }));
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }

    infowindow.setContent('<strong>' + place.name + '</strong><br>' + address);
    infowindow.open(map, marker);

    fromPoint = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
  });

  // Sets a listener on a radio button to change the filter type on Places
  // Autocomplete.
  function setupClickListener(id, types) {
    var radioButton = document.getElementById(id);
    google.maps.event.addDomListener(radioButton, 'click', function() {
      autocomplete.setTypes(types);
    });
  }

  function toggleBounce() {
		if (marker.getAnimation() != null) 
		{
  			marker.setAnimation(null);
		} 
		else 
		{
  			marker.setAnimation(google.maps.Animation.BOUNCE);
		}
		var point = marker.getPosition();
		fromPoint = new google.maps.LatLng(point.lat(), point.lng());
  }
  setupClickListener('changetype-all', []);
  setupClickListener('changetype-establishment', ['establishment']);
  setupClickListener('changetype-geocode', ['geocode']);
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php endif; ?>


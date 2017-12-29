<?php $googleApi = Engine_Api::_()->getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyBUNcrjiTBFrc5NS9b_yrmH6TNRRUkSQzM'); ?>
<script src="//maps.googleapis.com/maps/api/js?key=<?php echo $googleApi; ?>&v=3.exp&sensor=false"></script>
<?php if($this->datas != '[]'):?>
<script type="text/javascript">
	var datas = <?php echo $this->datas?>;
	var contents = <?php echo $this->contents?>;
    var center = new google.maps.LatLng(datas[0]['latitude'], datas[0]['longitude']);
    var neighborhoods = [];
    var markers = [];
	var iterator = 0;
	var latlng;
    for(i=0 ; i< datas.length ; i++)
    {
    	neighborhoods.push(new google.maps.LatLng(datas[i]['latitude'], datas[i]['longitude']));
    }
    latlng = neighborhoods;
    
	function initialize() {
		var mapOptions = {
	    	zoom: 11,
	    	center: center
  	  	};
  	  	
	  	map = new google.maps.Map(document.getElementById('layout_ynmember_list_most_items-map-canvas'),mapOptions);
      	for (var i = 0; i < neighborhoods.length; i++) {
      		addMarker(i);
  		}
      	 
      	var latlngbounds = new google.maps.LatLngBounds();
		for (var i = 0; i < latlng.length; i++) {
			latlngbounds.extend(latlng[i]);
		}
		map.fitBounds(latlngbounds);
	}
	function addMarker(i) {
  		marker = new google.maps.Marker({
	    	position: neighborhoods[iterator],
	    	map: map,
	    	draggable: false,
	    	animation: google.maps.Animation.DROP,
	    	icon: datas[i]['icon']
  		})  		
  		markers.push(marker);
  		iterator++;  		
  		infowindow = new google.maps.InfoWindow({});  		
  		google.maps.event.addListener(marker, 'mouseover', function() {    		
    		infowindow.close();
    		infowindow.setContent(contents[i])
    		infowindow.open(map,markers[i]);
  		});
	}
	google.maps.event.addDomListener(window, 'load', initialize);
</script>
 
<div id="layout_ynmember_list_most_items-map-canvas" style="height: 450px;"></div>
<?php else:?>
	<div class="tip">
        <span>
            <?php echo $this->translate("There are no members view on this map") ?>
        </span>
    </div>
<?php endif;?>
<?php if($this->layout()->orientation == 'right-to-left'):?> 
    <style>
      #layout_ynmember_list_most_items-map-canvas .gm-style-iw{
          left: auto !important;
          right: 15px;
          direction: rtl;
          unicode-bidi: embed; 
          text-align: right; 
      }

      #layout_ynmember_list_most_items-map-canvas .ynmember-maps-content > span{
        margin-left: 5px !important;
        margin-right: 0 !important; 
      }

      #layout_ynmember_list_most_items-map-canvas  .gm-style-iw *{
          direction: rtl;
          unicode-bidi: embed; 
          text-align: right; 
      }

      #layout_ynmember_list_most_items-map-canvas  .gm-style-iw + div{
          left: 12px;
          right: auto !important;
      }
    .tip{
      text-align: right;
    }
    </style>
<?php endif;?>
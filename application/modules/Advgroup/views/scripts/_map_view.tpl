<?php

if($this->datas != '[]'):?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc') ?>&v=3.exp"></script>
<script type="text/javascript">
	var datas = <?php echo $this->datas?>;
	var contents = <?php echo $this->contents?>;
    var center = new google.maps.LatLng(datas[0]['latitude'], datas[0]['longitude']);
    var neighborhoods = [];
    var markers = [];
	var iterator = 0;
    
    for(i=0 ; i< datas.length ; i++)
    {
    	neighborhoods.push(new google.maps.LatLng(datas[i]['latitude'], datas[i]['longitude']));
    }
	
	function initialize() {
		var mapOptions = {
	    	zoom: 10,
	    	center: center
  	  	};
  	  	
	  	map = new google.maps.Map(document.getElementById('layout_advgroup_list_most_items-map-canvas'),mapOptions);
      
      	for (var i = 0; i < neighborhoods.length; i++) {
      		addMarker(i);
  		}
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
 
<div id="layout_advgroup_list_most_items-map-canvas" style="height: 450px;"></div>
<?php else:?>
	<div class="tip">
    <?php if($this->layout()->orientation == 'right-to-left'):?> 
    <style>
      .tip{
        text-align: right;
      }
    </style>
  <?php endif;?>
        <span>
            <?php echo $this->translate("There are no group view on this map") ?>
        </span>
    </div>
<?php endif;?>
<?php if($this->layout()->orientation == 'right-to-left'):?> 
    <style>
      .advgroup-maps-content > img{
            float: right !important;
            margin-right: 0 !important;
            margin-left: 10px !important;
        }
        .advgroup-maps-info{
          float: right !important;
        }
        .advgroup-maps-info *{
          direction: rtl;
          unicode-bidi: embed;
          text-align: right;
        }
        .advgroup-maps-info > span{
          margin-right: 0 !important;
          margin-left: 10px;
          float: right;
        }
        a.advgroup-maps-title{
          float: right !important;
        }
    </style>
     <?php endif;?>
<script>

	function checkValidate() {
		    var error = [];
		    if ($('email_subscribe').value == '') 
		    {
		        error.push('<?php echo $this->translate('Email must not be empty!');?>');
		    }
		    else
		    {	
		    	if(!validateEmail($('email_subscribe').value))
		    	{
		    		error.push('<?php echo $this->translate('Email is invalid!');?>');
		    	}
		    }
		    if (($('lat_subscribe').value != 0) && ($('long_subscribe').value != 0))
		    {
		    	if (isInteger(($('within_subscribe').value))) {
			    	if($('within_subscribe').value < 0)
			    	{
			    		error.push('<?php echo $this->translate('Radius is invalid!');?>');
			    	}
		    	}
		    	else
		    	{
		    		 error.push('<?php echo $this->translate('Radius must be a Integer!');?>');
		    	}
		    }
		    var ul_element;
		    if (error.length > 0) {
		        var error_list = $('error_list');
		        error_list.empty();
		        for (var i = 0; i < error.length; i++) {
		        	if(i == 0)
		        	{
		        		ul_element = new Element('ul', {
				                text: "",
				            });
				         ul_element.set('class', 'form-errors');
				         error_list.grab(ul_element);
		        	}
		            var li = new Element('li', {
		                text: ''+error[i],
		            });
		            li.set('class', 'form-errors');
		            ul_element.grab(li);
		            document.getElementById('error_list').scrollIntoView();
		        }
		        var notice = $('notice');
	            notice.empty();
		    }
		    else {
		    	subscribeListing();
		    }
		}
	function subscribeListing() {
	    var category_id = $('category_id_subscribe').value;
	    var latitude = $('lat_subscribe').value;
	    var longitude = $('long_subscribe').value;
	    var email = $('email_subscribe').value;
	    var within = $('within_subscribe').value;
	    
	    var url = '<?php echo $this -> url(array('action' => 'subscribe-listing'), 'ynmultilisting_general', true);?>';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'category_id': category_id,
	            'latitude': latitude,
	            'longitude': longitude,
	            'email': email,
	            'within': within,
	        },
	        onSuccess: function(responseJSON) 
			{
				if(responseJSON.json == 'true')
				{
					 var notice = $('notice');
					 var error_list = $('error_list');
					 error_list.empty();
					 notice.empty();
					 
					 var ul_element = new Element('ul', {
			                text: "",
			            });
			         ul_element.set('class', 'form-notices');
			         notice.grab(ul_element);
					 
					 var li = new Element('li', {
			                text: responseJSON.message,
		             });
		             ul_element.grab(li);
		             
		             document.getElementById('notice').scrollIntoView();
	            }
	            else
	            {
	            	 var notice = $('notice');
	            	 var error_list = $('error_list');
	            	 notice.empty();
			         error_list.empty();   
			         
			         var ul_element = new Element('ul', {
			                text: "",
			            });
			         ul_element.set('class', 'form-errors');
			         error_list.grab(ul_element);
			         
					 var li = new Element('li', {
		                text: responseJSON.message,
		             });
			         
		             ul_element.grab(li);
		             document.getElementById('error_list').scrollIntoView();
	            }  
			}
	    }).send();
	}	
		
	function isInteger(obj) {
    	return (obj.toString().search(/^-?[0-9]+$/) == 0 );
	}	
	
	function validateEmail(email) { 
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	} 
	
</script>

<style type="text/css" media="screen">
	ul.form-notices {
		margin: 0px 0px 0px 0px;
	}
	ul.form-errors {
		margin: 0px 0px 0px 0px;
	}
</style>

<div id="notice" style="margin-top: 0px;" class="form-notices">    
</div>

<div id="error_list" style="margin-top: 0px;" class="form-errors">    
</div>

<?php echo $this->form->render($this) ?>

<?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?key=". Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc')."&v=3.exp&sensor=false&libraries=places");?>

<script type="text/javascript">

  function initialize() {
	 	var input = /** @type {HTMLInputElement} */(
			document.getElementById('location_subscribe'));
	
	  	var autocomplete = new google.maps.places.Autocomplete(input);
	
	  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    	var place = autocomplete.getPlace();
		    if (!place.geometry) {
		     	return;
		    }
			document.getElementById('location_address_subscribe').value = place.formatted_address;		
			document.getElementById('lat_subscribe').value = place.geometry.location.lat();		
			document.getElementById('long_subscribe').value = place.geometry.location.lng();
	    });
	}
  
   google.maps.event.addDomListener(window, 'load', initialize); 
  
  var getSubscribeCurrentLocation = function(obj)
	{	
		if(navigator.geolocation) {
	    	navigator.geolocation.getCurrentPosition(function(position) {
	      	var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			if(pos)
			{
				var current_posstion = new Request.JSON({
					'format' : 'json',
					'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynmultilisting_general') ?>',
					'data' : {
						latitude : pos.lat(),
						longitude : pos.lng(),
					},
					'onSuccess' : function(json, text) {
						
						if(json.status == 'OK')
						{
							document.getElementById('location_subscribe').value = json.results[0].formatted_address;
							document.getElementById('location_address_subscribe').value = json.results[0].formatted_address;
							document.getElementById('lat_subscribe').value = json.results[0].geometry.location.lat;		
							document.getElementById('long_subscribe').value = json.results[0].geometry.location.lng; 		
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
    		document.getElementById('location_subscribe').value = 'Error: The Geolocation service failed.';
  		} 
  		else {
   			document.getElementById('location_subscribe').value = 'Error: Your browser doesn\'t support geolocation.';
   		}
 	}
</script>

<h2><?php echo $this->translate("YouNet Multiple Listings Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
<?php
// Render the menu
//->setUlClass()
echo $this->navigation()->menu()->setContainer($this->navigation)->render()
?>
</div>
<?php endif; ?>

<?php if ($this->error): ?>
<div class="tip">
    <span><?php echo $this->message;?></span>
</div>
<?php else: ?>
    
<div class='clear'>
    <div class='settings'>
    <?php echo $this -> form -> render($this); ?>
    </div>
</div>

<?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?key=". Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc')."&v=3.exp&sensor=false&libraries=places");?>

<!-- for autocomplete-->
<style>
    #owner_ids-wrapper, #listing_ids-wrapper {
        display: none;
    }
</style>
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/AutocompleterExtend.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/Autocompleter.Request.js');
?>
<script type="text/javascript">
    // Populate data
    var maxRecipients = 0;
    var to = {
        id : false,
        type : false,
        guid : false,
        title : false
    };
    
    window.addEvent('domready', function() {
        //for owners autocomplete
        new Autocompleter2.Request.JSON('owners', '<?php echo $this->url(array('module' => 'ynmultilisting', 'controller' => 'quicklink', 'action' => 'suggest-owner'), 'admin_default', true) ?>', {
            'toValues': 'owner_ids',
            'minLength': 1,
            'delay' : 250,
            'autocompleteType' : 'message',
            'multiple': true,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'injectChoice': function(token){
                if(token.type == 'user'){
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices',
                        'html': token.photo,
                        'id':token.label
                    });
                    new Element('div', {
                        'html': this.markQueryValue(token.label),
                        'class': 'autocompleter-choice'
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                }
            },
            onPush : function(){
                if((maxRecipients != 0) && (document.getElementById('owner_ids').value.split(',').length >= maxRecipients) ){
                    document.getElementById('owners').style.display = 'none';
                }
            }
        });
        
        <?php foreach ($this->owners as $owner) : ?>
        var myElement = new Element("span", {
            'id' : 'owner_ids_tospan_' + '<?php echo $owner->getIdentity()?>',
            'class': 'user_tag',
            'html' :  "<a target='_blank' href='<?php echo $owner->getHref()?>'>"+'<?php echo $this->itemPhoto($owner, 'thumb.icon')?><?php echo $owner->getTitle()?>'+"</a> <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\"<?php echo $owner->getIdentity()?>\", \"owner_ids\",\"owners\");'>x</a>"
        });
        document.getElementById('owner_ids-element').appendChild(myElement);
        document.getElementById('owner_ids-wrapper').show();
        document.getElementById('owner_ids-wrapper').style.height = 'auto';
        <?php endforeach; ?>
    
        //for listings autocomplete
        new Autocompleter2.Request.JSON('listings', '<?php echo $this->url(array('module' => 'ynmultilisting', 'controller' => 'quicklink', 'action' => 'suggest-listing', 'listingtype_id' => $this->listingType->getIdentity()), 'admin_default', true) ?>', {
            'toValues': 'listing_ids',
            'minLength': 1,
            'delay' : 250,
            'autocompleteType' : 'message',
            'multiple': true,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'injectChoice': function(token){
                if(token.type == 'ynmultilisting_listing'){
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices',
                        'id':token.label
                    });
                    new Element('div', {
                        'html': this.markQueryValue(token.label),
                        'class': 'autocompleter-choice'
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                }
            },
            onPush : function(){
                if((maxRecipients != 0) && (document.getElementById('listing_ids').value.split(',').length >= maxRecipients) ){
                    document.getElementById('listings').style.display = 'none';
                }
            }
        });
        <?php foreach ($this->listings as $listing) : ?>
        var myElement = new Element("span", {
            'id' : 'listing_ids_tospan_' + '<?php echo $listing->getIdentity()?>',
            'class': 'listing_tag'
        });
        var html = '';
        html += '<a class="listing-link" target="_blank" href="<?php echo $listing->getHref()?>"><?php echo $listing->title?></a>';
        html += '<p class="listing-owner"><?php echo $this->htmlLink($listing->getOwner()->getHref(), $listing->getOwner()->getTitle())?></p>';
        html += '<p class="listing-category"><?php echo $this->translate('Category: %s', $listing->getCategory()->getTitle())?></p>';
        html += "<a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\"<?php echo $listing->getIdentity()?>\", \"listing_ids\", \"listings\");'>x</a>";
        myElement.innerHTML = html; 
        document.getElementById('listing_ids-element').appendChild(myElement);
        document.getElementById('listing_ids-wrapper').show();
        document.getElementById('listing_ids-wrapper').style.height = 'auto';
        <?php endforeach; ?>
    });
  
    function removeFromToValue(id, hideLoc, elem) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = document.getElementById(hideLoc).value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";

        var checkMulti = id.search(/,/);

        // check if we are removing multiple recipients
        if (checkMulti!=-1){
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++){
                removeToValue(recipientsArray[i], toValueArray, hideLoc);
            }
        }
        else{
            removeToValue(id, toValueArray, hideLoc);
        }

        // hide the wrapper for usernames if it is empty
        if (document.getElementById(hideLoc).value==""){
            document.getElementById(hideLoc+'-wrapper').style.height = '0';
            document.getElementById(hideLoc+'-wrapper').hide();
        }

        document.getElementById(elem).style.display = 'block';
    }

    function removeToValue(id, toValueArray, hideLoc){
        for (var i = 0; i < toValueArray.length; i++){
            if (toValueArray[i]==id) toValueIndex =i;
        }

        toValueArray.splice(toValueIndex, 1);
        document.getElementById(hideLoc).value = toValueArray.join();
    }

</script>


<!-- for day picker-->
<script src="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<script type="text/javascript">
    window.addEvent('load', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
        });
    });
</script>

<script type="text/javascript">
    window.addEvent('domready', function() {
        if ($('add-more-price')) {
            $('add-more-price').addEvent('click', function() {
                var count = $$('.prices-input').length;
                var tr = this.getParent('.prices-input');
                var newTr = tr.clone();
                var price_from = newTr.getElement('.price-from');
                price_from.set('value', '');
                var price_to = newTr.getElement('.price-to');
                price_to.set('value', '');
                var action = newTr.getElement('.price-action');
                action.empty();
                action.innerHTML = '<i class="fa fa-minus"></i>';
                action.addEvent('click', function() {
                    newTr.destroy();
                });
                newTr.inject(tr, 'after');
            })
        }
        
        $$('.price-action:not([id="add-more-price"])').addEvent('click', function() {
            this.getParent('.prices-input').destroy();
        })
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
            
            document.getElementById('latitude').value = place.geometry.location.lat();       
            document.getElementById('longitude').value = place.geometry.location.lng();
        });
    }
  
    google.maps.event.addDomListener(window, 'load', initialize); 
  
    var getCurrentLocation = function(obj) {   
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
            var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            if(pos) {
                
                var current_posstion = new Request.JSON({
                    'format' : 'json',
                    'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynmultilisting_general') ?>',
                    'data' : {
                        latitude : pos.lat(),
                        longitude : pos.lng(),
                    },
                    'onSuccess' : function(json, text) {
                        
                        if(json.status == 'OK') {
                            document.getElementById('location').value = json.results[0].formatted_address;
                            document.getElementById('latitude').value = json.results[0].geometry.location.lat;       
                            document.getElementById('longitude').value = json.results[0].geometry.location.lng;      
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
<?php endif; ?>
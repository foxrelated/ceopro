<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/AutocompleterExtend.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/Autocompleter.Request.js');
?>

<style>
	#owners-wrapper {
		display: none;
	}
	
</style>
<script type="text/javascript">
    window.addEvent('domready', function() {
    	$('ynmultilisting-import-module').addEvent('submit', function() {
	        if ($('all_owner-0').checked && ($('owner_ids').get('value') == '')) {
	        	var div = new Element('div', {
	               'class': 'ynmultilisting-form-error' 
	            });
	            var p = new Element('p', {
	                'class': 'ynmultilisting-form-message',
	                text: '<?php echo $this->translate('Please select at least 1 owner for import listings.')?>',
	            });
	            var button = new Element('button', {
	                'class': 'ynmultilisting-form-button',
	                text: '<?php echo $this->translate('Ok')?>',
	                onclick: 'parent.Smoothbox.close();'
	                
	            });
	            div.grab(p);
	            div.grab(button);
	            Smoothbox.open(div);
	            return false;
	        }
	        return true;
    	});
    });
    
    // Populate data
    var maxRecipients = 0;
    var to = {
        id : false,
        type : false,
        guid : false,
        title : false
    };
    
    window.addEvent('domready', function() {
    	
    	if ($('all_owner-0') && $('all_owner-1')) {
    		$('all_owner-0').addEvent('click', function() {
				$$('#owners-wrapper').show();
				if ($('owner_ids').get('value') != '') {
					$$('#owner_ids-wrapper').show();
				}
    		});
    		$('all_owner-1').addEvent('click', function() {
				$$('#owners-wrapper').hide();
				$$('#owner_ids-wrapper').hide();
    		});
    	}
        //for owners autocomplete
        new Autocompleter2.Request.JSON('owners', '<?php echo $this->url(array('module' => 'ynmultilisting', 'controller' => 'import', 'action' => 'suggest-owner'), 'admin_default', true) ?>', {
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

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Import Listings') ?></h3>
<a href="<?php echo $this->url(array('module'=>'ynmultilisting','controller'=>'import','action'=>'view-history'),'admin_default')?>"><i class="fa fa-history"></i> <?php echo $this->translate('View Import History')?></a>
<p><?php echo $this->translate("YNMULTILISTING_ADMIN_IMPORT_DESCRIPTION") ?></p>      
<br/>

<div id="ynmultilisting-import-tab">
    <div id="ynmultilisting-file-tab">
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module'=>'ynmultilisting', 'controller'=>'import', 'action' => 'file'), $this->translate('Import Listings From Files'))?>
    </div>
    <div id="ynmultilisting-module-tab" class="active">
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module'=>'ynmultilisting', 'controller'=>'import', 'action' => 'module'), $this->translate('Import Listings From Modules'))?>
    </div>
</div>

<div class='clear'>
    <div class='settings'>
    <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">
    $('listingtype').addEvent('change', function(){
        window.location.href = '<?php echo $this->url(array('module'=>'ynmultilisting','controller'=>'import','action'=>'module'), 'admin_default', true)?>/listingtype/'+this.get('value');
    });
</script>
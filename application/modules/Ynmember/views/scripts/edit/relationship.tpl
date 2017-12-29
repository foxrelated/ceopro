<style>
.ynmember_relationship_form_elm
{
	margin-bottom: 8px;
}
.ynmember_relationship_form_elm .label
{
	margin-bottom: 8px;
}
.message_hide
{
	display: none;
}
</style>
<div class="headline">
  <h2>
    <?php if ($this->viewer->isSelf($this->user)):?>
      <?php echo $this->translate('Edit My Profile');?>
    <?php else:?>
      <?php echo $this->translate('%1$s\'s Profile', $this->htmlLink($this->user->getHref(), $this->user->getTitle()));?>
    <?php endif;?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php
  if (APPLICATION_ENV == 'production')
    $this->headScript()
      -> appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      -> appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      -> appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      -> appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      -> appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
      
 	
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Ynmember/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
	$this->headScript()  
    ->appendFile($baseUrl . 'application/modules/Ynmember/externals/scripts/jquery-1.10.2.min.js')
    ->appendFile($baseUrl . 'application/modules/Ynmember/externals/scripts/jquery-ui-1.11.4.min.js');
?>
<link href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynmember/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<script type="text/javascript">
	var relationships = <?php echo $this->relationshipStr;?>;
    window.addEvent('load', function() {
        var current = new Date();
        var yearRange = (current.getFullYear() - 100) +':' + (current.getFullYear());
        jQuery('#anniversary').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            buttonImage: '',
            changeMonth: true,
            changeYear: true,
            yearRange: yearRange,
            buttonImageOnly: true,
            buttonText: ''
        });
        changeStatus();
    });
    function changeStatus() {
	    value = $("relationship").value;
	    Object.each(relationships, function(rel, key){
	    	if (rel.rel_id == value)
		    {
			    if (rel.rel_with == '0')
			    {
				    $("with-wrapper").set("style","display: none;");
				    $("toValues-wrapper").set("style","display: none;");
				}
			    else
			    {
			        $("with-wrapper").set("style","");
			        $("toValues-wrapper").set("style","");
			        $("toValues-wrapper").set("style","height: 0px;");
				}
				return;
			}
		});
	}
	
</script>
<script type="text/javascript">
  // Populate data
  var maxRecipients = 1;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;

  <?php if( !empty($this->isPopulated) && !empty($this->toObject) ): ?>
    isPopulated = true;
    xButton = '<a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue(<?php echo $this->toObject->getIdentity();?>, \'toValues\');">x</a>';
    to = {
      id : <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
      type : '<?php echo $this->toObject->getType() ?>',
      guid : '<?php echo $this->toObject->getGuid() ?>',
      title : '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
    };
  <?php endif; ?>
  
  function removeFromToValue(id) 
  {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = document.getElementById('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    //var checkMulti = id.search(/,/);
    var checkMulti = -1;
    // check if we are removing multiple recipients
    if (checkMulti != -1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if (document.getElementById('toValues').value==""){
      document.getElementById('toValues-wrapper').style.height = '0';
    }

    document.getElementById('with').style.display = 'block';
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
    //if( !isPopulated ) { // NOT POPULATED
      new Autocompleter.Request.JSON('with', '<?php echo $this->url(array('controller' => 'index', 'action' => 'suggest'), 'ynmember_extended', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
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
          else {
            var choice = new Element('li', {
              'class': 'autocompleter-choices friendlist',
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
          if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
            //document.getElementById('with').style.display = 'none';
            document.getElementById('with-wrapper').style.display = 'none';
          }
        }
      });
      /*
      new Composer.OverText($document.getElementById('with'), {
        'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
        'element' : 'label',
        'isPlainText' : true,
        'positionOptions' : {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
	*/
    //} else { // POPULATED
    if( isPopulated )
    {
      var myElement = new Element("span", {
        'id' : 'tospan' + to.id,
        'class' : 'tag tag_' + to.type,
        'html' :  to.title + ' <a href="javascript:void(0);" ' +
                  'onclick="this.parentNode.destroy();removeFromToValue(\'' + to.id + '\');">x</a>'
      });
      document.getElementById('with-element').appendChild(myElement);
      document.getElementById('with-wrapper').style.height = 'auto';

      // Hide to input?
      document.getElementById('with').style.display = 'none';
      //document.getElementById('toValues-wrapper').style.display = 'none';
    }
    //}
  });
</script>

<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>
<div class="ynmember-container clearfix">
<?php echo $this->form->render();?>

<?php $data_privacy = 'self'; ?>
<?php if (!is_null($this->linkage)):?>
<?php
	$linkage = $this->linkage;
	$tableAuthAllow = Engine_Api::_() -> getDbTable('allow','authorization');
	$select = $tableAuthAllow -> select() -> where("resource_type = 'ynmember_linkage'") -> where('resource_id = ?', $linkage->getIdentity());
	$result = $tableAuthAllow -> fetchAll($select);
	switch (count($result)) {
		case '3':
			$data_privacy = 'everyone';
			break;
		case '2':
			$data_privacy = 'registered';
			break;
		case '1':
			$data_privacy = 'friends';
			break;
		case '0':
			$data_privacy = 'self';
			break;
	}
?>
<?php endif;?>
<div id='relationship-privacy-selector-1' class="field-privacy-selector" data-privacy="<?php echo $data_privacy ?>">                  
		<span class="icon"></span>
		<span class="caret"></span>
		<ul>
		    <li data-id="1" data-value="everyone" class="field-privacy-option-everyone"><span class="icon"></span><span class="text">Everyone</span></li>
		    <li data-id="1" data-value="registered" class="field-privacy-option-registered "><span class="icon"></span><span class="text">All Members</span></li>
		    <li data-id="1" data-value="friends" class="field-privacy-option-friends "><span class="icon"></span><span class="text">Friends</span></li>
		    <li data-id="1" data-value="self" class="field-privacy-option-self "><span class="icon"></span><span class="text">Only Me</span></li>
		</ul>
	</div>
<?php if (isset($this -> confirm) && $this -> confirm) :?>
<form method="post" class="global_form" id="confirm_form" action="<?php echo $this->url(array('controller' => 'member', 'action' => 'confirm'), 'ynmember_extended', true );?>">
		<div>
			<h3><?php echo $this->translate("You have requests");?></h3>
		</div>
		<div class="tip message_hide" id="confirm_message_request_notify">
		    <span>
		      <?php echo $this->translate("Confirm successfully.") ?>
		    </span>
		</div>
		<div class="tip message_hide" id="cancel_message_request_notify" >
		    <span>
		      <?php echo $this->translate("Cancel successfully.") ?>
		    </span>
		</div>
		
	<?php foreach ($this->request as $request):?>
	<?php $relationship = Engine_Api::_()->getItem('ynmember_relationship', $request -> relationship_id);?>
    <div class="ynmember-most-item ynmember-clearfix" id="ynmember_request_<?php echo $request->resource_id;?>">
      <!-- image -->
      <div class="ynmember-most-item-avatar">
        <?php $user = Engine_Api::_()->user()->getUser($request->resource_id);?>
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'thumb')) ?>
      </div>
      <div class="ynmember-most-item-info">
        <div class="ynmember-most-item-status">
          <span><?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('class' => 'thumb')) ?></span> 
          <span><?php echo $relationship->status;?><?php echo $this->translate(" with you ");?></span>
        </div>
        <div  class="ynmember-most-item-confirm">
          <a href="javascript:void(0);" onclick="confirmRequest(<?php echo $request->resource_id;?>, 1)"><?php echo $this->translate("Confirm");?></a>
          <?php echo $this->translate("Or");?>
          <a href="javascript:void(0);" onclick="confirmRequest(<?php echo $request->resource_id;?>, 0)"><?php echo $this->translate("Cancel");?></a>
        </div>  
      </div>
      <input type="hidden" name="confirm" id="confirm" value="1" />		
    </div>
    <?php endforeach;?>
</form>
<?php endif;?>

<script>
/*
	var cancel = function(){
		$('confirm').value = 0;
		$('confirm_form').submit();
	};
*/
	var confirmRequest = function(request_id, status){
		new Request.JSON({
	        'format' : 'json',
	        'method' : 'post',
	        'url' : '<?php echo $this->url(array('controller' => 'member', 'action' => 'confirm'), 'ynmember_extended', true );?>',
			'data' : {
				'confirm' : status,
				'id': request_id
			},
			'onSuccess' : function() {
				$("ynmember_request_"+request_id).dispose();
				if (status == 1)
				{
					$("confirm_message_request_notify").removeClass('message_hide');
					$("cancel_message_request_notify").addClass('message_hide');
				}
				else if (status == 0)
				{
					$("cancel_message_request_notify").removeClass('message_hide');
					$("confirm_message_request_notify").addClass('message_hide');
				}
				window.location = window.location;
			}
	    }).send();
	};


	function ajaxAuthViewRelationship(id, auth_view)
	{
		new Request.JSON({
	        'format' : 'json',
			'url' : '<?php echo $this->url(array('action'=>'edit-privacy-relationship'), 'ynmember_extended') ?>',
			'data' : {
				'id': <?php echo $this->user->getIdentity();?>,
				'id_linkage': id,
	            'auth_view': auth_view
			},
			'onSuccess' : function() {
			}
	    }).send();
	}
	
	function removeActive()
	{
		$$('.field-privacy-selector').each(function(el){
			el.removeClass('active');
		});
	}
	
	window.addEvent('domready', function() 
	{
    (function($,$$){
    var events;
    var check = function(e){
      var target = $(e.target);
      var parents = target.getParents();
      events.each(function(item){
        var element = item.element;
        if (element != target && !parents.contains(element))
          item.fn.call(element, e);
      });
    };
    Element.Events.outerClick = {
      onAdd: function(fn){
        if(!events) {
          document.addEvent('click', check);
          events = [];
        }
        events.push({element: this, fn: fn});
      },
      onRemove: function(fn){
        events = events.filter(function(item){
          return item.element != this || item.fn != fn;
        }, this);
        if (!events.length) {
          document.removeEvent('click', check);
          events = null;
        }
      }
    };
  })(document.id,$$);

		$("auth_view").value = '<?php echo $data_privacy;?>';
		$$('.field-privacy-selector').addEvent('click', function(event) {
			this.toggleClass('active');
		});
    $$('.field-privacy-selector').addEvent('outerClick', function(event) {
      this.removeClass('active');
    });
		
		$$('.field-privacy-option-everyone').addEvent('click', function(event) {
			var data_value = this.get('data-value');
			var data_id = this.get('data-id');
			var str = 'relationship-privacy-selector-';
			var selector_id = str + data_id;
			$(selector_id).set('data-privacy', data_value);
			$("auth_view").value = data_value;
			//ajaxAuthViewRelationship(data_id, data_value);
		});
		
		$$('.field-privacy-option-registered').addEvent('click', function(event) {
			var data_value = this.get('data-value');
			var data_id = this.get('data-id');
			var str = 'relationship-privacy-selector-';
			var selector_id = str + data_id;
			$(selector_id).set('data-privacy', data_value);
			$("auth_view").value = data_value;
			//ajaxAuthViewRelationship(data_id, data_value);
		});
		
		$$('.field-privacy-option-friends').addEvent('click', function(event) {
			var data_value = this.get('data-value');
			var data_id = this.get('data-id');
			var str = 'relationship-privacy-selector-';
			var selector_id = str + data_id;
			$(selector_id).set('data-privacy', data_value);
			$("auth_view").value = data_value;
			//ajaxAuthViewRelationship(data_id, data_value);
		});
		
		$$('.field-privacy-option-self').addEvent('click', function(event) {
			var data_value = this.get('data-value');
			var data_id = this.get('data-id');
			var str = 'relationship-privacy-selector-';
			var selector_id = str + data_id;
			$(selector_id).set('data-privacy', data_value);
			$("auth_view").value = data_value;
			//ajaxAuthViewRelationship(data_id, data_value);
		});
		
		$('anniversary').removeEvents('keydown').addEvent('keydown', function(){
			if (event.keyCode == 8 || event.keyCode == 46) {
				document.getElementById('anniversary').value = "";
			}
		});
	});

  var myGrabElement = $$('.field-privacy-selector')[0];
  $('relationship-wrapper').grab( myGrabElement);
</script>
</div>
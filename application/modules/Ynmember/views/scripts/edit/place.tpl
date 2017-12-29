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

<div class="ynmember-container">
<div class="ynmember-profile-place-item">
<h3><?php echo $this->translate('Schools');?></h3>
<?php foreach($this -> studyplaces as $studyplace) :?>
	<div class="ynmember-profile-place-item-item ynmember-clearfix">
		<span class="ynicon yn-graduation-cap"></span>
		<?php
			$tableAuthAllow = Engine_Api::_() -> getDbTable('allow','authorization');
			$select = $tableAuthAllow -> select() -> where("resource_type = 'ynmember_studyplace'") -> where('resource_id = ?', $studyplace->getIdentity());
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
		<div id='study-privacy-selector-<?php echo $studyplace -> getIdentity()?>' class="field-privacy-selector" data-privacy='<?php echo $data_privacy ?>'>                  
			<?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_extended', 
		                'module' => 'ynmember',
			            'controller' => 'edit' ,
			            'action' => 'edit-study-place', 
			            'id_studyplace' => $studyplace->getIdentity(), 
			            'id' => $this->user->getIdentity()),
			            $this->translate('edit'), 
		            array('class' => 'smoothbox'));
	   		 ?>
	   		 |
	   		 <?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_extended', 
		                'module' => 'ynmember',
			            'controller' => 'edit' ,
			            'action' => 'delete-study-place',
			            'id_studyplace' => $studyplace->getIdentity(), 
			            'id' => $this->user->getIdentity()),
			            $this->translate('delete'), 
		            array('class' => 'smoothbox'));
	   		 ?>
			<span class="icon"></span>
			<span class="caret"></span>
			<ul>
			    <li data-type="study" data-id="<?php echo $studyplace->getIdentity();?>" data-value="everyone" class="field-privacy-option-everyone"><span class="icon"></span><span class="text"><?php echo $this->translate('Everyone');?></span></li>
			    <li data-type="study" data-id="<?php echo $studyplace->getIdentity();?>" data-value="registered" class="field-privacy-option-registered "><span class="icon"></span><span class="text"><?php echo $this->translate('All Members');?></span></li>
			    <li data-type="study" data-id="<?php echo $studyplace->getIdentity();?>" data-value="friends" class="field-privacy-option-friends "><span class="icon"></span><span class="text"><?php echo $this->translate('Friends');?></span></li>
			    <li data-type="study" data-id="<?php echo $studyplace->getIdentity();?>" data-value="self" class="field-privacy-option-self "><span class="icon"></span><span class="text"><?php echo $this->translate('Only Me');?></span></li>
			</ul>
		</div>

		<div class="ynmember-profile-place-info">
			<div><b><?php echo $this->translate('School');?>:</b> <?php echo $studyplace -> name;?></div>
			<div><b><?php echo $this->translate('Location');?>:</b> <?php echo $studyplace -> location;?></div>
			<input id='study-place-checkbox-<?php echo $studyplace->getIdentity();?>' class='study-place-checkbox' onclick="check_study_place(this, <?php echo $studyplace->getIdentity();?>);"  type='checkbox' <?php if($studyplace -> current) echo "checked='true'"?>><?php echo $this->translate('I currently study here');?>
		</div>	
		
	</div>
<?php endforeach;?>

<a href='<?php echo $this->url(array('controller' => 'edit', 'action' => 'add-study-place'),'ynmember_extended')?>' class='smoothbox'>
	<div class="ynmember_edit_place_block">
	<span class="ynicon yn-plus-thin"></span>
	</div>
	<div class="ynmember_edit_place_label">
	<?php echo $this->translate('Add a school');?>
	</div>
</a>
</div>
<div class="ynmember-profile-place-item">
<h3><?php echo $this->translate('Workplaces');?></h3>
<?php foreach($this -> workplaces as $workplace) :?>
	<div class="ynmember-profile-place-item-item ynmember-clearfix">
		<span class="ynicon yn-building"></span>
		<?php
			$tableAuthAllow = Engine_Api::_() -> getDbTable('allow','authorization');
			$select = $tableAuthAllow -> select() -> where("resource_type = 'ynmember_workplace'") -> where('resource_id = ?', $workplace->getIdentity());
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
		<div id='work-privacy-selector-<?php echo $workplace -> getIdentity()?>' class="field-privacy-selector" data-privacy='<?php echo $data_privacy ?>'>                  
			<?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_extended', 
		                'module' => 'ynmember',
			            'controller' => 'edit' ,
			            'action' => 'edit-work-place', 
			            'id_workplace' => $workplace->getIdentity(), 
			            'id' => $this->user->getIdentity()),
			            $this->translate('edit'), 
		            array('class' => 'smoothbox'));
	   		 ?>
	   		 |
	   		 <?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_extended', 
		                'module' => 'ynmember',
			            'controller' => 'edit' ,
			            'action' => 'delete-work-place',
			            'id_workplace' => $workplace->getIdentity(), 
			            'id' => $this->user->getIdentity()),
			            $this->translate('delete'), 
		            array('class' => 'smoothbox'));
	   		 ?>
			<span class="icon"></span>
			<span class="caret"></span>
			<ul>
			    <li data-type="work" data-id="<?php echo $workplace->getIdentity();?>" data-value="everyone" class="field-privacy-option-everyone"><span class="icon"></span><span class="text"><?php echo $this->translate('Everyone');?></span></li>
			    <li data-type="work" data-id="<?php echo $workplace->getIdentity();?>" data-value="registered" class="field-privacy-option-registered "><span class="icon"></span><span class="text"><?php echo $this->translate('All Members');?></span></li>
			    <li data-type="work" data-id="<?php echo $workplace->getIdentity();?>" data-value="friends" class="field-privacy-option-friends "><span class="icon"></span><span class="text"><?php echo $this->translate('Friends');?></span></li>
			    <li data-type="work" data-id="<?php echo $workplace->getIdentity();?>" data-value="self" class="field-privacy-option-self "><span class="icon"></span><span class="text"><?php echo $this->translate('Only Me');?></span></li>
			</ul>
		</div>

		<div class="ynmember-profile-place-info">
			<div><b><?php echo $this->translate('Company');?>:</b> <?php echo $workplace -> company;?></div>
			<div><b><?php echo $this->translate('Location');?>:</b> <?php echo $workplace -> location;?></div>
			<input id='work-place-checkbox-<?php echo $workplace->getIdentity();?>' class='work-place-checkbox' onclick="check_place(this, <?php echo $workplace->getIdentity();?>);"  type='checkbox' <?php if($workplace -> current) echo "checked='true'"?>><?php echo $this->translate('I currently work here');?>
		</div>
	</div>
<?php endforeach;?>

<a href='<?php echo $this->url(array('controller' => 'edit', 'action' => 'add-work-place'),'ynmember_extended')?>' class='smoothbox'>
	<div class="ynmember_edit_place_block">
	<span class="ynicon yn-plus-thin"></span>
	</div>
	<div class="ynmember_edit_place_label">
	<?php echo $this->translate('Add a workplace');?>
	</div>
</a>
</div>

<div class="ynmember-profile-place-item">
<h3><?php echo $this->translate('Current place I\'m living');?></h3>

<?php foreach($this -> currentliveplaces as $currentliveplace) :?>
	<div class="ynmember-profile-place-item-item ynmember-clearfix">
		<span class="ynicon yn-map-marker"></span>
		<?php
			$tableAuthAllow = Engine_Api::_() -> getDbTable('allow','authorization');
			$select = $tableAuthAllow -> select() -> where("resource_type = 'ynmember_liveplace'") -> where('resource_id = ?', $currentliveplace->getIdentity());
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
		<div id='live-privacy-selector-<?php echo $currentliveplace -> getIdentity()?>' class="field-privacy-selector" data-privacy="<?php echo $data_privacy ?>">                  
			<?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_extended', 
		                'module' => 'ynmember',
			            'controller' => 'edit' ,
			            'action' => 'edit-live-place', 
			            'id_liveplace' => $currentliveplace->getIdentity(), 
			            'id' => $this->user->getIdentity()),
			            $this->translate('edit'), 
		            array('class' => 'smoothbox'));
	   		 ?>
	   		 |
	   		 <?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_extended', 
		                'module' => 'ynmember',
			            'controller' => 'edit' ,
			            'action' => 'delete-live-place',
			            'id_liveplace' => $currentliveplace->getIdentity(), 
			            'id' => $this->user->getIdentity()),
			            $this->translate('delete'), 
		            array('class' => 'smoothbox'));
	   		 ?>
			<span class="icon"></span>
			<span class="caret"></span>
			<ul>
			    <li data-type="live" data-id="<?php echo $currentliveplace->getIdentity();?>" data-value="everyone" class="field-privacy-option-everyone"><span class="icon"></span><span class="text"><?php echo $this->translate('Everyone');?></span></li>
			    <li data-type="live" data-id="<?php echo $currentliveplace->getIdentity();?>" data-value="registered" class="field-privacy-option-registered "><span class="icon"></span><span class="text"><?php echo $this->translate('All Members');?></span></li>
			    <li data-type="live" data-id="<?php echo $currentliveplace->getIdentity();?>" data-value="friends" class="field-privacy-option-friends "><span class="icon"></span><span class="text"><?php echo $this->translate('Friends');?></span></li>
			    <li data-type="live" data-id="<?php echo $currentliveplace->getIdentity();?>" data-value="self" class="field-privacy-option-self "><span class="icon"></span><span class="text"><?php echo $this->translate('Only Me');?></span></li>
			</ul>
		</div>	

		<div class="ynmember-profile-place-info">
			<div><b><?php echo $this->translate('Location');?>:</b> <?php echo $currentliveplace -> location;?></div>
		</div>		
		
	</div>
<?php endforeach;?>

<a href='<?php echo $this->url(array('controller' => 'edit', 'action' => 'add-live-place', 'current' => '1'),'ynmember_extended')?>' class='smoothbox'>
	<div class="ynmember_edit_place_block">
	<span class="ynicon yn-plus-thin"></span>
	</div>
	<div class="ynmember_edit_place_label">
	<?php echo $this->translate('Add a place');?>
	</div>
</a>
</div>

<div class="ynmember-profile-place-item">
<h3><?php echo $this->translate('Places I Lived');?></h3>
<?php foreach($this -> pastliveplaces as $pastliveplace) :?>
	<div class="ynmember-profile-place-item-item ynmember-clearfix">
		<span class="ynicon yn-map-marker"></span>
		<?php
			$tableAuthAllow = Engine_Api::_() -> getDbTable('allow','authorization');
			$select = $tableAuthAllow -> select() -> where("resource_type = 'ynmember_liveplace'") -> where('resource_id = ?', $pastliveplace->getIdentity());
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
		<div id='live-privacy-selector-<?php echo $pastliveplace -> getIdentity()?>' class="field-privacy-selector" data-privacy="<?php echo $data_privacy;?>">                  
			<?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_extended', 
		                'module' => 'ynmember',
			            'controller' => 'edit' ,
			            'action' => 'edit-live-place', 
			            'id_liveplace' => $pastliveplace->getIdentity(), 
			            'id' => $this->user->getIdentity()),
			            $this->translate('edit'), 
		            array('class' => 'smoothbox'));
	   		 ?>
	   		 |
	   		 <?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_extended', 
		                'module' => 'ynmember',
			            'controller' => 'edit' ,
			            'action' => 'delete-live-place',
			            'id_liveplace' => $pastliveplace->getIdentity(), 
			            'id' => $this->user->getIdentity()),
			            $this->translate('delete'), 
		            array('class' => 'smoothbox'));
	   		 ?>
			<span class="icon"></span>
			<span class="caret"></span>
			<ul>
			    <li data-type="live" data-id="<?php echo $pastliveplace->getIdentity();?>" data-value="everyone" class="field-privacy-option-everyone"><span class="icon"></span><span class="text"><?php echo $this->translate('Everyone');?></span></li>
			    <li data-type="live" data-id="<?php echo $pastliveplace->getIdentity();?>" data-value="registered" class="field-privacy-option-registered "><span class="icon"></span><span class="text"><?php echo $this->translate('All Members');?></span></li>
			    <li data-type="live" data-id="<?php echo $pastliveplace->getIdentity();?>" data-value="friends" class="field-privacy-option-friends "><span class="icon"></span><span class="text"><?php echo $this->translate('Friends');?></span></li>
			    <li data-type="live" data-id="<?php echo $pastliveplace->getIdentity();?>" data-value="self" class="field-privacy-option-self "><span class="icon"></span><span class="text"><?php echo $this->translate('Only Me');?></span></li>
			</ul>
		</div>

		<div class="ynmember-profile-place-info">
			<div><b><?php echo $this->translate('Location');?>:</b> <?php echo $pastliveplace -> location;?></div>
		</div>		
	</div>
<?php endforeach;?>

<a href='<?php echo $this->url(array('controller' => 'edit', 'action' => 'add-live-place'),'ynmember_extended')?>' class='smoothbox'>
	<div class="ynmember_edit_place_block">
	<span class="ynicon yn-plus-thin"></span>
	</div>
	<div class="ynmember_edit_place_label">
	<?php echo $this->translate('Add a place');?>
	</div>
</a>
</div>

<script type="text/javascript">
	
	function check_study_place(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    new Request.JSON({
	        'format' : 'json',
			'url' : '<?php echo $this->url(array('action'=>'check-study-place'), 'ynmember_extended') ?>',
			'data' : {
				'id': <?php echo $this->user->getIdentity();?>,
				'id_check': id,
	            'value': value
			},
			'onSuccess' : function() {
				 if(value == 1)
			     {
			    	$$('.study-place-checkbox').each(function(el){
			    		el.checked = 0;
			    	});
			    	var id_checked = 'study-place-checkbox-'+id;
			    	$(id_checked).set('checked','true');
			     }
			}
	    }).send();
	}
	
	function check_place(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    new Request.JSON({
	        'format' : 'json',
			'url' : '<?php echo $this->url(array('action'=>'check-place'), 'ynmember_extended') ?>',
			'data' : {
				'id': <?php echo $this->user->getIdentity();?>,
				'id_check': id,
	            'value': value
			},
			'onSuccess' : function() {
				 if(value == 1)
			     {
			    	$$('.work-place-checkbox').each(function(el){
			    		el.checked = 0;
			    	});
			    	var id_checked = 'work-place-checkbox-'+id;
			    	$(id_checked).set('checked','true');
			     }
			}
	    }).send();
	}
	
	function ajaxAuthViewWorkPlace(id, auth_view)
	{
		new Request.JSON({
	        'format' : 'json',
			'url' : '<?php echo $this->url(array('action'=>'edit-privacy-work-place'), 'ynmember_extended') ?>',
			'data' : {
				'id': <?php echo $this->user->getIdentity();?>,
				'id_workplace': id,
	            'auth_view': auth_view
			},
			'onSuccess' : function() {
			}
	    }).send();
	}
	
	function ajaxAuthViewLivePlace(id, auth_view)
	{
		new Request.JSON({
	        'format' : 'json',
			'url' : '<?php echo $this->url(array('action'=>'edit-privacy-live-place'), 'ynmember_extended') ?>',
			'data' : {
				'id': <?php echo $this->user->getIdentity();?>,
				'id_liveplace': id,
	            'auth_view': auth_view
			},
			'onSuccess' : function() {
			}
	    }).send();
	}
	
	function ajaxAuthViewStudyPlace(id, auth_view)
	{
		new Request.JSON({
	        'format' : 'json',
			'url' : '<?php echo $this->url(array('action'=>'edit-privacy-study-place'), 'ynmember_extended') ?>',
			'data' : {
				'id': <?php echo $this->user->getIdentity();?>,
				'id_studyplace': id,
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
	
	window.addEvent('domready', function() 
	{
		$$('.field-privacy-selector').removeEvent('click').addEvent('click', function(event) {
			this.toggleClass('active');
		});

		$$('.field-privacy-selector').addEvent('outerClick', function(){
			this.removeClass('active');
		});
		
		$$('.field-privacy-option-everyone').addEvent('click', function(event) {
			var data_type = this.get('data-type');
			var data_value = this.get('data-value');
			var data_id = this.get('data-id');
			if(data_type == 'work')
			{
				$str = 'work-privacy-selector-';
			}
			else if(data_type == 'live')
			{
				$str = 'live-privacy-selector-';
			}
			else{
				$str = 'study-privacy-selector-';
			}
			var selector_id = $str + data_id;
			$(selector_id).set('data-privacy', data_value);
			if(data_type == 'work')
			{
				ajaxAuthViewWorkPlace(data_id, data_value);
			}	
			else if(data_type == 'live')
			{
				ajaxAuthViewLivePlace(data_id, data_value);
			}
			else{
				ajaxAuthViewStudyPlace(data_id, data_value);
			}
		});
		
		$$('.field-privacy-option-registered').addEvent('click', function(event) {
			var data_type = this.get('data-type');
			var data_value = this.get('data-value');
			var data_id = this.get('data-id');
			if(data_type == 'work')
			{
				$str = 'work-privacy-selector-';
			}
			else if(data_type == 'live')
			{
				$str = 'live-privacy-selector-';
			}
			else{
				$str = 'study-privacy-selector-';
			}
			var selector_id = $str + data_id;
			$(selector_id).set('data-privacy', data_value);
			if(data_type == 'work')
			{
				ajaxAuthViewWorkPlace(data_id, data_value);
			}	
			else if(data_type == 'live')
			{
				ajaxAuthViewLivePlace(data_id, data_value);
			}
			else{
				ajaxAuthViewStudyPlace(data_id, data_value);
			}
		});
		
		$$('.field-privacy-option-friends').addEvent('click', function(event) {
			var data_type = this.get('data-type');
			var data_value = this.get('data-value');
			var data_id = this.get('data-id');
			if(data_type == 'work')
			{
				$str = 'work-privacy-selector-';
			}
			else if(data_type == 'live')
			{
				$str = 'live-privacy-selector-';
			}
			else{
				$str = 'study-privacy-selector-';
			}
			var selector_id = $str + data_id;
			$(selector_id).set('data-privacy', data_value);
			if(data_type == 'work')
			{
				ajaxAuthViewWorkPlace(data_id, data_value);
			}	
			else if(data_type == 'live')
			{
				ajaxAuthViewLivePlace(data_id, data_value);
			}
			else{
				ajaxAuthViewStudyPlace(data_id, data_value);
			}
		});
		
		$$('.field-privacy-option-self').addEvent('click', function(event) {
			var data_type = this.get('data-type');
			var data_value = this.get('data-value');
			var data_id = this.get('data-id');
			if(data_type == 'work')
			{
				$str = 'work-privacy-selector-';
			}
			else if(data_type == 'live')
			{
				$str = 'live-privacy-selector-';
			}
			else{
				$str = 'study-privacy-selector-';
			}
			var selector_id = $str + data_id;
			$(selector_id).set('data-privacy', data_value);
			if(data_type == 'work')
			{
				ajaxAuthViewWorkPlace(data_id, data_value);
			}	
			else if(data_type == 'live')
			{
				ajaxAuthViewLivePlace(data_id, data_value);
			}
			else{
				ajaxAuthViewStudyPlace(data_id, data_value);
			}
		});
	});
</script>
</div> <!-- ynmember-container -->
<?php
	$table = Engine_Api::_() -> getDbTable("icons", "ynforum");
	$select = $table -> select();
	$Icons = $table -> fetchAll($select);
	$arrIcon = array();
	$arrIcon[0]['title'] = $this -> translate('No icon');
	$arrIcon[0]['photoUrl'] = '';
	foreach ($Icons as $icon) {
		$arrIcon[$icon -> icon_id]['title'] = $icon -> title;
		$arrIcon[$icon -> icon_id]['photoUrl'] = $icon -> getPhotoUrl("thumb.icon");
	}
	
?>

<div id="icon_id-wrapper" class="form-wrapper ynforum_topic_icon">
	<div id="icon_id-label" class="form-label">
		<label for="icon" class="optional"><?php echo $this -> translate('YNFORUM_POST_ICON'); ?></label>
	</div>
	<div id="icon_id-element" class="form-element">
		<ul class="form-options-wrapper">	
			<?php foreach($arrIcon as $key => $item):?>
			<li>
				
				<?php if((int)$this->element->getAttrib('item_id') == (int)$key):?>
					<input type="radio" name="icon_id" id="icon_id-<?php echo $key?>" value="<?php echo $key?>" checked="checked"> 
				<?php else:?>
					<input type="radio" name="icon_id" id="icon_id-<?php echo $key?>" value="<?php echo $key?>" >
				<?php endif;?>
				<?php if($item['photoUrl']!=''):?>
					<img src="<?php echo $item['photoUrl']?>">
				<?php endif;?>
				
				<label for="icon_id-<?php echo $key?>"><?php echo $item['title']?></label>
				
			
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
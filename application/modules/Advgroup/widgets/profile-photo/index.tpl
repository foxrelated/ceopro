<div id='group_photo'>
	<?php $coverPhotoUrl = "";
		if ($this->subject()->cover_photo)
		{
			$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->subject()->cover_photo)->current();
			$coverPhotoUrl = $coverFile->map();
			?>
			<img src="<?php echo $coverPhotoUrl?>" alt="" class="main item_photo_group  main">
		<?php
		}
		else 
		{
			echo $this->itemPhoto($this->subject(), 'thumb.profile');
		}?>
</div>
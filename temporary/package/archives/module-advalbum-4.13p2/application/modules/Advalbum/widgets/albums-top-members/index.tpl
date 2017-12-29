<div class="ynadvalbum_top_members">
	<?php 
		foreach ($this->members as $item):  ?>
		<div class='ynadvalbum_top_members_photo'>
			<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.profile', $item->getOwner()->getTitle()), array('title'=>$item->getOwner()->getTitle())) ?>
		</div>      
	<?php endforeach; ?>              
</div>

<div class="callbacks_container">
<ul class="rslides" id="ymb_home_featuredphoto">
	<?php 
		foreach ($this->photo_list as $photo_item) 
		{ ?>
	      <li>
	      	<a href="<?php echo $photo_item->getHref() ?>">
	      		<img src="<?php echo $photo_item->getPhotoUrl('thumb.main'); ?>" alt="">
	      	</a>
	      </li>
    <?php } ?>
    
</ul>
</div>


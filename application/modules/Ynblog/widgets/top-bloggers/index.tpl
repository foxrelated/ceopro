<ul class="generic_list_widget ynblog-top-bloggers clearfix">
  <?php if(count($this->bloggers) > 0):?>
	  <?php foreach( $this->bloggers as $item ):?>
	      <?php
		      $owner = $item->getOwner();
		      if ($owner->getIdentity() <= 0) continue;
	      ?>
		  <li class="ynblog-top-blogger">
		  		<div>
					<?php 
					   $photoUrl = $owner ->getPhotoUrl();
					   if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynblog/externals/images/nophoto_user_thumb_profile.png';
					?>

					<a class="blogger_photo clearfix" href="blogs/<?php echo $item->owner_id;?>" style="background-image: url(<?php echo $photoUrl; ?>)">
						
					</a>
					<a class="blogger_name" title="<?php echo $item->getOwner()->getTitle();?>" href="blogs/<?php echo $item->owner_id;?>">
						<?php echo Engine_Api::_()->ynblog()->subPhrase($item->getOwner()->getTitle(),12); ?>
					</a>
		      </div>
		  </li>
	  <?php endforeach; ?>
  <?php else: ?>
	<div class="tip">
	  <span>
			<?php echo $this->translate('There is no blogger.');?>
	  </span>
	</div>  
  <?php endif;?>
</ul>




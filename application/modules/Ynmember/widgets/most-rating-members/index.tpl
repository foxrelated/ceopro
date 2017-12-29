<ul class="ynmember-most-rating-members">
  <?php foreach( $this->list_show_users as $user ): ?>
    <li class="ynmember-most-item ynmember-clearfix">
    	<!-- image -->
    	<div class="ynmember-most-item-avatar">
    		<?php $background_image = $this->layout()->staticBaseUrl."application/modules/User/externals/images/nophoto_user_thumb_profile.png"; ?>
    		<?php $userPhoto = $user->getPhotoUrl('thumb.profile');?>
			<?php if ($userPhoto) 
				$background_image = $userPhoto; ?>
			<?php echo $this->htmlLink($user->getHref(), '<span alt="'.$user->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$user->getTitle())) ?>

    		<!-- add friend button -->
			<?php $canAdd = Engine_Api::_()->ynmember()->canAddFriendButton($user);?>
			<?php if(is_array($canAdd)):?>
				<a href="<?php echo $this->url($canAdd['params'], $canAdd['route'], array());?>" class="smoothbox ynmember-btn-addfriend">
	               	<span class="ynicon yn-plus"></span>
	            </a>
			<?php endif;?>
    	</div>
    	<div class="ynmember-most-item-info">
    		<!-- title -->
	    	<a class="ynmember-most-item-title" href='<?php echo $user->getHref();?>'><?php echo $user->getTitle();?></a>
			
			<!-- studyplace -->
	    	<?php
			$studyPlacesTbl = Engine_Api::_()->getDbTable('studyplaces', 'ynmember');
			$studyplaces = $studyPlacesTbl -> getCurrentStudyPlacesByUserId($user -> getIdentity());
			if ($studyplaces) :?>
			<?php
				$str_studyplace = "";
				if($studyplaces -> isViewable())
				{
					$str_studyplace = "<a target='_blank' href='https://www.google.com/maps?q={$studyplaces->latitude},{$studyplaces->longitude}'>{$studyplaces->name}</a>";
				}
			?>
			<div class="ynmember-most-item-work">
				<span class="ynicon yn-graduation-cap"></span>
				<span><?php echo $str_studyplace; ?></span>
			</div>
			<?php endif;?>
			
	    	<!-- workplace -->
	    	<?php
			$workPlacesTbl = Engine_Api::_()->getDbTable('workplaces', 'ynmember');
			$workplaces = $workPlacesTbl -> getCurrentWorkPlacesByUserId($user -> getIdentity());
			if ($workplaces) :?>
			<?php
				$str_workplace = "";
				if($workplaces -> isViewable())
				{
					$str_workplace = "<a target='_blank' href='https://www.google.com/maps?q={$workplaces->latitude},{$workplaces->longitude}'>{$workplaces->company}</a>";
				}
			?>
			<div class="ynmember-most-item-work">
				<span class="ynicon yn-briefcase"></span>
				<span><?php echo $str_workplace; ?></span>
			</div>
			<?php endif;?>

			<!-- living place -->
			<?php
			$livePlacesTbl = Engine_Api::_()->getDbTable('liveplaces', 'ynmember');
			$liveplaces = $livePlacesTbl -> getLiveCurrentPlacesByUserId($user -> getIdentity());
			$lives = array();
			foreach ($liveplaces as $live)
			{
				if($live -> isViewable())
				{
					$lives[] = "<a target='_blank' href='https://www.google.com/maps?q={$live->latitude},{$live->longitude}'>{$live->location}</a>";
				}
			}
			$lives = implode(", ", $lives);
			if($lives) :?>
			<div class="ynmember-most-item-live">
				<span class="ynicon yn-map-marker"></span>
				<span><?php echo $lives; ?></span>
			</div>
			<?php endif;?>

			<!-- mutual friend -->
			<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $user -> rating));?>	
    	</div>    	
    </li>
  <?php endforeach; ?>
</ul>
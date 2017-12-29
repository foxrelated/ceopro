<?php 
	$user = $this -> user; 
$facebook = $twitter = "";
$fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
foreach( $fieldStructure as $map ) {
   $field = $map->getChild();
   $value = $field->getValue($user);
   if($field->type == 'facebook')
   {
   	 $facebook = $value['value'];
   }
   if($field->type == 'twitter')
   {
   	 $twitter = $value['value'];
   }
}
?>
<div class="ynmember-member-of-item ynmember-clearfix">
	<div class="ynmember-of-item-avatar">
    	<!-- image -->
    	<?php $background_image = $this->layout()->staticBaseUrl."application/modules/User/externals/images/nophoto_user_thumb_profile.png"; ?>
		<?php if ($user->getPhotoUrl()) 
			$background_image = $user->getPhotoUrl(); ?>
		<?php echo $this->htmlLink($user->getHref(), '<span alt="'.$user->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$user->getTitle())) ?>
    </div>
    
    <!-- title -->
    <a class="ynmember-of-item-title" href='<?php echo $user->getHref();?>'><?php echo $user->getTitle();?></a>
	
	<div class="ynmember-of-item-info">
	     
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
		<div class="ynmember-of-item-work">
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
				if($live -> isViewable())
				{
					$lives[] = "<a target='_blank' href='https://www.google.com/maps?q={$live->latitude},{$live->longitude}'>{$live->location}</a>";
				}
			}
		}
		$lives = implode(", ", $lives);
		if($lives) :?>
		<div class="ynmember-of-item-live">
			<span class="ynicon yn-map-marker"></span>
			<span><?php echo $this->translate("Lives in ");?></span>
			<span><?php echo $lives; ?></span>
		</div>
		<?php endif;?>

		<!-- add friend button -->
		<?php $canAdd = Engine_Api::_()->ynmember()->canAddFriendButton($user);?>
		<?php if(is_array($canAdd)):?>
			<a href="<?php echo $this->url($canAdd['params'], $canAdd['route'], array());?>" class="smoothbox ynmember-btn-addfriend">
               	<span class="ynicon yn-plus"></span>
            </a>
		<?php endif;?>

		<!-- mutual friend -->
		<?php if(!$this -> viewer -> isSelf($user)):?>
			<div class="ynmember-of-item-mutual">
			<?php 
				$list_mutual_friends = Engine_Api::_() -> ynmember() -> getMutualFriends($user); 
				$imutual_user = 0;				
			?>
			<?php if($list_mutual_friends) :?>
				<div class="ynmember-of-item-count"><?php echo count($list_mutual_friends).$this->translate(" mutual friend").((count($list_mutual_friends)>1)?"s":""); ?> </div>
				<div class="ynmember-of-item-mutuals">
				<?php foreach ($list_mutual_friends as $mutual_user) :?>
					<?php if ($imutual_user < 3) : ?>
					<div class="ynmember-of-item-mutual-avatar">
				    	<!-- image -->
				    	<?php $background_image = $this->layout()->staticBaseUrl."application/modules/User/externals/images/nophoto_user_thumb_profile.png"; ?>
						<?php if ($mutual_user->getPhotoUrl('thumb.profile')) 
							$background_image = $mutual_user->getPhotoUrl('thumb.profile'); ?>
						<?php echo $this->htmlLink($mutual_user->getHref(), '<span alt="'.$mutual_user->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$mutual_user->getTitle())) ?>
				    </div>
					<?php $imutual_user++; ?>
					<?php endif; ?>
				<?php endforeach;?>
				</div>
			<?php endif;?>
			</div>
		<?php endif;?>
	</div>
</div>
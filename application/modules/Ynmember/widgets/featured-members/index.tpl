<?php
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/jquery-1.10.2.min.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/jquery.easing.min.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/masterslider.min.js');
	$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/styles/masterslider.css');
		
	$max_shown_items = $this->limit;

	if (count($this->users) < $max_shown_items) {
		$max_shown_items = count($this->users);
	}

	if ($max_shown_items%2==0) {
		$max_shown_items = $max_shown_items-1;
	}
?>

<!-- template -->
<div class="ms-staff-carousel ms-round">
	<!-- masterslider -->
	<div class="master-slider" id="ynmember_masterslider">
		<?php foreach($this -> users as $user) :?>
		<div class="ms-slide" data-delay="3">
			<?php $canAdd = Engine_Api::_()->ynmember()->canAddFriendButton($user);?>
			<?php if(is_array($canAdd)):?>
				<div><a href="<?php echo $this->url($canAdd['params'], $canAdd['route'], array());?>" class="smoothbox"><span class="ynicon yn-plus"></span></a></div>
			<?php endif; ?>
			<img src="" data-src="<?php echo $photoUrl = Engine_Api::_()->ynmember()->getMemberPhoto($user);?>"/>
			<div class="ms-info">
				<?php echo $user ?>

				<!-- study -->
				<div class="ynmember-item-info-studyplaces">
					<span class="ynicon yn-graduation-cap"></span>
					<span><?php echo $this-> translate("Studied at");?></span>
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
						<?php echo $str_studyplace;?>
					<?php endif;?>
				</div>

				<!-- workplace -->
				<div class="ynmember-item-info-workplaces">
					<span class="ynicon yn-briefcase"></span>
					<span><?php echo $this-> translate("Works at");?></span>
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
						<?php echo $str_workplace;?>
					<?php endif;?>
				</div>

				<!-- living places -->
				<div class="ynmember-item-info-live">
					<span class="ynicon yn-map-marker"></span>
					<span><?php echo $this-> translate("Lives in");?></span>
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
						<?php echo $lives; ?>
					<?php endif;?>
				</div>

				<!-- groups -->
				<div class="ynmember-item-info-group">
					<?php
					if (Engine_Api::_()->hasModuleBootstrap('group') || Engine_Api::_()->hasModuleBootstrap('advgroup'))
					{
						$groupTbl = Engine_Api::_()->getItemTable('group');
						$membership = (Engine_Api::_()->hasModuleBootstrap('advgroup'))
							? Engine_Api::_()->getDbtable('membership', 'advgroup')
							: Engine_Api::_()->getDbtable('membership', 'group');

						$select = $membership->getMembershipsOfSelect($user);
						$groups = $groupTbl->fetchAll($select);
					}
					?>
					<span class="ynicon yn-user-group"></span>
					<span><?php echo $this-> translate("Groups:");?></span>
					<?php if (count($groups)):?>
						<?php foreach ($groups as $group) :?>
							<a href="<?php echo $group->getHref();?>"><?php echo $group->getTitle();?></a>
							<?php break;?>
						<?php endforeach;?>
						<?php if (count($groups) > 1) : ?>
							<?php echo $this-> translate("and");?> <a class="smoothbox" href="<?php echo $this->url(array('controller' => 'review', 'action' => 'user-group', 'id' => $user -> getIdentity()),'ynmember_extended');?>"><?php echo $this -> translate(array("%s other", "%s others" , (count($groups) - 1 )), (count($groups) - 1))?></a>
						<?php endif;?>
					<?php endif;?>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<!-- end of masterslider -->
	<div class="ms-staff-info" id="staff-info"> </div>
</div>
<!-- end of template -->

<script type="text/javascript">
	jQuery.noConflict();
//	(function(){
	jQuery(document).ready(function(){
		var slider = new MasterSlider();
		slider.setup('ynmember_masterslider' , {
			loop:true,
			width:200,
			height:200,
			speed:25,
			view:'focus',
			preload:0,
			space:0,
			space:35,
			autoplay: true,
			wheel: true,
			viewOptions:{centerSpace:1.6}
		});
		slider.control('arrows');
		slider.control('slideinfo',{insertTo:'#staff-info'});

	});
//	})();
</script>

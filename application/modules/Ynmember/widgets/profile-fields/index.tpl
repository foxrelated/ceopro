<?php echo $this->fieldValueLoop($this->subject(), $this->fieldStructure) ?>
<?php if($this -> can_add_place) :?>
	<?php if(count($this -> workplaces) > 0) :?>
	<div class="profile_fields">
	<h4>
		<span><?php echo $this->translate('Workplaces');?></span>
	</h4>
	<ul>
		<?php $i = 0; ?>
		<?php foreach($this -> workplaces as $workplace) :?>
		<?php if($workplace -> isViewable()) :?>	
			<?php $i++; ?>
			<li>
				<?php if($workplace -> current) :?>
					<span><?php echo $this->translate('Currently work at') ?></span>
					<span>
						<?php echo $workplace -> company ?>
						-
						<?php echo $workplace -> location ?>
						<?php if ($workplace -> latitude != '0' && $workplace -> longitude != '0'):?>
						-
						 <?php echo $this->htmlLink(
			                array('route' => 'ynmember_general', 'action' => 'direction','type'=>'work', 'id' => $workplace->getIdentity()), 
			                $this->translate('<span title="Get Direction" class="ynicon yn-map-marker ynmember_get_directions"></span> '), 
			                array('class' => 'smoothbox get_direction')) ?>
			            <?php endif;?>
					</span>
				<?php else:?>
					<span><?php echo $this->translate('Past') ?></span>
					<span>
							<?php echo $workplace -> company ?>
							-
							<?php echo $workplace -> location ?>
							<?php if ($workplace -> latitude != '0' && $workplace -> longitude != '0'):?>
							-
							<?php echo $this->htmlLink(
			                array('route' => 'ynmember_general', 'action' => 'direction','type'=>'work', 'id' => $workplace->getIdentity()), 
			                $this->translate('<span title="Get Direction" class="ynicon yn-map-marker ynmember_get_directions"></span> '), 
			                array('class' => 'smoothbox get_direction')) ?>
			               	<?php endif;?>
					</span>	
				<?php endif;?>
			</li>
		<?php endif;?>
		<?php endforeach;?>
	</ul>
	<?php if($i == 0) :?>
	<span></span>
	<span><?php echo $this->translate('No information available');?></span>	
	<?php endif;?>
	</div>
	<?php endif;?>
	
	
	<?php if(count($this -> studyplaces) > 0) :?>
	<div class="profile_fields">
	<h4>
		<span><?php echo $this->translate('Schools');?></span>
	</h4>
	<ul>
		<?php $i = 0; ?>
		<?php foreach($this -> studyplaces as $studyplace) :?>
		<?php if($studyplace -> isViewable()) :?>	
			<?php $i++; ?>
			<li>
				<?php if($studyplace -> current) :?>
					<span><?php echo $this->translate('Currently study at') ?></span>
					<span>
						<?php echo $studyplace -> name ?>
						-
						<?php echo $studyplace -> location ?>
						<?php if ($studyplace -> latitude != '0' && $studyplace -> longitude != '0'):?>
						-
						 <?php echo $this->htmlLink(
			                array('route' => 'ynmember_general', 'action' => 'direction','type'=>'study', 'id' => $studyplace->getIdentity()), 
			                $this->translate('<span title="Get Direction" class="ynicon yn-map-marker ynmember_get_directions"></span> '), 
			                array('class' => 'smoothbox get_direction')) ?>
			            <?php endif;?>
					</span>
				<?php else:?>
					<span><?php echo $this->translate('Past') ?></span>
					<span>
							<?php echo $studyplace -> name ?>
							-
							<?php echo $studyplace -> location ?>
							<?php if ($studyplace -> latitude != '0' && $studyplace -> longitude != '0'):?>
							-
							<?php echo $this->htmlLink(
			                array('route' => 'ynmember_general', 'action' => 'direction','type'=>'study', 'id' => $studyplace->getIdentity()), 
			                $this->translate('<span title="Get Direction" class="ynicon yn-map-marker ynmember_get_directions"></span> '), 
			                array('class' => 'smoothbox get_direction')) ?>
			                <?php endif;?>
					</span>	
				<?php endif;?>
			</li>
		<?php endif;?>
		<?php endforeach;?>
	</ul>
	<?php if($i == 0) :?>
	<span></span>
	<span><?php echo $this->translate('No information available');?></span>	
	<?php endif;?>
	</div>
	<?php endif;?>
	
	
	<?php if((count($this -> currentliveplaces) > 0) || (count($this -> pastliveplaces) > 0)) :?>
	<div class="profile_fields">
	<h4>
		<span><?php echo $this->translate('Live');?></span>
	</h4>
	<ul>
		<?php $j = 0; ?>
		<?php foreach($this -> currentliveplaces as $currentliveplace) :?>
			<?php if($currentliveplace -> isViewable()) :?>	
				<?php $j++; ?>
				<li>
					<span><?php echo $this->translate('Live at') ?></span> 
					<span>
						<?php echo $currentliveplace -> location ?>
						<?php if ($currentliveplace -> latitude != '0' && $currentliveplace -> longitude != '0'):?>
						-
						<?php echo $this->htmlLink(
			            array('route' => 'ynmember_general', 'action' => 'direction','type'=>'live', 'id' => $currentliveplace->getIdentity()), 
			            $this->translate('<span title="Get Direction" class="ynicon yn-map-marker ynmember_get_directions"></span> '), 
			            array('class' => 'smoothbox get_direction')) ?>
			            <?php endif;?>
					</span>
				</li>
			<?php endif;?>
		<?php endforeach;?>
		
		<?php foreach($this -> pastliveplaces as $pastliveplace) :?>
			<?php if($pastliveplace -> isViewable()) :?>
				<?php $j++; ?>
				<li>
					<span><?php echo $this->translate('Past') ?></span>
					<span>
						<?php echo $pastliveplace -> location ?>
						<?php if ($pastliveplace -> latitude != '0' && $pastliveplace -> longitude != '0'):?>
						-
						<?php echo $this->htmlLink(
			            array('route' => 'ynmember_general', 'action' => 'direction','type'=>'live', 'id' => $pastliveplace->getIdentity()), 
			            $this->translate('<span title="Get Direction" class="ynicon yn-map-marker ynmember_get_directions"></span> '), 
			            array('class' => 'smoothbox get_direction')) ?>
			            <?php endif;?>
					</span>
				</li>
			<?php endif;?>
		<?php endforeach;?>
	</ul>
	<?php if($j == 0) :?>
		<span></span>
		<span><?php echo $this->translate('No information available');?></span>	
	<?php endif;?>
	</div>
	<?php endif;?>
<?php endif;?>

<?php if($this -> viewer -> isSelf($this->subject)) :?>
<?php if($this -> row_feature) :?>
<div class="profile_fields">
	<h4>
		<span><?php echo $this->translate('Featured Expiration Date');?></span>
	</h4>
	<ul>
		<li>
			<span><?php echo $this->translate('Is a featured member until')?>
				<?php
					if(!empty($this -> row_feature->expiration_date))
					{
				        $expiration_date = new Zend_Date(strtotime($this -> row_feature->expiration_date));
						if( $this->viewer() && $this->viewer()->getIdentity() ) 
						{
							$tz = $this->viewer()->timezone;
							$expiration_date->setTimezone($tz);
						}
						echo $this->locale()->toDate($expiration_date);
					}
					else
						echo $this->translate('Not specified');
				  ?>
			</span>
			<span>
				<?php 
		            echo $this->htmlLink(
			            array('route' => 'ynmember_general', 
			                'module' => 'ynmember',
				            'controller' => 'index' ,
				            'action' => 'feature-member'), 
				            $this->translate('Feature Profile'), 
			            array('class' => 'smoothbox'));
		   		 ?>
			</span>
		</li>
	</ul>
</div>
<?php endif;?>
<?php endif;?>

<?php if($this->allow_update_relationship) :?>
<?php if (!is_null($this -> linkage)) :?>
	<?php if($this -> linkage -> isViewable()) :?>
		<h4>
			<span><?php echo $this->translate('Relationship Status');?></span>
		</h4>
		<span><?php echo $this -> linkage -> status;?></span>
		<?php if (!is_null($this -> linkage -> with_member == '1')) :?>
			<?php $member = Engine_Api::_()->user() -> getUser($this -> linkage -> resource_id); ?>
			<?php if ($member->getIdentity()):?>
				<span> <?php echo $this->translate("with"); ?> <?php echo $this->htmlLink($member->getHref(), $member->getTitle(), array('target' => '_blank')); ?></span>
			<?php endif;?>	
		<?php endif;?>
		<?php if (!is_null($this -> linkage -> anniversary)) :?>
		<?php $dateObject = new Zend_Date(strtotime($this -> linkage -> anniversary));?>
			<span><?php echo " " . $this -> translate("since") . " " . $dateObject->toString('MM-dd-y')?></span>
		<?php endif;?>
	<?php endif;?>
<?php endif;?>
<?php endif;?>


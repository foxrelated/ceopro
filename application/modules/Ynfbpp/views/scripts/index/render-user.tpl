<?php
	$settings =  Engine_Api::_() -> getApi('settings', 'core');
	$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');
	$coverPhotoUrl = Engine_Api::_()->ynfbpp()->getCoverPhotoUrl($this->subject);
	$userFields = Engine_Api::_()->ynfbpp()->getUserFields($this->subject);
	$userLivePlaces = Engine_Api::_()->ynfbpp()->getUserLivePlaces($this->subject);
	$userGroups = Engine_Api::_()->ynfbpp()->getUserGroups($this->subject);
	$userGroupsCount = 0;
	if ($userGroups && $userGroups instanceof Zend_Paginator)
		$userGroupsCount = $userGroups->getTotalItemCount();
	$userBusinessPages = Engine_Api::_()->ynfbpp()->getUserBusinessPages($this->subject);
	$userBusinessPagesCount = 0;
	if ($userBusinessPages && $userBusinessPages instanceof Zend_Paginator)
		$userBusinessPagesCount = $userBusinessPages->getTotalItemCount();
	$genderIconClass = 'yn-info-circle';
	if (!empty($userFields['gender'])) {
		if (strtolower($userFields['gender']) == 'male') {
			$genderIconClass = 'yn-user';
		} else if (strtolower($userFields['gender']) == 'female') {
			$genderIconClass = 'yn-user-woman';
		} else {
			$genderIconClass = 'yn-sex-unknown';
		}
	}
//	print_r($userFields);
?>

<?php echo $this->partial('_popup-style.tpl') ?>

<div class="uiContextualDialogContent uiYnfbpp_user_popup <?php if($coverPhotoUrl) echo 'have_cover_user' ?>">
	
		<div class="uiYnfbppHovercardCover">
			<span style="background-image:url(<?php echo $coverPhotoUrl ?>)"></span>
		</div>
	
	<div class="uiYnfbppHovercardTitle">
		<h2>
			<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
		</h2>
	</div>
	<div class="uiYnfbppHovercardStage">
		<div class="uiYnfbpp_avarta_owner">
            <a href="<?php echo $this->subject->getHref() ?>"  style="background-image:url(<?php echo $this->itemPhotoUrl($this->subject, 'thumb.profile') ?>)"></a>
		</div>
		<div class="uiYnfbpp_main_info">
			<div class="uiYnfbpp_info_content">
				<div class="uiYnfbpp_title_parent">
					<h2 class="uiYnfbpp_title ">
						<span class="ynicon <?php if($this->isSubjectOnline) echo 'active' ?>"></span>
						<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
					</h2>
					<div class="uiYnfbpp_name_gender">
						<ul>
							<div class="uiYnfbpp_full_name">
								<?php if (!empty($userFields['first_name']) || !empty($userFields['last_name']) || !empty($userFields['gender'])): ?>
								<span class="ynicon <?php echo $genderIconClass ?>"></span>
								<?php endif; ?>
								<span class="uiYnfbpp_full_name">
									<?php
										if( !empty($userFields['first_name']))
											echo $userFields['first_name'];
										if( !empty($userFields['first_name']) && !empty($userFields['last_name']) )
											echo ' ';
										if( !empty($userFields['last_name']))
											echo $userFields['last_name'];
									?>
								</span>
							</div>
							<span>
								<?php
								if( (!empty($userFields['first_name']) || !empty($userFields['last_name'])) && !empty($userFields['gender']) )
									echo '-';
								?>
							</span>
							<?php if (!empty($userFields['gender'])): ?>
								<li>
									<span><?php echo $userFields['gender']; ?></span>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
				<ul class="uiYnfbpp_communicate_icon">
					<?php if (!empty($userFields['facebook']))
						echo '<li><a href="https://www.facebook.com/' . $userFields['facebook'] . '"><span class="ynicon yn-facebook"></span></a></li>';
					?>
					<?php if (!empty($userFields['twitter']))
						echo '<li><a href="https://www.twitter.com/#!/' . $userFields['twitter'] . '"><span class="ynicon yn-twitter"></span></a></li>';
					?>
					<?php if (!empty($userFields['website']))
						echo '<li><a href="http://' . $userFields['website'] . '"><span class="ynicon yn-global"></span></a></li>';
					?>
					<?php if (!empty($userFields['aim']))
						echo '<li><a href="aim:goim?screenname=' . $userFields['aim'] . '"><span class="ynicon yn-aim"></span></a></li>';
					?>
				</ul>
				<ul class="uiYnfbpp_personal_info clearfix">
					<?php if (count($userLivePlaces)): ?>
						<li class="uiYnfbpp_local">
							<span class="ynicon yn-map-marker"></span>
							<span><?php echo array_shift($userLivePlaces) ?><?php if (count($userLivePlaces)) echo ' ' . $this->translate('and') . ' ' . implode(', ', $userLivePlaces) ?></span>
						</li>
					<?php endif ?>
					<?php if ($userGroups && $userGroups->getTotalItemCount()): ?>
						<li class="uiYnfbpp_group <?php if ($userGroupsCount == 1) echo 'uiYnfbpp_single_name' ?>">
							<span class="ynicon yn-user-group"></span>
							<?php echo $userGroups->getItem(0) ?>
							<?php if ($userGroupsCount > 1): ?>
								<span class="uiYnfbpp_and"><?php echo $this->translate('and') ?></span>
									<span class="uiYnfbpp_number"><?php echo $this->locale()->toNumber($userGroupsCount - 1) ?></span>
									<span><?php echo $this->translate(array('other', 'others', $userGroupsCount - 1)) ?></span>
							<?php endif; ?>
						</li>
					<?php endif; ?>
					<?php if (!empty($userFields['birthdate']))
					 echo '<li>
						<span class="ynicon yn-birthday-cake"></span>
						<span>' . $userFields['birthdate'] . '</span>
					</li>';
					?>
					<?php if ($userBusinessPagesCount): ?>
						<li class="uiYnfbpp_business <?php if ($userBusinessPagesCount == 1) echo 'uiYnfbpp_single_name' ?>">
							<span class="ynicon yn-briefcase"></span>
							<?php echo $userBusinessPages->getItem(0) ?>
							<?php if ($userBusinessPages->getTotalItemCount() > 1): ?>
								<span class="uiYnfbpp_and"><?php echo $this->translate('and') ?></span>
								<span class="uiYnfbpp_number"><?php echo $this->locale()->toNumber($userBusinessPages->getTotalItemCount() - 1) ?></span>
								<span><?php echo $this->translate(array('other', 'others', $userBusinessPages->getTotalItemCount() - 1)) ?></span>
							<?php endif; ?>
						</li>
				<?php endif; ?>
				</ul>
				<?php if (!empty($userFields['about_me'])): ?>
					<div class="uiYnfbpp_personal_decs">
						<span><span class="uiYnfbpp_personal_decs_about"><?php echo $this->translate('About me') ?>: </span><?php echo $userFields['about_me'] ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php if($allow && $settings->getSetting('ynfbpp.user.mutual',1)): ?>
		<?php echo $this->mutualFriends($this->subject, null, 9, '%s mutual friend','%s mutual friends'); ?>
	<?php endif; ?>
</div>
<?php if ($this->ynmember_actions || $this->actions): ?>
<div class="uiYnfbppHovercardFooter clearfix">
	<?php if ($this->ynmember_actions): ?>
		<div class="uiYnfbpp_option">
			<div class="uiYnfbpp_option_button">
				<span class="ynicon yn-arr-up"></span>
			</div>
			<ul class="uiYnfbpp_options">
				<?php foreach($this->ynmember_actions as $action): ?>
					<li><?php echo $action ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif;?>
	<?php if ($this->actions): ?>
		<ul class="uiYnfbppListHorizontal">
			<?php foreach($this->actions as $action): ?>
				<li class="uiYnfbppListItem">
					<?php echo $action?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif;?>
</div>
<?php endif;?>

<?php $settings =  Engine_Api::_() -> getApi('settings', 'core'); 
$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');
$hasCover = Engine_Api::_()->hasModuleBootstrap('advgroup') && !empty($this->subject->cover_photo);
$coverPhotoUrl = '';
$locationText = '';
if (!empty($this->subject->cover_photo))
{
	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->subject->cover_photo)->current();
	if($coverFile)
		$coverPhotoUrl = $coverFile->map();
}
if (!empty($this->subject->location)) {
	$location = json_decode($this->subject->location);
	if ($location->{'location'} != "0")
		$locationText = $location->{'location'};
}
?>

<?php echo $this->partial('_popup-style.tpl') ?>

<div class="uiContextualDialogContent uiYnfbpp_group_popup <?php if ($hasCover) echo 'have_cover_group' ?>">
	<div class="uiYnfbppHovercardCover">
		<?php if ($hasCover): ?>
		<span style="background-image:url(<?php echo $coverPhotoUrl ?>)"></span>
		<?php endif; ?>
		<h2 class="uiYnfbpp_title ">
			<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
		</h2>
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
				<h2 class="uiYnfbpp_title ">
					<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
				</h2>
				<ul class="uiYnfbpp_personal_info clearfix">
					<?php if($allow && $settings->getSetting('ynfbpp.group.location', 1) && $locationText): ?>
						<li class="uiYnfbpp_fullwidth uiYnfbpp_overflow">
							<span class="ynicon yn-map-marker"></span>
							<span class="uiYnfbpp_local"><?php echo $locationText ?></span>
						</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.group.owner', 1)): ?>
						<li class="uiYnfbpp_overflow">
							<span class="ynicon yn-user"></span>
							<span><?php echo $this->translate("Owner") ?>:</span>
							<?php echo $this->subject->getOwner() ?>
						</li>
					<?php endif; ?>
				</ul>
				<?php if($allow && $settings->getSetting('ynfbpp.group.description', 1) && !empty($this->subject->description)): ?>
					<div class="uiYnfbpp_personal_decs">
						<span><?php echo strip_tags($this->subject->description) ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php if($allow && $settings->getSetting('ynfbpp.group.mutual',1)): ?>
		<?php echo $this->itemMembers($this->subject,null, 9, '%s member joined','%s members joined'); ?>
	<?php endif; ?>
</div>
<?php if(($allow && $settings->getSetting('ynfbpp.group.location', 1) && $this->getDirection($this->subject)) || (isset($this->actions) && !empty($this->actions))): ?>
<div class="uiYnfbppHovercardFooter clearfix">
	<?php if($allow && $settings->getSetting('ynfbpp.group.location', 1)): ?>
		<?php echo $this->getDirection($this->subject) ?>
	<?php endif; ?>
	<?php if(isset($this->actions) && !empty($this->actions)): ?>
		<ul class="uiYnfbppListHorizontal">
			<?php foreach($this->actions as $action): ?>
			<li class="uiYnfbppListItem">
				<?php echo $action?>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
<?php endif; ?>

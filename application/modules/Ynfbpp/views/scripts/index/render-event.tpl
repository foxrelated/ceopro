<?php $settings =  Engine_Api::_() -> getApi('settings', 'core');
$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');
$coverPhotoUrl = '';
if (!empty($this->subject->cover_photo))
{
	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->subject->cover_photo)->current();
	$coverPhotoUrl = $coverFile->map();
}
$status = 'Ongoing';
$startDateObject = new Zend_Date(strtotime($this->subject->starttime));
$endDateObject = new Zend_Date(strtotime($this->subject->endtime));
if ($this->viewer && $this->viewer->getIdentity()) {
	$tz = $this->viewer()->timezone;
	$startDateObject->setTimezone($tz);
	$endDateObject->setTimezone($tz);
}
if ( $endDateObject->toValue() < time() ) {
	$status = 'Past';
} else if ( $startDateObject->toValue() > time() ) {
	$status = 'Upcoming';
}
$url = '';
if ($this->subject->url)
{
	$url = $this->subject->url;
	$pos = strpos($this->subject->url, "http");
	if ($pos === false){
		$url = "http://" . $this->subject->url;
	}
}
?>

<?php echo $this->partial('_popup-style.tpl') ?>

<div class="uiContextualDialogContent uiYnfbpp_event_popup <?php echo ($coverPhotoUrl) ? 'have_cover_event' : ''?>">
	<div class="uiYnfbppHovercardCover">
		<?php if ($coverPhotoUrl): ?>
			<span style="background-image:url(<?php echo $coverPhotoUrl; ?>)"></span>
		<?php endif; ?>
		<?php if ($allow && $settings->getSetting('ynfbpp.event.time', 1)): ?>
			<div class="uiYnfbpp_time">
				<div>
					<ul>
						<li><?php echo $this->locale()->toDate($startDateObject, array('format'=>'dd MMM'))?></li>
						<li><?php echo $this->locale()->toTime($startDateObject)?></li>
					</ul>
					<span class="ynicon yn-arr-right"></span>
					<ul>
						<li><?php echo $this->locale()->toDate($endDateObject, array('format'=>'dd MMM'))?></li>
						<li><?php echo $this->locale()->toTime($endDateObject)?></li>
					</ul>
				</div>
			</div>
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
				<?php if ($allow && $settings->getSetting('ynfbpp.event.status', 1)): ?>
					<li class="uiYnfbpp_event_status_parent">
						<ul class="uiYnfbpp_event_status">
							<li class="status_active uiYnfbpp_<?php echo strtolower($status) ?>"><?php echo $this->translate($status) ?></li>
						</ul>
					</li>
					<?php endif ?>
					<li class="uiYnfbpp_time">
						<?php if ($allow && $settings->getSetting('ynfbpp.event.time', 1)): ?>
						<div>
							<ul>
								<li><?php echo $this->locale()->toDate($startDateObject, array('format'=>'dd MMM'))?></li>
								<li><?php echo $this->locale()->toTime($startDateObject)?></li>
							</ul>
							<span class="ynicon yn-arr-right"></span>
							<ul>
								<li><?php echo $this->locale()->toDate($endDateObject, array('format'=>'dd MMM'))?></li>
								<li><?php echo $this->locale()->toTime($endDateObject)?></li>
							</ul>
						</div>
						<?php endif; ?>
						<?php if ($allow && $settings->getSetting('ynfbpp.event.status', 1)): ?>
						<ul class="uiYnfbpp_event_status">
							<li class="status_active uiYnfbpp_<?php echo strtolower($status) ?>"><?php echo $this->translate($status) ?></li>
						</ul>
						<?php endif; ?>
					</li>
					<?php if($allow && $settings->getSetting('ynfbpp.event.location',1) && !empty($this->subject->location)): ?>
					<li class="uiYnfbpp_local">
						<span class="ynicon yn-map-marker"></span>
						<span><?php echo $this->subject->location ?></span>
					</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.event.owner',1) && !empty($this->subject->host)): ?>
						<li class="uiYnfbpp_hostby">
							<span class="ynicon yn-user"></span>
							<span><?php echo $this->translate('Hosted by')?></span>
							<span class="uiYnfbpp_hostby_name"><?php
							if(strpos($this->subject->host,'younetco_event_key_') !== FALSE)
							{
								$user_id = substr($this->subject->host, 19, strlen($this->subject->host));
								$user = Engine_Api::_() -> getItem('user', $user_id);
								echo $user;
							}
							else {
								echo $this->subject->host;
							}
							?></span>
						</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.event.website',1) && !empty($this->subject->url)): ?>
						<li class="uiYnfbpp_fullwidth">
							<span class="ynicon yn-global"></span><a target="_blank" href="<?php echo $url ?>"><?php echo $url ?></a>
						</li>
					<?php endif; ?>
				</ul>
				<?php if($allow && $settings->getSetting('ynfbpp.event.description', 1) &&  !empty($this->subject->description)): ?>
				<div class="uiYnfbpp_personal_decs">
					<span><?php echo strip_tags($this->subject->description) ?></span>
				</div>
				<?php endif ?>
			</div>
		</div>
	</div>
	<?php if($allow && $settings->getSetting('ynfbpp.event.mutual',1)): ?>
		<?php echo $this->itemMembers($this->subject,null, 9, '%s member joined','%s members joined'); ?>
	<?php endif; ?>
</div>
<?php if(($allow && $settings->getSetting('ynfbpp.event.location', 1) && !empty($this->subject->latitude)) || (isset($this->actions) && !empty($this->actions))): ?>
<div class="uiYnfbppHovercardFooter clearfix">
		<?php if($allow && $settings->getSetting('ynfbpp.event.location', 1)): ?>
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

<?php $settings =  Engine_Api::_() -> getApi('settings', 'core');
$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');

// get cover
$coverPhotoUrl = "";
$hasCover = !empty($this->subject->cover_photo);
if ($hasCover)
{
	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->subject->cover_photo)->current();
	$coverPhotoUrl = $coverFile->map();
}

//// get jobs
$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
$searchParams['status'] = 'published';
$searchParams['company_id'] = $this->subject->getIdentity();
$searchParams['order'] = 'creation_date';
$searchParams['direction'] = 'DESC';
$jobs = $jobTbl->getJobsPaginator($searchParams);
$jobs->setItemCountPerPage(2);
$jobCount = $jobs->getTotalItemCount();

// get follower
$tableFollow = Engine_Api::_() -> getItemTable('ynjobposting_follow');
$list_follow = $tableFollow -> getFollowByCompanyId($this->subject->getIdentity());
$followerCount = count($list_follow);
$limit = 9;
$count = ($followerCount > $limit) ? $limit - 1 : $limit;
?>

<?php echo $this->partial('_popup-style.tpl') ?>

<div class="uiContextualDialogContent uiYnfbpp_company_popup <?php if($hasCover) echo 'have_cover_company' ?>">
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
				<div class="uiYnfbpp_title_parent">
					<h2 class="uiYnfbpp_title ">
						<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
					</h2>
				</div>
				<ul class="uiYnfbpp_personal_info clearfix">
					<?php if($allow && $settings->getSetting('ynfbpp.company.location', 1) && !empty($this->subject->location)): ?>
						<li class="uiYnfbpp_local">
							<span class="ynicon yn-map-marker"></span>
							<span><?php echo $this->subject->location;?></span>
						</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.company.jobs', 1) && $jobCount): ?>
						<li class="uiYnfbpp_jobs">
							<span class="ynicon yn-briefcase"></span>
							<a href="<?php echo $this->subject->getHref() ?>?view=job"><?php echo $this->translate(array('%s job', '%s jobs', $jobCount), $this->locale()->toNumber($jobCount)) ?></a>
						</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.company.website', 1) && !empty($this->subject->website)): ?>
						<li class="uiYnfbpp_website">
							<span class="ynicon yn-global"></span>
							<a href="<?php echo $this->subject->getWebsite();?>" target="_blank"><?php echo $this->subject->getWebsite();?></a>
						</li>
					<?php endif; ?>
				</ul>
				<?php if($allow && $settings->getSetting('ynfbpp.company.description', 1) && !empty($this->subject->description)): ?>
					<div class="uiYnfbpp_personal_decs">
						<span><?php echo strip_tags($this->subject->description) ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="uiYnfbpp_out_info clearfix">
				<?php if($followerCount): ?>
					<div class="uiYnfbpp_manual_friend">
						<div><a href="<?php echo $this->subject->getHref() ?>"><?php echo $this->translate(array('%s follower', '%s followers', $followerCount), $this->locale()->toNumber($followerCount)) ?></a></div>
						<ul class="uiYnfbpp_manual_friend_list clearfix">
							<?php foreach($list_follow as $row): ?>
								<?php $person = Engine_Api::_()->getItem('user', $row -> user_id); ?>
								<li><?php echo $this->htmlLink($person -> getHref(), $this->itemPhoto($person, 'thumb.icon'),array('title'=>$person->getTitle())) ?></li>
								<?php
									if (--$count <= 0)
									{
										break;
									}
								?>
							<?php endforeach; ?>
							<?php if ($followerCount > $limit): ?>
							<li class="uiYnfbpp-arrow">
								<a href="<?php echo $this->subject->getHref() ?>">
									<span class="ynicon yn-arr-right"></span>
								</a>
							</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
</div>
<?php if (($allow && $settings->getSetting('ynfbpp.company.location', 1) && !empty($this->subject->latitude)) || !empty($this->actions)): ?>
<div class="uiYnfbppHovercardFooter clearfix">
	<?php if($allow && $settings->getSetting('ynfbpp.company.location', 1)): ?>
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

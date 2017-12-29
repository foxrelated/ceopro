<?php $settings =  Engine_Api::_() -> getApi('settings', 'core');
//$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');
$allow = true;
$productsCount = $this->subject->getTotalProduct();
$isFollowing = $this->subject->isFollowed($this->viewer->getIdentity());
$followCount = Engine_Api::_()->ynfbpp()->getStoreFollowCount($this->subject->getIdentity());
?>

<?php echo $this->partial('_popup-style.tpl') ?>

<div class="uiContextualDialogContent uiYnfbpp_company_popup uiYnfbpp_store_popup">
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
					<?php if($allow && $settings->getSetting('ynfbpp.store.address', 1) && !empty($this->subject->contact_address)): ?>
						<li class="uiYnfbpp_local">
							<span class="ynicon yn-map-marker"></span>
							<span><?php echo $this->subject->contact_address;?></span>
						</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.store.website', 1) && !empty($this->subject->contact_website)): ?>
						<li class="uiYnfbpp_website">
							<span class="ynicon yn-global"></span>
							<a href="<?php echo $this->subject->contact_website ?>" target="_blank"><?php echo $this->subject->contact_website ?></a>
						</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.store.products', 1) && $productsCount): ?>
						<li class="uiYnfbpp_jobs">
							<span class="ynicon yn-briefcase"></span>
							<a href="<?php echo $this->subject->getHref() ?>"><?php echo $this->translate(array('%s product', '%s products', $productsCount), $this->locale()->toNumber($productsCount)) ?></a>
						</li>
					<?php endif; ?>
				</ul>
				<?php if($allow && $settings->getSetting('ynfbpp.store.description', 1) && !empty($this->subject->description)): ?>
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
							<?php endforeach; ?>
							<li class="uiYnfbpp-arrow"><a href="<?php echo $this->subject->getHref() ?>">
									<span class="ynicon yn-arr-right"></span>
								</a></li>
						</ul>
					</div>
				<?php endif; ?>
			</div>
</div>
<?php if ($this->viewer->getIdentity() || $followCount): ?>
<div class="uiYnfbppHovercardFooter clearfix">
	<?php if($followCount): ?>
		<div class="uiynfbpp_get_localtion">
		<?php echo $this->htmlLink($this->subject->getHref(), $this->translate(array('%s follower', '%s followers', $followCount), $this->locale()->toNumber($followCount))); ?>
		</div>
	<?php endif; ?>
	<ul class="uiYnfbppListHorizontal">
		<li class="uiYnfbppListItem">
			<?php if ($isFollowing): ?>
				<a href="javascript:void(0);" class="store_follow_<?php echo $this->subject->getIdentity() ?> <?php if(!$isFollowing) echo 'active' ?>" onclick="ynfbppFollowStore(<?php echo $this->subject->getIdentity() ?>, <?php echo $this->viewer->getIdentity() ?>,'<?php echo $this->translate('Following') ?>');location.reload()"><span class="ynicon yn-check"></span><?php echo $this->translate('Following') ?></a>
			<?php else: ?>
				<a href="javascript:void(0);" class="store_follow_<?php echo $this->subject->getIdentity() ?> <?php if(!$isFollowing) echo 'active' ?>" onclick="ynfbppFollowStore(<?php echo $this->subject->getIdentity() ?>, <?php echo $this->viewer->getIdentity() ?>,'<?php echo $followLabel ?>');location.reload()"><span class="ynicon yn-plus"></span><?php echo $this->translate('Follow') ?></a>
			<?php endif;?>
		</li>
	</ul>
</div>
<?php endif; ?>
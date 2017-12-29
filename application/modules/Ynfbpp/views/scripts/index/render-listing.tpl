<?php $settings =  Engine_Api::_() -> getApi('settings', 'core');
$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');
$isLiked = $this->subject->likes()->isLike($this->viewer) ? 1 : 0;
$likeText = $this->translate('Like');
$likedText = $this->translate('Liked');
?>

<?php echo $this->partial('_popup-style.tpl') ?>

<div class="uiContextualDialogContent uiYnfbpp_listing_popup">
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
				<?php if($allow && $settings->getSetting('ynfbpp.listing.price', 1) && floatval($this->subject->price)): ?>
				<div class="uiYnfbpp_title ">
					<?php echo $this -> locale()->toCurrency($this->subject->price, $this->subject->currency); ?>
				</div>
				<?php endif; ?>
				<ul class="uiYnfbpp_personal_info clearfix">
					<?php if($allow && $settings->getSetting('ynfbpp.listing.enddate', 1) && !empty($this->subject->end_date)): ?>
					<li class="uiYnfbpp_fullwidth">
						<span><?php echo $this->translate('Expired') . ': ' ?></span>
						<span class="uiYnfbppexp"><?php echo date('M d Y', strtotime($this->subject->end_date)) ?></span>
					</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.listing.description', 1) && !empty($this->subject->description)): ?>
					<li class="uiYnfbpp_personal_decs">
						<span><?php echo strip_tags($this->subject->description) ?></span>
					</li>
					<li class="uiynfbpp_border"></li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.listing.location', 1) && $this->subject->location): ?>
					<li class="uiYnfbpp_fullwidth uiYnfbpp_listing_local">
						<span><span class="ynicon yn-map-marker"></span><?php echo $this->subject->location ?></span>
					</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.listing.category', 1)): ?>
					<?php $category = $this->subject->getCategory();  ?>
					<?php if($category) :?>
					<li class="uiYnfbpp_fullwidth uiYnfbpp_category">
						<span class="ynicon yn-folder-open"></span>
						<?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
					</li>
					<?php endif;?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php if(($this->subject->like_count) || $this->viewer->getIdentity()): ?>
<div class="uiYnfbppHovercardFooter clearfix">
	<?php if($this->subject->like_count): ?>
		<div class="uiynfbpp_get_localtion">
		<?php echo $this->htmlLink($this->subject->getHref(), $this->translate(array('%s people', '%s peoples', $this->subject->like_count), $this->locale()->toNumber($this->subject->like_count))); ?>
		<span><?php echo $this->translate('liked this') ?></span>
		</div>
	<?php endif; ?>
	<?php if($this->viewer->getIdentity()): ?>
	<ul class="uiYnfbppListHorizontal">
		<li class="uiYnfbppListItem">
			<a href="javascript:void(0);" class="<?php echo !$isLiked ? 'active' : '' ?>" type="<?php echo $this->subject->getType() ?>" like_text="<?php echo $likeText ?>" liked_text="<?php echo $likedText ?>" action="<?php echo $isLiked ? 'unlike' : 'like' ?>" listing_id="<?php echo $this->subject->getIdentity() ?>" onclick="ynfbppLike(this)"><span class="ynicon yn-thumb-up"></span><?php echo $isLiked ? $likedText : $likeText ?></a>
		</li>
	</ul>
	<?php endif; ?>
</div>
<?php endif; ?>

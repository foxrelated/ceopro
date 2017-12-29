<div class="ynmultilisting-wishlist-manage">
	<div id="create-wishlist-btn">
		<?php echo $this->htmlLink(array(
			'route' => 'ynmultilisting_wishlist',
			'action' => 'create'
		), $this->translate('Create New Wish List'), array(
			'class' => 'smoothbox btn'
		))?>
	</div>

	<?php if ($this->paginator->getTotalItemCount()) :?>
	<ul class="wishlist-list clearfix">
	<?php foreach ($this->paginator as $item): ?>
		<?php $photo_url = ($item->getPhotoUrl('thumb.profile')) ? $item->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>
		<li class="wishlist-item">

			<div class="wishlist-photo" style="background-image:url('<?php echo $photo_url ?>')">

			</div>

			<ul class="wishlist-option">
				<li><?php echo $this->htmlLink($item->getHref(), '<i class="fa fa-eye"></i> '.$this->translate('View Wish List'))?></li>
				<li><?php echo $this->htmlLink(array(
					'route' => 'ynmultilisting_wishlist',
					'action' => 'edit',
					'id' => $item->getIdentity()
				), '<i class="fa fa-pencil-square-o"></i> '.$this->translate('Edit Wish List'), array(
					'class' => 'smoothbox'
				))?></li>
				<li><?php echo $this->htmlLink(array(
					'route' => 'ynmultilisting_wishlist',
					'action' => 'delete',
					'id' => $item->getIdentity()
				), '<i class="fa fa-times"></i> '.$this->translate('Delete Wish List'), array(
					'class' => 'smoothbox'
				))?></li>
			</ul>

			<div class="wishlist-info">
				<div class="wishlist-title"><?php echo $item?></div>
				<?php $listings = $item->getAllListings();?>
				<div class="listing-count"><?php echo $this->translate(array('%s Listing', '%s Listings', count($listings)), count($listings))?></div>
				<div class="wishlist-description"><?php echo $item->getDescription()?></div>
			</div>



		</li>
	<?php endforeach;?>
	</ul>

	<div id='paginator'>
		<?php if( $this->paginator->count() > 1 ): ?>
		     <?php echo $this->paginationControl($this->paginator, null, null, array()); ?>
		<?php endif; ?>
	</div>
	<?php else: ?>
	<div class="tip">
		<span><?php echo $this->translate('No wish lists found.')?></span>
	</div>
	<?php endif; ?>
</div>

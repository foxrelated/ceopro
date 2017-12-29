<?php $viewer = Engine_Api::_()->user()->getViewer();?>


<div class="ynmultilisting-all-wishlist">

	<?php if ($this->paginator->getTotalItemCount()) : ?>
	<div class="wishlist-count">
		<?php echo $this->translate(array('%s Wish List', '%s Wish Lists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?>
	</div>

	<ul class="wishlist-list clearfix">
	<?php foreach ($this->paginator as $item): ?>
		<li class="wishlist-item">
			<div class="wishlist-title"><?php echo $item?></div>
			<?php $listings = $item->getAllListings();?>
			<div class="listing-count"><?php echo $this->translate(array('<span>%s</span> Listing', '<span>%s</span> Listings', count($listings)), count($listings))?></div>

			<div class="show-photo-slide" style="background-image: url('') ">

			</div>


			<ul class="photo-slide">
				<?php $i = 0;?>
				<?php foreach ($listings as $listing) :?>
				<?php if ($i == 3) break;?>
				<?php $photo_url = ($listing->getPhotoUrl('thumb.main')) ? $listing->getPhotoUrl('thumb.main') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>

				<li style="background-image: url('<?php echo $photo_url ?>')">
					<img class="photo-slide-img<?php if( $i == 0) echo ' photo-slide-img-first'; ?>"    src="<?php echo $photo_url ?>" alt="" style="display: none">

				</li>


				<?php $i++;?>
				<?php endforeach;?>

			</ul>

			<div class="wishlist-option">
			<?php if (!$item->isOwner($viewer)) :?>
				<?php echo $this->htmlLink(array(
					'route' => 'ynmultilisting_wishlist',
					'action' => 'add-to-my',
					'id' => $item->getIdentity()
				), '<i class="fa fa-bookmark"></i> '.$this->translate('Add Wishlist'), array(
					'class' => 'smoothbox'
				))?>

				<?php echo $this->htmlLink(array(
					'route' => 'messages_general',
					'action' => 'compose',
					'to' => $item -> getOwner() -> getIdentity()
				), '<i class="fa fa-envelope"></i> '.$this->translate('Message'), array(
					'class' => 'smoothbox'
				))?>
			<?php else:?>
				<?php echo $this->htmlLink(array(
					'route' => 'ynmultilisting_wishlist',
					'action' => 'delete',
					'id' => $item->getIdentity()
				), '<i class="fa fa-times"></i> '.$this->translate('Delete Wishlist'), array(
					'class' => 'smoothbox delete-wishlist'
				))?>
			<?php endif;?>
			</div>
		</li>
	<?php endforeach;?>
	</ul>

	<div id='paginator'>
		<?php if( $this->paginator->count() > 1 ): ?>
		     <?php echo $this->paginationControl($this->paginator, null, null, array(
		            'pageAsQuery' => true,
		            'query' => $this->formValues,
		          )); ?>
		<?php endif; ?>
	</div>

	<?php else: ?>
	<div class="tip">
		<span><?php echo $this->translate('No wish lists found.')?></span>
	</div>
	<?php endif; ?>

</div>

<script type="text/javascript">
	$$('.wishlist-item .photo-slide-img-first').each(function(el) {
	     var src = el.get('src');
	     el.getParent('.wishlist-item').getChildren('.show-photo-slide').setStyle('background-image','url( '+ src +' )');
	     el.getParent('.wishlist-item li').addClass('active');
	});

	$$('.photo-slide li').addEvent('click',function(){
		this.getParent().getElements('li').removeClass('active');
		this.addClass('active')

		var bg_src = this.getChildren('.photo-slide-img').get('src');

		this.getParent('.wishlist-item').getChildren('.show-photo-slide').setStyle('background-image','url('+ bg_src +')')
	});

</script>

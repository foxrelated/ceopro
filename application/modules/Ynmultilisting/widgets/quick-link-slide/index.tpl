<?php 

	$this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js');
	$this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/owl.carousel.js');
	$this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Ynmultilisting/externals/styles/owl.carousel.css');
 ?>

<div class="ynmultilisting-quicklink-list">
	<div class="title-description">
		<div class="title"><?php if (!empty($this->params['title'])) echo $this->params['title'];?></div>
		<div class="description"><?php if (!empty($this->params['description'])) echo $this->params['description'];?></div>
	</div>

	<ul class="quicklink-list owl-carousel" id="quicklink-owl-demo-<?php echo $this->identity?>">
	<?php foreach($this->quicklinks as $quicklink) : ?>
		<li class="quicklink-item item">
			<?php $listings = $quicklink->getListings(array('random' => true, 'limit'=> 3))?>

			
			<?php foreach ($listings as $listing) :?>
			<?php $photo_url = ($listing->getPhotoUrl('thumb.main')) ? $listing->getPhotoUrl('thumb.main') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>

			<div class="listing-list" style="background-image: url(<?php echo $photo_url; ?>) ">
				<div class="listing-list-hover" onclick="window.location.href ='<?php echo $listing->getHref();?>'">
					<?php if (isset($this->params['show_name']) && $this->params['show_name']) : ?>
					<div class="listing-title"><?php echo $listing?></div>
					<?php endif;?>

					<?php if (isset($this->params['show_onwer']) && $this->params['show_onwer']) : ?>
					<div class="listing-owner"><?php echo $this->translate('by %s', $listing->getOwner())?></div>
					<?php endif;?>

					<?php if (isset($this->params['show_category']) && $this->params['show_category']) : ?>
					<div class="listing-category"><?php echo '<i class="fa fa-folder-open"></i>&nbsp;'.$listing->getCategory()?></div>
					<?php endif;?>

					<?php if (isset($this->params['show_price']) && $this->params['show_price']) : ?>
					<div class="listing-price"><?php echo $this->locale()->toCurrency($listing->price, $listing->currency)?></div>
					<?php endif;?>
				</div>
			</div>
			<?php endforeach;?>
		</li>
	<?php endforeach;?>
	</ul>
</div>
    <script type="text/javascript">
    jQuery.noConflict(); 

    jQuery(document).ready(function() {
       jQuery("#quicklink-owl-demo-<?php echo $this->identity?>").owlCarousel({

      navigation : true,
      slideSpeed : 300,
      paginationSpeed : 400,
      singleItem : true,
      autoPlay: <?php echo (isset($this->params['change_speed']) && $this->params['change_speed']) ? intval($this->params['change_speed'])*1000 : 5000;?>,

      // "singleItem:true" is a shortcut for:
      // items : 1, 
      // itemsDesktop : false,
      // itemsDesktopSmall : false,
      // itemsTablet: false,
      // itemsMobile : false

      });
    });
    </script>
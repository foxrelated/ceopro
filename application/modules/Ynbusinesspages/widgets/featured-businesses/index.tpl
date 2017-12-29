<?php
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery-1.10.2.min.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/owl.carousel.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/prettify.js');
	$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/owl.carousel.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/prettify.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/owl.theme.default.min.css');
?>

	<div class="ynbusinesspages-featured-businesspages">
		<ul id="ynbusinesspages_featured" class="ynbusinesspages-featured-list owl-carousel owl-theme">
		<?php if ( count($this->businesses) ):?>
			<?php $i_count = 1; $total_count = $this->businesses_count; ?>
				<?php foreach ( $this->businesses as $business ) :?>
				<?php 
					if ($i_count %4 == 1) {
						if ( $total_count - $i_count < 4 ) {
							echo '<li class="item"><div class="slides-item slides-item-'.($total_count - $i_count + 1).'">'; 
						} else {
							echo '<li class="item"><div class="slides-item over_number">'; 							
						}										
					}
				?>
				<div class="ynbusinesspages-featured-item">
					<div class="ynbusinesspages-featured-item-content">
						<?php echo Engine_Api::_()->ynbusinesspages()->getFeaturedSpan($business); ?>
						<div class="ynbusinesspages-featured-item-content-bottom">
							<?php if ($business -> getIdentity() > 0):?>
							<div class="ynbusinesspages-featured-item-content-right">
								<div class="ynbusinesspages-featured-item-rating">
									<?php echo Engine_Api::_()->ynbusinesspages()->renderBusinessRating($business->getIdentity(), false); ?>
								</div>
								<div class="ynbusinesspages-featured-item-review">
									<?php echo $this -> translate(array("%s review", "%s reviews", $business->review_count), $business->review_count) ?>
								</div>		
							</div>					
							<?php endif;?>

							<div class="ynbusinesspages-featured-item-content-left">
								<div class="ynbusinesspages-featured-item-title">
									<?php echo $this->htmlLink($business->getHref(), $business->name);?>
								</div>
					
								<?php if ($business -> getIdentity() > 0):?>
								<div class="ynbusinesspages-featured-item-info">
                                    <?php if($mainLocation = $business -> getMainLocation()): ?>
									<span class="ynbusinesspages-featured-item-location">
										<i class="ynicon yn-map-marker"></i> <?php echo $mainLocation;?>
									</span>
                                    <?php endif; ?>
								
									<?php $category = $business -> getMainCategory()?>
									<?php if ($category):?>
										<span class="ynbusinesspages-featured-item-categories">
											<i class="ynicon yn-folder-open-o"></i> <?php echo $this -> translate($category -> title); ?>	
										</span>
									<?php endif;?>
								</div>
								<?php endif;?>
							</div>
						</div>				
					</div>
				</div>			
				<?php $i_count++; ?>			
				<?php if ($i_count %4 == 1) echo '</div></li>'; ?>
			<?php endforeach;?>

			<?php if ($i_count %4 != 1) echo '</div></li>'; ?>
			<!-- ELSE NOT HAVE ANY TO SHOW -->
			<?php else: ?>
			<div class="tip">
				<span>
				  <?php echo $this->translate('There are no featured businesses.'); ?>
				</span>
			</div>
			<?php endif; ?>
		</ul>
	</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
 		var owl_article = jQuery('#ynbusinesspages_featured');
 		var item_amount = parseInt(owl_article.find('.item').length); 
 		var true_false = 0;
 		if (item_amount > 1) {
 			true_false = true;
 		} else{
 			true_false = false;
		}
		var rtl = false;
		if(jQuery("html").attr("dir") == "rtl") {
			rtl = true;
		}
		jQuery("#ynbusinesspages_featured").owlCarousel({
			rtl:rtl,
			nav:true_false,
			navText:["<i class='ynicon yn-arr-left'></i>","<i class='ynicon yn-arr-right'></i>"],
			loop: true_false,
			mouseDrag:true_false, 
			autoplay: true_false,
			dotsSpeed:1000,
			autoplayHoverPause:true,
			items:1,
			dots: true_false,
	 	});
	});
</script>

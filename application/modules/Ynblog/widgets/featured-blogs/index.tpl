<?php 
	$this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynblog/externals/scripts/jquery-1.9.1.min.js');
	$this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynblog/externals/scripts/owl.carousel.js');
	$this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Ynblog/externals/styles/owl.carousel.css');
 ?>

<div id="ynblog-featured-blogs" class="owl-carousel owl-theme">
	<?php foreach($this -> blogs as $item):?>
	<div class="item">
		<?php 
		   $photoUrl = $item ->getPhotoUrl();
		   if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynblog/externals/images/nophoto_blog_thumb_main.png';
		?>
		<div class="ynblog-featured-blog" style="background-image: url(<?php echo $photoUrl ?>)">
			<div class="ynblog-featured-blog-info">
				<div class="ynblog-fbi-left">
					<?php 
                    $creation_date = strtotime($item -> creation_date);
					$oldTz = date_default_timezone_get();
					if($this->viewer() && $this->viewer()->getIdentity())
					{
						date_default_timezone_set($this -> viewer() -> timezone);
					}
					else 
					{
						date_default_timezone_set( $this->locale() -> getTimezone());
					}
                    $day = date("d", $creation_date); 
					$month = date("M", $creation_date);
					$year = date("Y", $creation_date);
					date_default_timezone_set($oldTz);
                    ?>
					<span class="ynblog-fbi-mdy ynblog-mdy-day"><?php echo $day?></span>
					<span class="ynblog-fbi-mdy ynblog-mdy-month"><?php echo $month?></span>
					<span class="ynblog-fbi-mdy ynblog-mdy-year"><?php echo $year?></span>
				</div>

				<div class="ynblog-fbi-right">
					<div class="ynblog-title">
						<a href="<?php echo $item -> getHref();?>"><?php echo $item -> getTitle();?></a>
					</div>

					<div class="ynblog-owner-views">
						<span><i class="fa fa-user"></i>
							<?php
			                  $owner = $item->getOwner();
			                  echo $this->translate('By %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
			                ?>
						</span>
						<span><i class="fa fa-eye"></i> 
							<?php echo $this -> translate(array('%s view', '%s view', $item -> view_count), $this->locale()->toNumber($item->view_count));?>
						</span> 
					</div>
					
					<div class="ynblog-description">
						<?php echo strip_tags($item -> body);?>
					</div>
				</div>	
			</div>
		</div>
	</div>
	<?php endforeach;?>
</div>


<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function() {
	var owl_article = jQuery('#ynblog-featured-blogs');
	var item_amount = parseInt(owl_article.find('.item').length); 
	var true_false = 0;
	if (item_amount > 1) {
		true_false = true;
	}else{
		true_false = false;
	}

	var rtl = false;
	if(jQuery("html").attr("dir") == "rtl") {
		rtl = true;
	}

  	jQuery("#ynblog-featured-blogs").owlCarousel({
	rtl:rtl,
	nav:true_false,
	navText:["",""],
	loop: true_false,
	mouseDrag:true_false,	
	autoplay:true_false,
	dotsSpeed:1000,
	autoplayHoverPause:true,
	items:1
  });
	});
</script>
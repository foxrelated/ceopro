<?php if ($this -> view_mode == '1') :?>

<?php
    $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js');
    $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/owl.carousel.js');
    $this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Ynmultilisting/externals/styles/owl.carousel.css');
?>

<div class="ynmultilisting-featured-slider-style-2">
    <div class="ynmultilisting-featured-slider owl-carousel" id="featured-owl-demo"> 
    <?php foreach($this->listings as $listing) :?>
		<?php
		      
			$listing_photo = "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";
			if ( $listing->getPhotoUrl('thumb.main') ) {
				$listing_photo = $listing->getPhotoUrl('thumb.main');
			}
		?>
		<div class="featured-item item" style="background-image: url(<?php echo $listing_photo ?>) ">
            <div class="featured-item-infomation">

                <div class="listing_title">
                    <a href="<?php echo $listing->getHref(); ?>"><?php echo $listing->title; ?></a>
                </div>
                
                <div class="listing-rating-desc-price">
                    <div class="listing_rating">
                        <?php
                           echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing));
                        ?>

                        <span>
                            <?php echo $listing->rating ?>
                        </span>
                    </div>

                    <div class="short_description">
                        <?php echo strip_tags($listing->short_description)?>
                    </div>

                    <div class="listing_price">
                        <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
                    </div>
                </div>

            </div>
        </div>
	<?php endforeach; ?>        
    </div>
</div>
<!-- end of template -->

<script type="text/javascript">
    jQuery(document).ready(function() {
       jQuery("#featured-owl-demo").owlCarousel({

      navigation : true,
      slideSpeed : 500,
      paginationSpeed : 600,
      singleItem : true,
      autoPlay: true,
      });
    });
</script>

<?php else: ?>

<?php
$this->headScript()
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js')
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/jquery.easing.min.js')
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/masterslider.js');

$this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Ynmultilisting/externals/styles/masterslider/ms-partialview.css');
$this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Ynmultilisting/externals/styles/masterslider/masterslider.css');
$this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Ynmultilisting/externals/styles/masterslider/masterslider-style.css');
?>
<div class="ynmultilisting-featured-slider-style-1">
    <!-- template -->
    <div class="ms-partialview-template" id="partial-view-1">
        <!-- masterslider -->
        <div class="master-slider ms-skin-default" id="masterslider">
        <?php foreach($this->listings as $listing) :?>

        <?php
              
            $listing_photo = "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";
            if ( $listing->getPhotoUrl('thumb.main') ) {
                $listing_photo = $listing->getPhotoUrl('thumb.main');
            }
        ?>

            <div class="ms-slide">
                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $listing_photo ?>" alt="lorem ipsum dolor sit"/> 
                <div class="ms-info">
                    <div class="listing_title">
                        <a href="<?php echo $listing->getHref(); ?>"><?php echo $listing->title; ?></a>
                    </div>
                    
                    <div class="listing-rating-price">
                        <div class="listing_price">
                            <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
                        </div>

                        <div class="listing_rating">
                            <?php
                               echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing));
                            ?>
                            
                            &nbsp;

                            <span>
                                <?php echo $listing->rating ?>
                            </span>
                        </div>
                    </div>

                    <div class="short_description">
                        <?php echo strip_tags($listing->short_description)?>
                    </div>
                    
                </div>  
            </div>

        <?php endforeach; ?>  
        </div>
        <!-- end of masterslider -->
    </div>
    <!-- end of template -->
</div>
<script type="text/javascript">      
 
    jQuery.noConflict(); 
    var slider = new MasterSlider();
    slider.control('arrows');  
    slider.control('slideinfo',{insertTo:"#partial-view-1" , autohide:false, align:'bottom', size:160});
    slider.control('circletimer' , {color:"#FFFFFF" , stroke:9});
 
    slider.setup('masterslider' , {
        width:450,
        height:255,
        space:30,
        loop:true,
        autoplay: true,
        view:'partialWave',
        layout:'partialview'
    });
 
</script>



<?php endif; ?>
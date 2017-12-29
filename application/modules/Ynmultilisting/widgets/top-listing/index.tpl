
<?php
    $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js');
    $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/owl.carousel.js');
    $this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Ynmultilisting/externals/styles/owl.carousel.css');
?>

<h3><?php echo $this -> translate("Top listings in %s", $this -> category -> title);?></h3>

<div class="ynmultilisting-top-listing">
    <div class="ynmultilisting-top-listing-item owl-carousel" id="listing-top-owl-demo">

        <?php foreach ($this -> listings as $listing) :?>
            <div class="top-listing-block item">
                <div class="top-listing-title">
                    <?php echo $this->htmlLink($listing->getHref(), $listing->getTitle());?>
                </div>

                <?php $photo_url = ($listing->getPhotoUrl('thumb.main')) ? $listing->getPhotoUrl('thumb.main') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_main.png";?>

                <div class="top-listing-item" style="background-image: url('<?php echo $photo_url ?>') ">
                    <div class="listing_price">
                        <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
                    </div>
                    
                    <div class="listing_creation">
                        <span class=""><?php echo $this->translate('by ')?></span>
                        <span><?php echo $listing->getOwner()?></span>
                    </div>
                </div>

                <div class="top-listing-content">
                    <div class="author-avatar">
                        <?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?>
                    </div>
                    <div class="listing_rating">
                        <span><?php
                            echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing));
                            ?>
                        </span>
                    </div>
                    
                    <div class="listing_category">
                        <i class="fa fa-folder-open"></i>
                        <?php echo $listing -> getCategoryTitle();?>
                    </div>
                    <div class="listing_location">
                        <?php if ($listing->location): ?>
                        <span class="fa fa-map-marker"></span>
                        <?php echo $listing->location;?>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>
    </div> 
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
       jQuery("#listing-top-owl-demo").owlCarousel({

      navigation : true,
      slideSpeed : 500,
      paginationSpeed : 600,
      singleItem : true

      });
    });
</script>

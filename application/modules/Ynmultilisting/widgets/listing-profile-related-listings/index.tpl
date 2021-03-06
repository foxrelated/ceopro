<div id="related_listings">
    <div class="content clearfix">
    <?php foreach ($this->listings as $listing) : ?>
      <div class="ynmultilisting-grid-item">
        <div class="ynmultilisting-grid-item-content">
          <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>

          <div class="item-background" style="background-image: url(<?php echo $photo_url ?>);">

            <?php if ($listing->featured) : ?>
              <div class="featureListing"></div>
            <?php endif; ?>

            <?php if ($listing->isNew()) : ?>
              <div class="newListing"></div>
            <?php endif; ?>

            <div class="ynmultilisting-item-rating">
              <?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing)); ?>
            </div>
          </div>
          <div class="item-front-info">
            <div class="listing_title">
              <?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
            </div>    

            <div class="listing_price">
              <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
            </div>
          </div>
        </div>
        <div class="ynmultilisting-grid-item-hover">
          <div class="ynmultilisting-grid-item-hover-background">
            <div class="listing_view_more"> 
              <?php echo $this->htmlLink($listing->getHref(), $this->translate('View more ').'<span class="fa fa-arrow-right"></span>' );?>
            </div>
            
            <div class="short_description">
                <?php echo strip_tags($listing->short_description)?>
            </div>
                                
            <div class="listing_creation">
              <span class="author-avatar"><?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?></span>
              <span><?php echo $this->translate('by ')?></span>
              <span class="author-title"><?php echo $listing->getOwner()?></span>
            </div>
          </div>
        </div>
      </div> 
    <?php endforeach; ?>
    </div>
</div>

  <script>
  window.addEvent('domready', function() {
    var widget = document.getElement('.layout_ynmultilisting_listing_profile_related_listings');
    if (widget) {
      var main = widget.getParent('.layout_main');
      if (main) {
          
          main.adopt(widget);
      }
    }  
  });
  </script>
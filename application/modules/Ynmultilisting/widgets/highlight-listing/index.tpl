<?php $listing = $this -> listing;?>
<div class="ynmultilisting-highlight-listing">
    <div class="highlight-listing-title">
        <?php echo $listing;?>
    </div>
    <?php $photo_url = ($listing->getPhotoUrl('thumb.main')) ? $listing->getPhotoUrl('thumb.main') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_main.png";?>

    <div class="highlight-listing-item" style="background-image: url('<?php echo $photo_url ?>') ">
        <div class="listing_price">
            <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
        </div>
        
        <div class="listing_creation">
            <span class=""><?php echo $this->translate('by ')?></span>
            <span><?php echo $listing->getOwner()?></span>
        </div>

    </div>


    <div class="highlight-listing-content">
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
            <span class="fa fa-map-marker"></span>&nbsp;
            <?php echo $listing->location;?>
            <?php endif; ?>
        </div>
    </div>

</div>
<div class="most-listing-widget-style-<?php echo $this->view_mode?>">
<?php foreach ($this->listings as $listing) : ?>
<div class="item-widget-listing">
    <div class="most-listing-widget-title" style="display: none">
        <?php echo $this->htmlLink(
            $listing->getHref(),
            $listing->title,
            array('class' => 'title')
        )?>
    </div>
    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>
    <div class="image-background" style="background-image: url(<?php echo $photo_url; ?>);"></div>

    <div class="item-content">
        <?php echo $this->htmlLink(
            $listing->getHref(),
            $listing->title,
            array('class' => 'title')
        )?>
        <div class="comments">
            <span class="fa fa-heart"></span>
            <?php echo $this->translate(array('%s like', '%s likes', $listing->like_count), $listing->like_count)?>
        </div>

        <div class="description">
            <?php echo $listing->getDescription(); ?>
        </div>

        <div class="price">
            <?php echo $this->locale()->toCurrency($listing->price, $listing->currency); ?>
        </div>

    </div>
</div>
<?php endforeach; ?>
</div>

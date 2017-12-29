<h2>
    <?php echo $this->translate('Manage Listing') ?>
</h2>
<?php echo
    $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listings'),
    $this->translate('Manage Listings'),
    array());
?>
&nbsp; &raquo; &nbsp;
<?php echo $this -> listing;?>
<br />
<?php $listing = $this -> listing; ?>
<br />
<h3><?php echo $this -> translate("General Information"); ?></h3>
<div>
    <span><?php echo $this -> translate("Listing name");?>:</span>
    <span><?php echo $listing -> title?></span>
</div>
<div>
    <span><?php echo $this -> translate("Listing type");?>:</span>
    <span><?php echo $listing -> getListingType() -> title?></span>
</div>
<div>
    <span><?php echo $this -> translate("Category");?>:</span>
    <span><?php echo $listing -> getCategory() -> title?></span>
</div>
<div>
    <span><?php echo $this -> translate("Created date");?>:</span>
    <span><?php echo $this->locale()->toDate(strtotime($listing->creation_date)); ?></span>
</div>
<div>
<span><?php echo $this -> translate('Expired date');?>:</span>  
<span><?php if(strtotime($listing->expiration_date) > 0 )echo $this->locale()->toDate(strtotime($listing->expiration_date)); ?></span>
</div>
<div>
    <span><?php echo $this -> translate("Posted By");?>:</span>
    <span><?php echo $listing -> getOwner() -> getTitle(); ?></span>
</div>
<div>
    <span><?php echo $this -> translate("Short Description");?>:</span>
    <span><?php echo $this->viewMore(strip_tags($listing -> short_description))?></span>
</div>
<div>
    <span><?php echo $this -> translate("Price");?>:</span>
    <span><?php echo $listing -> price; ?></span>
</div>
<div>
    <span><?php echo $this -> translate("Location");?>:</span>
    <span><?php echo $listing -> location; ?></span>
</div>
<div>
    <span><?php echo $this -> translate("Status");?>:</span>
    <span><?php echo $listing -> status; ?></span>
</div>

<br />

<h3><?php echo $this -> translate("Statistic"); ?></h3>
<div>
    <span><?php echo $this -> translate("Total view");?>:</span>
    <span><?php echo $listing -> view_count; ?></span>
</div>
<div>
    <span><?php echo $this -> translate("Total like");?>:</span>
    <span><?php echo $listing -> like_count; ?></span>
</div>
<div>
    <span><?php echo $this -> translate("Total comment");?>:</span>
    <span><?php echo $listing -> comment_count; ?></span>
</div>
<div>
    <span><?php echo $this -> translate("Total review");?>:</span>
    <span><?php echo $listing -> comment_count; ?></span>
</div>
<div>
    <span><?php echo $this -> translate("Overal rating");?>:</span>
    <span><?php echo $listing -> rating ; ?></span>
</div>
<?php $review = $listing -> getReview(); ?>
<?php if ($review):?>
<?php $ratings = $review->getRating();?>
<?php foreach($ratings as $r):?>
<div>
    <span><?php echo $this -> translate($r -> title);?>:</span>
    <span><?php echo $r -> rating ?>/5</span>
</div>
<?php endforeach; ?>
<?php endif;?>
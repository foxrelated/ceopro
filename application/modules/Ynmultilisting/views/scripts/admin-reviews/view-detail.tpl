<form class="global_form_popup">
<h1><?php echo $this->translate('View Review Detail'); ?></h1>
<br />
<h3><?php echo $this->translate('General Information'); ?></h3>

<?php $listingType = $this -> review -> getListingType();?>
<div><?php echo $this -> translate('Listing Type') ?> : 
	<?php if($listingType) :?>
		<a target="_blank" href="<?php echo $listingType ->  getHref();?>"><?php echo $listingType -> getTitle();?></a>
	<?php endif;?>
</div>

<div><?php echo $this-> translate('Category') ?> : 
	<?php if($this -> review -> getParent() && $this -> review -> getParent() -> getCategory()) :?>
		<a target="_blank" href="<?php echo $this -> review -> getParent() ->  getCategory() -> getHref();?>"><?php echo $this -> review -> getParent() -> getCategory() -> getTitle();?></a>
	<?php endif;?>
</div>

<div><?php echo $this-> translate('Listing') ?> : 
	<?php if($this -> review -> getParent()) :?>
		<a target="_blank" href="<?php echo $this -> review -> getParent() -> getHref();?>"><?php echo $this -> review -> getParent() -> getTitle();?></a>
	<?php endif;?>
</div>

<div><?php echo $this-> translate('Review by') ?> : 
	<?php if($this -> review -> getOwner()) :?>
		<a target="_blank" href="<?php echo $this -> review -> getOwner() -> getHref();?>"><?php echo $this -> review -> getOwner() -> getTitle();?></a>
	<?php endif;?>
</div>

<?php
	$isEditor = Engine_Api::_() -> ynmultilisting() -> checkIsEditor($this -> review -> getListingType() -> getIdentity(), $this -> review -> getOwner());
?>
<div><?php echo $this-> translate('Reviewer type') ?> : <?php echo ($isEditor)? $this -> translate("Editor") : $this -> translate("User");?></div>

<div><?php echo $this->translate('Review date') ?> : <?php echo $this->locale()->toDateTime($this -> review ->creation_date) ?></div>

<br />

<h3><?php echo $this->translate('Review Information'); ?></h3>

<div><?php echo $this-> translate('Overall rating') ?> : <?php echo $this->partial('_review_rating_big.tpl', 'ynmultilisting', array('review' => $this -> review));?> </div>
<br/>
<!-- RATING -->
<?php foreach($this -> review -> getRating() as $ratingValue) :?>
	<div>
		<?php echo $this -> translate($ratingValue -> title);?>
		<?php echo $this->partial('_item_rating_big.tpl', 'ynmultilisting', array('item' => $ratingValue, 'attr' => 'rating'));?>
	</div>
	<br/>
<?php endforeach;?>
<!-- REVIEW -->
<?php foreach($this -> review -> getReview() as $reviewValue) :?>
	<div>
		<?php echo $this -> translate($reviewValue -> title);?>: 
		<?php echo $reviewValue -> content;?>
	</div>
	<br/>
<?php endforeach;?>

<div class="ynmulti_listing_review-info"><?php echo $this-> translate('<span>Pros</span>') ?> : <?php echo $this -> review -> pros ?></div>
<br/>
<div class="ynmulti_listing_review-info"><?php echo $this-> translate('<span>Cons</span>') ?> : <?php echo $this -> review -> cons ?></div>
<br/>
<div class="ynmulti_listing_review-info"><?php echo $this-> translate('<span>Summary</span>') ?> : <?php echo $this -> review -> overal_review ?></div>
<br />
	<button id='close_button' onclick="parent.Smoothbox.close()"><?php echo $this->translate('Close')?></button>
</form>
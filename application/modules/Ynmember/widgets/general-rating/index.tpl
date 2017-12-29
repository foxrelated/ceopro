<?php $tableReview = Engine_Api::_() -> getItemTable('ynmember_review'); ?>
<?php $tableRating = Engine_Api::_() -> getItemTable('ynmember_rating'); ?>
<div class="ynmember-widget-rating-item ynmember-clearfix ynmember-widget-rating-header">
	<div class="ynmember-widget-rating-label">
	<?php $reviews = $tableReview->getAllReviewsByResourceId($this -> resource -> getIdentity()); ?>
	<a href="<?php echo $this->url(array("controller" => "review", "action" => "user", "id" => $this -> resource -> getIdentity()),'ynmember_general');?>"><?php echo $this -> translate(array("%s review", "%s reviews" , count($reviews)), count($reviews));?></a>
	</div>

	<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $this -> resource -> rating));?>								
</div>
<?php foreach($this->ratingTypes as $ratingType) :?>
	<?php if(!empty($ratingType -> title)) :?>
			<?php $value = $tableRating -> getRatingOfType($ratingType -> getIdentity(), $this -> resource -> getIdentity()); ?>
			<?php if(!empty($value)) :?>
				<div class="ynmember-widget-rating-item ynmember-clearfix">
					<div class="ynmember-widget-rating-label"><?php echo $this -> translate($ratingType -> title);?></div>
					<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $value));?>
				</div>
			<?php endif;?>
	<?php endif;?>
<?php endforeach;?>

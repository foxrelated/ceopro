<div class="form-wrapper form-ynmember-rate">
<?php $tableRating = Engine_Api::_ () -> getItemTable('ynmember_rating');  ?>
	<div class='form-label'>
		<?php echo $this-> translate('General rating') ?>
	</div>
	<div class="form-element" style="height: 30px">
		<?php echo $this->partial('_yn_review_rating_big.tpl', 'ynmember', array('rate_number' => $tableRating -> getGeneralRatingOfReview($this -> review)));?>
	</div>
<?php foreach($this->ratings as $rating) :?>
	<?php $rating_type = Engine_Api::_()->getItem('ynmember_ratingtype', $rating -> rating_type);?>
	<?php if(!empty($rating_type -> title)) :?>
			<div class='form-label'>
				<?php echo $rating_type -> title;?> 
			</div>
			<div class="form-element" style="height: 30px">
				<?php echo $this->partial('_yn_review_rating_big.tpl', 'ynmember', array('rate_number' => $rating->rating));?>
			</div>
	<?php endif;?>
<?php endforeach;?>
</div>
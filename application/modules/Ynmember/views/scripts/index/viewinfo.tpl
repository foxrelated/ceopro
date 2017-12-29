<?php foreach($this->reviews as $review): ?>
	<?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($review); ?>
	<?php if($this -> fieldValueLoop($review, $fieldStructure)):?>
	<h3><?php echo $this -> translate('Review Specifications'); ?> </h3>
		<div>
		       <?php echo $this -> fieldValueLoop($review, $fieldStructure); ?>
		</div>
	<?php endif; ?>
	</br>
<?php endforeach;?>

<h3>General Rating</h3>
<span><?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $this->user->rating));?></span>
</br>
<?php foreach($this->ratings as $rating) :?>
	<?php $rating_type = Engine_Api::_()->getItem('ynmember_ratingtype', $rating -> rating_type);?>
	<?php if(!empty($rating_type -> title)) :?>
		<h3><?php echo $rating_type -> title;?></h3>
		<span><?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $rating->rating));?></span>
		</br>
	<?php endif;?>
<?php endforeach;?>

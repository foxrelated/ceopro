<h1><?php echo $this->translate('View Review Detail'); ?></h1>
<br />
<h2><?php echo $this->translate('General Information'); ?></h2>

<?php $user = Engine_Api::_() -> getItem('user', $this -> review -> user_id); ?>
<?php if($user): ?>
	<div><?php echo $this-> translate('Review by') ?> : <a target="_blank" href='<?php echo $user -> getHref() ?>'><?php echo $user -> getTitle(); ?></a></div>
<?php else:?>
	<div><?php echo $this-> translate('Review by') ?> : </div><?php echo $this->translate('Unknow')?></div>	
<?php endif;?>

<?php $resource = Engine_Api::_() -> getItem('user', $this -> review -> resource_id); ?>
<?php if($resource): ?>
	<div><?php echo $this-> translate('Review for') ?> : <a target="_blank" href='<?php echo $resource -> getHref() ?>'><?php echo $resource -> getTitle(); ?></a></div>
<?php else:?>
	<div><?php echo $this-> translate('Review for') ?> : <?php echo $this->translate('Unknow')?>	</div>
<?php endif;?>

<div><?php echo $this->translate('Review date') ?> : <?php echo $this->locale()->toDateTime($this -> review ->creation_date) ?></div>

<br />

<h2><?php echo $this->translate('Review Information'); ?></h2>

<?php $tableRating = Engine_Api::_ () -> getItemTable('ynmember_rating');  ?>
<div class='ynmember_label'><?php echo $this-> translate('General rating') ?> :</div><?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $tableRating -> getGeneralRatingOfReview($this -> review)));?>

<br />

<?php foreach($this->ratings as $rating) :?>
	<?php $rating_type = Engine_Api::_()->getItem('ynmember_ratingtype', $rating -> rating_type);?>
	<?php if(!empty($rating_type -> title)) :?>
		<div class='ynmember_label'><?php echo $rating_type -> title;?> :</div>
			<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $rating->rating));?>
		</br>
	<?php endif;?>
<?php endforeach;?>

<br />

<?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> review); ?>
<?php if($this -> fieldValueLoop($this -> review, $fieldStructure)):?>
<h3><?php echo $this -> translate('Review Specifications'); ?> </h3>
	<div>
	       <?php echo $this -> fieldValueLoop($this -> review, $fieldStructure); ?>
	</div>
<?php endif; ?>

<br />

<?php echo $this-> translate('Summary') ?> : <?php echo $this -> review -> summary ?>
<br />
<form class="global_form_popup">
	<button id='close_button' onclick="parent.Smoothbox.close()"><?php echo $this->translate('Close')?></button>
</form>
<ul class="ynmultilisting-list-recent-review">
<?php foreach ($this->paginator as $review):?>
	<li>
		<div class="ynmultilisting-list-recent-review">
			<?php $listing = $review->getParent();?>
			<i class="fa fa-pencil-square-o"></i> <?php echo $this->translate("Review for %s", $this->htmlLink($listing->getHref(), $listing->getTitle())); ?>
		</div>
		<div class="review-note">
			<div>
				<span><?php echo $this->partial('_review_rating_big.tpl', 'ynmultilisting', array('review' => $review));?></span>

				<span class="count-point"><?php echo number_format($review->overal_rating, 1, '.', '');?></span>

			</div>
			<div class="review-body">
				<?php echo '"'.$this->string()->truncate($this->string()->stripTags($review -> overal_review), 100).'"';?>
			</div>
		</div>
		<div class="review-owner">
			<?php $owner = $review -> getOwner();?>
			<span><?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'));?></span>
			<span> <?php echo $this->translate("&nbsp;by&nbsp;") ?> </span>
			<span><?php echo $this->htmlLink($owner->getHref(),$owner->getTitle());?></span>
		</div>
	</li>
<?php endforeach;?>
</ul>

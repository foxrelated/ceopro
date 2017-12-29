<?php 
$this -> headScript()
      -> appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/core.js');
?>

<?php if ($this -> reviews -> getTotalItemCount()):?>
<?php foreach ($this -> reviews as $review): ?>
	<div class="ynmember-review-detail-main">
		<div class="ynmember-review-item-rate">
			<span class="ynmember-review-item-rate-userful" id="ynmember_useful_<?php echo $review->getIdentity();?>">
				<?php 
					$params = $review->getReviewUseful();
					echo $this->partial(
				      '_useful.tpl',
				      'ynmember',
				      $params
				    );
				?>	
			</span>

			<span class="ynmember-review-item-rate-avatar">
				<?php
					$resourceUser = Engine_Api::_()->user()->getUser($review->user_id);
					if (is_null($resourceUser))
						continue;
				?>


				<?php echo $this->itemPhoto($resourceUser, 'thumb.icon');?>
			</span>
			
			<span class="ynmember-review-item-rate-author">			
			<?php echo " " . $this-> translate("by") . " ";?>
			<?php 
				$reviewer = Engine_Api::_()->user()->getUser($review->user_id);
				echo $this->htmlLink($reviewer->getHref(), $this->string()->truncate($reviewer->getTitle(), 10));
			?>			
			</span>
			-
			<span class="ynmember-review-item-rate-time">
			<?php
		        // Convert the dates for the viewer
		        $reviewDateObject = new Zend_Date(strtotime($review->creation_date));
		        if( $this->viewer() && $this->viewer()->getIdentity() ) 
		        {
					$tz = $this->viewer()->timezone;
					$reviewDateObject->setTimezone($tz);
		        }
		    ?>
		    <?php echo $this->locale()->toDate($reviewDateObject) . " ". $this->locale()->toTime($reviewDateObject); ?>
		    </span>		    
		</div>

		<h3 class="ynmember-review-item-title clearfix">
			<a href="<?php echo $this->url(array('controller' => 'review' ,'action' => 'detail', 'id' => $review->getIdentity()), 'ynmember_extended')?>"><?php echo $review->title;?></a>

			<?php if(($this -> can_edit_own_review) && ($this -> viewer -> getIdentity() == $review -> user_id) ) :?>
			<?php 
	            echo $this->htmlLink(
		            array('route' => 'ynmember_general', 
		                'module' => 'ynmember',
			            'controller' => 'index' ,
			            'action' => 'edit-rate-member', 
			            'id' => $review -> getIdentity()),
			            $this->translate('<span class="ynicon yn-pencil-square-o"></span>'), 
		            array('class' => 'smoothbox btn-ynmember-edit'));
	   		 ?>
	   		 <?php endif;?>
		</h3>

		<h3 class="ynmember-review-item-rating-title"><?php echo $this->translate("Rating") ?></h3>
		<div class="ynmember-review-item-rate-group">
			<?php foreach($review->getRating() as $rating) :?>
				<div class="ynmember-review-item-rate-item ynmember-clearfix">
				<?php if(!is_null($rating -> title)) :?>					
					<div class='ynmember_label'><?php echo $rating -> title;?></div>
					<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $rating->rating));?>
				<?php else:?>
					<div class='ynmember_label'><?php echo $this-> translate('General rating') ?></div>
					<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $rating->rating));?>
				<?php endif;?>
				</div>
			<?php endforeach;?>
		</div>

		<?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($review); ?>
		<?php if($this -> fieldValueLoop($review, $fieldStructure)):?>
			<h3 class="ynmember-review-item-field-tilte"><?php echo $this->translate("Review") ?></h3>
			<div class="ynmember-review-item-field-group">
				<?php echo $this -> fieldValueLoop($review, $fieldStructure); ?>
			</div>
		<?php endif; ?>


		<div class="ynmember-review-item-description">
			<?php echo $review -> summary;?>
		</div>

		<div class="ynmember-review-item-stats">
			<?php //echo $this->action("list", "comment", "ynmember", array("type" => 'ynmember_review', "id" => $review->getIdentity()))?>
			
			<span class="ynicon yn-thumb-up"></span>
			<span><?php $likeCount = $review->likes()->getLikePaginator()->getTotalItemCount(); echo $this->translate(array('%s like', '%s likes', $likeCount), $this->locale()->toNumber($likeCount)); ?></span> - 
			<span class="ynicon yn-comments"></span>
			<span><?php $commentCount = $review->comments()->getCommentPaginator()->getTotalItemCount(); echo $this->translate(array('%s comment', '%s comments', $commentCount), $this->locale()->toNumber($commentCount)); ?></span>
		</div>
		
		<?php if($this -> can_report_reviews) :?>
			<?php echo $this->htmlLink(Array(
				'module'=>'activity', 
				'controller'=>'index', 
				'action'=>'share', 
				'route'=>'default', 
				'type'=>'ynmember_review', 
				'id'=>$review->getIdentity(), 
				'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?> - 
			<?php endif;?>
		<?php if($this -> can_share_reviews) :?>	
			<?php echo $this->htmlLink(Array(
				'module'=>'core', 
				'controller'=>'report', 
				'action'=>'create', 
				'route'=>'default', 
				'subject'=>$review->getGuid(), 
				'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?> - 
		<?php endif;?>
		<?php if ($review->user_id == $this->viewer()->getIdentity()):?>
			<?php if($this -> can_delete_own_reviews) :?>
				<?php
	            	echo $this->htmlLink(array(
	                    'route' => 'ynmember_extended',
	            		'controller' => 'review',
	                    'action' => 'delete',
	                    'id' => $review->review_id,
	                    'format' => 'smoothbox'
	                ),
	                $this->translate('Delete Review'),
	                array(
	                    'class' => 'smoothbox'
	                ));
	            ?>
            <?php endif;?>
		<?php endif;?>
	</div>
<?php endforeach;?>
<?php else:?>
	<div class="tip">
    <span>
          <?php echo $this -> translate("Nobody has reviewed yet.");?>
   	</span>
  	</div>
<?php endif;?>
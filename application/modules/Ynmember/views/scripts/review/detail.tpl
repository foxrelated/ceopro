<?php 
$this -> headScript()
      -> appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/core.js');
?>
<?php $review = $this->review;?>
<div class="ynmember-review-title-top">
	<?php echo $this->translate('View review') ;?>
</div>

<div class="ynmember-review-detail">
	<div class="ynmember-review-detail-left">
		<div class="ynmember-review-item-for">
			<?php echo $this->translate('for') ;?>
			<?php
				$resourceUser = Engine_Api::_()->user()->getUser($review->resource_id);
				if (is_null($resourceUser))
					continue;
				echo $this->htmlLink($resourceUser->getHref(), $this->string()->truncate($resourceUser->getTitle(), 10));
			?>
		</div>

		<div class="ynmember-review-item-avatar">
			<?php $background_image = $this->layout()->staticBaseUrl."application/modules/User/externals/images/nophoto_user_thumb_profile.png"; ?>
			<?php if ($resourceUser->getPhotoUrl('thumb.profile')) 
				$background_image = $resourceUser->getPhotoUrl('thumb.profile'); ?>
			<?php echo $this->htmlLink($resourceUser->getHref(), '<span alt="'.$resourceUser->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$resourceUser->getTitle())) ?>
		</div>

		<div class="ynmember-review-item-userful" id="ynmember_useful_<?php echo $review->getIdentity();?>">
			<?php 
				$params = $review->getReviewUseful();
				echo $this->partial(
			      '_useful.tpl',
			      'ynmember',
			      $params
			    );
			?>	
		</div>
		
		<div class="ynmember-review-item-show">
			<a href="<?php echo $this->url(array("controller" => "review", "action" => "user", "id" => $review->resource_id),'ynmember_general');?>"><?php echo $this->translate("Show All Reviews");?></a>
		</div>
	</div>
	<div class="ynmember-review-detail-main">
		<div class="ynmember-review-item-rate">
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
					<div class='ynmember_label'><?php echo $this -> translate($rating -> title);?></div>
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
			<?php echo nl2br($review -> summary);?>
		</div>
		
		<?php if($this -> can_report_reviews) :?>
			<?php echo $this->htmlLink(Array(
				'module'=>'activity', 
				'controller'=>'index', 
				'action'=>'share', 
				'route'=>'default', 
				'type'=>'ynmember_review', 
				'id'=>$review->getIdentity(), 
				'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
			<?php endif;?>
		<?php if($this -> can_share_reviews) :?>	
			 - 
			<?php echo $this->htmlLink(Array(
				'module'=>'core', 
				'controller'=>'report', 
				'action'=>'create', 
				'route'=>'default', 
				'subject'=>$review->getGuid(), 
				'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
		<?php endif;?>
		<?php if ($review->user_id == $this->viewer()->getIdentity()):?>
			<?php if($this -> can_delete_own_reviews) :?>
				 - 
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
</div>

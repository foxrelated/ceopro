<?php 
$this -> headScript()
      -> appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/core.js');
?>

<?php if ($this -> reviews -> getTotalItemCount()):?>
<?php foreach ($this -> reviews as $review): ?>
<div class="ynmember-review-item-main">
	<div class="ynmember-review-item-rate">
		<span class="ynmember-review-item-rating">
		<?php
		$rating = $review -> getGeneralRating();
		echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $rating->rating));
		?>
		</span>
	
		<span class="ynmember-review-item-rate-author">			
		<?php echo " " . $this-> translate("by") . " ";?>
		<?php 
			$reviewer = Engine_Api::_()->user()->getUser($review->user_id);
			echo $this->htmlLink($reviewer->getHref(), $this->string()->truncate($reviewer->getTitle(), 10), array('target' => '_blank'));
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
	    
	    	echo $this->locale()->toDate($reviewDateObject) . " ". $this->locale()->toTime($reviewDateObject); 
	    ?>			
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
	
	<div class="ynmember-review-item-description">
		<?php echo nl2br($review -> summary);?>
	</div>

	<div class="ynmember-review-item-stats">
		<?php //echo $this->action("list", "comment", "ynmember", array("type" => 'ynmember_review', "id" => $review->getIdentity()))?>
		<span class="ynicon yn-thumb-up"></span>
		<span><?php $likeCount = $review->likes()->getLikePaginator()->getTotalItemCount(); echo $this->translate(array('%s like', '%s likes', $likeCount), $this->locale()->toNumber($likeCount)); ?></span> - 
		<span class="ynicon yn-comments"></span>
		<span><?php $commentCount = $review->comments()->getCommentPaginator()->getTotalItemCount(); echo $this->translate(array('%s comment', '%s comments', $commentCount), $this->locale()->toNumber($commentCount)); ?></span> 
		
	</div>
</div>
<?php endforeach;?>
<?php else:?>
	<div class="tip">
    <span>
          <?php echo $this -> translate("Nobody has reviewed yet.");?>
   	</span>
  	</div>
<?php endif;?>
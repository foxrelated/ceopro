<div id="ynmultilisting-full-review-block">
	<ul id="ynmultilisting_profile_reviews">	
	    <?php 
	    	$review = $this -> review;
	    	$owner = $review->getOwner();
			$isEditor = Engine_Api::_() -> ynmultilisting() -> checkIsEditor($review -> getListingType() -> getIdentity(), $owner);
    	?>
	        <li>
				
				<div class="user_name">
					<?php echo $this->htmlLink($owner, $this->itemPhoto($owner, 'thumb.icon'))?>
					
	                <span class="review-infomation">
	                    <span class="review-info-by">
	                        <?php echo $this -> translate('by');?>
	                    </span>
	                    <span class="review-info-username">
	                        <?php if($isEditor) :?>
	                        	<a href="<?php echo $owner -> getHref();?>"><?php echo $this -> translate('Editor');?></a>
	                        <?php else:?>
	                        	<?php echo $owner?>
	                        <?php endif;?>
	                    </span>
	            	  	<span class="review-info-date">
		                    <?php 
				        		$createdDateObj = new Zend_Date(strtotime($review -> creation_date));	
								$createdDateObj->setTimezone($this->timezone);
								echo '&nbsp;&nbsp;-&nbsp;&nbsp;'.date('M d Y', $createdDateObj -> getTimestamp());
		                    ?>
	                    </span>
	                </span>

					<?php if($this -> viewer() -> getIdentity()) :?>
            	 	<div class="ynmultilisting-review-item-userful ynmultilisting_useful_<?php echo $review->getIdentity();?>" id="ynmultilisting_useful_<?php echo $review->getIdentity();?>">
			            <?php
							$params = $review->getReviewUseful();
			                echo $this->partial(
			                    '_useful.tpl',
			                    'ynmultilisting',
			                    $params
			                );
			            ?>
			        </div>
			        <?php endif;?>

				</div>

				<div class="review-title-option">
                	<div class="review-title"><?php echo $review ;?></div>

					<div class="option_div">
	                <?php if ($review->isDeletable()) : ?>
	                    <?php echo $this->htmlLink(
	                        array(
	                            'route' => 'ynmultilisting_review',
	                            'action' => 'delete',
	                            'id' => $review->getIdentity(),
	                            'tab' => $this->identity,
	                            'page' => $this->page
	                        ),
	                        $this->translate('<i class="fa fa-trash-o"></i>'),
	                        array(
	                            'class' => 'smoothbox'
	                        )
	                    ); ?>
	                <?php endif; ?>
	
	                <?php
	                    if ($review->isEditable()) {
	                        echo $this->htmlLink(
	                            array(
	                                'route' => 'ynmultilisting_review',
	                                'action' => 'edit',
	                                'id' => $review->getIdentity(),
	                                'tab' => $this->identity,
	                                'page' => $this->page
	                            ),
	                            $this->translate('<i class="fa fa-pencil-square-o"></i>'),
	                            array(
	                                'class' => 'smoothbox'
	                            )
	                        );
	                    }
	                ?> 
            		</div>
            	</div>

				<div class="review-general-rating">
					<h3><?php echo $this->translate("General Rating") ?></h3>
	                <div class="review-rating">
	                    <span>
	                    	<?php echo $this->partial('_review_rating_big.tpl', 'ynmultilisting', array('review' => $review));?>
	                		<!--<?php echo number_format($review->overal_rating, 1, '.', '');?>-->
	                	</span>
	                </div>
                </div>

	            
	            <!-- FULL REVIEW - RATING -->

				<div class="review-full-rating">
					<?php foreach($review -> getRating() as $ratingValue) :?>
						<div>							
							<h6><?php echo $this -> translate($ratingValue -> title);?></h6>
							<?php echo $this->partial('_item_rating_big.tpl', 'ynmultilisting', array('item' => $ratingValue, 'attr' => 'rating'));?>
						</div>
					<?php endforeach;?>
				</div>

				<!-- END FULL REVIEW - RATING -->


				<br />
				<!-- FULL REVIEW - REVIEW -->
				<div class="review-general-review">
	            	<h3><?php echo $this->translate("Review") ?></h3>
       		 	</div>

				<!-- END FULL REVIEW - REVIEW -->

             	<div class="review_pros"> 
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Pros:');?></h5>
	                <p><?php echo $this -> viewMore($review->pros)?></p>
	            </div>
	            
	            <div class="review_cons"> 
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Cons:');?></h5>
	                <p><?php echo $this -> viewMore($review->cons)?></p>
	            </div>

				<div class="review-table">
					<?php foreach($review-> getReview() as $reviewValue) :?>
						<div class="review-table-rows">
							<h5><?php echo $this -> translate($reviewValue -> title);?></h5>
							<p><?php echo $reviewValue -> content;?></p>
						</div>
					<?php endforeach;?>
				</div>


	            <div class="review_detail">
	                <div><p><?php echo $this -> viewMore($review->overal_review)?></p></div>   
	            </div>
	        </li>
	</ul>
</div>
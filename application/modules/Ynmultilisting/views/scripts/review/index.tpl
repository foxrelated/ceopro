<?php if ($this -> paginator -> getTotalItemCount()) :?>

<div class="ynmultilisting_listing_browse_review">
    <div id="ynmultilisting-general-review-block">
        <ul id="ynmultilisting_profile_reviews">
            <?php foreach ($this->paginator as $review) :
            $owner = $review->getOwner();
            $isEditor = Engine_Api::_() -> ynmultilisting() -> checkIsEditor($review -> getListingType() -> getIdentity(),
            $owner);
            ?>
            <li class="<?php echo ($isEditor)? " ynmultilisting-profile-review-editor
            " : "ynmultilisting-profile-review-member" ;?>">

            <div class="browse_review-box-image">
                <?php $listing = Engine_Api::_() ->getItem('ynmultilisting_listing', $review -> listing_id); ?>
                <div class="review-box-title">
                    <?php echo $this -> translate("for ");?>
                    <b><?php echo $this -> htmlLink($listing -> getHref(), $listing -> title); ?></b>
                </div>

                <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>

                <div class="review-box-image">
                    <a href="<?php echo $listing -> getHref(); ?>" style="background-image:url('<?php echo $photo_url ?>')"></a>
                </div>

                <div class="review-box-more">
                    <?php $reviewCount = $listing -> reviewCount(); ?>
                    <a href="<?php echo $listing -> getHref();?>"><i class="fa fa-caret-right"></i> <?php echo $this -> translate(array("Show %s review","Show %s reviews", $reviewCount), $reviewCount);?></a>
                </div>
            </div>

            <div class="browse_review-box-infomation">
                <div class="user_name">
                    <!--<?php echo $this->htmlLink($owner, $this->itemPhoto($owner, 'thumb.icon'))?>-->
				
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

                    <div class="review-rating">
                        <span>
                        	<?php echo $this->partial('_review_rating_big.tpl', 'ynmultilisting', array('review' => $review));?>
                        	<span class="review-rating-point"><?php echo number_format($review->overal_rating, 1, '.', '');?></span>
                    	</span>

                        <span>
                        <span class="review-rating-by"><?php echo $this ->
                            translate('&nbsp;&nbsp;-&nbsp;&nbsp;by');?></span>

                            <span class="review-rating-username">
                                <?php if($isEditor) :?>
                                <a href="<?php echo $owner -> getHref();?>"><?php echo $this ->
                                    translate('Editor');?></a>
                                <?php else:?>
                                <?php echo $owner?>
                                <?php endif;?>
                        	</span>
                        </span>

                	  	<span class="review-rating-date">
                            <?php
    			        		$createdDateObj = new Zend_Date(strtotime($review -> creation_date));
    							$createdDateObj->setTimezone($this->timezone);
    							echo '&nbsp;&nbsp;-&nbsp;&nbsp;'.date('M d Y', $createdDateObj -> getTimestamp());
                            ?>
                        </span>

                    </div>

                    <div class="review-title-option">
                        <div class="review-title"><?php echo $review;?></div>
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

                </div>
                <?php if(!empty($review->pros)) :?>
                <div class="review_pros">
                    <h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Pros:');?></h5>

                    <p><?php echo $this -> viewMore($review->pros)?></p>
                </div>
                <?php endif;?>
                <?php if(!empty($review->pros)) :?>
                <div class="review_cons">
                    <h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Cons:');?></h5>

                    <p><?php echo $this -> viewMore($review->cons)?></p>
                </div>
                <?php endif;?>
                <div class="review_detail">
                    <div><p><?php echo $this -> viewMore($review->overal_review)?></p></div>
                </div>
            </div>

            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>


<?php echo $this->paginationControl($this->paginator, null, null, array(
'pageAsQuery' => true,
'query' => $this->formValues,
)); ?>

<?php else: ?>

<div class="tip">
    <span><?php echo $this -> translate("No reviews.");?></span>
</div>

<?php endif; ?>
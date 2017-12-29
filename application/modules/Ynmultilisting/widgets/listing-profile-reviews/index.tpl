<div id="ynmultilisting-general-review-paging">

<script type="text/javascript">
<?php if($this->paginator->getTotalItemCount() > 0) :?>
 window.addEvent('domready', function() {
    <?php if( !$this->renderOne ): ?>
    var anchor = $('ynmultilisting-general-review-paging');
    $('ynmultilisting_profile_reviews_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynmultilisting_profile_reviews_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynmultilisting_profile_reviews_previous').removeEvents('click').addEvent('click', function(){
      var request = new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				anchor.innerHTML = responseHTML;
	     		eval(responseJavaScript);
	    }
      });
      request.send();
    });

    $('ynmultilisting_profile_reviews_next').removeEvents('click').addEvent('click', function(){
      var request = new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				anchor.innerHTML = responseHTML;
	     		eval(responseJavaScript);
	    }
      });
      request.send();
   });
   <?php endif; ?>
  });
 <?php endif; ?>
</script>

<div class="ynmultilisting-profile-module-header">
<?php if ($this->my_review || $this->paginator->getTotalItemCount() > 0) : ?>
	<div id="ynmultilisting-general-review" class="ynmultilisting-general-review-active"><?php echo $this -> translate('General Reviews');?></div>
	<div id="ynmultilisting-full-review"><?php echo $this -> translate('Full Reviews');?></div>
	<select id="ynmultilisting-review-switch">
		<option value="all"><?php echo $this -> translate('All');?></option>
		<option value="member"><?php echo $this -> translate('Reviewed by Members');?></option>
		<option value="editor"><?php echo $this -> translate('Reviewed by Editors');?></option>
	</select>
<?php endif; ?>

<?php if (!$this->my_review && ($this -> viewer -> getIdentity() > 0)) : ?>
    <div class="ynmultilisting-profile-header-right">
    <?php if ($this->can_review): ?>
	    <div id="add_review">
		    <?php echo $this->htmlLink(
		        array(
		            'route' => 'ynmultilisting_review',
		            'action' => 'create',
		            'id' => $this->listing->getIdentity(),
		            'tab' => $this->identity,
		            'page' => $this->page
		        ),'<i class="fa fa-pencil-square-o"></i> '.$this->translate('Add Your Review'),
		        array(
		            'class' => 'smoothbox buttonlink'
		        )
		    )?>
    	</div>
    <?php endif; ?>
    </div>
<?php endif; ?>
</div>

<!-- GENERAL REVIEW BLOCK -->
<div id="ynmultilisting-general-review-block">
	<ul id="ynmultilisting_profile_reviews">
	    <?php if ($this->my_review) :
	    	$owner = $this->my_review->getOwner();?>
	        <li id="my_review" class="my-review">
	            <div class="user_name">
	                <!--<?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'))?> -->

					<?php if($this -> viewer() -> getIdentity()) :?>
					<div class="ynmultilisting-review-item-userful ynmultilisting_useful_<?php echo $this->my_review->getIdentity();?>" id="ynmultilisting_useful_<?php echo $this->my_review->getIdentity();?>">
			            <?php
							$params = $this->my_review->getReviewUseful();
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
                        	<?php echo $this->partial('_review_rating_big.tpl', 'ynmultilisting', array('review' => $this->my_review));?>
                        	<span class="review-rating-point"><?php echo number_format($this -> my_review -> overal_rating, 1, '.', '');?></span>
	                 		<span class="review-rating-by"><?php echo $this -> translate('&nbsp;&nbsp;-&nbsp;&nbsp;by');?></span>
                            <span class="review-rating-username"><?php echo $this -> my_review -> getOwner();?></span>
                        </span>
                        <span class="review-rating-date">
                        <?php
			        		$createdDateObj = new Zend_Date(strtotime($this -> my_review -> creation_date));
							$createdDateObj->setTimezone($this->timezone);
							echo '&nbsp;&nbsp;-&nbsp;&nbsp;'.date('M d Y', $createdDateObj -> getTimestamp());
                        ?>
                        </span>
                        <span class="small_description"><?php echo $this->translate('(My Review)')?></span>
                    </div>

					<div class="review-title-option">
                    	<div class="review-title"><?php echo $this -> my_review;?></div>
			            <?php if ($this -> viewer -> getIdentity() > 0) : ?>
			                <div class="option_div">
			                    <?php echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_review',
			                                'action' => 'delete',
			                                'id' => $this->my_review->getIdentity(),
			                                'tab' => $this->identity,
			                                'page' => $this->page
			                            ),
			                            $this->translate('<i class="fa fa-trash-o"></i>'),
			                            array(
			                                'class' => 'smoothbox',
			                            )
			                        )?>
			                        <?php echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_review',
			                                'action' => 'edit',
			                                'id' => $this->my_review->getIdentity(),
			                                'tab' => $this->identity,
			                                'page' => $this->page
			                            ),
			                            $this->translate('<i class="fa fa-pencil-square-o"></i>'),
			                            array(
			                                'class' => 'smoothbox'
			                            )
			                        )?>
			                </div>
			            <?php endif; ?>
		            </div>


	            </div>
	            <?php if(!empty($this->my_review->pros)) :?>
	            <div class="review_pros">
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Pros:');?></h5>
	                <p><?php echo $this -> viewMore($this->my_review->pros)?></p>
	            </div>
	            <?php endif;?>
	            <?php if(!empty($this->my_review->cons)) :?>
	            <div class="review_cons">
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Cons:');?></h5>
	                <p><?php echo $this -> viewMore($this->my_review->cons)?></p>
	            </div>
	            <?php endif;?>
	            <div class="review_detail">
	                <div><?php echo $this -> viewMore($this->my_review->overal_review)?></div>
	            </div>

	        </li>
	    <?php endif; ?>
	    <?php foreach ($this->paginator as $review) :
			$owner = $review->getOwner();
			$isEditor = Engine_Api::_() -> ynmultilisting() -> checkIsEditor($review -> getListingType() -> getIdentity(), $owner);
	    ?>
	        <li class="<?php echo ($isEditor)? "ynmultilisting-profile-review-editor" : "ynmultilisting-profile-review-member" ;?>">


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
                        <span class="review-rating-by"><?php echo $this -> translate('&nbsp;&nbsp;-&nbsp;&nbsp;by');?></span>

                            <span class="review-rating-username">
	                            <?php if($isEditor) :?>
	                            	<a href="<?php echo $owner -> getHref();?>"><?php echo $this -> translate('Editor');?></a>
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
	            <?php if(!empty($review->cons)) :?>
	            <div class="review_cons">
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Cons:');?></h5>
	                <p><?php echo $this -> viewMore($review->cons)?></p>
	            </div>
	            <?php endif;?>
	            <div class="review_detail">
	                <div><p><?php echo $this -> viewMore($review->overal_review)?></p></div>
	            </div>
	        </li>
	    <?php endforeach; ?>
	</ul>
</div>
<!-- END GENERAL REVIEW BLOCK -->

<!-- FULL REVIEW BLOCK -->
<div id="ynmultilisting-full-review-block">
	<ul id="ynmultilisting_profile_reviews">
	    <?php if ($this->my_review) :
	    	$owner = $this->my_review->getOwner();?>
	        <li id="my_review" class="my-review">



	            <div class="user_name">
	                <?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'))?>

					<?php if($this -> viewer() -> getIdentity()) :?>
					<div class="ynmultilisting-review-item-userful ynmultilisting_useful_<?php echo $this->my_review->getIdentity();?>" id="ynmultilisting_useful_<?php echo $this->my_review->getIdentity();?>">
			            <?php
							$params = $this->my_review->getReviewUseful();
			                echo $this->partial(
			                    '_useful.tpl',
			                    'ynmultilisting',
			                    $params
			                );
			            ?>
			        </div>
					<?php endif;?>

	                <span class="review-infomation">
                 		<span class="review-info-by"><?php echo $this -> translate('by');?></span>
                        <span class="review-info-username"><?php echo $this -> my_review -> getOwner();?></span>
                        <span class="review-info-date">
	                    <?php
			        		$createdDateObj = new Zend_Date(strtotime($this -> my_review -> creation_date));
							$createdDateObj->setTimezone($this->timezone);
							echo '&nbsp;&nbsp;-&nbsp;&nbsp;'.date('M d Y', $createdDateObj -> getTimestamp());
	                    ?>
	                    </span>
	                    <span class="small_description"><?php echo $this->translate('(My Review)')?></span>
	                </span>
                </div>

				<div class="review-title-option">
					<div class="review-title"><?php echo $this ->my_review;?></div>
		            <?php if ($this -> viewer -> getIdentity() > 0) : ?>
		                <div class="option_div">
		                    <?php echo $this->htmlLink(
		                            array(
		                                'route' => 'ynmultilisting_review',
		                                'action' => 'delete',
		                                'id' => $this->my_review->getIdentity(),
		                                'tab' => $this->identity,
		                                'page' => $this->page
		                            ),
		                            $this->translate('<i class="fa fa-trash-o"></i>'),
		                            array(
		                                'class' => 'smoothbox',
		                            )
		                        )?>

		                        <?php echo $this->htmlLink(
		                            array(
		                                'route' => 'ynmultilisting_review',
		                                'action' => 'edit',
		                                'id' => $this->my_review->getIdentity(),
		                                'tab' => $this->identity,
		                                'page' => $this->page
		                            ),
		                            $this->translate('<i class="fa fa-pencil-square-o"></i>'),
		                            array(
		                                'class' => 'smoothbox'
		                            )
		                        )?>
		                </div>
	            	<?php endif; ?>
				</div>

                <!--GENERAL RATING-->
				<div class="review-general-rating">
	            	<h3><?php echo $this->translate("General Rating") ?></h3>
	                <div class="review-rating">
	                    <span>
	                    	<?php echo $this->partial('_review_rating_big.tpl', 'ynmultilisting', array('review' => $this->my_review));?>
	                    	<!--<?php echo number_format($this -> my_review -> overal_rating, 1, '.', '');?>-->
	                    </span>
	                    <!-- <span class="small_description"><?php echo $this->translate('(My Review)')?></span> -->
	                </div>
				</div>

	            <!-- FULL REVIEW - RATING -->
	            <?php if(count($this -> my_review -> getRating()) > 0) :?>
					<div class="review-full-rating">
						<?php foreach($this -> my_review -> getRating() as $ratingValue) :?>
							<div>
								<h6><?php echo $this -> translate($ratingValue -> title);?></h6>
								<?php echo $this->partial('_item_rating_big.tpl', 'ynmultilisting', array('item' => $ratingValue, 'attr' => 'rating'));?>
							</div>
						<?php endforeach;?>
					</div>
				<?php endif;?>
				<!-- END FULL REVIEW - RATING -->

				<br />
				<!-- FULL REVIEW - REVIEW -->
				<div class="review-general-review">
	            	<h3><?php echo $this->translate("Review") ?></h3>
	            </div>

				<!-- END FULL REVIEW - REVIEW -->
				<?php if(!empty($this->my_review->pros)) :?>
	            <div class="review_pros">
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Pros:');?></h5>
	                <p><?php echo $this -> viewMore($this->my_review->pros)?></p>
	            </div>
	            <?php endif;?>
	            <?php if(!empty($this->my_review->cons)) :?>
	            <div class="review_cons">
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Cons:');?></h5>
	                <p><?php echo $this -> viewMore($this->my_review->cons)?></p>
	            </div>
				<?php endif;?>

				<div class="review-table">
					<?php foreach($this -> my_review -> getReview() as $reviewValue) :?>
						<div class="review-table-rows">
							<h5><?php echo $this -> translate($reviewValue -> title);?></h5>
							<p><?php echo $reviewValue -> content;?></p>
						</div>
					<?php endforeach;?>
				</div>

	            <div class="review_detail">
	                <div><?php echo $this -> viewMore($this->my_review->overal_review)?></div>
	            </div>
	        </li>
	    <?php endif; ?>
	    <?php foreach ($this->paginator as $review) :
			$owner = $review->getOwner();
			$isEditor = Engine_Api::_() -> ynmultilisting() -> checkIsEditor($review -> getListingType() -> getIdentity(), $owner);
	    ?>
	        <li class="<?php echo ($isEditor)? "ynmultilisting-profile-review-editor" : "ynmultilisting-profile-review-member" ;?>">



	            <div class="user_name">
	                <?php echo $this->htmlLink($owner, $this->itemPhoto($owner, 'thumb.icon'))?>

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
	            <?php if(count($review -> getRating()) > 0) :?>

				<div class="review-full-rating">
					<?php foreach($review -> getRating() as $ratingValue) :?>
						<div>
							<h6><?php echo $this -> translate($ratingValue -> title);?></h6>
							<?php echo $this->partial('_item_rating_big.tpl', 'ynmultilisting', array('item' => $ratingValue, 'attr' => 'rating'));?>
						</div>
					<?php endforeach;?>
				</div>
				<?php endif;?>
				<!-- END FULL REVIEW - RATING -->
				<br />
				<!-- FULL REVIEW - REVIEW -->
				<div class="review-general-review">
	            	<h3><?php echo $this->translate("Review") ?></h3>
       		 	</div>

				<?php if(!empty($review->pros)) :?>
             	<div class="review_pros">
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Pros:');?></h5>
	                <p><?php echo $this -> viewMore($review->pros)?></p>
	            </div>
	            <?php endif;?>
	            <?php if(!empty($review->cons)) :?>
	            <div class="review_cons">
	            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Cons:');?></h5>
	                <p><?php echo $this -> viewMore($review->cons)?></p>
	            </div>
				<?php endif;?>
				<div class="review-table">
					<?php foreach($review-> getReview() as $reviewValue) :?>
						<div class="review-table-rows">
							<h5><?php echo $this -> translate($reviewValue -> title);?></h5>
							<p><?php echo $reviewValue -> content;?></p>
						</div>
					<?php endforeach;?>
				</div>
				<!-- END FULL REVIEW - REVIEW -->


	            <div class="review_detail">
	                <div><p><?php echo $this -> viewMore($review->overal_review)?></p></div>   
	            </div>
	        </li>
	    <?php endforeach; ?>
	</ul>
</div>
<!-- END FULL REVIEW BLOCK -->

<?php if($this->paginator->getTotalItemCount() > 0) :?>
 	<div class="ynmultilisting-paginator">
      <div id="ynmultilisting_profile_reviews_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="ynmultilisting_profile_reviews_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    </div>
<?php endif;?>
<br/>
<?php if (!$this->my_review && $this->paginator->getTotalItemCount() == 0) : ?>
<div class="tip">
    <span><?php echo $this->translate('No reviews have been posted in this listing yet.')?></span>
</div>
<?php endif;?>

<script type="text/javascript">

	function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
	    }
	    return "";
	}

	window.addEvent('domready', function() {
		<?php if ($this->my_review || $this->paginator->getTotalItemCount() > 0) : ?>

			$('ynmultilisting-review-switch').addEvent('change', function (){
				var value = this.getSelected().get('value')[0];
				switch(value) {
				    case 'member':
				        $$('.ynmultilisting-profile-review-editor').each(function(el) {
							el.setStyle('display', 'none');
						});
						$$('.ynmultilisting-profile-review-member').each(function(el) {
							el.setStyle('display', 'block');
						});
				        break;
				    case 'editor':
				        $$('.ynmultilisting-profile-review-editor').each(function(el) {
							el.setStyle('display', 'block');
						});
						$$('.ynmultilisting-profile-review-member').each(function(el) {
							el.setStyle('display', 'none');
						});
				        break;
				    default:
				        $$('.ynmultilisting-profile-review-editor').each(function(el) {
							el.setStyle('display', 'block');
						});
						$$('.ynmultilisting-profile-review-member').each(function(el) {
							el.setStyle('display', 'block');
						});
				}
			});

			$('ynmultilisting-general-review').addEvent('click', function(){
				$('ynmultilisting-general-review').addClass('ynmultilisting-general-review-active');
				$('ynmultilisting-full-review').removeClass('ynmultilisting-full-review-active');

				$('ynmultilisting-full-review-block').setStyle('display', 'none');
				$('ynmultilisting-general-review-block').setStyle('display', 'block');
				setCookie('ynmultilisting_profile_review_mode_view', 'general', 1);
			});

			$('ynmultilisting-full-review').addEvent('click', function(){
				$('ynmultilisting-full-review').addClass('ynmultilisting-full-review-active');
				$('ynmultilisting-general-review').removeClass('ynmultilisting-general-review-active');

				$('ynmultilisting-full-review-block').setStyle('display', 'block');
				$('ynmultilisting-general-review-block').setStyle('display', 'none');
				setCookie('ynmultilisting_profile_review_mode_view', 'full', 1);
			});
			var mode_view = getCookie('ynmultilisting_profile_review_mode_view');
			if(mode_view == "")
			{
				$('ynmultilisting-general-review').click();
			}
			if(mode_view == "general")
			{
				$('ynmultilisting-general-review').click();
			}
			else
			{
				$('ynmultilisting-full-review').click();
			}
		<?php endif;?>
	});
</script>

</div>

<?php if(count($this->listings) > 0):?>
	<?php 
	$listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
	?>
	<ul class="generic_list_widget listing_browse listing_browse_view_content clearfix">
		<?php foreach( $this->listings as $listing ): ?>
			<li>
				<div class="list-view ynmultilisting-list-item ynmultilisting-list-item-mode-<?php echo $listingtype->list_view?>">
					<div class="listing_photo">
					    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>
						<div class="listing_photo_main" style="background-image: url(<?php echo $photo_url; ?>);">

							<?php if ($listing->isNew()) : ?>
								<div class="newListing"></div>
							<?php endif; ?>

							<?php if ($listing->featured) : ?>
								<div class="featureListing"></div>
							<?php endif; ?>
							<?php if(!(($listing -> longitude == 0) && ($listing -> latitude == 0))): ?>
								<?php echo $this->htmlLink(array('route' => 'ynmultilisting_specific', 'action' => 'direction', 'listing_id' => $listing->getIdentity()), $this->translate('Get Direction'), array('class' => 'smoothbox get_direction')); ?>
							<?php endif;?>
							<div class="listing_photo_hover">
								<div class="listing_view_more"> 
									<?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
								</div>
							</div>
						</div>						
					</div>

					<div class="listing_info">
						<div class="listing_title ynmultilisting_listing_title <?php if ($listing->featured) echo "listing_title_feature" ?>">
							<?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
						</div>						

						<div class="listing_price">
							<?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
						</div>

						<div class="listing_location full">
							<?php if ($listing->location): ?>										
								<span class="fa fa-map-marker"></span>
								<?php echo $listing->location;?>
							<?php endif; ?>									
						</div>

						<div class="short_description">
							<?php echo strip_tags($listing->short_description)?>
						</div>

						<div class="listing_info_footer">
							<div class="author-avatar">
								<?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?>
							</div>	
							<div class="listing_info_footer_main">
								<div>
									<div class="listing_creation">							
										<span class=""><?php echo $this->translate('by ')?></span>
										<span><?php echo $listing->getOwner()?></span>
									</div>

									<div class="category">										
									<?php $category = $listing->getCategory()?>
									<?php if ($category) : ?>   
										<span class="fa fa-folder-open"></span>
										<span><?php echo ' '.$this->htmlLink($category->getHref(), $category->getTitle())?></span>
									<?php endif; ?>
									</div>
								</div>

								<div>
									<div class="listing_rating">
										<span><?php 
											echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing));
											?>
										</span>
										
										<a href="<?php echo $listing -> getHref();?>">
											<b>&nbsp;<?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $listing -> review_count), $listing -> review_count); ?></b>
										</a>
									</div>

									<div class="listing_location mobile">
									<?php if ($listing->location): ?>										
										<span class="fa fa-map-marker"></span>
										<?php echo $listing->location;?>
									<?php endif; ?>									
									</div>
								</div>
							</div>
							
							<div class="ynmultilisting_buttons">
								<h6><?php echo $this->translate('more details') ?></h6>
			                <!-- buttons -->
			                	<span class="ynmultilisting_buttons_action"><i class="fa fa-chevron-down"></i></span>
			                    <!-- rate button -->
			                    <div class="ynmultilisting_buttons_showoptions">
			                    	
			                    
			                    <?php
			                    $canReview = $listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'review');
			                    $viewer = $this -> viewer();
			                    if($canReview) :?>
			                    <span>
			                        <?php $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');?>
			                        <?php $review = $tableReview -> checkHasReviewed($listing -> getIdentity(), $viewer -> getIdentity(), true);?>
			                        <?php if($review) :?>
			                        <?php echo $this->htmlLink(
			                                array(
			                                    'route' => 'ynmultilisting_review',
			                                    'action' => 'edit',
			                                    'id' => $review->getIdentity(),
			                                ), '<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'), array(
			                                    'class' => 'smoothbox'
			                                )
			                            )?>
			                        <?php else :?>
			                        <?php
			                                echo $this->htmlLink(
			                                    array(
			                                        'route' => 'ynmultilisting_review',
			                                        'action' => 'create',
			                                        'id' => $listing->getIdentity(),
			                                    ),'<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'),
			                                    array(
			                                        'class' => 'smoothbox'
			                                ));
			                            ?>
			                        <?php endif;?>
			                    </span>
			                    <?php endif;?>

			                    <!--end rate button -->
			
			                    <!-- like button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span class="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>">
			                            <?php if( $listing->likes()->isLike($this->viewer()) ): ?>
			                                <a  href="javascript:void(0);" onclick="unlike('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart"></i><?php echo $this -> translate('Liked');?></a>
			                        <?php else: ?>
			                        <a  href="javascript:void(0);" onclick="like('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart-o"></i><?php echo $this -> translate('Like');?></a>
			                        <?php endif; ?>
			                    </span>
			                    <?php endif;?>
			                    <!--end like button -->
			
			                    <!-- comment button -->
			                    <span><a href="<?php echo $listing -> getHref();?>"><i class="fa fa-comment"></i><?php echo $this -> translate('Comment');?></a></span>
			                    <!--end comment button -->
			
			                    <!-- share button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span>
			                        <a title="<?php echo $this -> translate('Share');?>" class="smoothbox" href="<?php echo $this -> url(array(
			                            'module' => 'activity',
			                            'controller' => 'index',
			                            'action' => 'share',
			                            'type' => $listing->getType(),
			                            'id' => $listing->getIdentity(),
			                            'format' => 'smoothbox',
			                        ), 'default', true);?>">
			                            <i class="fa fa-share-square-o"></i><?php echo $this -> translate('Share');?></a>
			                    </span>
			                    <?php endif;?>
			                    <!--end share button -->
			
			                    <!-- follow button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <?php $tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynmultilisting');?>
			                    <?php $rowFollow = $tableFollow -> getRow($viewer -> getIdentity(), $listing->user_id, true);?>
			                    <span class="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>">
			                        <?php if($rowFollow) :?>
			                        <a  href="javascript:void(0);" onclick="unfollow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Followed Seller');?></a>
			                        <?php else: ?>
			                        <a  href="javascript:void(0);" onclick="follow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Follow Seller');?></a>
			                        <?php endif; ?>
			                    </span>
			                    <?php endif;?>
			                    <!-- end follow button -->
			
			                    <!-- mail seller button -->
			                    <?php if (!$this->viewer()->isSelf($listing -> getOwner())):?>
			                    <span>
			                        <a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose', 'to' => $listing -> getOwner() -> getIdentity()), 'messages_general', true);?>"><i class="fa fa-envelope"></i> <?php echo $this -> translate('Mail to Seller');?></a>
			                    </span>
			                    <?php endif;?>
			                    <!-- mail seller button -->
			
			                    <!-- message to friend button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span>
			                        <?php echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_specific',
			                                'action' => 'email-to-friends',
			                                'listing_id' => $listing->getIdentity()
			                            ),
			                            '<i class="fa fa-envelope-o"></i>'.$this->translate('Email to Friends'),
			                            array(
			                                'class' => 'smoothbox'
			                            )
			                        )?>
			                    </span>
			                    <?php endif;?>
			                    <!-- message to friend button -->
			
			                    <!-- print button -->
			                    <span>
			                        <?php
			                        echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_specific',
			                                'action' => 'print',
			                                'listing_id' => $listing->getIdentity()
			                            ),
			                            '<i class="fa fa-print"></i>'.$this->translate('Print Listing'),
			                            array())
			                        ?>
			                    </span>
			                    <!-- end print button -->
			
			                    <!-- get direction -->
			                    <span>
			                        <?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_specific', 'action' => 'direction', 'listing_id' => $listing->getIdentity()),
			                        '<i class="fa fa-location-arrow"></i>'.$this->translate('Get Direction'),
			                        array('class' => 'smoothbox')) ?>
			                    </span>
			                    <!-- get direction -->
			
			                    <!-- HOANGND add to compare-->
			                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
			                    	<span>
			                        <?php if ($listing->inCompare()) : ?>
			                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $listing -> getIdentity();?>)">
			                                <i class="fa fa-exchange"></i><?php echo $this->translate('Remove from Compare')?>
			                            </a>
			                        <?php else: ?>
			                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $listing -> getIdentity();?>)">
			                                <i class="fa fa-exchange"></i><?php echo $this->translate('Add to Compare')?>
			                            </a>
			                        <?php endif; ?>
			                        </span>
			                    <?php endif;?>
			                    <!-- add to compare-->
			
			                    <!-- HOANGND add to wishlist-->
			                    <?php if($viewer->getIdentity()) :?>
			                    	<span>
			                        	<?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $listing->getIdentity()),
			                        '<i class="fa fa-bookmark"></i>'.$this->translate('Add to Wish List'),
			                        array('class' => 'smoothbox')) ?>
			                        </span>
			                    <?php endif;?>
			                    <!-- add to wishlist-->
			                    
			                    <!-- HOANGND remove from this wishlist-->
			                    <?php if (isset($this->wishlist) && $listing->inWishlist($this->wishlist->getIdentity()) && $this->wishlist->isOwner($this->viewer())) :?>
			                    	<span>
									<?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_wishlist', 'action' => 'remove-listing', 'listing_id' => $listing->getIdentity(), 'id' => $this->wishlist->getIdentity()),
			                        '<i class="fa fa-bookmark-o"></i>'.$this->translate('Remove from this Wish List'),
			                        array('class' => 'smoothbox')) ?>
			                        </span>
								<?php endif; ?>
								<!-- remove from this wishlist-->
								
			                    </div>
			                <!-- end buttons -->
			                </div>
						</div>
					</div>
				</div>

				<div class="grid-view grid-view-mode-<?php echo $listingtype->grid_view?>">
					<div class="ynmultilisting-grid-item ynmultilisting-grid-item-mode-1">
						
						<div class="item-front-info">
							<div class="listing_title <?php if ($listing->featured) echo 'listing_title_feature' ?>">
								<?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
							</div>    
							
							<div class="ynmultilisting-item-rating">
								<?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing)); ?>

								<a href="<?php echo $listing -> getHref();?>">
									<b>&nbsp;<?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $listing -> review_count), $listing -> review_count); ?></b>
								</a>
							</div>

						</div>

						<div class="ynmultilisting-grid-item-content">
						    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>
                            <div class="item-background" style="background-image: url(<?php echo $photo_url; ?>);">

								<?php if ($listing->featured) : ?>
									<div class="featureListing"></div>
								<?php endif; ?>

								<?php if ($listing->isNew()) : ?>
									<div class="newListing"></div>
								<?php endif; ?>
							</div>

							
							<div class="ynmultilisting-grid-item-hover">
								<div class="ynmultilisting-grid-item-hover-background">
									<div class="listing_view_more"> 
										<?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
									</div>

									<div class="short_description">
										<?php echo strip_tags($listing->short_description)?>
									</div>

									<div class="listing_creation">
										<span class="author-avatar"><?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?></span>
										<span><?php echo $this->translate('by ')?></span>
										<span class="author-title"><?php echo $listing->getOwner()?></span>
									</div>
								</div>
							</div>

						</div>
						
						<div class="listing_price-buttons">	
							
							<div>
								<div class="listing_price">
									<?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
								</div>

								<?php if($listing->location): ?>
									<div class="listing_location">
										<i class="fa fa-map-marker"></i> &nbsp;
										<?php echo $listing->location ?>
									</div>	
								<?php  endif; ?>
							</div>
							
							<div class="ynmultilisting_buttons">
			                <!-- buttons -->
			                	<span class="ynmultilisting_buttons_action"><i class="fa fa-chevron-down"></i></span>
			                    <!-- rate button -->
			                    <div class="ynmultilisting_buttons_showoptions">
			                    	
			                    
			                    <?php
			                    $canReview = $listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'review');
			                    $viewer = $this -> viewer();
			                    if($canReview) :?>
			                    <span>
			                        <?php $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');?>
			                        <?php $review = $tableReview -> checkHasReviewed($listing -> getIdentity(), $viewer -> getIdentity(), true);?>
			                        <?php if($review) :?>
			                        <?php echo $this->htmlLink(
			                                array(
			                                    'route' => 'ynmultilisting_review',
			                                    'action' => 'edit',
			                                    'id' => $review->getIdentity(),
			                                ), '<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'), array(
			                                    'class' => 'smoothbox'
			                                )
			                            )?>
			                        <?php else :?>
			                        <?php
			                                echo $this->htmlLink(
			                                    array(
			                                        'route' => 'ynmultilisting_review',
			                                        'action' => 'create',
			                                        'id' => $listing->getIdentity(),
			                                    ),'<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'),
			                                    array(
			                                        'class' => 'smoothbox'
			                                ));
			                            ?>
			                        <?php endif;?>
			                    </span>
			                    <?php endif;?>

			                    <!--end rate button -->
			
			                    <!-- like button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span class="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>">
			                            <?php if( $listing->likes()->isLike($this->viewer()) ): ?>
			                                <a  href="javascript:void(0);" onclick="unlike('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart"></i><?php echo $this -> translate('Liked');?></a>
			                        <?php else: ?>
			                        <a  href="javascript:void(0);" onclick="like('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart-o"></i><?php echo $this -> translate('Like');?></a>
			                        <?php endif; ?>
			                    </span>
			                    <?php endif;?>
			                    <!--end like button -->
			
			                    <!-- comment button -->
			                    <span><a href="<?php echo $listing -> getHref();?>"><i class="fa fa-comment"></i><?php echo $this -> translate('Comment');?></a></span>
			                    <!--end comment button -->
			
			                    <!-- share button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span>
			                        <a title="<?php echo $this -> translate('Share');?>" class="smoothbox" href="<?php echo $this -> url(array(
			                            'module' => 'activity',
			                            'controller' => 'index',
			                            'action' => 'share',
			                            'type' => $listing->getType(),
			                            'id' => $listing->getIdentity(),
			                            'format' => 'smoothbox',
			                        ), 'default', true);?>">
			                            <i class="fa fa-share-square-o"></i><?php echo $this -> translate('Share');?></a>
			                    </span>
			                    <?php endif;?>
			                    <!--end share button -->
			
			                    <!-- follow button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <?php $tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynmultilisting');?>
			                    <?php $rowFollow = $tableFollow -> getRow($viewer -> getIdentity(), $listing->user_id, true);?>
			                    <span class="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>">
			                        <?php if($rowFollow) :?>
			                        <a  href="javascript:void(0);" onclick="unfollow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Followed Seller');?></a>
			                        <?php else: ?>
			                        <a  href="javascript:void(0);" onclick="follow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Follow Seller');?></a>
			                        <?php endif; ?>
			                    </span>
			                    <?php endif;?>
			                    <!-- end follow button -->
			
			                    <!-- mail seller button -->
			                    <?php if (!$this->viewer()->isSelf($listing -> getOwner())):?>
			                    <span>
			                        <a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose', 'to' => $listing -> getOwner() -> getIdentity()), 'messages_general', true);?>"><i class="fa fa-envelope"></i> <?php echo $this -> translate('Mail to Seller');?></a>
			                    </span>
			                    <?php endif;?>
			                    <!-- mail seller button -->
			
			                    <!-- message to friend button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span>
			                        <?php echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_specific',
			                                'action' => 'email-to-friends',
			                                'listing_id' => $listing->getIdentity()
			                            ),
			                            '<i class="fa fa-envelope-o"></i>'.$this->translate('Email to Friends'),
			                            array(
			                                'class' => 'smoothbox'
			                            )
			                        )?>
			                    </span>
			                    <?php endif;?>
			                    <!-- message to friend button -->
			
			                    <!-- print button -->
			                    <span>
			                        <?php
			                        echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_specific',
			                                'action' => 'print',
			                                'listing_id' => $listing->getIdentity()
			                            ),
			                            '<i class="fa fa-print"></i>'.$this->translate('Print Listing'),
			                            array())
			                        ?>
			                    </span>
			                    <!-- end print button -->
			
			                    <!-- get direction -->
			                    <span>
			                        <?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_specific', 'action' => 'direction', 'listing_id' => $listing->getIdentity()),
			                        '<i class="fa fa-location-arrow"></i>'.$this->translate('Get Direction'),
			                        array('class' => 'smoothbox')) ?>
			                    </span>
			                    <!-- get direction -->
			
			                    <!-- HOANGND add to compare-->
			                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
			                    	<span>
			                        <?php if ($listing->inCompare()) : ?>
			                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $listing -> getIdentity();?>)">
			                                <i class="fa fa-exchange"></i><?php echo $this->translate('Remove from Compare')?>
			                            </a>
			                        <?php else: ?>
			                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $listing -> getIdentity();?>)">
			                                <i class="fa fa-exchange"></i><?php echo $this->translate('Add to Compare')?>
			                            </a>
			                        <?php endif; ?>
			                        </span>
			                    <?php endif;?>
			                    <!-- add to compare-->
			
			                    <!-- HOANGND add to wishlist-->
			                    <?php if($viewer->getIdentity()) :?>
			                    	<span>
			                        	<?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $listing->getIdentity()),
			                        '<i class="fa fa-bookmark"></i>'.$this->translate('Add to Wish List'),
			                        array('class' => 'smoothbox')) ?>
			                        </span>
			                    <?php endif;?>
			                    <!-- add to wishlist-->
			                    
			                    <!-- HOANGND remove from this wishlist-->
			                    <?php if (isset($this->wishlist) && $listing->inWishlist($this->wishlist->getIdentity()) && $this->wishlist->isOwner($this->viewer())) :?>
			                    	<span>
									<?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_wishlist', 'action' => 'remove-listing', 'listing_id' => $listing->getIdentity(), 'id' => $this->wishlist->getIdentity()),
			                        '<i class="fa fa-bookmark-o"></i>'.$this->translate('Remove from this Wish List'),
			                        array('class' => 'smoothbox')) ?>
			                        </span>
								<?php endif; ?>
								<!-- remove from this wishlist-->
								
		
			                    </div>
			                <!-- end buttons -->
			                </div>

		                </div>

					</div>  


					<div class="ynmultilisting-grid-item ynmultilisting-grid-item-mode-2">
						<div class="ynmultilisting-grid-item-content">
						    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>
                            <div class="item-background" style="background-image: url(<?php echo $photo_url; ?>);">

								<?php if ($listing->featured) : ?>
									<div class="featureListing"></div>
								<?php endif; ?>

								<?php if ($listing->isNew()) : ?>
									<div class="newListing"></div>
								<?php endif; ?>

								
								<div class="ynmultilisting-grid-item-hover">
									<div class="ynmultilisting-grid-item-hover-background">
										<div class="listing_view_more"> 
											<?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
										</div>

										<div class="short_description">
											<?php echo strip_tags($listing->short_description)?>
										</div>

										<div class="listing_creation">
											<span class="author-avatar"><?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?></span>
											<span><?php echo $this->translate('by ')?></span>
											<span class="author-title"><?php echo $listing->getOwner()?></span>
										</div>
									</div>
								</div>
								
								<div class="ynmultilisting-item-rating-buttons">
									<div class="ynmultilisting-item-rating">
										<?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing)); ?>

										<a href="<?php echo $listing -> getHref();?>">
											<b>&nbsp;<?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $listing -> review_count), $listing -> review_count); ?></b>
										</a>
									</div>

									<div class="ynmultilisting_buttons">
					                <!-- buttons -->
					                	<span class="ynmultilisting_buttons_action"><i class="fa fa-chevron-down"></i></span>
					                    <!-- rate button -->
					                    <div class="ynmultilisting_buttons_showoptions">
					                    	
					                    
					                    <?php
					                    $canReview = $listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'review');
					                    $viewer = $this -> viewer();
					                    if($canReview) :?>
					                    <span>
					                        <?php $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');?>
					                        <?php $review = $tableReview -> checkHasReviewed($listing -> getIdentity(), $viewer -> getIdentity(), true);?>
					                        <?php if($review) :?>
					                        <?php echo $this->htmlLink(
					                                array(
					                                    'route' => 'ynmultilisting_review',
					                                    'action' => 'edit',
					                                    'id' => $review->getIdentity(),
					                                ), '<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'), array(
					                                    'class' => 'smoothbox'
					                                )
					                            )?>
					                        <?php else :?>
					                        <?php
					                                echo $this->htmlLink(
					                                    array(
					                                        'route' => 'ynmultilisting_review',
					                                        'action' => 'create',
					                                        'id' => $listing->getIdentity(),
					                                    ),'<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'),
					                                    array(
					                                        'class' => 'smoothbox'
					                                ));
					                            ?>
					                        <?php endif;?>
					                    </span>
					                    <?php endif;?>

					                    <!--end rate button -->
					
					                    <!-- like button -->
					                    <?php if ($this->viewer()->getIdentity()):?>
					                    <span class="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>">
					                            <?php if( $listing->likes()->isLike($this->viewer()) ): ?>
					                                <a  href="javascript:void(0);" onclick="unlike('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart"></i><?php echo $this -> translate('Liked');?></a>
					                        <?php else: ?>
					                        <a  href="javascript:void(0);" onclick="like('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart-o"></i><?php echo $this -> translate('Like');?></a>
					                        <?php endif; ?>
					                    </span>
					                    <?php endif;?>
					                    <!--end like button -->
					
					                    <!-- comment button -->
					                    <span><a href="<?php echo $listing -> getHref();?>"><i class="fa fa-comment"></i><?php echo $this -> translate('Comment');?></a></span>
					                    <!--end comment button -->
					
					                    <!-- share button -->
					                    <?php if ($this->viewer()->getIdentity()):?>
					                    <span>
					                        <a title="<?php echo $this -> translate('Share');?>" class="smoothbox" href="<?php echo $this -> url(array(
					                            'module' => 'activity',
					                            'controller' => 'index',
					                            'action' => 'share',
					                            'type' => $listing->getType(),
					                            'id' => $listing->getIdentity(),
					                            'format' => 'smoothbox',
					                        ), 'default', true);?>">
					                            <i class="fa fa-share-square-o"></i><?php echo $this -> translate('Share');?></a>
					                    </span>
					                    <?php endif;?>
					                    <!--end share button -->
					
					                    <!-- follow button -->
					                    <?php if ($this->viewer()->getIdentity()):?>
					                    <?php $tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynmultilisting');?>
					                    <?php $rowFollow = $tableFollow -> getRow($viewer -> getIdentity(), $listing->user_id, true);?>
					                    <span class="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>">
					                        <?php if($rowFollow) :?>
					                        <a  href="javascript:void(0);" onclick="unfollow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Followed Seller');?></a>
					                        <?php else: ?>
					                        <a  href="javascript:void(0);" onclick="follow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Follow Seller');?></a>
					                        <?php endif; ?>
					                    </span>
					                    <?php endif;?>
					                    <!-- end follow button -->
					
					                    <!-- mail seller button -->
					                    <?php if (!$this->viewer()->isSelf($listing -> getOwner())):?>
					                    <span>
					                        <a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose', 'to' => $listing -> getOwner() -> getIdentity()), 'messages_general', true);?>"><i class="fa fa-envelope"></i> <?php echo $this -> translate('Mail to Seller');?></a>
					                    </span>
					                    <?php endif;?>
					                    <!-- mail seller button -->
					
					                    <!-- message to friend button -->
					                    <?php if ($this->viewer()->getIdentity()):?>
					                    <span>
					                        <?php echo $this->htmlLink(
					                            array(
					                                'route' => 'ynmultilisting_specific',
					                                'action' => 'email-to-friends',
					                                'listing_id' => $listing->getIdentity()
					                            ),
					                            '<i class="fa fa-envelope-o"></i>'.$this->translate('Email to Friends'),
					                            array(
					                                'class' => 'smoothbox'
					                            )
					                        )?>
					                    </span>
					                    <?php endif;?>
					                    <!-- message to friend button -->
					
					                    <!-- print button -->
					                    <span>
					                        <?php
					                        echo $this->htmlLink(
					                            array(
					                                'route' => 'ynmultilisting_specific',
					                                'action' => 'print',
					                                'listing_id' => $listing->getIdentity()
					                            ),
					                            '<i class="fa fa-print"></i>'.$this->translate('Print Listing'),
					                            array())
					                        ?>
					                    </span>
					                    <!-- end print button -->
					
					                    <!-- get direction -->
					                    <span>
					                        <?php echo $this->htmlLink(
					                        array('route' => 'ynmultilisting_specific', 'action' => 'direction', 'listing_id' => $listing->getIdentity()),
					                        '<i class="fa fa-location-arrow"></i>'.$this->translate('Get Direction'),
					                        array('class' => 'smoothbox')) ?>
					                    </span>
					                    <!-- get direction -->
					
					                    <!-- HOANGND add to compare-->
					                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
					                    	<span>
					                        <?php if ($listing->inCompare()) : ?>
					                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $listing -> getIdentity();?>)">
					                                <i class="fa fa-exchange"></i><?php echo $this->translate('Remove from Compare')?>
					                            </a>
					                        <?php else: ?>
					                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $listing -> getIdentity();?>)">
					                                <i class="fa fa-exchange"></i><?php echo $this->translate('Add to Compare')?>
					                            </a>
					                        <?php endif; ?>
					                        </span>
					                    <?php endif;?>
					                    <!-- add to compare-->
					
					                    <!-- HOANGND add to wishlist-->
					                    <?php if($viewer->getIdentity()) :?>
					                    	<span>
					                        	<?php echo $this->htmlLink(
					                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $listing->getIdentity()),
					                        '<i class="fa fa-bookmark"></i>'.$this->translate('Add to Wish List'),
					                        array('class' => 'smoothbox')) ?>
					                        </span>
					                    <?php endif;?>
					                    <!-- add to wishlist-->
					                    
					                    <!-- HOANGND remove from this wishlist-->
					                    <?php if (isset($this->wishlist) && $listing->inWishlist($this->wishlist->getIdentity()) && $this->wishlist->isOwner($this->viewer())) :?>
					                    	<span>
											<?php echo $this->htmlLink(
					                        array('route' => 'ynmultilisting_wishlist', 'action' => 'remove-listing', 'listing_id' => $listing->getIdentity(), 'id' => $this->wishlist->getIdentity()),
					                        '<i class="fa fa-bookmark-o"></i>'.$this->translate('Remove from  this Wish List'),
					                        array('class' => 'smoothbox')) ?>
					                        </span>
										<?php endif; ?>
										<!-- remove from this wishlist-->
										
				
					                    </div>
					                <!-- end buttons -->
					                </div>
				                </div>
							</div>
							<div class="item-front-info">
								<div class="listing_title <?php if ($listing->featured) echo 'listing_title_feature' ?>">
									<?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
								</div>    

								<div class="listing_price">
									<?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
								</div>
							</div>

						</div>
					</div>  
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	
	<ul class="generic_list_widget listing_browse listing_pin_view_content clearfix listing_pin_view_content-mode-<?php echo $listingtype->pin_view?>">
		<?php foreach( $this->listings as $listing ): ?>
			<li>				
				<div class="pin-view pin-view-mode-1">
											
						<div class="item-front-info">
							<div class="listing_title <?php if ($listing->featured) echo 'listing_title_feature' ?>">
								<?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
							</div>    
							
							<div class="ynmultilisting-item-rating">
								<?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing)); ?>
								
								<a href="<?php echo $listing -> getHref();?>">
									<b>&nbsp;<?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $listing -> review_count), $listing -> review_count); ?></b>
								</a>
							</div>

						</div>

						<div class="ynmultilisting-pin-item-content">
						    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>
                            <div class="item-background">
								<img src="<?php echo $photo_url ?>" alt="">
								<?php if ($listing->featured) : ?>
									<div class="featureListing"></div>
								<?php endif; ?>

								<?php if ($listing->isNew()) : ?>
									<div class="newListing"></div>
								<?php endif; ?>
							</div>

							
							<div class="ynmultilisting-pin-item-hover">
								<div class="ynmultilisting-pin-item-hover-background">
									<div class="listing_view_more"> 
										<?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
									</div>


									<div class="listing_creation">
										<span class="author-avatar"><?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?></span>
										<span><?php echo $this->translate('by ')?></span>
										<span class="author-title"><?php echo $listing->getOwner()?></span>
									</div>
								</div>
							</div>

						</div>
						
						<div class="listing_price-buttons">	
							
							<div>
								<div class="listing_price">
									<?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
								</div>

								<?php if($listing->location): ?>
									<div class="listing_location">
										<i class="fa fa-map-marker"></i> &nbsp;
										<?php echo $listing->location ?>
									</div>	
								<?php  endif; ?>
							</div>
							
							<div class="ynmultilisting_buttons">
			                <!-- buttons -->
			                	<span class="ynmultilisting_buttons_action"><i class="fa fa-chevron-down"></i></span>
			                    <!-- rate button -->
			                    <div class="ynmultilisting_buttons_showoptions">
			                    	
			                    
			                    <?php
			                    $canReview = $listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'review');
			                    $viewer = $this -> viewer();
			                    if($canReview) :?>
			                    <span>
			                        <?php $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');?>
			                        <?php $review = $tableReview -> checkHasReviewed($listing -> getIdentity(), $viewer -> getIdentity(), true);?>
			                        <?php if($review) :?>
			                        <?php echo $this->htmlLink(
			                                array(
			                                    'route' => 'ynmultilisting_review',
			                                    'action' => 'edit',
			                                    'id' => $review->getIdentity(),
			                                ), '<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'), array(
			                                    'class' => 'smoothbox'
			                                )
			                            )?>
			                        <?php else :?>
			                        <?php
			                                echo $this->htmlLink(
			                                    array(
			                                        'route' => 'ynmultilisting_review',
			                                        'action' => 'create',
			                                        'id' => $listing->getIdentity(),
			                                    ),'<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'),
			                                    array(
			                                        'class' => 'smoothbox'
			                                ));
			                            ?>
			                        <?php endif;?>
			                    </span>
			                    <?php endif;?>

			                    <!--end rate button -->
			
			                    <!-- like button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span class="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>">
			                            <?php if( $listing->likes()->isLike($this->viewer()) ): ?>
			                                <a  href="javascript:void(0);" onclick="unlike('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart"></i><?php echo $this -> translate('Liked');?></a>
			                        <?php else: ?>
			                        <a  href="javascript:void(0);" onclick="like('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart-o"></i><?php echo $this -> translate('Like');?></a>
			                        <?php endif; ?>
			                    </span>
			                    <?php endif;?>
			                    <!--end like button -->
			
			                    <!-- comment button -->
			                    <span><a href="<?php echo $listing -> getHref();?>"><i class="fa fa-comment"></i><?php echo $this -> translate('Comment');?></a></span>
			                    <!--end comment button -->
			
			                    <!-- share button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span>
			                        <a title="<?php echo $this -> translate('Share');?>" class="smoothbox" href="<?php echo $this -> url(array(
			                            'module' => 'activity',
			                            'controller' => 'index',
			                            'action' => 'share',
			                            'type' => $listing->getType(),
			                            'id' => $listing->getIdentity(),
			                            'format' => 'smoothbox',
			                        ), 'default', true);?>">
			                            <i class="fa fa-share-square-o"></i><?php echo $this -> translate('Share');?></a>
			                    </span>
			                    <?php endif;?>
			                    <!--end share button -->
			
			                    <!-- follow button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <?php $tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynmultilisting');?>
			                    <?php $rowFollow = $tableFollow -> getRow($viewer -> getIdentity(), $listing->user_id, true);?>
			                    <span class="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>">
			                        <?php if($rowFollow) :?>
			                        <a  href="javascript:void(0);" onclick="unfollow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Followed Seller');?></a>
			                        <?php else: ?>
			                        <a  href="javascript:void(0);" onclick="follow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Follow Seller');?></a>
			                        <?php endif; ?>
			                    </span>
			                    <?php endif;?>
			                    <!-- end follow button -->
			
			                    <!-- mail seller button -->
			                    <?php if (!$this->viewer()->isSelf($listing -> getOwner())):?>
			                    <span>
			                        <a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose', 'to' => $listing -> getOwner() -> getIdentity()), 'messages_general', true);?>"><i class="fa fa-envelope"></i> <?php echo $this -> translate('Mail to Seller');?></a>
			                    </span>
			                    <?php endif;?>
			                    <!-- mail seller button -->
			
			                    <!-- message to friend button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span>
			                        <?php echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_specific',
			                                'action' => 'email-to-friends',
			                                'listing_id' => $listing->getIdentity()
			                            ),
			                            '<i class="fa fa-envelope-o"></i>'.$this->translate('Email to Friends'),
			                            array(
			                                'class' => 'smoothbox'
			                            )
			                        )?>
			                    </span>
			                    <?php endif;?>
			                    <!-- message to friend button -->
			
			                    <!-- print button -->
			                    <span>
			                        <?php
			                        echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_specific',
			                                'action' => 'print',
			                                'listing_id' => $listing->getIdentity()
			                            ),
			                            '<i class="fa fa-print"></i>'.$this->translate('Print Listing'),
			                            array())
			                        ?>
			                    </span>
			                    <!-- end print button -->
			
			                    <!-- get direction -->
			                    <span>
			                        <?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_specific', 'action' => 'direction', 'listing_id' => $listing->getIdentity()),
			                        '<i class="fa fa-location-arrow"></i>'.$this->translate('Get Direction'),
			                        array('class' => 'smoothbox')) ?>
			                    </span>
			                    <!-- get direction -->
			
			                    <!-- HOANGND add to compare-->
			                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
			                    	<span>
			                        <?php if ($listing->inCompare()) : ?>
			                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $listing -> getIdentity();?>)">
			                                <i class="fa fa-exchange"></i><?php echo $this->translate('Remove from Compare')?>
			                            </a>
			                        <?php else: ?>
			                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $listing -> getIdentity();?>)">
			                                <i class="fa fa-exchange"></i><?php echo $this->translate('Add to Compare')?>
			                            </a>
			                        <?php endif; ?>
			                        </span>
			                    <?php endif;?>
			                    <!-- add to compare-->
			
			                    <!-- HOANGND add to wishlist-->
			                    <?php if($viewer->getIdentity()) :?>
			                    	<span>
			                        	<?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $listing->getIdentity()),
			                        '<i class="fa fa-bookmark"></i>'.$this->translate('Add to Wish List'),
			                        array('class' => 'smoothbox')) ?>
			                        </span>
			                    <?php endif;?>
			                    <!-- add to wishlist-->
			                    
			                    <!-- HOANGND remove from this wishlist-->
			                    <?php if (isset($this->wishlist) && $listing->inWishlist($this->wishlist->getIdentity()) && $this->wishlist->isOwner($this->viewer())) :?>
			                    	<span>
									<?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_wishlist', 'action' => 'remove-listing', 'listing_id' => $listing->getIdentity(), 'id' => $this->wishlist->getIdentity()),
			                        '<i class="fa fa-bookmark-o"></i>'.$this->translate('Remove from  this Wish List'),
			                        array('class' => 'smoothbox')) ?>
			                        </span>
								<?php endif; ?>
								<!-- remove from this wishlist-->
								
		
			                    </div>
			                <!-- end buttons -->
			                </div>

		                </div>

				</div>

				<div class="pin-view pin-view-mode-2">			
					<div class="highlight_listing">
						<div class="listing_title">
							<?php echo $this->htmlLink($listing->getHref(), $listing->title); ?>
						</div>

						<div class="listing_photo">
							<?php echo $this->htmlLink($listing->getHref(), $this->itemPhoto($listing, 'thumb.profile')); ?>

							<div class="prices">
								<?php echo $this -> locale()->toCurrency($listing->price, $listing->currency); ?>
							</div>

							<div class="listing_owner">
								<span>
									<?php echo $this->translate('by').' '?>
									<?php echo $listing->getOwner()?>
								</span>

								
							<div class="ynmultilisting_buttons">
			                <!-- buttons -->
			                	<span class="ynmultilisting_buttons_action"><i class="fa fa-chevron-down"></i></span>
			                    <!-- rate button -->
			                    <div class="ynmultilisting_buttons_showoptions">
			                    	
			                    
			                    <?php
			                    $canReview = $listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'review');
			                    $viewer = $this -> viewer();
			                    if($canReview) :?>
			                    <span>
			                        <?php $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');?>
			                        <?php $review = $tableReview -> checkHasReviewed($listing -> getIdentity(), $viewer -> getIdentity(), true);?>
			                        <?php if($review) :?>
			                        <?php echo $this->htmlLink(
			                                array(
			                                    'route' => 'ynmultilisting_review',
			                                    'action' => 'edit',
			                                    'id' => $review->getIdentity(),
			                                ), '<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'), array(
			                                    'class' => 'smoothbox'
			                                )
			                            )?>
			                        <?php else :?>
			                        <?php
			                                echo $this->htmlLink(
			                                    array(
			                                        'route' => 'ynmultilisting_review',
			                                        'action' => 'create',
			                                        'id' => $listing->getIdentity(),
			                                    ),'<i class="fa fa-star-half-o"></i> '.$this->translate('Rate'),
			                                    array(
			                                        'class' => 'smoothbox'
			                                ));
			                            ?>
			                        <?php endif;?>
			                    </span>
			                    <?php endif;?>

			                    <!--end rate button -->
			
			                    <!-- like button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span class="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-like-button-<?php echo $listing -> getIdentity();?>">
			                            <?php if( $listing->likes()->isLike($this->viewer()) ): ?>
			                                <a  href="javascript:void(0);" onclick="unlike('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart"></i><?php echo $this -> translate('Liked');?></a>
			                        <?php else: ?>
			                        <a  href="javascript:void(0);" onclick="like('<?php echo $listing->getType()?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-heart-o"></i><?php echo $this -> translate('Like');?></a>
			                        <?php endif; ?>
			                    </span>
			                    <?php endif;?>
			                    <!--end like button -->
			
			                    <!-- comment button -->
			                    <span><a href="<?php echo $listing -> getHref();?>"><i class="fa fa-comment"></i><?php echo $this -> translate('Comment');?></a></span>
			                    <!--end comment button -->
			
			                    <!-- share button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span>
			                        <a title="<?php echo $this -> translate('Share');?>" class="smoothbox" href="<?php echo $this -> url(array(
			                            'module' => 'activity',
			                            'controller' => 'index',
			                            'action' => 'share',
			                            'type' => $listing->getType(),
			                            'id' => $listing->getIdentity(),
			                            'format' => 'smoothbox',
			                        ), 'default', true);?>">
			                            <i class="fa fa-share-square-o"></i><?php echo $this -> translate('Share');?></a>
			                    </span>
			                    <?php endif;?>
			                    <!--end share button -->
			
			                    <!-- follow button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <?php $tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynmultilisting');?>
			                    <?php $rowFollow = $tableFollow -> getRow($viewer -> getIdentity(), $listing->user_id, true);?>
			                    <span class="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>" id="ynmultilisting-follow-button-<?php echo $listing -> getIdentity();?>">
			                        <?php if($rowFollow) :?>
			                        <a  href="javascript:void(0);" onclick="unfollow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Followed Seller');?></a>
			                        <?php else: ?>
			                        <a  href="javascript:void(0);" onclick="follow('<?php echo $listing->user_id?>', '<?php echo $listing->getIdentity() ?>')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Follow Seller');?></a>
			                        <?php endif; ?>
			                    </span>
			                    <?php endif;?>
			                    <!-- end follow button -->
			
			                    <!-- mail seller button -->
			                    <?php if (!$this->viewer()->isSelf($listing -> getOwner())):?>
			                    <span>
			                        <a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose', 'to' => $listing -> getOwner() -> getIdentity()), 'messages_general', true);?>"><i class="fa fa-envelope"></i> <?php echo $this -> translate('Mail to Seller');?></a>
			                    </span>
			                    <?php endif;?>
			                    <!-- mail seller button -->
			
			                    <!-- message to friend button -->
			                    <?php if ($this->viewer()->getIdentity()):?>
			                    <span>
			                        <?php echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_specific',
			                                'action' => 'email-to-friends',
			                                'listing_id' => $listing->getIdentity()
			                            ),
			                            '<i class="fa fa-envelope-o"></i>'.$this->translate('Email to Friends'),
			                            array(
			                                'class' => 'smoothbox'
			                            )
			                        )?>
			                    </span>
			                    <?php endif;?>
			                    <!-- message to friend button -->
			
			                    <!-- print button -->
			                    <span>
			                        <?php
			                        echo $this->htmlLink(
			                            array(
			                                'route' => 'ynmultilisting_specific',
			                                'action' => 'print',
			                                'listing_id' => $listing->getIdentity()
			                            ),
			                            '<i class="fa fa-print"></i>'.$this->translate('Print Listing'),
			                            array())
			                        ?>
			                    </span>
			                    <!-- end print button -->
			
			                    <!-- get direction -->
			                    <span>
			                        <?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_specific', 'action' => 'direction', 'listing_id' => $listing->getIdentity()),
			                        '<i class="fa fa-location-arrow"></i>'.$this->translate('Get Direction'),
			                        array('class' => 'smoothbox')) ?>
			                    </span>
			                    <!-- get direction -->
			
			                    <!-- HOANGND add to compare-->
			                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
			                    	<span>
			                        <?php if ($listing->inCompare()) : ?>
			                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $listing -> getIdentity();?>)">
			                                <i class="fa fa-exchange"></i><?php echo $this->translate('Remove from Compare')?>
			                            </a>
			                        <?php else: ?>
			                            <a class="listing-add-to-compare_<?php echo $listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $listing -> getIdentity();?>)">
			                                <i class="fa fa-exchange"></i><?php echo $this->translate('Add to Compare')?>
			                            </a>
			                        <?php endif; ?>
			                        </span>
			                    <?php endif;?>
			                    <!-- add to compare-->
			
			                    <!-- HOANGND add to wishlist-->
			                    <?php if($viewer->getIdentity()) :?>
			                    	<span>
			                        	<?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $listing->getIdentity()),
			                        '<i class="fa fa-bookmark"></i>'.$this->translate('Add to Wish List'),
			                        array('class' => 'smoothbox')) ?>
			                        </span>
			                    <?php endif;?>
			                    <!-- add to wishlist-->
			                    
			                    <!-- HOANGND remove from this wishlist-->
			                    <?php if (isset($this->wishlist) && $listing->inWishlist($this->wishlist->getIdentity()) && $this->wishlist->isOwner($this->viewer())) :?>
			                    	<span>
									<?php echo $this->htmlLink(
			                        array('route' => 'ynmultilisting_wishlist', 'action' => 'remove-listing', 'listing_id' => $listing->getIdentity(), 'id' => $this->wishlist->getIdentity()),
			                        '<i class="fa fa-bookmark-o"></i>'.$this->translate('Remove from  this Wish List'),
			                        array('class' => 'smoothbox')) ?>
			                        </span>
								<?php endif; ?>
								<!-- remove from this wishlist-->
			                    </div>
			                <!-- end buttons -->
			                </div>


							</div>

							<?php if ($listing->isNew()) : ?>
								<div class="newListing"></div>
							<?php endif; ?>

							<?php if ($listing->featured) : ?>
								<div class="featureListing"></div>
							<?php endif; ?>

							<div class="listing_photo_hover">
								<div class="listing_category">            
									<span class="fa fa-folder-open"></span>
									<?php 
									$category = $listing->getCategory();
									if ($category) {
										echo $this->htmlLink($category->getHref(), $category->getTitle());
									}
									?>
								</div>  

								<div class="listing_view_more"> 
									<?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
								</div>
							</div>
						</div>

						<div class="listing_owner_avatar"><?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?></div>

						<div class="listing_rating">
							<?php 
							echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing));
							?>   

							<a href="<?php echo $listing -> getHref();?>">
								<b>&nbsp;<?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $listing -> review_count), $listing -> review_count); ?></b>
							</a>
						</div>

						<?php if ($listing->location): ?>
							<div class="listing_location">
								<span class="fa fa-map-marker"></span>
								<?php echo $listing->location;?>
							</div>
						<?php endif; ?>
					</div>
				</div>

			</li>
		<?php endforeach; ?>
	</ul>
<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate("There are no listings.") ?>
		</span>
	</div>
<?php endif;?>

    
		<?php 
			//Set theme Default
			
			$themes = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll();
			$activeTheme = $themes->getRowMatching('active', 1);
			$arrname = explode("-", $activeTheme->name);
			$name_theme = $arrname[0];
		?>



<script type="text/javascript">

	//SET THEM DEFAULT
	window.addEvent('domready', function(){
		$$('body')[0].addClass('<?php echo $name_theme; ?>');
	});

	//TPD set show options
    (function($,$$){
      var events;
      var check = function(e){
        var target = $(e.target);
        var parents = target.getParents();
        events.each(function(item){
          var element = item.element;
          if (element != target && !parents.contains(element))
            item.fn.call(element, e);
        });
      };
      Element.Events.outerClick = {
        onAdd: function(fn){
          if(!events) {
            document.addEvent('click', check);
            events = [];
          }
          events.push({element: this, fn: fn});
        },
        onRemove: function(fn){
          events = events.filter(function(item){
            return item.element != this || item.fn != fn;
          }, this);
          if (!events.length) {
            document.removeEvent('click', check);
            events = null;
          }
        }
      };
    })(document.id,$$);

	$$('.ynmultilisting_buttons_action').removeEvents('click').addEvent('click',function(){
		var ynparent = this.getParent('.ynmultilisting_buttons');
		if( ynparent.hasClass('ynshow-options') ){
			ynparent.removeClass('ynshow-options');
		}else{
			$$('.ynshow-options').removeClass('.ynshow-options');
			ynparent.addClass('ynshow-options');
		}

		//check box is cut
        var layout_middle = ynparent.getParent('.listing_browse');
        
        var y_position = ynparent.getPosition(layout_middle).y; // item top
        var y_height = ynparent.getDimensions().height; // item top
        
        var p_height = layout_middle.getHeight(); // container

        var c_height = parseInt(ynparent.getChildren('.ynmultilisting_buttons_showoptions').getHeight()); // menu

        if (p_height - y_position - y_height < c_height) {
	        if (y_position > c_height)
	        {
	            ynparent.addClass('ynmultilisting_buttons_reverse');
	        } else {
	        	layout_middle.setStyle('padding-bottom', (y_position + y_height + c_height) - p_height + 'px');
	        }
        } else {
        	layout_middle.setStyle('padding-bottom', '0');
        }
	});

	$$('.ynmultilisting_buttons').addEvent('outerClick',function(){
		var layout_middle = this.getParent('.listing_browse');
		if (this.hasClass('ynshow-options')){
			this.removeClass('ynshow-options');
			layout_middle.setStyle('padding-bottom','0px');
		}
	});

    <?php if ($this->viewer()->getIdentity()):?>
    function follow(owner_id, itemId)
    {
        html = '<a><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Followed Seller');?></a>';
		
		$$('.ynmultilisting-follow-button-'+itemId).each(function(el) {
			el.set('html', html);
		});
		
        var url = '<?php echo $this->url(array('action'=>'follow'), 'ynmultilisting_general')?>';

        new Request.JSON({
            'url': url,
            'method': 'post',
            'data' : {
                'status' : 1,
                'owner_id' : owner_id
            },
            'onSuccess': function(responseJSON) {
                if(responseJSON.json == 'true')
                {
                    html = '<a href="javascript:void(0);" onclick="unfollow(\''+owner_id+'\', \''+itemId+'\')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Followed Seller');?></a>';
                    $$('.ynmultilisting-follow-button-'+itemId).each(function(el) {
						el.set('html', html);
					});
                }
            },
            'onComplete': function(responseJSON) {
            }
        }).send();
    }

    function unfollow(owner_id, itemId)
    {
        html = '<a><i class="fa fa-arrow-right"></i></i><?php echo $this -> translate('Follow Seller');?></a>';
        $$('.ynmultilisting-follow-button-'+itemId).each(function(el) {
			el.set('html', html);
		});

        var url = '<?php echo $this->url(array('action'=>'follow'), 'ynmultilisting_general')?>';
        new Request.JSON({
            'url': url,
            'method': 'post',
            'data' : {
                'status' : 0,
                'owner_id' : owner_id
            },
            'onSuccess': function(responseJSON) {
                if(responseJSON.json == 'false')
                {
                    html = '<a href="javascript:void(0);" onclick="follow(\''+owner_id+'\', \''+itemId+'\')"><i class="fa fa-arrow-right"></i><?php echo $this -> translate('Follow Seller');?></a>';
                    $$('.ynmultilisting-follow-button-'+itemId).each(function(el) {
						el.set('html', html);
					});
                }
            },
            'onComplete': function(responseJSON) {
            }
        }).send();
    }

    function like(itemType, itemId)
    {
        html = '<a><i class="fa fa-heart"></i><?php echo $this -> translate('Liked');?></a>';
		
		$$('.ynmultilisting-like-button-'+itemId).each(function(el) {
			el.set('html', html);
		});
		
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/like',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    html = '<a href="javascript:void(0);" onclick="unlike(\''+itemType+'\', \''+itemId+'\')"><i class="fa fa-heart"></i><?php echo $this -> translate('Liked');?></a>';
                    $$('.ynmultilisting-like-button-'+itemId).each(function(el) {
						el.set('html', html);
					});
                }
            },
            onComplete: function(responseJSON, responseText) {
            }
        }).send();
    }

    function unlike(itemType, itemId)
    {
        html = '<a><i class="fa fa-heart"></i><?php echo $this -> translate('Like');?></a>';
        $$('.ynmultilisting-like-button-'+itemId).each(function(el) {
			el.set('html', html);
		});

        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/unlike',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    html = '<a href="javascript:void(0);" onclick="like(\''+itemType+'\', \''+itemId+'\')"><i class="fa fa-heart-o"></i><?php echo $this -> translate('Like');?></a>';
                    $$('.ynmultilisting-like-button-'+itemId).each(function(el) {
						el.set('html', html);
					});
                }
            }
        }).send();
    }
    <?php endif;?>

    //HOANGND script for add to compare
    function addToCompare(obj, id) {
        var url = obj.get('rel');
        var jsonRequest = new Request.JSON({
            url : url,
            onSuccess : function(json, text) {
                if (!json.error) {
                    obj.set('html', '<i class="fa fa-exchange"></i><?php echo $this->translate('Remove from Compare')?>');
                    obj.set('onclick', 'removeFromCompare(this, '+id+')');
                    var params = {};
                    params['format'] = 'html';
                    var request = new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/name/ynmultilisting.compare-bar',
                        data : params,
                        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                            $$('.layout_ynmultilisting_compare_bar').destroy();
                            var body = document.getElementsByTagName('body')[0];
                            Elements.from(responseHTML).inject(body);
                            eval(responseJavaScript);
                        }
                    });
                    request.send();
                }
                else {
                    alert(json.message);
                }
            }
        }).get({value:1});
    }
    
    function removeFromCompare(obj, id) {
        var url = obj.get('rel');
        var jsonRequest = new Request.JSON({
            url : url,
            onSuccess : function(json, text) {
                if (!json.error) {
                    obj.set('html', '<i class="fa fa-exchange"></i><?php echo $this->translate('Add to Compare')?>');
                    obj.set('onclick', 'addToCompare(this, '+id+')');
                    var params = {};
                    params['format'] = 'html';
                    var request = new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/name/ynmultilisting.compare-bar',
                        data : params,
                        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                            $$('.layout_ynmultilisting_compare_bar').destroy();
                            var body = document.getElementsByTagName('body')[0];
                            Elements.from(responseHTML).inject(body);
                            eval(responseJavaScript);
                        }
                    });
                    request.send();
                }
                else {
                    alert(json.message);
                }
            }
        }).get({value:0});
    }
</script>

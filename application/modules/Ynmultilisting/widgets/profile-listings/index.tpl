<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('ynmultilisting_profile_listings').getParent();
    $('ynmultilisting_profile_listings_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynmultilisting_profile_listings_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynmultilisting_profile_listings_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('ynmultilisting_profile_listings_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<div id="ynmultilisting_profile_listings" class="listings_profile_tab ynmultilisting_grid-view">
	<ul class="generic_list_widget listing_browse ynmultilisting-tabs-content clearfix">
	<?php $oldListingTypeId = 0;?>
    <?php foreach ($this->paginator as $listing) : ?>
    <?php $newListingTypeId = $listing->listingtype_id; ?>
	<?php if ($oldListingTypeId != $newListingTypeId) : ?>
    </ul>

    	<div class="listingtype-title">
          	<?php 
          		$oldListingTypeId = $newListingTypeId;
        			$listingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $newListingTypeId);
        			echo $listingtype;
          	?>	
    	</div>
      <ul class="generic_list_widget listing_browse ynmultilisting-tabs-content clearfix">
    <?php endif;?>
	    <li>
	    	<div class="grid-view-mode-1">
				<div class="ynmultilisting-grid-item-mode-1">

                    <div class="item-front-info">
                        <div class="listing_title">
                            <?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
                        </div>   

                        <div class="ynmultilisting-item-rating">
                            <?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing)); ?>

                            <b>&nbsp;<?php echo $listing -> rating;?></b>
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



				</div>            
			</div>
	    </li>  
    <?php endforeach; ?>
    </ul>
</div>

<div style="clear:both">
  <div id="ynmultilisting_profile_listings_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="ynmultilisting_profile_listings_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
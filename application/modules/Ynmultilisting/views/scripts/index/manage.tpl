<script type="text/javascript">
	var pageAction =function(page){
		$('page').value = page;
		$('filter_form').submit();
	}
</script>

<div class="ynmultilisting-my-listings">
    
    <div class="ynmultilisting-countitem-port">  
   
        <?php if( count($this->paginator) > 0 ): ?>
        <div class="count-item-listings">
            <?php echo  $this->translate(array('<span>%s</span> listing', '<span>%s</span> listings', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?>
        </div>
        <?php endif; ?>
        
        <div class="import-export-listing">
            <?php if ($this->can_import) :?>
            <div id="import_listings" class='import_listings'>
                <?php echo $this->htmlLink(
                array('route' => 'ynmultilisting_import'),
                '<i class="fa fa-arrow-circle-o-down"></i> &nbsp;'.$this->translate('Import Listings'), 
                array('class' => 'icon_listings_import')) ?>
            </div>
            <?php endif; ?>

            <?php if( count($this->paginator) > 0 && $this->can_export): ?>
            <div id="export_listings" class='export_listings'>
                <?php echo $this->htmlLink(
                array('route' => 'ynmultilisting_general', 'action' => 'export'),
                '<i class="fa fa-arrow-circle-o-up"></i> &nbsp;'.$this->translate('Export Listings'), 
                array('class'=>'smoothbox icon_listings_export')) ?>
            </div>
            <?php endif;?>
        </div>
    </div>



<div class='layout_middle'>
<?php if( count($this->paginator) > 0 ): ?>
<ul class='listing_browse'>
    <?php foreach( $this->paginator as $listing): ?>
    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png";?>
    <li>
        
    <div class="listing_photo" style="background-image: url('<?php echo $photo_url ?>') ">
            <?php if ($listing->featured) : ?>
                <div class="featureListing"></div>
            <?php endif; ?>

            <?php if ($listing->status == 'expired') : ?>
                <div class="expiredListing"></div>
            <?php endif; ?>
        </div>

        <div class="listing_options">
        <?php if( $listing->isOwner($this->viewer())): ?>
            
            <!-- edit link -->
            <?php if ($listing->isEditable()) : ?>
	            <?php echo $this->htmlLink(
	            array('route' => 'ynmultilisting_specific', 'action' => 'edit', 'listing_id' => $listing->getIdentity()), 
	            '<i class="fa fa-wrench"></i> &nbsp;'.$this->translate('Edit Listing'), 
	            array('class' => 'icon_listings_edit')) ?>
            <?php endif; ?>
            
            <!-- select theme -->
            <?php if (!in_array($listing -> status, array('draft', 'expired'))): ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynmultilisting_specific', 'action' => 'select-theme', 'listing_id' => $listing->getIdentity()), 
                '<i class="fa fa-check-square-o"></i> &nbsp;'.$this->translate('Select Theme'), 
                array('class' => 'smoothbox icon_listings_select_theme')) ?>
            <?php endif; ?>
            
            <?php if(!in_array($listing -> status, array('draft', 'expired'))) :?>      
                
                <!-- add photo -->
                <?php echo $this->htmlLink(
                array('route' => 'ynmultilisting_extended','controller' => 'photo','action' => 'index', 'listing_id' => $listing->getIdentity()), 
                '<i class="fa fa-camera"></i> &nbsp;'.$this->translate('Add Photos'), 
                array('class' => 'icon_listings_add_photos')) ?>

                <!-- add videos -->
                <?php if(Engine_Api::_()->hasItemType('video') && in_array($this->video_type, array('video', 'ynvideo'))): ?>
                    <?php echo $this->htmlLink(
                    array('route' => 'ynmultilisting_extended','controller' => 'video','action' => 'list', 'listing_id' => $listing->getIdentity()),
                    '<i class="fa fa-film"></i> &nbsp;'.$this->translate('Add Videos'),
                    array('class' => 'icon_listings_add_videos')) ?>
                <?php endif;?>

                <!-- add videos -->
                <?php if(Engine_Api::_()->hasItemType('ynultimatevideo_video') && $this->video_type == 'ynultimatevideo_video'): ?>
                    <?php echo $this->htmlLink(
                    array('route' => 'ynmultilisting_extended','controller' => 'ultimate-videos','action' => 'list', 'listing_id' => $listing->getIdentity()),
                    '<i class="fa fa-film"></i> &nbsp;'.$this->translate('Add Ultimate Videos'),
                    array('class' => 'icon_listings_add_videos')) ?>
                <?php endif;?>
                
                <!-- dicussion link -->
                <?php echo $this->htmlLink(array(
                    'route' => 'ynmultilisting_extended',
                    'controller' => 'topic',
                    'action' => 'create',
                    'subject' => $listing->getGuid(),
                    ), '<i class="fa fa-comment-o"></i> &nbsp;'.$this->translate('Add Discussions'), array(
                    'class' => 'icon_ynmultilisting_post_new'
                  )) ?>
                  
            <?php endif;?>
            
           <?php if ($listing->approved_status == 'approved' && $listing->status != "expired") : ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynmultilisting_specific', 'action' => 'publish-close', 'listing_id' => $listing->getIdentity()), 
                ($listing->status == 'open')? '<i class="fa fa-times"></i> &nbsp;'.$this->translate('Close Listing') : '<i class="fa fa-external-link"></i> &nbsp;'.$this->translate('Open Listing'), 
                array('class' => ($listing->status == "open")? 'smoothbox icon_listings_close' : ' smoothbox icon_listings_publish')) ?>
            <?php endif; ?>
            
            <?php if ($listing->status != 'closed') : ?>
                
                <!-- package link -->
                <?php 
                    switch ($listing->status) {
                        case 'expired':
                        case 'draft':
                            $labelPayment = '<i class="fa fa-shopping-cart"></i> &nbsp;'.$this->translate('Make Payment') ;
                            break;
                        case 'open':
                            $labelPayment = '<i class="fa fa-cube"></i> &nbsp;'.$this->translate('Change Package') ;
                            break;
                        default:
                            
                            break;
                    }
                ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynmultilisting_specific', 'action' => 'package', 'listing_id' => $listing->getIdentity()), 
                $labelPayment, 
                array('class' => 'icon_listings_publish')) ?>
                
                <!-- feature link -->
                <?php if ($listing->status != 'draft') : ?>
                      <?php echo $this->htmlLink(
                        array('route' => 'ynmultilisting_specific', 'action' => 'feature', 'listing_id' => $listing->getIdentity()), 
                        '<i class="fa fa-certificate"></i> &nbsp;'.$this->translate('Feature Listing'),
                        array('class' => 'smoothbox icon_listings_publish')) ?>
                <?php endif;?>
                
            <?php endif;?>
            
            <!-- transfer link -->
            <?php if ($listing->isAllowed('delete')) : ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynmultilisting_specific', 'action' => 'transfer-owner', 'listing_id' => $listing->getIdentity()), 
                '<i class="fa fa-exchange"></i> &nbsp;'.$this->translate('Transfer Owner'), 
                array('class' => 'smoothbox icon_listings_publish')) ?>
            <?php endif; ?>
            
            <!-- delete link -->
            <?php if ($listing->isAllowed('delete')) : ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynmultilisting_specific', 'action' => 'delete', 'listing_id' => $listing->getIdentity()), 
                '<i class="fa fa-trash-o"></i> &nbsp;'.$this->translate('Delete Listing'), 
                array('class' => 'smoothbox icon_listings_delete')) ?>
            <?php endif; ?>
            
        <?php endif ;?>
        </div>

        <div class="listing_info">

            <div class="listing_info_top">
                <div class="listing_title">
                    <?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
                </div>

                <div class="listing-price-rating">
                    <div class="price">
                        <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
                    </div>

                    <div class="listing_rating">
                        <?php 
                            echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $listing));
                        ?>
                        &nbsp;
                        <span class="listing_rating_point">
                            <?php echo $listing->rating ?>
                        </span>

                        <span class="listing_count_review">
                            <?php echo $this -> translate(array("(%s review)", "(%s reviews)" ,$listing->review_count), $listing->review_count) ?>
                        </span>
                    </div>
                </div>

                <div class="listing_creation_activity">
                    <div class="listing_creation">
                        <?php 
                            $creation_date = $listing->getCreationDate();
                        ?>
                        <span><?php echo $creation_date->get('MMM d Y')?></span>
                        <span class="listing_owner"><?php echo $this->translate(' - by ')?></span>
                        <span class="listing_owner"><?php echo $listing->getOwner()?></span>
                    </div>

                    <div class="listing_activity">
                        <span>
                            <?php echo '<i class="fa fa-heart"></i>&nbsp;'.$listing->like_count; ?>
                        </span>

                        <span>
                            <?php echo '<i class="fa fa-comment"></i>&nbsp;'.$listing->comment_count; ?>
                        </span>

                        <span>
                            <?php echo '<i class="fa fa-eye"></i>&nbsp;'.$listing->view_count; ?>
                        </span>
                    </div>
                </div>
            </div>


            <div class="listing_info_bottom">
                <div class="category">
                    <span class="small_description"><?php echo $this->translate('Category: ')?></span>
                    <span><?php echo $this->translate($listing->getCategoryTitle())?></span>
                </div>

                <div class="location">
                    <span class="small_description"><?php echo $this->translate('Location: ')?></span>
                    <span class="location-description"><?php echo ($listing->location) ? $listing->location : $this->translate('unknown')?></span>
                    <?php if(!(($listing -> longitude == 0) && ($listing -> latitude == 0))): ?>
                    <span class="small_description"><?php echo $this->translate('&nbsp;-&nbsp;')?></span>   
                        <span>
                        <?php echo $this->htmlLink(
                        array('route' => 'ynmultilisting_specific', 'action' => 'direction', 'listing_id' => $listing->getIdentity()), 
                        '<i class="fa fa-location-arrow"></i>'.$this->translate(' Get Direction'), 
                        array('class' => 'smoothbox get_direction')) ?>
                        </span>
                    <?php endif;?>
                </div>
                
                <div class="approved_status">
                    <span class="small_description"><?php echo $this->translate('Approved Status: ')?></span>

                    <span class="approved_status_title"><?php echo (($listing->approved_status) ? $this->translate($listing->approved_status) : 'N/A')?></span>

                    <?php if ($listing->featured) :?>
                    &nbsp;-&nbsp; 
                    <span class="small_description"><?php echo $this->translate('Feature Until: ')?></span>

                    <?php $feature_expiration_date = $listing->getFeatureExpirationDate();?>
                    <span class="feature-until"><?php echo ($feature_expiration_date) ? $feature_expiration_date->get('MMM d Y') : $this->translate('Forever');?></span>
                    <?php endif;?>

                </div>

                <div class="status">
                    <span class="small_description"><?php echo $this->translate('Listing Status: ')?></span>
                    <span class="listing-status"><?php echo $this->translate($listing->status)?></span>
                    <?php if ($listing->expiration_date) :?>
                    &nbsp;-&nbsp; 
                    <span class="small_description"><?php echo $this->translate('Expiration Date: ')?></span>
                    <?php $expiration_date = $listing->getExpirationDate();?>
                    <span class="expiration_date"><?php echo $expiration_date->get('MMM d Y');?></span>
                    <?php endif;?>
                </div>

            </div>

        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php if( count($this->paginator) > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
        )); ?>
    <?php endif; ?>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate('No listings found.') ?></span>
    </div>
<?php endif; ?>
</div>

</div>
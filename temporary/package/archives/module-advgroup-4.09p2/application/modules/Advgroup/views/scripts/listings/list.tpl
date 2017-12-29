<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
        <div class="headline">
		<h2>
			<?php echo $this->group->__toString()." ";
				echo $this->translate(' &#187; Listings');
			?>
		</h2>
        </div>
	</div>
</div>
<div class="generic_layout_container layout_main advgroup_list ynlistings_grid-view">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="listing_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>

	<div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
        <div class="advgroup-profile-module-header">
            <!-- Menu Bar -->
            <div class="advgroup-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity(), 'slug' => $this->group-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Group'), array(
                'class' => 'buttonlink'
                )) ?>
                <?php if ($this->canCreate):?>
                    <?php echo $this->htmlLink(array(
                    'route' => 'ynlistings_general',
                    'controller' => 'index',
                    'action' => 'create',
                    'subject_id' => $this->subject()->getGuid(),
                    'parent_type' => 'group',
                    ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Listing'), array(
                    'class' => 'buttonlink'
                    ))
                    ?>
                <?php endif; ?>         
            </div>      
            <?php if( $this->paginator->getTotalItemCount() > 0 ): $group = $this->group;?>
            <div class="advgroup-profile-header-content">
                <span class="advgroup-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php echo $this-> translate(array("listing_count", "Listings", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
            </div>
            <?php endif; ?>
        </div>  
		
		<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
		<ul class="advgroup_listing generic_list_widget listing_browse listing_browse_view_content ynlistings-tabs-content clearfix">  
            <?php foreach ($this->paginator as $listing): 
            	$owner = $listing -> getOwner();?>
            <li>
                <div class="grid-view">
                    <div class="ynlisting-grid-item">
                        <div class="ynlisting-grid-item-content">
                            <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png";?>
                            <div class="item-background" style="background-image: url(<?php echo $photo_url; ?>);">
    
                                <?php if ($listing->featured) : ?>
                                    <div class="featureListing"></div>
                                <?php endif; ?>
    
                                <?php if ($listing->isNew()) : ?>
                                    <div class="newListing"></div>
                                <?php endif; ?>
    
                                <div class="ynlisting-item-rating">
                                    <?php echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $listing)); ?>
                                </div>
                            </div>
                            <div class="item-front-info">
                                <div class="listing_title">
                                    <?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
                                </div>    
    
                                <div class="listing_price">
                                    <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
                                </div>
                            </div>
                        </div>
                        <div class="ynlisting-grid-item-hover">
                            <div class="ynlisting-grid-item-hover-background">
                                <div class="listing_view_more"> 
                                    <?php echo $this->htmlLink($listing->getHref(), $this->translate('View more ').'<span class="fa fa-arrow-right"></span> ' );?>
                                </div>
    
                                <div class="short_description">
                                    <?php echo strip_tags($listing->short_description)?>
                                </div>
    
                                <div class="listing_creation">
                                    <span class="author-avatar"><?php echo $this->htmlLink($owner, $this->itemPhoto($owner, 'thumb.icon'))?></span>
                                    <span><?php echo $this->translate('by ')?></span>
                                    <span class="author-title"><?php echo $owner?></span>
                                </div>                                                               
                            </div>
                        </div>
                    </div>            
                </div> 

                <div class="advgroup-profile-module-option">
                    <?php 
                    $canRemove = $group -> authorization() -> isAllowed($this->viewer, "view") || ($listing->isOwner($this->viewer));
                    $canDelete = $listing->isDeletable();
                    $canEdit = $listing->isEditable();
                    $canPublish = $listing->isEditable() && ($listing->status == 'draft');
                    ?>
                    <?php if ($canRemove || $canPublish || $canDelete || $canEdit): ?>
                    <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink(
                        array(
                            'action' => 'edit',
                            'id' => $listing->getIdentity(),
                            'route' => 'ynlistings_general',
                            'subject_id' => $this->subject()->getGuid(),
                            'parent_type' => 'group',
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Listing'), array(
                            'class' => 'buttonlink',
                        ))
                        ?>
                    <?php endif; ?>

                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynlistings_general',
                            'action' => 'delete',
                            'id' => $listing->getIdentity(),
                            'subject_id' => $this->subject()->getGuid(),
                            'parent_type' => 'group',
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete Listing'),
                        array('class'=>'buttonlink smoothbox'))
                      ?>
                    <?php endif; ?> 

                    <?php if ($canPublish): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynlistings_general',
                            'action' => 'place-order',
                            'id' => $listing->getIdentity(),
                            'subject_id' => $this->subject()->getGuid(),
                            'parent_type' => 'group',
                        ),
                        '<i class="fa fa-cloud-upload"></i>'.$this->translate('Publish Listing'),
                        array('class'=>'buttonlink'))
                      ?>
                    <?php endif; ?>    

                    <?php if ($canRemove): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'group_extended',
                            'module' => 'advgroup',
                            'controller' => 'listings',
                            'action' => 'delete',
                            'item_id' => $listing->getIdentity(),
                            'group_id' => $this->group->getIdentity(),
                            'type' => 'ynlistings_listing',
                        ),
                        '<i class="fa fa-times"></i>'.$this->translate('Remove Listing To Group'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?> 
                    <?php endif; ?>
                </div>
            </li> 
            <?php endforeach; ?>             
        </ul>   
		<?php if( $this->paginator->count() > 0 ): ?>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		<?php endif; ?>
		<?php else: ?>
		<div class="tip">
			<span>
			  <?php echo $this->translate('No listings have been created.');?>
			</span>
		</div>
		<?php endif; ?>
        </div>
	</div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>
  
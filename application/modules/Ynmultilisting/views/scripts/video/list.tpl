<style type="text/css">
	#delete-wrapper{
		float:left;
	}
	#delete-element{
		min-width: 70px;
	}
	.photo-delete-wrapper
	{
		padding-left: 135px
	}
	#profile_main_video
	{
		padding-left: 135px
	}
	.ynmultilisting_editphotos_info > div{
		margin-top: 10px;
	}
	#buttons-label{
		display:none;
	}
	.listing_options a{
		margin-left: 10px;
	}
	.form-wrapper{
		padding-left: 130px;
	}
</style>
<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->listing->__toString();
				echo $this->translate('&#187; Videos');
			?>
		</h2>
	</div>
</div>
<div class="listing_options">
    <?php if( $this->listing->isOwner($this->viewer())): ?>
        <?php if ($this->listing->isEditable()) : ?>
	       <?php echo $this->htmlLink(
	        array('route' => 'ynmultilisting_specific', 'action' => 'edit', 'listing_id' => $this->listing->getIdentity()),
	        $this->translate('Edit Listing'),
	        array('class' => 'buttonlink icon_listings_edit')) ?>
        <?php endif; ?>

       <!-- select theme -->
	    <?php if (!in_array($this -> listing -> status, array('draft', 'expired'))): ?>
	        <?php echo $this->htmlLink(
	        array('route' => 'ynmultilisting_specific', 'action' => 'select-theme', 'listing_id' => $this->listing->getIdentity()),
	        $this->translate('Select Theme'),
	        array('class' => 'smoothbox buttonlink icon_listings_select_theme')) ?>
	    <?php endif; ?>

        <?php echo $this->htmlLink(
        array('route' => 'ynmultilisting_extended','controller' => 'photo','action' => 'index', 'listing_id' => $this->listing->getIdentity()),
        $this->translate('Add Photos'),
        array('class' => 'buttonlink icon_listings_add_photos')) ?>

       <!-- publish - close link -->
        <?php if ($this->listing->approved_status == 'approved') : ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynmultilisting_specific', 'action' => 'publish-close', 'listing_id' => $this->listing->getIdentity()),
            ($this->listing->status == 'open')? $this->translate('Close Listing') : $this->translate('Open Listing'),
            array('class' => ($this->listing->status == "open")? 'buttonlink smoothbox icon_listings_close' : 'buttonlink smoothbox icon_listings_publish')) ?>
        <?php endif; ?>

       <!-- delete link -->
        <?php if ($this->listing->isAllowed('delete')) : ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynmultilisting_specific', 'action' => 'delete', 'listing_id' => $this->listing->getIdentity()),
            $this->translate('Delete Listing'),
            array('class' => 'buttonlink smoothbox icon_listings_delete')) ?>
        <?php endif; ?>
    <?php endif ;?>
</div>
<br/>
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<div class="ynmultilisting_action_grid-view">
		<?php if (!in_array($this -> listing -> status, array('draft', 'expired'))): ?>
		<?php $package = $this -> listing -> getPackage();	?>
			<?php if($package -> getIdentity()):?>
				<!-- Menu Bar -->
				<div>
					<?php if(Engine_Api::_() -> ynmultilisting() -> countVideoByListing($this -> listing, true) >= $package -> max_videos):?>
				 	<div class="tip">
						<span>
						<?php echo $this -> translate('You have reached the video limitation.');?>
						</span>
					</div>
					<?php else :?>
						<?php if( $this->canCreate ): ?>
							<?php echo $this->htmlLink(array(
								'route' => 'video_general',
								'action' => 'create',
								'type_parent' =>'ynmultilisting_listing',
								'profile' => 'profile',
								'id_subject' =>  $this->listing->getIdentity(),
							  ), $this->translate('Add New Video'), array(
								'class' => 'buttonlink icon_listings_add_videos'
							)) ?>
							<br/><br/>
							<div class="tip">
								<span><?php echo $this -> translate(array("You can add maximum %s video.", "You can add maximum %s videos." , $package -> max_videos), $package -> max_videos);?></span>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif;?>
		<?php endif;?>
		<br/>
		<!-- Content -->
		<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form ynmultilisting_browse_filters">
		    <div style="float: none">
		      <div class="form-elements">
		        <?php echo $this->form->getDecorator('FormErrors')->setElement($this->form)->render();?>

		     <?php if ($this->paginator->getTotalItemCount()> 0) : ?>
		      <ul class='ynmultilisting_editphotos'>
		        <?php foreach ($this->paginator as $item): ?>
		          <li>
		            <div class="ynmultilisting_editphotos_photo">
						<?php
							echo $this->partial('_video_listing.tpl', 'ynmultilisting', array(
								'video' => $item,
								'infoCol' => $this->infoCol,
							));
						?>
		            </div>
		            <div class="ynmultilisting_editphotos_info">
		            	<?php
			                $key = $item->getGuid();
			                echo $this->form->getSubForm($key)->render($this);
			              ?>
			          <br/>
			          <div id='profile_main_video'>
		              	  <div class="ynmultilisting_editphotos_cover">
			                <input type="radio" name="cover" value="<?php echo $item->getIdentity() ?>" <?php if( $this->listing->video_id == $item->video_id ): ?> checked="checked"<?php endif; ?> />
			              </div>

			              <div class="ynmultilisting_editphotos_label">
			                <label><?php echo $this->translate('Main Video');?></label>
			              </div>
		              </div>
		            </div>
		            <br/>
		          </li>
		        <?php endforeach; ?>
		      </ul>
			      <div class="form-wrapper">
			      <div class="form-label" id="buttons-label">&nbsp;</div>
			      <?php echo $this->form->execute->render(); ?>
			       <?php echo $this->form->cancel; ?>
		       	</div>
		       <?php else : ?>
				<div class="tip">
					<span>
						<?php echo $this->translate('There is no videos found.'); ?>
					</span>
				</div>
		      <?php endif; ?>
		        </div>
		    </div>
		</form>
		<br/>
	</div>
	</div>
</div>
<!-- Menu Bar -->


<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('title'))
	    {
	      new OverText($('title'),
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

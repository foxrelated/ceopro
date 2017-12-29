<style type="text/css">
	.listing_options a{
		margin-left: 10px;
	}
</style>
<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->listing->__toString();
				echo $this->translate('&#187; Photos');
			?>
		</h2>
	</div>
</div>
<div class="ynmultilisting_action_grid-view">
	<div class="listing_options">
	    <?php if( $this->listing->isOwner($this->viewer())): ?>
	        <?php if ($this->listing->isEditable()) : ?>
		        <?php echo $this->htmlLink(
		        array('route' => 'ynmultilisting_specific', 'action' => 'edit', 'listing_id' => $this->listing->getIdentity()),
		        $this->translate('Edit Listing'),
		        array('class' => 'buttonlink icon_listings_edit')) ?>
	        <?php endif; ?>

	        <!-- select theme -->
		    <?php if (!in_array($listing -> status, array('draft', 'expired'))): ?>
		        <?php echo $this->htmlLink(
		        array('route' => 'ynmultilisting_specific', 'action' => 'select-theme', 'listing_id' => $this->listing->getIdentity()),
		        $this->translate('Select Theme'),
		        array('class' => 'smoothbox buttonlink icon_listings_select_theme')) ?>
		    <?php endif; ?>

	        <?php if(Engine_Api::_()->hasItemType('video')): ?>
	        <?php echo $this->htmlLink(
	        array('route' => 'ynmultilisting_extended','controller' => 'video','action' => 'list', 'listing_id' => $this->listing->getIdentity()),
	        $this->translate('Add Videos'),
	        array('class' => 'buttonlink icon_listings_add_videos')) ?>
	        <?php endif;?>

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
	<div class="ynlisting_listing_action">
	<?php if (!in_array($this -> listing -> status, array('draft', 'expired'))): ?>
	<?php $package = $this -> listing -> getPackage();	?>
		<?php if($package -> getIdentity()):?>
			<!-- Menu Bar -->
			<div>
				<?php if($this->listing->getSingletonAlbum()->getPhotoCount() >= $package -> max_photos):?>
			 	<div class="tip">
					<span>
					<?php echo $this -> translate('You have reached the photo limitation.');?>
					</span>
				</div>
				<?php else :?>
					<?php if( $this->canUpload ): ?>
					<a class='buttonlink icon_listings_add_photos' href="<?php echo $this->url(array('controller'=>'photo','action'=>'upload','listing_id'=>$this->listing->getIdentity(),'profile' => '1'),'ynmultilisting_extended') ?>">
						<?php echo $this->translate('Add more photos'); ?>
					</a>
					<br/><br/>
					<div class="tip">
						<span><?php echo $this -> translate(array("You can add maximum %s photo.", "You can add maximum %s photos." , $package -> max_photos), $package -> max_photos);?></span>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<br/>
	<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form ynmultilisting_browse_filters">
	  <div>
	    <div>
	      <div class="form-elements">
	        <?php echo $this->form->getDecorator('FormErrors')->setElement($this->form)->render();?>

	     <?php if(Count($this->paginator) > 0): ?>
	      <ul class='ynmultilisting_editphotos'>
	        <?php foreach( $this->paginator as $photo ): ?>
	          <li class="ynmultilisting_editphotos_item">
	            <div class="ynmultilisting_editphotos_photo">
	              <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
	            </div>
	            <div class="ynmultilisting_editphotos_info">
	              <?php
	                $key = $photo->getGuid();
	                echo $this->form->getSubForm($key)->render($this);
	              ?>
	              <div class="ynmultilisting_editphotos_action">
		              <div class="ynmultilisting_editphotos_cover">
		                <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->listing->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
		              </div>
		              <div class="ynmultilisting_editphotos_label">
		                <label><?php echo $this->translate('Main Photo');?></label>
		              </div>
	              </div>
	            </div>
	          </li>
	        <?php endforeach; ?>
	      </ul>
	      <div class="ynmultilisting_editphotos_button_updated">
		      <?php echo $this->form->execute->render(); ?>
		      <?php echo $this->form->cancel; ?>
	      </div>
	      <?php else : ?>
				<div class="tip">
					<span>
						<?php echo $this->translate('There is no photos found.'); ?>
					</span>
				</div>
	      <?php endif; ?>
	        </div>
	    </div>
	  </div>
	</form>

</div>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>


<script type="text/javascript">
function removeSubmit(){
   $('execute').hide();
}
</script>

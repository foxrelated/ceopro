<?php if(isset($this -> error) && $this -> error) :?>
	<div class="tip">
		<span>
			<?php echo $this -> translate('You have reached the photo limitation.');?>
			<?php echo $this -> translate('Click %s to listing manage page.', $this -> htmlLink($this -> url(array('action' => 'manage'), 'ynmultilisting_general', true), $this -> translate('here')));?>
		</span>
	</div>
<?php else:?>
	<style type="text/css">
		#file-wrapper .form-label{
			display: none;
		}
		#submit-label {
			display: none;
		}
	</style>
	<?php
	if($this->canUpload):
	 ?>
	<h2>
		<?php echo $this->listing->__toString() ?>
		<?php echo $this->translate('&#187;'); ?>
		<?php echo $this->translate('Listing Photos');?>
	</h2>
	<?php echo $this->form->render($this) ?>
	<?php  else: ?>
	<div class="tip" style="clear: inherit;">
	      <span>
	<?php  echo $this->translate('You can not upload photos!');?>
	 </span>
	           <div style="clear: both;"></div>
	    </div>
	<?php endif; ?>
<?php endif;?>
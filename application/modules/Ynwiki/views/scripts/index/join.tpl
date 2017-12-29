


<?php

if(isset($this->type) && $this->type != 'group_member'):?>
	<div>
		<h3><?php echo $this->translate('Join Wiki Space')?></h3>
		<p class="form-description"> <?php echo $this->translate('This is Group Wiki Space, Please join in %1$sGroup%2$s before join this wiki.', '<a  target="_blank"  href="'.$this->group->getHref().'">','</a>')?> </p>
		
		
		<div class="form-elements">	
		<?php echo $this->translate('Please %1$sClose%2$s and join again.', '<a id="cancel" onclick="parent.Smoothbox.close();" href="javascript:void(0);" type="button" name="cancel">','</a>')?>
		</div>
	</div>
<?php else: ?>
	<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php endif;?>


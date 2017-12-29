<?php if($this->error_message) :?>
	<div class="tip">
		<span><?php echo $this->error_message; ?></span>
	</div>
<?php else:?>
	<?php echo $this->form->render($this) ?>
<?php endif;?>
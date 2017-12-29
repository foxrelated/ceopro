<?php if($this -> status) :?>
	<?php echo $this->form->render($this) ?>
<?php else:?>
	<div class="tip">
		<span><?php echo $this -> error;?></span>
	</div>
<?php endif;?>
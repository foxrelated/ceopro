<?php if( $this->viewer()->getIdentity() ): ?>
	<?php echo $this->form->render($this); ?>
<?php endif;?>
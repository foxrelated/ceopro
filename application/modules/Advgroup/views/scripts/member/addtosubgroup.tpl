<?php
if($this -> error_msg): ?>
	<div class="tip global_form_popup">
		<span>
			<?php echo $this -> error_msg;?>
		</span>
	</div>
<?php
else:
	echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ;
endif;
?>
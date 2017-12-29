<div class="advmenusystem-popup">		
	<div class="advmenusystem-popup-content">
		<div class='advmenusystem_lightbox' id='user_form_default_sea_lightbox'>
			<!--LOGIN PAGE CONTENT-->
				<?php echo $this->form->render($this) ?>
				<?php if($this->socialconnect_enable): ?>
					<div class="form-elements">
					<?php echo $this->content()->renderWidget('social-connect.quick-login'); ?>
					</div>
				<?php endif; ?>
		</div>
	</div>
</div>


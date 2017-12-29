<div id='global_content_wrapper'> 
    <div id='global_content'> 
	<h2><?php echo $this->translate("Ultimate News Plugin") ?></h2>
	<?php if( count($this->navigation) ): ?>
	  <div class='tabs'>
	    <?php
	      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
	    ?>
	  </div>
	<?php endif; ?>
	  <div class='clear'>
	  <div class='settings'>
	  	<div style="color:#717171;font-weight:bold;padding-bottom:5px;"><?php echo($this->mess); ?></div>
	    <?php echo $this->form->render($this) ?>
	  </div>
	  </div>
	</div>
</div>

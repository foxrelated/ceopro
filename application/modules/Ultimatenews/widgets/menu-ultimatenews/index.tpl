<div class="headline">
  <h2>
    <?php echo $this->translate('Ultimate News');?>
  </h2>
  <div class="tabs">
	  <?php if( count($this->navigation) > 0 ): ?>		  
	    <?php
	      // Render the menu
	      echo $this->navigation()
	        ->menu()
	        ->setContainer($this->navigation)
	        ->render();
	    ?>		  
	
  <?php endif; ?>
  </div>
</div>

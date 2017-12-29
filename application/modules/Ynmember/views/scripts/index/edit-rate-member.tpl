<?php echo $this->form->render($this) ?>
<script type="text/javascript">
  window.addEvent('domready', function() {
	if($('0_0_1-wrapper'))
  	{
  		$('0_0_1-wrapper').setStyle('display','none');
  	}
  	
  	 function removeSubmit()
	  {
	   $('buttons-wrapper').hide();
	  }
  });
</script>
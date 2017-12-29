<?php if($this -> error) :?>

	<div class="tip">
		<span><?php echo $this -> message;?></span>
	</div>	
	
<?php else :?>

<?php echo $this->form->render($this) ?>
<script type="text/javascript">
  window.addEvent('domready', function() {
  	 function removeSubmit()
	  {
	   $('buttons-wrapper').hide();
	  }
  });
</script>

<?php endif;?>
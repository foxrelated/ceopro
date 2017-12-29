<?php echo $this->form->render($this) ?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		if ($('wishlist_id')) {
			$('wishlist_id').addEvent('change', function() {
				var value = this.value;
				if (value == '0') {
					$('title-wrapper').show();
					$('description-wrapper').show();
					$('view-wrapper').show();
				}
				else {
					$('title-wrapper').hide();
					$('description-wrapper').hide();
					$('view-wrapper').hide();
				}
 			});
		}
	});
</script>

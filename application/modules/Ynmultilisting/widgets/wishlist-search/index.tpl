<?php
    echo $this->form->render($this)
?>

<script type="text/javascript">
<?php
$session = new Zend_Session_Namespace('mobile');
if($session -> mobile): ?>
	window.addEvent('domready', function() {	
	    $$('#title').addEvent('change', function() {
	         $('filter_form').submit();
	    });
    });
<?php
endif;
?>
</script>
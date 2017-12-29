<div>
  <?php echo $this->form->render($this) ?>
</div>
<script type = "text/javascript">
window.addEvent('domready',function(){
	var count = '<?php echo $this->level?>';
	if (count != '') {
		var route = '<?php echo $this->route?>';
	//	en4.store.changeCategory($('location_id_' + count),'location_id','Socialstore_Model_DbTable_Locations',route);
	}
});
</script>
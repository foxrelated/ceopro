
<div class="">
<?php 
$menu = $this->partial('_options.tpl', array('page'=>$this->page, 'rootPage'=> $this->root_id));  
echo $menu;
?>
</div>

<script type="text/javascript">
  var tagAction =function(tag){
        window.location = en4.core.baseUrl + 'wiki/listing?tag=' + tag;  
  }
</script>

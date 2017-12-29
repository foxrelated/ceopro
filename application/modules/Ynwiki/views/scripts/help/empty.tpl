<?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>
<div class="tip"><span> <?php echo $this->translate("No item found.") ?> </span></div>
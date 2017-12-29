<?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>
<div class="global_content">
	<!--  do not remove this line  -->
	<a id="faq-0" name="faq-0"></a>
	<div class="layout_left">
		<?php echo $this->content()->renderWidget('ynwiki.help-navigator') ?>	
	</div>
	<div class="layout_middle">
		<h3><?php echo $this->item->getTitle() ?></h3>	
		<div>
			<?php echo $this->item->content ?>
		</div>
	</div>
</div>

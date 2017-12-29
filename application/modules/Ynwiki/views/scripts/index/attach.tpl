 <?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>
<div class="layout_left">
<?php 
$menu = $this->partial('_options.tpl', array('page'=>$this->page));  
echo $menu;
?>
</div>
<div class="layout_middle" style="float: left; width: 74%; padding-left: 10px;"> 
<?php 
$options = $this->partial('_page_detail.tpl', array('page'=>$this->page,'viewer'=>$this->viewer,'can_rate'=>$this->can_rate));  
echo $options;
?>
<?php echo $this->form->render($this) ?>
</div>
 <script type="text/javascript"> 
function removeSubmit()
{
   $('buttons-wrapper').hide(); 
}
</script> 
 <?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>
<?php 
$options = $this->partial('_page_detail.tpl', array('page'=>$this->page,'viewer'=>$this->viewer,'can_rate'=>$this->can_rate));  
echo $options;
?>
<div class="ynwiki_content">  
<?php echo $this->revision->body;?>
</div>

<h4 style="border: none;">  
<?php echo $this->htmlLink(array(
                  'action' => 'history',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), "&laquo; ".$this->translate('View Page History'), array(
                )) ?>
</h4>

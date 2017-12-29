<?php
$page_list = array();
if (isset($this->arr_pages)) {
    $page_list = $this->arr_pages;
} else if (isset($this->paginator)) {
    $page_list = $this->paginator;
}
if (count($page_list)<=0) { // no page
    if (isset($this->no_pages_message) && $this->no_pages_message) {
?>
<div class="tip">
      <span><?php echo $this->no_pages_message;?></span>
</div>
<?php
    }
    return;
}
?>
<div class = '<?php echo $this->css ?>' style="margin-bottom: 10px; overflow: hidden;">
<ul class="ynwiki_browse">
<?php $viewer = Engine_Api::_()->user()->getViewer();
    foreach($page_list as $page): 
        if(Engine_Api::_()->ynwiki()->checkparentallow($page, $viewer,'view') == true): ?>
<li class="ynwiki_wiki_space">
<div class='ynwiki_browse_photo'>
    <?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.icon')) ?>
</div>
<div class='ynwiki_browse_info' style="padding-top: 4px;">
    <p class='ynwiki_browse_info_title' title="<?php echo $page->title ?>">
        <?php echo $this->htmlLink($page->getHref(),$this->string()->truncate($page->title,55)); ?>
    </p>
</div>
</li>
<?php endif; ?>
<?php endforeach;?>
</ul> 
</div>
<div style="font-weight: bold; margin-bottom: 20px;">
    <?php echo $this->htmlLink(array(
                  'action' => 'listing',
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('More Wiki Spaces')." &raquo;", array(
                )) ?>
</div>
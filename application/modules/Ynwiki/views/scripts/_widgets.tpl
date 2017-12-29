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
<div class = '<?php echo $this->css ?>' style="margin-bottom: 15px; overflow: auto;">
<ul class="ynwiki_browse">
<?php foreach($page_list as $page): ?>
<li style="border: none;">
<div class='ynwiki_browse_photo'>
    <?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.icon')) ?>
</div>
<div class='ynwiki_browse_info' style="padding-top: 4px;">
    <p class='ynwiki_browse_info_title' title="<?php echo $page->title ?>">
        <?php echo $this->htmlLink($page->getHref(), strlen($page->title)>30?substr($page->title,0,27).'...':$page->title); ?>
    </p>
</div>
</li>
<?php endforeach;?>
</ul>
</div>
<?php $childPages = $this->page->getChilds();
$viewer = Engine_Api::_()->user()->getViewer();?>
<div style="padding-bottom: 10px; padding-top: 10px;">
<strong>
<?php echo $this->translate(array('%s Child Page', '%s Child Pages', count($childPages)), $this->locale()->toNumber(count($childPages))) ;?>
</strong> 
</div> 
<ul class="ynwiki_browse" style="margin-bottom: 20px;">
<?php foreach($childPages  as $child): ?>
<li>
    <?php echo $this->htmlLink($child->getHref(), $child->title) ?>
</li>
<?php endforeach;?>
</ul>
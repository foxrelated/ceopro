<?php $childPages = $this->page->getChilds();
$viewer = Engine_Api::_()->user()->getViewer();?>
<div style="padding-bottom: 10px; padding-top: 10px;">
<strong>
<?php echo $this->translate(array('%s Child Page', '%s Child Pages', count($childPages)), $this->locale()->toNumber(count($childPages))) ;?>
<?php if($this->page->authorization()->isAllowed($viewer,'edit') && $viewer->getIdentity() > 0 && Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynwiki_page', null, 'create')->checkRequire()):?>
 (<?php echo $this->htmlLink(array(
                  'action' => 'create',
                  'fromPageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Add A Child Page'), array(
                )) ?>)
<?php endif;?>  
<br/>
</strong> 
</div> 
<ul class="ynwiki_browse" style="margin-bottom: 20px;">
<?php foreach($childPages  as $child): ?>
<li style="border: none;">
 <div class='ynwiki_browse_photo'>
    <?php echo $this->htmlLink($child->getHref(), $this->itemPhoto($child, 'thumb.icon')) ?>
 </div>
<div class='ynwiki_browse_info' style="padding-top: 14px;">
    <p class='ynwiki_browse_info_title'>
    <?php echo $this->htmlLink($child->getHref(), $child->title) ?>
    </p>
</div>
</li>
<?php endforeach;?>
</ul>
<div>
<div style="padding: 5px; float: left;">
    <?php echo $this->htmlLink(array(
                  'action' => 'view',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('View full page'), array(
                )) ?>
</div>
<?php if($this->page->authorization()->isAllowed($this->viewer,'edit') && $this->viewer->getIdentity() > 0):?> 
    <div style="padding: 5px; text-align: right;">
    <?php echo $this->htmlLink(array(
                  'action' => 'edit',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Edit page'), array(
                )) ?>
    </div>
    <?php endif;?>
</div>
<div class="ynwiki_content">
<?php echo $this->page->body;?>
</div>
<div>
<?php 
 $creator =  Engine_Api::_()->getItem('user', $this->page->creator_id);
 echo $this->translate('Create by <b>%1$s</b> ', $this->htmlLink($creator->getHref(), $creator->getTitle(), array('target'=>'_top')));?> |
 <?php echo $this->timestamp($this->page->creation_date) ?>   
 <?php $revision = $this->page->getLastUpdated();
 if($revision):  ?>
  |
  <?php $owner =  Engine_Api::_()->getItem('user', $revision->user_id); 
 echo $this->translate(' Last updated by <b>%1$s</b> ',$this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle(), array('target'=>'_top')));?> 
 <?php echo $this->timestamp($revision->creation_date) ?>
  (<?php echo $this->htmlLink(array(
          'action' => 'compare-versions',
          'pageId' => $this->page->page_id,
          'route' => 'ynwiki_general',
          'reset' => true,
        ), $this->translate("view change"), array(
        )) ?>)
   <?php endif;?>  
 </div>
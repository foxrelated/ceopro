  <ul class="ynwiki_browse" style="padding-top: 10px;">
  <?php foreach( $this->pages as $item ): ?>
    <li>
      <div class='ynwiki_browse_photo'>
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
      </div>
      <div class='ynwiki_browse_info'>
        <p class='ynwiki_browse_info_title'>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </p>
        <p class='ynwiki_browse_info_date'>
          <?php echo $this->translate('Create by <b>%1$s</b> ', $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target'=>'_top')));?> 
          |
          <?php echo $this->timestamp($item->creation_date) ?> 
          <?php $revision = $item->getLastUpdated();
              if($revision):  ?>
              |
              <?php $owner =  Engine_Api::_()->getItem('user', $revision->user_id); 
             echo $this->translate(' Last updated by <b>%1$s</b> ',$this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle(), array('target'=>'_top')));?> 
              <?php echo $this->timestamp($revision->creation_date) ?>
              (<?php echo $this->htmlLink(array(
                      'action' => 'compare-versions',
                      'pageId' => $item->page_id,
                      'route' => 'ynwiki_general',
                      'reset' => true,
                    ), $this->translate("view change"), array(
                    )) ?>)
               <?php endif;?>
        </p>
        <?php foreach($item->getBreadCrumNode() as $node): ?>
            <?php echo $this->htmlLink($node->getHref(), $node->title) ?>
            &raquo;
            <?php endforeach; ?>
            <?php echo $this->htmlLink($item->getHref(), $item->title) ?>
      </div>
      <p class='ynwiki_browse_info_blurb' style="margin-left: 58px">
          <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
      </p>
    </li>
  <?php endforeach; ?>
</ul>
<?php
  // show view all
  if( $this->pages->getTotalItemCount() > 0 ):
  echo $this->htmlLink($this->url(array('action'=>'listing','edit'=>true,'profile' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'ynwiki_general'), $this->translate('View All')." &raquo;", array('class' => 'icon_ynwiki_viewall')) ?>
<?php endif;?>
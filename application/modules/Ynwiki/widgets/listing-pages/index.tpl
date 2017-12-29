  <script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  /*var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }*/
</script>
<div class='layout_middle'> 
<?php if( $this->pages->getTotalItemCount() > 0 ): ?>
    <ul class="ynwiki_browse" style="padding-top: 10px;">
    	
      <?php 
		$viewer = Engine_Api::_()->user()->getViewer();      	
        foreach( $this->pages as $item ):
      
        if(Engine_Api::_()->ynwiki()->checkparentallow($item, $viewer,'view') == true):
                
      ?>
        <li>
          <div class='ynwiki_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
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
            <?php endif; ?>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any pages that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any pages.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->pages, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
  </div>
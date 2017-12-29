 <?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>
 <script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  var tagAction =function(tag){
     window.location = en4.core.baseUrl + 'wiki/listing?tag=' + tag;
  }
</script>
<div class="generic_layout_container layout_main">
<div class='generic_layout_container layout_right'>
  <div style="margin-bottom: 15px;;">
  <?php echo $this->form->render($this) ?>
  </div>
   	<?php if(count($this->tags) > 0):?>
    <ul class = "global_form_box"  style="margin-bottom: 15px;">
    	<span id="script" style="font-size: 9pt;">
                 <?php 
                        $index = 0; $flag = false;
                        foreach($this->tags as $tag):
                          $index ++;
                          if(trim($tag->text) != ""):
                          if($index > 25 && $flag == false): $flag = true;?>
                              <p id="showlink" style="display: block; font-weight: bold">[<a id = 'title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('show all');?></a>]</p>
                              </span><span id="hide" style="display:none;font-size: 8pt;">
                          <?php  endif;?>
                 <span style="<?php if($tag->count > 99 && $tag->count < 599): echo "font-size:".($tag->count/80 + 8)."pt"; elseif($tag->count > 599): echo "font-size: 14pt"; endif; ?>">
              <a  href='javascript:void(0);'onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' ><?php echo $tag->text?></a> 
              </span>
                 <?php endif; endforeach;
                 if($flag == true):?>
                 <p id="hidelink" style="display: none;font-weight: bold">[<a id = 'title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('hide');?></a>]</p>
                 <?php endif; ?>
    	</span>
     </ul>
	<?php endif;?>
</div>
<div class='generic_layout_container layout_middle'> 
<?php if( $this->pages->getTotalItemCount() > 0 ): ?>
    <ul class="ynwiki_browse">
      <?php foreach( $this->pages as $item ):
                if(Engine_Api::_()->ynwiki()->checkparentallow($item, $viewer,'view') == true): 
       ?>   
        <li>
          <div class='ynwiki_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
          </div>
          <div class='ynwiki_browse_options'>
           <?php echo $this->htmlLink(array(
              'action' => 'un-follow',
              'pageId' => $item->getIdentity(),
              'route' => 'ynwiki_general',
              'reset' => true,
            ), $this->translate('Unfollow'), array(
              'class' => 'buttonlink smoothbox icon_ynwiki_unfollow',
            )) ?>
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
</div>
<script type="text/javascript">
function showhide(id)
{
    if (document.getElementById)
    {
        obj = document.getElementById(id);
        if (obj.style.display == "none")
        {
            obj.style.display = "";
            $('showlink').style.display = "none";
            $('hidelink').style.display = "";
        } else
        {
            obj.style.display = "none";
             $('showlink').style.display = "";
            $('hidelink').style.display = "none";
        }
    }
}
</script>
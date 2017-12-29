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
<!--
<h2>
<?php echo $this->translate("Wiki Detail") ?>
</h2>
-->
<?php 
$options = $this->partial('_page_detail.tpl', array('page'=>$this->page,'viewer'=>$this->viewer,'can_rate'=>$this->can_rate));  
echo $options;
?>
<div class="ynwiki_content">
<?php echo $this->page->body;?>
</div>
<br/>
<?php echo $this->translate("Tags: ");
if(count($this->tags) > 0):
foreach($this->tags as $tag): ?>
<a  href='javascript:void(0);'onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' ><?php echo $tag->text?></a> 
<?php endforeach;
else:
echo $this->translate("None");
endif; ?> 

<?php $attachment = $this->page->getAttachments();
      $viewer = Engine_Api::_()->user()->getViewer();?>
<div style="padding-bottom: 10px; padding-top: 10px;">
<strong>
<?php echo $this->translate(array('%s Attached File', '%s Attached Files', count($attachment)), $this->locale()->toNumber(count($attachment))) ;?>
 <?php if($this->page->authorization()->isAllowed($viewer,'edit') && $viewer->getIdentity() > 0):?> 
 (<?php echo $this->htmlLink(array(
                  'action' => 'attach',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Attach File'), array(
                )) ?>)
<?php endif;?>
<br/>
</strong> 
</div>
<?php  if(count($attachment) > 0): ?>   
<div class="ynwiki_content">  
<table>
<tr>
<th><?php echo $this->translate("Name")?></th>
<th><?php echo $this->translate("Size")?></th>
<th><?php echo $this->translate("Creator")?></th>
<th><?php echo $this->translate("Created Date")?></th>
<th></th>
</tr>
<?php 
foreach($attachment as $attach): ?>
<tr>
    <?php $file =  Engine_Api::_()->getItem('storage_file', $attach->file_id);?>
    <td><a href="./application/modules/Ynwiki/externals/scripts/download.php?f=<?php echo $file->storage_path ?>&fc='<?php echo $attach->title ?>'"> <?php echo $attach->title ?> </a></td>
    <td> <?php if($file->size < 1024*1024):
        echo round($file->size/(1024),2)." Kb";
        else:
        echo round($file->size/(1024*1024),2)." Mb"; 
        endif;?> 
    </td>
    <td>
        <?php
        $user = Engine_Api::_()->getItem('user', $attach->user_id); 
        echo $user;?>
    </td>
    <td>
         <?php echo $attach->creation_date;?>
    </td>
    <td style="padding-left: 10px; padding-right: 10px;">
    <?php if($this->page->authorization()->isAllowed($viewer,'edit') && $viewer->getIdentity() > 0):?>
    <?php echo $this->htmlLink(array(
                  'action' => 'delete-attach',
                  'attachId' => $attach->attachment_id,
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('delete'), array('class' => 'smoothbox'
                )) ?>
    <?php endif;?>
    </td>
</tr>
<?php
endforeach;
?>
</table>
</div>
<?php endif;?> 

<?php 
$menu = $this->partial('_childs.tpl', array('page'=>$this->page));  
echo $menu;
?>

<div style="margin-bottom: 10px; border-bottom: 1px solid #EAEAEA;"></div>

<?php echo $this->action("list", "comment", "core", array("type"=>"ynwiki_page", "id"=>$this->page->getIdentity())) ?>
</div>
<script type="text/javascript">
  var tagAction =function(tag){
        window.location = en4.core.baseUrl + 'wiki/listing?tag=' + tag;  
  }
</script>

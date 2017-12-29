 <?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;?>
<div class="layout_left">
<?php 
$menu = $this->partial('_options.tpl', array('page'=>$this->page));  
echo $menu;
?>
</div>
<div class="layout_middle" style="float: left; width: 74%; padding-left: 10px;"> 
<?php 
$options = $this->partial('_page_detail.tpl', array('page'=>$this->page,'viewer'=>$this->viewer,'can_rate'=>$this->can_rate));  
echo $options;
?>
<form onsubmit="return actionSelected();" id="action_selected" name="diff" method="POST" action="wiki/compare-versions/pageId/<?php echo $this->page->page_id?>">
 <button><?php echo $this->translate("Compare selected versions")?></button>
 <div style="padding-bottom: 10px; padding-top: 15px;">
 <h3><?php echo $this->translate("History")?></h3>
 </div> 
<table class="ynwiki_table">
<tr class="ynwiki_header">
<th></th>
<th><?php echo $this->translate("Version"); ?></th>
<th><?php echo $this->translate("Date"); ?></th>
<th><?php echo $this->translate("Changed By"); ?></th>
<th><?php echo $this->translate("Operations"); ?></th>
</tr>
<?php $index = $this->paginator->getTotalItemCount();
foreach($this->paginator as $revision): $index --;
$page = Engine_Api::_()->getItem('ynwiki_page', $revision->page_id);
 ?>
   <tr class="ynwiki_table_body">
   <td class="ynwiki_version" style="padding-left: 10px; width: 10px;">
   <input type="checkbox" onclick="checkCount(this)" name="<?php echo $index + 1;?>" value = "<?php echo $revision->revision_id?>" class="checkbox" ></td>
   <td>
   <?php
   $str = $this->translate("CURRENT");  
   if($this->page->revision_id == $revision->revision_id):
   $str = $this->translate("CURRENT");
   else:
   $str = $this->translate("v. %s",$index + 1);
   endif;
   ?>
   <?php echo $this->htmlLink(array(
          'action' => 'preview-revision',
          'revisionId' => $revision->revision_id,
          'route' => 'ynwiki_general',
          'reset' => true,
        ), $str, array(
        )) ?> 
  <?php if($this->page->revision_id == $revision->revision_id):  
   echo $this->translate("(v. %s)",$index + 1);
   endif;
   ?>
   </td>
   <td><?php echo  $this->timestamp($revision->creation_date);?> </td>
   <td><?php 
   $owner =  Engine_Api::_()->getItem('user', $revision->user_id);
   echo $this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle());
   ?> </td>
   <td>
   <?php if($this->page->revision_id != $revision->revision_id): ?>
     <?php echo $this->htmlLink(array(
          'action' => 'restore-revision',
          'revisionId' => $revision->revision_id,
          'route' => 'ynwiki_general',
          'reset' => true,
        ), $this->translate("Restore this version"), array(
        )) ?>
        <?php endif;?> 
   </td>
   </tr>
<?php endforeach;?>
</table>

 <input type="hidden" id="ids" name="ids" value=""/>
 <input type="hidden" id="versions" name="versions" value=""/>
</form>
<div style="padding-top: 5px;">
<h4 style="border: none;">
<?php echo $this->htmlLink($this->page->getHref(),"&laquo; ".$this->translate("Back To Wiki Detail")); ?>
</h4>
</div>
</div>
<script type="text/javascript">
function actionSelected(){
    var checkboxes = $('action_selected').elements;
    var selecteditems = [];
    var selectedversions = [];
    var count = 0;
    for (i = 1; i < checkboxes.length; i++) {   
      var checked = checkboxes[i].checked;
      var value = checkboxes[i].value;
      var version = checkboxes[i].name;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
        selectedversions.push(version);
        count ++;
      }
    }
    if(count != 2)
    {
        return false;
    }
    $('ids').value = selecteditems;
    $('versions').value = selectedversions;
    $('action_selected').submit();
  }
function checkCount(obj)
{
      var checkboxes = $('action_selected').elements;   
      var count = 0;
      for (i = 1; i < checkboxes.length; i++) { 
          var checked = checkboxes[i].checked;
          var value = checkboxes[i].value;
          if (checked == true && value != 'on'){
            count ++;
          }
      }
      if(count == 3)
      {
          obj.checked = false;
      }
}
</script>
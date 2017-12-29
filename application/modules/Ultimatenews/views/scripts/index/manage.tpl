<script type="text/javascript">
function multiDelete()
{  
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (inputs[i].checked == true){
        return confirm("<?php echo $this->translate("Are you sure you want to delete the selected news?") ?>");
    }
  }
  alert("<?php echo $this->translate("You don't choose any news to delete")?>");  
  return false;
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
var currentOrder = '<?php echo $this->filterValues['order'] ?>';
var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
var changeOrder = function(order, default_direction)
{
  // Just change direction
  if( order == currentOrder ) {
    $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    $('order').value = order;
    $('direction').value = default_direction;
  }
  $('admin_filter_form').submit();
}
</script>
<div id='global_content_wrapper'> 
	<div id='global_content'> 
		<div class='admin_search ultimatenews_filter_form'>   
			<?php  echo $this->form->render($this); ?>
		</div>
<?php 
if (isset($_SESSION['result'])):?>
<ul class="form-notices" style="width:99%">
    <li>
        <ul class="errors"> <li>
            <?php if ($_SESSION['result'] ==0):?>
    <?php echo $this->translate("Error occurs when delete the category !" );?>
    <?php else:?>
        <?php 
            switch($_SESSION['result'])
            {
                case 1:
                    echo $this->translate("Delete successfully.");break;
                case 2 :
                    echo $this->translate("Update status successfully. ");break;
                default:
                    break;
            }
        ?>
    <?php endif;?>  
       </li>  </ul>
    </li>
</ul>
<?php  unset($_SESSION['result']); $_SESSION['result'] = null;   ?>
<?php endif;?>    
<?php if( count($this->paginator) ): ?>
<div class="ynulnews_manage_new_table">
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete();">
  <div>
  <table class='ultimatenews_admin_table'>
    <thead>
      <tr>
        <th width="2%" class='ultimatenews_admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th width="3%" class='ultimatenews_admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('content_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th width="15%"><a href="javascript:void(0);" onclick="javascript:changeOrder('feed_name', 'DESC');"><?php echo $this->translate("Feed") ?></a></th>
        <th width="50%"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Title") ?></a></th>        
        <th width="15%"><a href="javascript:void(0);" onclick="javascript:changeOrder('pubDate_parse', 'DESC');"><?php echo $this->translate("Posted Date") ?></a></th>
        <th width="5%"><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
        <th width="5%"><a href="javascript:void(0);" onclick="javascript:changeOrder('is_featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th>
        <th width="20%"><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->content_id;?>' value='<?php echo $item->content_id ?>' class='checkbox' value="<?php echo $item->content_id ?>"/></td>
          <td><?php echo $item->content_id ?></td>
          <td><?php $category = Engine_Api::_()->ultimatenews()->getAllCategories(array('category_id'=>$item->category_id,)); if(isset($category[0])){ echo($category[0]['category_name']);} else {echo "";} ?></td>
          <td><?php echo $item->title; ?></td>          
          <td><?php echo $item->pubDate_parse?></td>
          <td>
            <?php if ( $item->approved == 1):?>
                <?php echo $this->translate('Approved')?>
            <?php elseif($item->approved == -1):?>
            	<?php echo $this->translate('Denied')?>
            <?php else:?>
                <?php echo $this->translate('Pending')?>
            <?php endif;?>
          </td>
          <td>
            <?php if ( $item->is_featured == 1):?>
                <?php echo $this->translate('Yes')?>
            <?php else:?>
                <?php echo $this->translate('No')?>
            <?php endif;?>
          </td>
          <td>            
                  <a href="<?php echo 
                    $this->url(array('content_id'=>$item->content_id, 'format' => 'smoothbox'), 'ultimatenews_edit_ultimatenews') ?>" class = 'smoothbox '><?php echo $this->translate('Edit') ?> </a>
                            | 
                  <?php echo $this->htmlLink($item->getHref(), $this->translate("View")) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  
    <div class='buttons clearfix'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
      <button type='button' id='buttonfeatured' onclick ="getUltimatenewsSelect(1);if (is_select){document.getElementById('is_set_featured_form').submit();}" ><?php echo $this->translate("Set Featured") ?></button>
      <button type='button' id='buttonunfeatured' onclick ="getUltimatenewsSelect(0);if (is_select){document.getElementById('is_set_featured_form').submit();}" ><?php echo $this->translate("Unset Featured") ?></button>
      <button type='button' id='buttonapprove' onclick ="getUltimatenewsSelect(1);if (is_select){document.getElementById('is_set_approve_form').submit();}" ><?php echo $this->translate("Approve") ?></button>
      <button type='button' id='buttondeny' onclick ="getUltimatenewsSelect(0);if (is_select){document.getElementById('is_set_approve_form').submit();}" ><?php echo $this->translate("Deny") ?></button>
    </div>
  </form>
   <form method="post" id ="is_set_featured_form" name ="is_set_featured_form" action="<?php echo $this->url(array('module'=>'ultimatenews','controller'=>'index','action'=>'featured'),'ultimatenews_general');?>" >
        <input type="hidden" value="1" name="is_set_featured" id="is_set_featured"/>
        <input type="hidden" value="" name="ultimatenews_featured" id="ultimatenews_featured"/>
    </form>
    
    <form method="post" id ="is_set_approve_form" name ="is_set_approve_form" action="<?php echo $this->url(array('module'=>'ultimatenews','controller'=>'index','action'=>'approve'),'ultimatenews_general');?>" >
        <input type="hidden" value="1" name="is_set_approve" id="is_set_approve"/>
        <input type="hidden" value="" name="ultimatenews_approve" id="ultimatenews_approve"/>
    </form>
  </div>
  <div>
    <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no news got from remote servers.") ?>
    </span>
  </div>
<?php endif; ?>
</div>
</div>
<script type="text/javascript">
var is_select = false;
function getUltimatenewsSelect(active)
{
    var Checkboxs = document.getElementById('multidelete_form').elements;
    var values = "";
    for(var i = 0; i < Checkboxs.length; i++) 
    {    
         var type = Checkboxs[i].type;
         if (type=="checkbox" && Checkboxs[i].checked)
         {                 
               values += "," + Checkboxs[i].value;                
         }
         
    }
    if(values != "")
    {
        values = "(" + values + ",)";
        is_select = true;
    }
    else
    {
        is_select = false;
        alert('<?php echo $this->translate("Please select a news!")?>');
    }
    if ( active > -1)
    {
        $('is_set_featured').value = active;
        $('ultimatenews_featured').value =values;  
        $('is_set_approve').value = active;
        $('ultimatenews_approve').value =values;      
    }
    else
    {
        return is_select ;
    }
    return false;
}
</script>

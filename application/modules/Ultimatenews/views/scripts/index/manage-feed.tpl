
<script>
    function getrssSelect(active, formID)
    {
            var Checkboxs = $('multidelete_form').elements;
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
            }
            else
            {
            	alert('<?php echo $this->translate('Please select a feed!'); ?>');
            	return false;
            }
            $('is_active_name').value = active;
            $('categories_active').value = values;
            $('is_approve_name').value = active;
            $('categories_approve').value = values;
            $(formID).submit();
            return true;
    }
	
	function removeNews(url)
	{
		var Checkboxs = $('multidelete_form').elements;
		var values = "";
		for(var i = 0; i < Checkboxs.length; i++) 
		{	
			 var type = Checkboxs[i].type;
		     if (type=="checkbox" && Checkboxs[i].checked)
			 {				 
		       	values += "," + Checkboxs[i].value;				
		     }
		}
        if(values == "")
        {
            alert("You don\'t choose any category to remove news");  
            return false;
        }
		else if(values != "")
		{
			values = "(" + values + ")";
		}
		url += "?cat=" + values;
		Smoothbox.open(url);
	}
	
	function getData(url)
	{
		var Checkboxs = $('multidelete_form').elements;
		var values = "";
		for(var i = 0; i < Checkboxs.length; i++) 
		{	
			 var type = Checkboxs[i].type;
		     if (type=="checkbox" && Checkboxs[i].checked)
			 {				 
		       	values += "," + Checkboxs[i].value;				
		     }
		}
        if(values == "")
        {
            alert("You don\'t choose any category to get data");  
            return false;
        }
		else if(values != "")
		{
			values = "(" + values + ")";
		}
		url += "?cat=" + values;
		Smoothbox.open(url);
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

<script type="text/javascript">

function multiDelete()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (inputs[i].checked == true){
        return confirm("<?php echo $this->translate("Are you sure you want to delete the selected RSS Feed?") ?>");
    }
  }
  alert("You don\'t choose any rss feed to delete");  
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
</script>
<div id='global_content_wrapper'> 
    <div id='global_content'> 
<?php 
if (isset($_SESSION['result'])):?>
<ul class="form-notices">
    <li>
        <ul class="errors"> <li>
            <?php if ($_SESSION['result'] == 0):?>
    <?php echo $this->translate("Error occurs when delete the feed!" );?>
    <?php else:?>
        <?php 
            switch($_SESSION['result'])
            {
                case 1:
                    echo $this->translate("Delete successfully.");break;
                case 2 :
                    echo $this->translate("Update status successfully.");break;
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
<div class='admin_search ultimatenews_filter_form'>   
		<?php  echo $this->form->render($this); ?>
</div>
<?php if( $this->paginator -> getTotalItemCount()): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <div>
  <table class='ultimatenews_admin_table'>
    <thead>
      <tr>
        <th width="2%" class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th width="3%" class='admin_table_short' ><a href="javascript:void(0);" onclick="javascript:changeOrder('category_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>       
        <th width="10%" ><a href="javascript:void(0);" onclick="javascript:changeOrder('category_name', 'DESC');"><?php echo $this->translate("Feed") ?></a></th>
        <th width="30%" ><a href="javascript:void(0);" onclick="javascript:changeOrder('url_resource', 'DESC');"><?php echo $this->translate("Feed URL") ?></a></th>
        <th width="15%" ><a href="javascript:void(0);" onclick="javascript:changeOrder('category_parent_id 	', 'DESC');"><?php echo $this->translate("Category") ?></a></th>
        <th width="15%" ><a href="javascript:void(0);" onclick="javascript:changeOrder('posted_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
        <th width="10%" ><?php echo $this->translate("Status") ?></th> 
        <th width="10%" ><?php echo $this->translate("Logo") ?></th> 
        <th width="25%" ><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' 
                      name='delete_<?php echo $item->category_id;?>' 
                      value='<?php echo $item->category_id ?>' class='checkbox' />
          </td>
          <td><?php echo $item->category_id ?></td>
          <td><?php echo wordwrap($item->category_name,30,"<br />\n",TRUE);?></td>
          <td><?php echo wordwrap($item->url_resource,40,"<br />\n",TRUE); ?></td>
          <td><?php if ($item->category_parent_id > 0 ):?> 
                <?php
                    $table = Engine_Api::_()->getDbtable('Categoryparents', 'ultimatenews');

                    $select = $table->select('engine4_ultimatenews_categoryparents ')->setIntegrityCheck(false)
                    ->where('engine4_ultimatenews_categoryparents.category_id = ? ', $item->category_parent_id)
                    ->limit(1);
                    $items = $table->fetchAll($select);
                    $this->category_parent = $items ;
                    foreach ( $this->category_parent as $category_parent)
                    	echo wordwrap($category_parent->category_name,30,"<br />\n",TRUE);
                ?>
                <?php else:?>
                <?php 	echo $this->translate("Other");?>
                <?php endif;?>
                
          </td>
          <td><?php echo $this->locale()->toDateTime($item->posted_date) ?></td>
          <td>
          	<?php if($item -> approved == 1):
	          	if ( $item->is_active == 1) :?> 
	          		<?php echo $this->translate("Actived")?>
	          	<?php else:?>
	          		<?php echo $this->translate("Inactived")?> 
	          	<?php endif;?>
	         <?php else:?>
	         	<?php if ( $item->approved == 0):?>
	                <?php echo $this->translate('Pending')?>
	            <?php elseif($item->approved == -1):?>
	            	<?php echo $this->translate('Denied')?>
	            <?php endif;?>
	         <?php endif;?>
          </td>
          <td><?php if ( $item->category_logo == "") :?> <?php echo $this->translate("No logo found")?><?php else:?><?php echo "<img src='".$item->category_logo."' alt='logo_".wordwrap($item->category_name,10,"\n",TRUE)."'/>"?> <?php endif;?></td>
          <td align="center">          
              <span><a class='smoothbox'  href="<?php echo $this->url(array('action' => 'edit', 'id' => $item->category_id), 'ultimatenews_manage_action', true);?>" ><?php echo $this->translate('Edit')?></a></span>
          </td>          
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
         
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
           <button type='button' id='buttonactive' onclick ="return getrssSelect(1, 'is_active_form');" ><?php echo $this->translate("Active Selected") ?></button>
      <button type='button' id='buttondeactive' onclick ="return getrssSelect(0, 'is_active_form');" ><?php echo $this->translate("Inactive Selected") ?></button>
      <button type='button' id='buttonapprov' onclick ="return getrssSelect(1, 'is_approve_form');" ><?php echo $this->translate("Approve Selected") ?></button>
      <button type='button' id='buttondeny' onclick ="return getrssSelect(-1, 'is_approve_form');" ><?php echo $this->translate("Deny Selected") ?></button>
      <button type='button' id='buttondremovenews' onclick ="removeNews('<?php echo $this->url(array('action' => 'removenews'), 'ultimatenews_manage_action', true);?>')" ><?php echo $this->translate("Remove News") ?></button>
      <button type='button' id='buttongetdata' onclick="getData('<?php echo $this->url(array('action' => 'getdata'), 'ultimatenews_manage_action', true);?>')"> <?php echo $this->translate("Get Data") ?></button>    
    </div>
  </form>
  
   <form method="post" id ="is_active_form" name ="is_active_form" action="<?php echo $this->url(array('action'=>'activerss', 'actionBack' => 'manage-feed') , 'ultimatenews_manage_action', true);?>" >
        <input type="hidden" value="1" name="is_active_name" id="is_active_name"/>
        <input type="hidden" value="" name="categories_active" id="categories_active"/>
    </form>
    
   <form method="post" id ="is_approve_form" name ="is_approve_form" action="<?php echo $this->url(array('action'=>'approve-rss') , 'ultimatenews_manage_action', true);?>" >
        <input type="hidden" value="1" name="is_approve_name" id="is_approve_name"/>
        <input type="hidden" value="" name="categories_approve" id="categories_approve"/>
    </form>
  <br />

  <div>
    <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no feeds posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
</div>
</div>
</style>   
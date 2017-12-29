<script>
    function getrssSelect(active)
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
            $('is_active_name').value =active;
            $('categories_active').value =values;
            return false;
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
		//url += "/cat/" + values;
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
      $('filter_form').submit();
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
<h2><?php echo $this->translate("Ultimate News Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<?php 
if (isset($_SESSION['result'])):?>
<ul class="form-notices">
    <li>
        <ul class="errors"> <li>
            <?php if ($_SESSION['result'] ==0):?>
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
<div class='admin_search' style="margin-bottom: 5px;">   
		<?php  echo $this->form->render($this); ?>
		</div>
<?php if( count($this->paginator) ): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='ultimatenews_admin_table'>
    <thead>
      <tr>
        <th width="2%" class='ultimatenews_admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th width="3%" class='ultimatenews_admin_table_short' style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('category_id', 'DESC');"><?php echo $this->translate("ID") ?></th>       
        <th width="10%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('category_name', 'DESC');"><?php echo $this->translate("Feed") ?></th>
        <th width="30%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('url_resource', 'DESC');"><?php echo $this->translate("Feed URL") ?></th>
        <th width="15%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('category_parent_id 	', 'DESC');"><?php echo $this->translate("Category") ?></th>
        <th width="15%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('posted_date', 'DESC');"><?php echo $this->translate("Date") ?></th>
        <th width="10%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('is_active', 'DESC');"><?php echo $this->translate("Status") ?></th> 
        <th width="10%" style="text-align: left;"><?php echo $this->translate("Logo") ?></th> 
        <th width="25%" style="text-align: left;"><?php echo $this->translate("Options") ?></th>
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
                <?php endif;?></td>
          <td><?php echo $this->locale()->toDateTime($item->posted_date) ?></td>
          <td><?php if ( $item->is_active == 1) :?> <?php echo $this->translate("Active")?><?php else:?><?php echo $this->translate("Inactive")?> <?php endif;?></td>
          <td><?php if ( $item->category_logo == "") :?> <?php echo $this->translate("No logo found")?><?php else:?><?php echo "<img src='".$item->category_logo."' width='80px' height='50px' alt='logo_".wordwrap($item->category_name,10,"\n",TRUE)."'/>"?> <?php endif;?></td>
          <td align="center">          
              <span><a class='smoothbox'  href="<?php echo $this->url(array('action' => 'edit', 'id' => $item->category_id));?>" ><?php echo $this->translate('Edit')?></a></span>
          </td>          
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
      <br />
         
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>&nbsp;&nbsp;
      <button type='button' id='buttonactive' onclick ="getrssSelect(1);document.getElementById('is_active_form').submit();" ><?php echo $this->translate("Active Selected") ?></button>&nbsp;&nbsp;
      <button type='button' id='buttondeactive' onclick ="getrssSelect(0);document.getElementById('is_active_form').submit();" ><?php echo $this->translate("Inactive Selected") ?></button>&nbsp;&nbsp;
      <button type='button' id='buttondremovenews' onclick ="removeNews('<?php echo $this->url(array('action' => 'removenews'));?>')" ><?php echo $this->translate("Remove News") ?></button>&nbsp;&nbsp;
      <button type='button' id='button' onclick="getData('<?php echo $this->url(array('action' => 'getdata'));?>')"> <?php echo $this->translate("Get Data") ?></button>    
    </div>
  </form>
  
    <form method="post" id ="is_active_form" name ="is_active_form" action="<?php echo $this->url(array('action'=>'activerss'));?>" >
        <input type="hidden" value="1" name="is_active_name" id="is_active_name"/>
        <input type="hidden" value="" name="categories_active" id="categories_active"/>
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

<?php
$this->headLink()
    ->appendStylesheet($this->baseUrl() . '/application/modules/Ultimatenews/externals/styles/main.css');
?>

<script type="text/javascript">

    function multiDelete()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length; i++) {
            if (inputs[i].checked == true){
                return confirm("<?php echo $this->translate("Are you sure you want to delete the selected categories?") ?>");
            }
        }
        alert("You don\'t choose any category to delete");
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
        $('admin_createcategory_form').submit();
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

        <?php if (isset($_SESSION['result'])):?>
            <ul class="form-notices">
                <li>
                    <ul class="errors"> <li>
                            <?php if ($_SESSION['result'] ==0):?>
                                <?php echo $this->translate("Error occurs when delete the category!" );?>
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
        <?php echo $this->htmlLink(array(
            'module' => 'ultimatenews',
            'controller' => 'manage',
            'action' => 'create-category'),
            '<button>'.$this->translate('Add New Category').'</button>', array('class' => 'smoothbox',)) ?>
        <?php if( count($this->paginator) ): ?>
            <form id='multidelete_form' style="margin-top: 15px; width: 100%; background: none; border: none; padding-left: 0px" method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
                <table class='ultimatenews_admin_table'>
                    <thead>
                    <tr>
                        <th width="2%" class='ultimatenews_admin_table_short' ><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                        <th width="15%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('category_name', 'DESC');"><?php echo $this->translate("Name") ?></a></th>
                        <th width="50%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('category_description', 'DESC');"><?php echo $this->translate("Description") ?></a></th>
                        <th width="10%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('total_feed', 'DESC');"><?php echo $this->translate("Total Feed") ?></a></th>
                        <th width="5%" style="text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('is_active', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
                        <th width="10%" style="text-align: left;"><?php echo $this->translate("Options") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->paginator as $item): ?>
                        <tr>
                            <td><input type='checkbox' name='delete_<?php echo $item->category_id;?>' value='<?php echo $item->category_id ?>' class='checkbox' value="<?php echo $item->category_id ?>"/></td>
                            <td><?php echo wordwrap($item->category_name,50,"<br />\n",TRUE); ?></td>
                            <td><?php echo wordwrap($item->category_description,50,"<br />\n",TRUE); ?></td>
                            <td>
                                <?php
                                echo $item->total_feed;
                                ?>
                            </td>
                            <td><?php if ( $item->is_active == 1) :?> <?php echo $this->translate("Active")?><?php else:?><?php echo $this->translate("Inactive")?> <?php endif;?></td>
                            <td align="center">
                                <span><a class='smoothbox'  href="<?php echo $this->url(array('action' => 'edit-category', 'id' => $item->category_id));?>" ><?php echo $this->translate('edit'); ?></a></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <br />

                <div class='buttons' style="border: none; padding: 0px">
                    <button type='submit' id="buttondelete" name="buttondelete"><?php echo $this->translate("Delete Selected") ?></button>&nbsp;&nbsp;
                    <button type='button' id='buttonactive' onclick ="getrssSelect(1);document.getElementById('is_active_form').submit();" ><?php echo $this->translate("Active Selected") ?></button>&nbsp;&nbsp;
                    <button type='button' id='buttondeactive' onclick ="getrssSelect(0);document.getElementById('is_active_form').submit();" ><?php echo $this->translate("Inactive Selected") ?></button>&nbsp;&nbsp;
                </div>
            </form>

            <form method="post" id ="is_active_form" style="background: none" name ="is_active_form" action="<?php echo $this->url(array('action'=>'caactiverss'));?>" >
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
      <?php echo $this->translate("There are no categories posted by your members yet.") ?>
    </span>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    function getrssSelect(active)
    {
        var Checkboxs = document.getElementById('multidelete_form').elements;
        console.log(Checkboxs);
        var values = "";
        for(var i = 0; i < Checkboxs.length; i++)
        {

            var type = Checkboxs[i].type;
            console.log(type);
            if (type=="checkbox" && Checkboxs[i].checked)
            {
                values += "," + Checkboxs[i].value;
            }
        }
        console.log(values);
        if(values != "")
        {
            values = "(" + values + ",)";
        }
        else
        {
            alert('<?php echo $this->translate('Please select a category!'); ?>');
            return false;
        }
        $('is_active_name').value =active;
        $('categories_active').value =values;
        return false;
    }
</script>

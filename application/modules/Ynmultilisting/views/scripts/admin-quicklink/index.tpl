<script type="text/javascript">
function selectAll() {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.getElements('input.checkbox[type=checkbox]');
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function deleteSelected(){
    var checkboxes = $$('td input.checkbox[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
        var checked = item.checked;
        var value = item.value;
        if (checked == true && value != 'on'){
            selecteditems.push(value);
        }
    });
    $('multidelete').action = en4.core.baseUrl +'admin/ynmultilisting/quicklink/multidelete';
    $('ids').value = selecteditems;
    $('multidelete').submit();
}

</script>
<h2>
    <?php echo $this->translate('YouNet Multiple Listings Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Listing Quick Links') ?></h3>

<?php if ($this->error): ?>
<div class="tip">
    <span><?php echo $this->message;?></span>
</div>
<?php else: ?>
    
<div id="listing-type">
    <span><?php echo $this->translate('Listing type: %s', $this->htmlLink($this->listingType->getHref(), $this->listingType->getTitle()))?></span>
</div>

<p><?php echo $this->translate("YNMULTILISTING_MANAGE_QUICKLINK_DESCRIPTION") ?></p>

<div class="add_link">
<?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'quicklink', 'action' => 'create', 'listingtype_id' => $this->listingType->getIdentity()),
    '<i class="fa fa-plus-square"></i>'.$this->translate('Add New Quick Link'), 
    array()) ?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multidelete' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="listingtype_id" name="listingtype_id" value="<?php echo $this->listingType->getIdentity()?>"/>
</form>
<form id='multidelete_form' class="ynadmin-table" method="post" action="<?php echo $this->url();?>">
    <table class='admin_table'>
        <thead>
            <tr>
                <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <th><?php echo $this->translate("Quick Link Name") ?></th>
                <th><?php echo $this->translate("Total Listings") ?></th>
                <th><?php echo $this->translate("Show") ?></th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->paginator as $item): ?>
            <tr>
                <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
                <td><?php echo $this->translate($item->getTitle()) ?></td>
                <td><?php echo $item->getTotalListings() ?></td>
                <td><input type="checkbox" rel="<?php echo $item->getIdentity()?>" class="show-checkbox" <?php if ($item->show) echo 'checked'?>/></td>
                <td>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'quicklink', 'action' => 'edit', 'id' => $item->getIdentity()),
                    $this->translate('edit')
                )?>
                |
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'quicklink', 'action' => 'delete', 'id' => $item->getIdentity()),
                    $this->translate('delete'),
                    array('class' => 'smoothbox')
                )?> 
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</form>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
<div class='buttons'>
    <button type='button' onclick="deleteSelected()"><?php echo $this->translate('Delete Selected') ?></button>
</div>

<br/>
<div><?php echo $this->paginationControl($this->paginator); ?></div>
<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate("There are no Quick Links.") ?></span>
</div>
<?php endif; ?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		$$('input.show-checkbox[type="checkbox"]').addEvent('click', function() {
			var value = (this.checked) ? 1 : 0;
			var id = this.get('rel');
		    new Request.JSON({
		        url: '<?php echo $this->url(array('module'=>'ynmultilisting','controller'=>'quicklink','action'=>'show'), 'admin_default', true)?>',
		        method: 'post',
		        data: {
		            'id': id,
		            'value': value
		        },
		        'onSuccess' : function(responseJSON, responseText) {
		      		if (responseJSON.error) alert(responseJSON.message);
		        }
		    }).send();
		});
	});
</script>
<?php endif; ?>
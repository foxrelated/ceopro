<h3><?php echo $this->translate('Import Listings') ?></h3>
<p style="line-height: 1.5em; margin-bottom: 10px"><?php echo $this->translate("YNMULTILISTING_IMPORT_DESCRIPTION") ?></p>
<div id="ynmultilisting-import-tab">
    <div id="ynmultilisting-file-tab">
        <?php echo $this->htmlLink(array('route' => 'ynmultilisting_import', 'action' => 'file'), $this->translate('Import Listings From Files'))?>
    </div>
    <div id="ynmultilisting-module-tab" class="active">
        <?php echo $this->htmlLink(array('route' => 'ynmultilisting_import', 'action' => 'module'), $this->translate('Import Listings From Modules'))?>
    </div>
</div>

<?php if ($this->error): ?>
<div class="tip">
    <span><?php echo $this->message;?></span>
</div>
<?php else: ?>
<?php if (!empty($this->importMessage)) :?>
<div class="tip">
	<span><?php echo $this->importMessage;?></span>
</div>
<?php endif;?>

<div class="module-import-search">
<?php echo $this->form->render($this);?>
</div>

<?php if (count($this->paginator)) :?>
<script type="text/javascript">
function selectAll() {
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.getElements('input.checkbox[type=checkbox]');
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}
</script>
<form id='multiselect_form' class="global_form_popup" method="post">
    <input type="hidden" name="module_id" value="<?php echo $this->module->getIdentity()?>" />
    <input type="hidden" name="category_id" value="<?php echo $this->category->getIdentity()?>" />
    <div class="total-description"><?php echo $this->translate(array('Total %s item can import from module %s.', 'Total %s items can import from module %s.', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount(), $this->module->getTitle())?></div>
    
    <table class="ynmultilisting frontend-table">
    	<tr>
    		<th><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
    		<th><?php echo $this->translate('Item')?></th>
    		<th><?php echo $this->translate('Imported')?></th>
    	</tr>
    	
    	<?php foreach ($this->paginator as $item) :?>
    	<tr>
    		<td><input type='checkbox' class='checkbox' name='item_ids[]' value="<?php echo $item->getIdentity(); ?>" /></td>
    		<td><?php echo $item?></td>
    		<td>
    			<?php if (in_array($item->getIdentity(), $this->importedIds)) :?>
    			<?php echo $this->translate('Yes') ?> (<?php echo $this->htmlLink(array(
    				'route' => 'ynmultilisting_import',
    				'action' => 'view-imported-listings',
    				'module_id' => $this->module->getIdentity(),
    				'item_id' => $item->getIdentity()
				), $this->translate('view listing(s)'), array('class'=>'smoothbox'))?>)
    			<?php else: echo $this->translate('No'); endif;?>
    		</td>
    	</tr>
    	<?php endforeach;?>
    </table>
    
    <div class="buttons">
	    <button type="submit"><?php echo $this->translate('Import')?></button>
	    <span><?php echo $this->translate(' or ')?></span>
	    <span><a href="<?php echo $this->url(array('action' => 'module'), 'ynmultilisting_import', true)?>"><?php echo $this->translate('Back')?></a></span>
	</div>
</form>

<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>

<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate('Cannot found any items for importing from %s.', $this->module->getTitle())?></span>
</div>
<a href="<?php echo $this->url(array('action' => 'module'), 'ynmultilisting_import', true)?>"><?php echo $this->translate('Back')?></a>    
<?php endif;?>
<?php endif;?>
<script type="text/javascript">

function selectAll() {
    var hasElement = false;
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled && inputs[i].hasClass('multiselect_checkbox')) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function multiSelected(action){
    var checkboxes = $$('td input.multiselect_checkbox[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('multiselect').action = en4.core.baseUrl +'admin/ynmultilisting/listings/multiselected';
    $('ids').value = selecteditems;
    $('select_action').value = action;
    $('multiselect').submit();
}

function featureListing(obj, id) {
    var value = (obj.checked) ? 1 : 0;
    var url = en4.core.baseUrl+'admin/ynmultilisting/listings/feature';
    new Request.JSON({
        url: url,
        method: 'post',
        data: {
            'id': id,
            'value': value
        }
    }).send();
}

function highlightListing(id) {
    var url = en4.core.baseUrl+'admin/ynmultilisting/listings/highlight';
    new Request.JSON({
        url: url,
        method: 'post',
        data: {
            'id': id
        }
    }).send();
}

function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
    // Just change direction
    if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } 
    else {
        $('order').value = listby;
        $('direction').value = default_direction;
    }
    $('filter_form').submit();
}

function filterCategory()
{
    type_id = $("listingtype_id").value;
    if (type_id != '0')
    {
        new Request.HTML({
            method: 'post',
            url: '<?php echo $this->url(Array('action'=>'filter-category', 'controller' => 'index'), 'ynmultilisting_general', true);?>',
            data: {
                format: 'html',
                type_id : type_id
            },
            onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript)
            {
                $("category_id").set('html', responseHTML);
            }
        }).send();
    } else {
    	$("category_id").set('html', '<option value="all"><?php echo $this->translate('All')?></option>');
    }
}

</script>
<h2>
    <?php echo $this->translate('YouNet Listings Plugin') ?>
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

<h3><?php echo $this->translate('Manage Listings') ?></h3>
<br/>
<p><?php echo $this->translate("YNMULTILISTING_MANAGE_LISTINGS_DESCRIPTION") ?></p>
<br/>
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br/>
<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>
<div class="admin_table_form">
<form id='multiselect_form' method="post" action="<?php echo $this->url();?>">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('listing.title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('category.title', 'ASC');"><?php echo $this->translate("Category") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('listing.creation_date', 'ASC');"><?php echo $this->translate("Creation Date") ?></a></th>
      <th><?php echo $this->translate("Approved Status") ?></th>
      <th><?php echo $this->translate("Listing Status") ?></th>
      <th><?php echo $this->translate("Featured") ?></th>
      <th><?php echo $this->translate("Highlight") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
    <?php $showFeature = ($item -> approved_status == 'approved' && $item -> status == 'open');?>
      <tr>
        <td><input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/></td>
        <td><?php echo $item;?></td>
        <td><?php echo $item->getOwner() ?></td>
        <td><?php echo $item->getCategoryTitle() ?></td>
        <td><?php echo $this->locale()->toDate(strtotime($item->creation_date)) ?></td>
        <td><?php echo ucfirst($this->translate($item->approved_status)) ?></td>
        <td><?php echo ucfirst($this->translate($item->status)) ?></td>
        <td><input type="checkbox" <?php echo (!$showFeature) ? 'disabled="true"' : ''; ?> class="featured_checkbox" value="1" onclick="featureListing(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->featured) echo 'checked'?>/></td>
        <td><input type="radio" <?php echo (!$showFeature) ? 'disabled="true"' : ''; ?> class="highlight_radio" name="highlight_radio" value="1" onclick="highlightListing('<?php echo $item->getIdentity()?>')" <?php if ($item->highlight) echo 'checked'?>/></td>
        <td>
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listings', 'action' => 'view-info', 'id' => $item->getIdentity()),
                $this->translate('View General Info'),
                array())
            ?>

            <?php if ($item->isAllowed('edit')) : ?>
             |
                <?php echo $this->htmlLink(
                    array('route' => 'ynmultilisting_specific', 'action' => 'edit', 'listing_id' => $item->getIdentity(), 'listingtype_id' => $item->listingtype_id),
                    $this->translate('Edit'),
                    array())
                ?>
            <?php endif; ?>

            <?php if ($item -> approved_status == 'pending' && $item -> status == 'open'): ?>
             |
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listings', 'action' => 'approve', 'id' => $item->getIdentity()),
                    $this->translate('Approve'),
                    array('class' => 'smoothbox'))
                ?>
             |
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listings', 'action' => 'deny', 'id' => $item->getIdentity()),
                    $this->translate('Deny'),
                    array('class' => 'smoothbox'))
                ?>
            <?php endif; ?>

            <?php if ($item -> approved_status == 'approved' && $item -> status == 'open'): ?>
             |
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listings', 'action' => 'close', 'id' => $item->getIdentity()),
                    $this->translate('Close'),
                    array('class' => 'smoothbox'))
                ?>
            <?php endif; ?>

            <?php if ($item->isAllowed('delete')) : ?>
             |
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listings', 'action' => 'delete', 'id' => $item->getIdentity()),
                    $this->translate('Delete'),
                    array('class' => 'smoothbox'))
                ?>
            <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo ($this->translate('Total').' '.$total.' '.$this->translate('result(s)'));
    echo '</p>';
}?>
</form>
</div>
<br/>
<div class='buttons'>
    <button type='button' onclick="multiSelected('Delete')"><?php echo $this->translate('Delete Selected') ?></button>
    <button type='button' onclick="multiSelected('Approve')"><?php echo $this->translate('Approve Selected') ?></button>
    <button type='button' onclick="multiSelected('Deny')"><?php echo $this->translate('Deny Selected') ?></button>
    <button type='button' onclick="multiSelected('Feature')"><?php echo $this->translate('Feature Selected') ?></button>
    <button type='button' onclick="multiSelected('Unfeature')"><?php echo $this->translate('Unfeature Selected') ?></button>
    <button type='button' onclick="multiSelected('Open')"><?php echo $this->translate('Open Selected') ?></button>
    <button type='button' onclick="importListing()"><?php echo $this->translate('Import Listings') ?></button>
</div>

<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no Listings.') ?>
    </span>
  </div>
<?php endif; ?>

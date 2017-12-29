<script src="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynmultilisting/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<script type="text/javascript">

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

    window.addEvent('load', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            onSelect: function(date){
            }
        });
    });
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
	    $('multiselect').action = en4.core.baseUrl +'admin/ynmultilisting/reviews/multiselected';
	    $('ids').value = selecteditems;
	    $('select_action').value = action;
	    $('multiselect').submit();
	}
</script>
<h2><?php echo $this->translate("YouNet Multiple Listings Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Reviews') ?></h3>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>	
<form id='multiselect_form' method="post" action="<?php echo $this->url();?>">
	<table class='admin_table ynsocial_table' style="width: 100%">
	  <thead>
	    <tr>
	      <th><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('listingtype.title', 'ASC');"><?php echo $this->translate('Listing Type') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('listing.title', 'ASC');"><?php echo $this->translate('Listing Title') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('review.title', 'ASC');"><?php echo $this->translate('Review Title') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'ASC');"><?php echo $this->translate('Review By') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('review.overal_rating', 'ASC');"><?php echo $this->translate('General Rating') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('review.creation_date', 'ASC');"><?php echo $this->translate('Review Date') ?></a></th>
	      <th><?php echo $this->translate('Options') ?></th>
	    </tr>
	  </thead>
	  <tbody>
	    <?php foreach ($this->paginator as $item): ?>
	      <tr>
	      	<td>
	       		<input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/>
	        </td>
	        <td><?php echo $item -> getListingType();?></td>
	        <td><?php echo $item -> getParent();?></td>
	        <td><?php echo $item;?></td>
	        <?php 
	        	$isEditor = Engine_Api::_() -> ynmultilisting() -> checkIsEditor($item -> getListingType() -> getIdentity(), $item -> getOwner());
	        ?>
	        <td>
	        	<?php echo $item -> getOwner();?> 
	        	<?php if($isEditor) :?>
	        		<i>(<?php echo $this -> translate('editor');?>)</i>
        		<?php endif;?>
	        </td>
	        <td><?php echo $this->partial('_review_rating_big.tpl', 'ynmultilisting', array('review' => $item));?></td>
	        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
	        <td>
	        	<?php 
		            echo $this->htmlLink(
			            array('route' => 'admin_default', 
			                'module' => 'ynmultilisting',
				            'controller' => 'reviews' ,
				            'action' => 'view-detail', 
				            'id' => $item->getIdentity()), 
				            $this->translate('View Detail'), 
			            array('class' => 'smoothbox'));
           		 ?>
           		 |
           		 <?php 
		            echo $this->htmlLink(
			            array('route' => 'admin_default', 
			                'module' => 'ynmultilisting',
				            'controller' => 'reviews' ,
				            'action' => 'delete', 
				            'id' => $item->getIdentity()), 
				            $this->translate('Delete'), 
			            array('class' => 'smoothbox'));
           		 ?>
	        </td>
	      </tr>
	    <?php endforeach; ?>
	  </tbody>
	</table>
</form>
<?php if ($this->paginator->getTotalItemCount()) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
<div class='buttons'>
    <button type='button' onclick="multiSelected('Delete')"><?php echo $this->translate('Delete Selected') ?></button>
</div>
<br/>
<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no reviews.') ?>
    </span>
  </div>
<?php endif; ?>

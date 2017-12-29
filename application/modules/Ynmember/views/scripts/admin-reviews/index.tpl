<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Ynmember/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');

$this->headScript()
	->appendFile($baseUrl . 'application/modules/Ynmember/externals/scripts/jquery-1.10.2.min.js')
	->appendFile($baseUrl . 'application/modules/Ynmember/externals/scripts/jquery-ui-1.11.4.min.js');
?>

<script type="text/javascript">
	window.addEvent('domready', function(){
		var current = new Date();
		var yearRange = (current.getFullYear() - 20) +':' + (current.getFullYear());
		jQuery('.date_picker').datepicker({
			firstDay: 1,
			dateFormat: 'yy-mm-dd',
			showOn: "button",
			buttonImage: '',
			changeMonth: true,
			changeYear: true,
			yearRange: yearRange,
			buttonImageOnly: true,
			buttonText: ''
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
	    $('multiselect').action = en4.core.baseUrl +'admin/ynmember/reviews/multiselected';
	    $('ids').value = selecteditems;
	    $('select_action').value = action;
	    $('multiselect').submit();
	}
</script>
<h2><?php echo $this->translate("YouNet Advanced Member Plugin") ?></h2>
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
	      <th><?php echo $this->translate('Review Title') ?></th>
	      <th><?php echo $this->translate('Review By') ?></th>
	      <th><?php echo $this->translate('Review For') ?></th>
	      <th><?php echo $this->translate('General Rating') ?></th>
	      <th><?php echo $this->translate('Review Date') ?></th>
	      <th><?php echo $this->translate('Options') ?></th>
	    </tr>
	  </thead>
	  <tbody>
	  	<?php $tableRating = Engine_Api::_() -> getItemTable('ynmember_rating'); ?>
	    <?php foreach ($this->paginator as $item): ?>
	      <tr>
	      	<td>
	       		<input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/>
	        </td>
	        <td><a href='<?php echo $item->getHref() ?>'><?php echo $item->title ?></a></td>
	        <?php $user = Engine_Api::_() -> getItem('user', $item -> user_id); ?>
	        <?php if($user): ?>
	       	 <td><a href='<?php echo $user -> getHref() ?>'><?php echo $user -> getTitle(); ?></a></td>
	        <?php else:?>
	        	<td><?php echo $this->translate('Unknow')?></td>	
	        <?php endif;?>
	        <?php $resource = Engine_Api::_() -> getItem('user', $item -> resource_id); ?>
	        <?php if($resource): ?>
	       	 <td><a href='<?php echo $resource -> getHref() ?>'><?php echo $resource -> getTitle(); ?></a></td>
	        <?php else:?>
	        	<td><?php echo $this->translate('Unknow')?></td>	
	        <?php endif;?>
	        <td><?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $tableRating -> getGeneralRatingOfReview($item)));?></td>
	        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
	        <td>
	        	<?php 
		            echo $this->htmlLink(
			            array('route' => 'admin_default', 
			                'module' => 'ynmember',
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
			                'module' => 'ynmember',
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
<?php if (count($this->paginator)) {
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

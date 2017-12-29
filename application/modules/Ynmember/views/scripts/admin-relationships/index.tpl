<script type="text/javascript">
	function withMember(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl+'admin/ynmember/relationships/with';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': id,
	            'value': value
	        }
	    }).send();
	}
	
	function appearFeed(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl+'admin/ynmember/relationships/appear';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': id,
	            'value': value
	        }
	    }).send();
	}
	
	function approvePartner(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl+'admin/ynmember/relationships/approve';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': id,
	            'value': value
	        }
	    }).send();
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

<h3><?php echo $this->translate('Manage Relationships') ?></h3>
<br />
<div class="add_link">
<?php echo $this->htmlLink(
	array('route' => 'admin_default', 'module' => 'ynmember', 'controller' => 'relationships', 'action' => 'create'),
	$this->translate('Add New Status'), 
	array(
	    'class' => 'smoothbox buttonlink add_icon',
	)) ?>
</div>
<br />
<?php if( count($this->relationships) ): ?>
<div class="admin_table_form">
<table class='admin_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Status") ?></th>
      <th><?php echo $this->translate("With other member") ?></th>
      <th><?php echo $this->translate("Appear in New Feed") ?></th>
      <th><?php echo $this->translate("Need approval from partner") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody id="demo-list">
    <?php  foreach ($this->relationships as $item): ?>
    	<td><?php echo $item -> status;?></td>
    	<td><input type="checkbox" onclick="withMember(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->with_member) echo 'checked'?>/></td>
    	<td><input type="checkbox" onclick="appearFeed(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->appear_feed) echo 'checked'?>/></td>
        <td><input type="checkbox" onclick="approvePartner(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->user_approved) echo 'checked'?>/></td>
        <td>
            <?php 
	            echo $this->htmlLink(
		            array('route' => 'admin_default', 
		                'module' => 'ynmember',
			            'controller' => 'relationships' ,
			            'action' => 'edit', 
			            'id' => $item->getIdentity()), 
			            $this->translate('Edit'), 
		            array('class' => 'smoothbox'));
            ?>
           	|
           	<?php 
	            echo $this->htmlLink(
		            array('route' => 'admin_default', 
		                'module' => 'ynmember',
			            'controller' => 'relationships' ,
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
<?php if (count($this->relationships) > 0) {
    echo '<p class=result_count>';
    echo $this->translate(array('Total %s result', 'Total %s results', count($this->relationships)), count($this->relationships));
    echo '</p>';
}?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no relationship status.') ?>
    </span>
  </div>
<?php endif; ?>

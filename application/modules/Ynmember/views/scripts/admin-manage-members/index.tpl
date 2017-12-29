<style>
.admin_search input[type=text]
{
	width: 120px;
}
.admin_table
{
	width: 100%;
}
.admin_table .input_container
{
	text-align: center;
}
</style>

<h2><?php echo $this->translate("YouNet Advanced Member Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    	echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>
<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  };
  var set_featured = function (obj, id){
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl + 'admin/ynmember/manage-members/feature';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'user_id': id,
	            'value': value
	        },
	        'onSuccess' : function(responseJSON, responseText)
	        {
	          alert(responseJSON.message);
	        }
	    }).send();
  };
  var set_member_of_day  = function(id){
	    var url = en4.core.baseUrl + 'admin/ynmember/manage-members/day';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'user_id': id
	        },
	        'onSuccess' : function(responseJSON, responseText)
	        {
	          alert(responseJSON.message);
	        }
	    }).send();
  };
	  
  
</script>

<?php if( count($this->paginator) ): ?>
<div class="admin_table_form">
<table class='admin_table'>
  <thead>
    <tr>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');"><?php echo $this->translate("ID") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('level_id', 'ASC');"><?php echo $this->translate("User Level") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('rating', 'ASC');"><?php echo $this->translate("Rating") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('review_count', 'ASC');"><?php echo $this->translate("Reviews") ?></a></th>
      <th><?php echo $this->translate("Featured") ?></th>
      <th><?php echo $this->translate("Member Of Day") ?></th>
    </tr>
  </thead>
  <tbody id="demo-list">
  	<?php $tableReview = Engine_Api::_() -> getItemTable('ynmember_review');?>
    <?php  foreach ($this->paginator as $member): ?>
    	<td><?php echo $member -> getIdentity();?></td>
    	<td class='admin_table_bold'>
              <?php echo $this->htmlLink($member->getHref(),
                  $this->string()->truncate($member->getTitle(), 10),
                  array('target' => '_blank'))?>
            </td>
        <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $member->user_id)->getHref(), $this->item('user', $member->user_id)->username, array('target' => '_blank')) ?></td>
    	<td><?php echo $member -> email;?></td>
    	<td><?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $member->level_id)->getTitle()) ?></td>
    	<td>
    		<div class="ynmember-review-item-rating">
					<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $member -> rating));?>
            </div>
    	</td>
    	<td class="input_container">
    		<?php $reviewCount = $tableReview -> countReviewByUser($member);?>
    		<?php echo $reviewCount; ?>
    	</td>
    	<td class="input_container"><input type="checkbox" onclick="set_featured(this, '<?php echo $member->getIdentity()?>')" <?php if ($member->active) echo 'checked'?>/></td>
    	<td class="input_container"><input name="mem_of_day" type="radio" onclick="set_member_of_day('<?php echo $member->getIdentity()?>')" <?php if ($member->member_of_day) echo 'checked'?>/></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
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
      <?php echo $this->translate('There are no members.') ?>
    </span>
  </div>
<?php endif; ?>
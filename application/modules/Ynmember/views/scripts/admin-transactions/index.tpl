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

<h3><?php echo $this->translate('Manage Transactions') ?></h3>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<table class='admin_table ynsocial_table' style="width: 100%">
  <thead>
    <tr>
      <th><?php echo $this->translate('Transaction ID') ?></th>
      <th><?php echo $this->translate('Purchased Date') ?></th>
      <th><?php echo $this->translate('Status') ?></th>
      <th><?php echo $this->translate('Method') ?></th>
      <th><?php echo $this->translate('Amount') ?></th>
      <th><?php echo $this->translate('User') ?></th>
      <th><?php echo $this->translate('Description') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><?php echo (($item->payment_transaction_id) ? $item->payment_transaction_id : $item->transaction_id) ?></td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td><?php echo $item->status ?></td>
        <td><?php echo ($this->methods[$item->gateway_id]) ? $this->methods[$item->gateway_id] : $this->translate('Unknown Method') ?></td>
        <td><?php echo $this -> locale()->toCurrency($item->amount, $item->currency)?></td>
        <?php $user = Engine_Api::_() -> getItem('user', $item -> user_id); ?>
        <?php if($user): ?>
       	 <td><a href='<?php echo $user -> getHref() ?>'><?php echo $user -> getTitle(); ?></a></td>
        <?php else:?>
        	<td><?php echo $this->translate('Unknow')?></td>	
        <?php endif;?>
        <td><?php echo $item->description ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
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
      <?php echo $this->translate('There are no transactions.') ?>
    </span>
  </div>
<?php endif; ?>

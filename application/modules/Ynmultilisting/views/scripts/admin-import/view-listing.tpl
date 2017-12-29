<form onsubmit="return false;" class="global_form_popup">
<h3>
	<?php echo $this->translate("Listings which were imported successfully") ?>
</h3>

<table class='admin_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Title") ?></th>
	  <th><?php echo $this->translate("Listing Owner") ?></th>
	  <th><?php echo $this->translate("Category") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->list_listings as $id): ?>
      <?php $item = Engine_Api::_()->getItem('ynmultilisting_listing', $id) ;?>
      <?php if(!$item->deleted) :?>
      <tr>
        <td><a href='<?php echo $item->getHref();?>'><?php echo $item->title ?></a></td>
        <td><a href='<?php echo $item->getOwner()->getHref();?>'><?php echo $item->getOwner()->getTitle() ?></a></td>
		<td><?php echo $item->getCategory()->getTitle() ?></td>
      </tr>
      <?php else:?>
      	<td><?php $this->translate('Deleted Listing');?></td>
      	<td></td>
      	<td></td>
      <?php endif;?>
    <?php endforeach; ?>
  </tbody>
</table>
</form>
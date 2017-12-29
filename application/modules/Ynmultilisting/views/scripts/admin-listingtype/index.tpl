<h2><?php echo $this->translate("YouNet Multiple Listings Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
    	<form class="global_form">
      		<div>
        		<h3><?php echo $this->translate("Listing Types") ?></h3>
        		<p><?php echo $this->translate("YNMULTILISTING_ADMIN_LISTINGTYPE_DESCRIPTION") ?></p><br />
        		<a class="smoothbox" href="<?php echo $this -> url(array('controller' => 'listingtype', 'action' => 'create'), 'admin_default' );?>"><strong><i class="fa fa-server"></i> <?php echo $this -> translate("Add New Listing Type");?></strong></a><br />
                <br />
        		<?php if(count($this->types)>0) : ?>
	        	<table style="position: relative;" class='admin_table'>
	          		<thead>
			            <tr>
			            	<th><input onclick="checkall(this);" type="checkbox" /></th>
			              	<th><?php echo $this->translate("Listing Type") ?></th>
			              	<th><?php echo $this->translate("Total Listings") ?></th>
			              	<th><?php echo $this->translate("Show") ?></th>
			              	<th><?php echo $this->translate("Options") ?></th>
			            </tr>
	          		</thead>
	          		<tbody id='demo-list'>
	          			<?php foreach ($this->types as $type): ?>
	          				<tr id='type_item_<?php echo $type->getIdentity(); ?>'>
	          					<td><input type="checkbox" name="ids[]" value="<?php echo $type->getIdentity();?>" class="listingtype_ids" /></td>
	          					<td><?php echo $type -> title;?></td>
	          					<td><?php echo $type -> getListingCount();?></td>
	          					<td><input type="checkbox" onclick="showListingType(this, '<?php echo $type->getIdentity()?>')" value="1" name="show_<?php echo $type->getIdentity(); ?>" <?php echo ($type->show == '1') ? 'checked="checked"' : '';?>/></td>
	          					<td>
	          						<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listingtype', 'action' => 'edit', 'id' => $type->getIdentity()), $this->translate('Edit'), array(
					                    'class' => 'smoothbox',
					                  )) 
					                ?>
					                |
					                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listingtype', 'action' => 'delete', 'id' => $type->getIdentity()), $this->translate('Delete'), array(
					                    'class' => 'smoothbox',
					                )) ?>
					                |
					                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listingtype', 'action' => 'member-level-permission', 'id' => $type->getIdentity()), $this->translate('Member Level Settings'), array()) ?>
					                | 
					                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'quicklink', 'action' => 'index', 'listingtype_id' => $type->getIdentity()), $this->translate('Listing Quick Link'), array()); ?>
					                |
					                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'category', 'action' => 'index', 'type_id' => $type->getIdentity()), $this->translate('Categories'), array()); ?> 
					                | 
					                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'listingtype', 'action' => 'manage-menu', 'id' => $type->getIdentity()), $this->translate('Listing Menu'), array()) ?>
					                | 
					                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'layout', 'action' => 'index', 'listingtype_id' => $type->getIdentity()), $this->translate('Manage Pages'), array()) ?>
	                  			</td>
	          				</tr>
	          			<?php endforeach;?>
	          		</tbody>
	          	</table>
	          	
	          	<?php echo $this->paginationControl($this->types, null, null, array(
					  'pageAsQuery' => true,
					)); 
				?>
				<?php $total = $this->types->getTotalItemCount();?>
                <br />
				<?php echo $this -> translate(array("Total %s result", "Total %s results", $total), $total);?>
				
				<br /><br />
			    <div class='buttons'>
			    	<button type='button' onclick="confirmDelete();"><?php echo $this->translate("Delete Selected") ?></button>
			    </div>
				
	        	<?php else:?>
	      		<div class="tip">
				    <span><?php echo $this->translate("There are currently no listing types.") ?></span>
				</div>
	        	<?php endif;?>
        	</div>
        </form>
	</div>
</div>

<script type="text/javascript">
window.addEvent('domready', function() {
    new Sortables('demo-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'listingtype', 'action'=>'sort'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString()
          }
        }).send();
      }
    });
});

function checkall(e)
{
	$$(".listingtype_ids").set('checked', e.checked);
}

function confirmDelete()
{
	ids = $$("input[name='ids[]']:checked").get('value');
	if (ids.length == 0)
	{
		alert('<?php echo $this->translate("Please choose any listing types!");?>');
		return false;
	}
	ids = ids.toString();
	url = '<?php echo $this -> url(array('action' => 'delete-selected'), 'admin_default');?>' + '/?ids=' + ids;
	Smoothbox.open(url);
}

function showListingType(obj, id) {
    var value = (obj.checked) ? 1 : 0;
    var url = en4.core.baseUrl+'admin/ynmultilisting/listingtype/show';
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
		
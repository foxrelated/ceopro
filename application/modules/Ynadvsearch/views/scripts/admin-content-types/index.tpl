<script type="text/javascript">

en4.core.runonce.add(function(){

    new Sortables('demo-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'content-types','action'=>'sort'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString(),
            'page' : <?php if($this->page) echo $this->page; else echo '1';?>,
          }
        }).send();
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

function searchBarAll() {
    var hasElement = false;
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled && inputs[i].hasClass('searchbar_checkbox')) {
            inputs[i].checked = $('check_all_search').checked;
            searchBarContentType(inputs[i], inputs[i].getProperty('data_id'));
        }
    }
}

function showAll() {
    var hasElement = false;
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled && inputs[i].hasClass('show_checkbox')) {
            inputs[i].checked = $('check_all_show').checked;
            showContentType(inputs[i], inputs[i].getProperty('data_id'));
        }
    }
}

function styleAll() {
    var hasElement = false;
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled && inputs[i].hasClass('orginal_style_checkbox')) {
            inputs[i].checked = $('check_all_original_style').checked;
            styleContentType(inputs[i], inputs[i].getProperty('data_id'));
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
    $('multiselect').action = en4.core.baseUrl +'admin/ynadvsearch/content-types/multiselected';
    $('ids').value = selecteditems;
    $('select_action').value = action;
    $('multiselect').submit();
}

function searchBarContentType(obj, id) {
    var value = (obj.checked) ? 1 : 0;
    var url = en4.core.baseUrl+'admin/ynadvsearch/content-types/search-bar';
    new Request.JSON({
        url: url,
        method: 'post',
        data: {
            'id': id,
            'value': value
        }
    }).send();
}

function showContentType(obj, id) {
    var value = (obj.checked) ? 1 : 0;
    var url = en4.core.baseUrl+'admin/ynadvsearch/content-types/show';
    new Request.JSON({
        url: url,
        method: 'post',
        data: {
            'id': id,
            'value': value
        }
    }).send();
}

function styleContentType(obj, id) {
    var value = (obj.checked) ? 1 : 0;
    var url = en4.core.baseUrl+'admin/ynadvsearch/content-types/style';
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
<h2>
    <?php echo $this->translate('YouNet Search Plugin') ?>
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

<h3><?php echo $this->translate('Manage Content Types') ?></h3>
<br />
<p><?php echo $this->translate("YNADVSEARCH_MANAGE_CONTENT_TYPES_DESCRIPTION") ?></p>
<br />
<?php if($this->canCreate):?>
<div class="add_link">
<?php echo $this->htmlLink(
array('route' => 'admin_default', 'module' => 'ynadvsearch', 'controller' => 'content-types', 'action' => 'create'),
$this->translate('add Content Type'), 
array(
    'class' => 'smoothbox buttonlink add_faq',
)) ?>
</div>
<?php endif;?>
<br />
<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>
<div class="admin_table_form">
<form style="position: relative;" id='multiselect_form' method="post" action="<?php echo $this->url();?>">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><?php echo $this->translate("Title") ?></th>
      <th><?php echo $this->translate("Module") ?></th>
      <th><?php echo $this->translate("Icon") ?></th>
      <th><input id="check_all_search" onclick='searchBarAll();' type='checkbox' class='checkbox' /><?php echo $this->translate("Apply to search bar") ?></th>
      <th><input id="check_all_show" onclick='showAll();' type='checkbox' class='checkbox' /><?php echo $this->translate("Show") ?></th>
      <th><input id="check_all_original_style" onclick='styleAll();' type='checkbox' class='checkbox' /><?php echo $this->translate("Orginal Style") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody id="demo-list">
    <?php $count = 0; foreach ($this->paginator as $item): ?>
    <?php
    	$advalbum_enable = false;
		if(Engine_Api::_()->hasItemType('advalbum_album') && $item -> type == 'album')
		{
			$advalbum_enable = true;
		}
    ?>
    <?php if(Engine_Api::_()->hasItemType($item -> type) || $advalbum_enable) :?>
    	<?php $count++ ;?>
      <tr id='contenttype_item_<?php echo $item->getIdentity() ?>'>
      	<td>
      		<?php if($item -> type != 'user') :?>
       			<input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/>
       		<?php endif; ?>
        </td>
        <td><?php echo $item->title?></td>
        <td><?php echo $item->module ?></td>
        <?php if($item->getPhotoUrl('thumb.icon')) :?>
       		<td><img src="<?php echo $item->getPhotoUrl('thumb.icon'); ?>"/></td>
        <?php else:?>
        	<td><img style="width: 50px;height: 50px;" src="<?php echo $this->baseUrl().'/application/modules/Ynadvsearch/externals/images/nophoto_contenttype_thumb_icon.png' ?>"/></td>
        <?php endif;?>
        <td><input data_id = '<?php echo $item->getIdentity()?>' type="checkbox" class="searchbar_checkbox" value="1" onclick="searchBarContentType(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->search) echo 'checked'?>/></td>
        <td><input data_id = '<?php echo $item->getIdentity()?>' type="checkbox" class="show_checkbox" value="1" onclick="showContentType(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->show) echo 'checked'?>/></td>
        <?php $hasOriginal = Engine_Api::_()->ynadvsearch()->hasOriginal($item->type);?>
        <td><input <?php echo ($hasOriginal) ? '' : 'disabled';?> data_id = '<?php echo $item->getIdentity()?>' type="checkbox" class="orginal_style_checkbox" value="1" onclick="styleContentType(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->original_style) echo 'checked'?>/></td>
        <td>
            <?php 
	            echo $this->htmlLink(
		            array('route' => 'admin_default', 
		                'module' => 'ynadvsearch',
			            'controller' => 'content-types' ,
			            'action' => 'edit', 
			            'id' => $item->getIdentity()), 
			            $this->translate('edit'), 
		            array('class' => 'smoothbox'));
            ?>
        </td>
      </tr>
    <?php endif;?>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if ($count > 0) {
    echo '<p class=result_count>';
    $total = $count;
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
</form>
</div>

<div class='buttons'>
    <button type='button' onclick="multiSelected('Delete')"><?php echo $this->translate('Delete Selected') ?></button>
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
      <?php echo $this->translate('There are no content types.') ?>
    </span>
  </div>
<?php endif; ?>

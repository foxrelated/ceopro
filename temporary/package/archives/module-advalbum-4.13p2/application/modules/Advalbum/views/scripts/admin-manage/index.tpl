
<script type="text/javascript">
en4.core.runonce.add(function(){
	$$('th.admin_table_checkbox input[type=checkbox]').addEvent('click', function(){
		var checked = $(this).checked;
		var checkboxes = $$('td.advalbum_check input[type=checkbox]');
		checkboxes.each(function(item){
			item.checked = checked;
		});
	})
});

function multiDelete()
{
  var ids = [];
  var count = 0;
  var checkboxes = $$('td.advalbum_check input[type=checkbox]');
  checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        count++;
        ids.push(item.value);
      }
  });
  if(count==0){
    alert('<?php echo $this->translate("Please select albums(s) to delete.") ?>');
    return false;
  }

  var url = en4.core.baseUrl + 'admin/advalbum/manage/deleteselected/type/album/ids/' + ids;
  Smoothbox.open(url);
  return false;
}

function album_feature(album_id){
    var element = document.getElementById('advalbum_content_'+album_id);
    var checkbox = document.getElementById('featurealbum_'+album_id);
    var status = 0;

    if(checkbox.checked==true) status = 1;
    else status = 0;
    var content = element.innerHTML;
    new Request.JSON({
      'format': 'json',
      'url' : '<?php echo $this->url(array('module' => 'advalbum', 'controller' => 'manage', 'action' => 'feature'), 'admin_default') ?>',
      'data' : {
        'format' : 'json',
        'album_id' : album_id,
        'status' : status
      },
      'onRequest' : function(){
          //element.innerHTML= "<img src='application/modules/advalbum/externals/images/featuredloading.gif'></img>";
      },
      'onSuccess' : function(responseJSON, responseText)
      {
        /* synchronize with View Photo page
        element.innerHTML = content;
        checkbox = document.getElementById('featurealbum_'+album_id);
        if( status == 1) checkbox.checked=true;
        else checkbox.checked=false;
        */
      }
    }).send();

}

// change order of album table's columns
function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['orderby'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('orderby').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
}
</script>

<h2>
  <?php echo $this->translate('YouNet Advanced Album Plugin') ?>
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

<p>
  <?php echo $this->translate("ALBUM_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br/>
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br />
<?php if( count($this->paginator) ): ?>

<form id="multidelete_form" action="<?php echo $this->url();?>" onSubmit="return multiDelete()" method="POST">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_checkbox'><input type='checkbox' class='checkbox' /></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('album_id', 'DESC')"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('title', 'ASC')"><?php echo $this->translate('Title') ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('owner_title', 'DESC')"><?php echo $this->translate('Owner') ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('featured', 'DESC')"><?php echo $this->translate("Featured") ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('view_count', 'DESC')"><?php echo $this->translate('Views') ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('creation_date', 'DESC')"><?php echo $this->translate('Creation Date') ?></a></th>
        <th><?php echo $this->translate('Options') ?></th>
      </tr>
    </thead>
    <tbody>
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <td class='advalbum_check'><input type='checkbox' class='checkbox' name='delete_<?php echo $item->album_id;?>' value="<?php echo $item->album_id ?>"/></td>
            <td><?php echo $item->getIdentity() ?></td>
            <td><?php echo $this->string()->truncate($item->getTitle(),30); ?></td>
            <td><?php echo $this->user($item->owner_id)->getTitle() ?></td>
			<td>
	          <div id='advalbum_content_<?php echo $item->getIdentity() ?>' style ="text-align: center;" >
	              <?php if($item->featured): ?>
	                <input type="checkbox" id='featurealbum_<?php echo $item->getIdentity(); ?>' onclick="album_feature(<?php echo $item->getIdentity(); ?>,this)" checked />
	              <?php else: ?>
	               <input type="checkbox" id='featurealbum_<?php echo $item->getIdentity(); ?>' onclick="album_feature(<?php echo $item->getIdentity(); ?>,this)" />
	              <?php endif; ?>
	          </div>
	        </td>
            <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
            <td>
              <a href="<?php echo $this->url(array('album_id' => $item->getIdentity()), 'album_specific') ?>">
                <?php echo $this->translate('view') ?>
              </a>
              | <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'advalbum', 'controller' => 'admin-manage', 'action' => 'delete-album', 'id' => $item->getIdentity()),
                  $this->translate('delete'),
                  array('class' => 'smoothbox')) ?>
            </td>
          </tr>
        <?php endforeach; ?>
    </tbody>
  </table>

  <br/>

  <div class='buttons'>
    <button type='submit'>
      <?php echo $this->translate('Delete Selected') ?>
    </button>
  </div>
</form>

<br />

<div>
<?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
    array(
    'pageAsQuery' => true,
    'query' => $this->formValues
)); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no albums posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
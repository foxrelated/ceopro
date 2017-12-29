<script type="text/javascript">

    function multiDelete()
    {
      return confirm("<?php echo $this->translate('Are you sure you want to delete the selected pages?');?>");
    }
    function selectAll()
    {
      var i;
      var multidelete_form = $('multidelete_form');
      var inputs = multidelete_form.elements;
      for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
          inputs[i].checked = inputs[0].checked;
        }
      }
    } 
   function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['orderby'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('orderby').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
</script>
<h2>
  <?php echo $this->translate('Wiki Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("YNWIKI_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br/>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div style="overflow: auto">  
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();'  type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('page_id', 'DESC');"><?php echo $this->translate("ID");?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'DESC');"><?php echo $this->translate("Creator") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');"><?php echo $this->translate("Views") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="ynwiki_check"><input type='checkbox' class='checkbox' name='delete_<?php echo $item->page_id; ?>' value="<?php echo $item->page_id ?>"/></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->title ?></td>
        <td><?php echo $item->getOwner()->getTitle() ?></td>
        <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view')) ?>
          |
          <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'ynwiki', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->page_id),
                  $this->translate('delete'),
                  array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />   
<div class='buttons'>
  <button type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>
</form> 
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
      <?php echo $this->translate("There are no pages.") ?>
    </span>
  </div>
<?php endif; ?>

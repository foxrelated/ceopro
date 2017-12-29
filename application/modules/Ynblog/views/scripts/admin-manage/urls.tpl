<script type="text/javascript">
  en4.core.runonce.add(function(){
        $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
            var checked = $(this).checked;
            var checkboxes = $$('td.ynblog_check input[type=checkbox]');
            checkboxes.each(function(item){
                item.checked = checked;
            });
        })
  });

  function actionSelected(actionType){
    var checkboxes = $$('td.ynblog_check input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('action_selected').action = en4.core.baseUrl +'admin/ynblog/manage/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
  }
   
   function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->filterValues['orderby'] ?>';
    var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
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
  <?php echo $this->translate('Advanced Blogs Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="admin_search" style="display: none">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<div style="overflow: auto">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('link_id', 'DESC');">ID</a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('link_url', 'DESC');"><?php echo $this->translate("URL") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'DESC');"><?php echo $this->translate("Owner") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('cronjob_enabled', 'DESC');"><?php echo $this->translate("Cronjob Enabled")?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('last_run', 'DESC');"><?php echo $this->translate("Last Run") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="ynblog_check"><input type='checkbox' class='checkbox' value="<?php echo $item->link_id ?>"/></td>
        <td><?php echo $item->link_id ?></td>
        <td><?php echo $item->link_url ?></td>
        <td><?php echo $item->getOwner() ?></td>
        <td>
                <?php if($item->cronjob_enabled) echo $this->translate("Yes");
                      else echo $this->translate("No");
                ?>
        </td>
        <td><?php echo $this->locale()->toDateTime($item->last_run) ?></td>
        <td>
          <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'ynblog', 'controller' => 'admin-manage', 'action' => 'delete-link', 'id' => $item->link_id),
                  $this->translate('delete'),
                  array('class' => 'smoothbox')) ?>
          |
          <?php if(!$item->cronjob_enabled):?>
               <?php echo $this->htmlLink(
                      array('route' => 'default', 'module' => 'ynblog', 'controller' => 'admin-manage', 'action' => 'enable-cron', 'id' => $item->link_id),
                      $this->translate('enable'),
                      array('class' => 'smoothbox')) ?>
          <?php else:?>
               <?php echo $this->htmlLink(
                      array('route' => 'default', 'module' => 'ynblog', 'controller' => 'admin-manage', 'action' => 'disable-cron', 'id' => $item->link_id),
                      $this->translate('disable'),
                      array('class' => 'smoothbox')) ?>
          <?php endif;?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />

<div class='buttons'>
  <button onclick="javascript:actionSelected('delete-link');" type='button'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
  
   <button onclick="javascript:actionSelected('enable-link');" type='button'>
    <?php echo $this->translate("Enable Selected") ?>
  </button>

   <button onclick="javascript:actionSelected('disable-link');" type='button'>
    <?php echo $this->translate("Disable Selected") ?>
  </button>
</div>

<form id='action_selected' method='post' action=''>
  <input type="hidden" id="ids" name="ids" value=""/>
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
      <?php echo $this->translate("There are no urls.") ?>
    </span>
  </div>
<?php endif; ?>
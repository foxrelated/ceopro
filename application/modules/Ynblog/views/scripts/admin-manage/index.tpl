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

  function blogentry_good(blogentry_id){
            var element = document.getElementById('ynblog_content_'+blogentry_id);
            var checkbox = document.getElementById('goodblog_'+blogentry_id);
            var status = 0;
            
            if(checkbox.checked==true) status = 1;
            else status = 0;
            var content = element.innerHTML;
            element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Ynblog/externals/images/loading.gif'></img>";
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'ynblog', 'controller' => 'manage', 'action' => 'feature'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'blog_id' : blogentry_id,
                'good' : status
              },
              'onRequest' : function(){
              },
              'onSuccess' : function(responseJSON, responseText)
              {
                element.innerHTML = content;
                checkbox = document.getElementById('goodblog_'+blogentry_id);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
              }
            }).send();
            
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

<p>
  <?php echo $this->translate("BLOG_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br/>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<div style="overflow: auto">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('blog_id', 'DESC');">ID</a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'DESC');"><?php echo $this->translate("Owner") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('is_featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('is_approved', 'DESC');"><?php echo $this->translate("Approved")?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');"><?php echo $this->translate("Views") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="ynblog_check"><input type='checkbox' class='checkbox' value="<?php echo $item->blog_id ?>"/></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->getTitle() ?></td>
        <td><?php echo $item->getOwner()->getTitle() ?></td>
        <td>
          <div id='ynblog_content_<?php echo $item->blog_id; ?>' style ="text-align: center;" >
              <?php if($item->is_featured): ?>
                <input type="checkbox" id='goodblog_<?php echo $item->blog_id; ?>' onclick="blogentry_good(<?php echo $item->blog_id; ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='goodblog_<?php echo $item->blog_id; ?>' onclick="blogentry_good(<?php echo $item->blog_id; ?>,this)" />
              <?php endif; ?>
          </div>
        </td>
        <td>
                <?php if($item->is_approved) echo $this->translate("Yes");
                      else echo $this->translate("No");
                ?>
        </td>
        <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view')) ?>
          |
          <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'ynblog', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->blog_id),
                  $this->translate('delete'),
                  array('class' => 'smoothbox')) ?>
          |
          <?php if(!$item->is_approved):?>
               <?php echo $this->htmlLink(
                      array('route' => 'default', 'module' => 'ynblog', 'controller' => 'admin-manage', 'action' => 'approve', 'id' => $item->blog_id),
                      $this->translate('approve'),
                      array('class' => 'smoothbox')) ?>
          <?php else:?>
               <?php echo $this->htmlLink(
                      array('route' => 'default', 'module' => 'ynblog', 'controller' => 'admin-manage', 'action' => 'unapprove', 'id' => $item->blog_id),
                      $this->translate('unapprove'),
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
  <button onclick="javascript:actionSelected('delete');" type='button'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
  
   <button onclick="javascript:actionSelected('approve');" type='button'>
    <?php echo $this->translate("Approve Selected") ?>
  </button>

   <button onclick="javascript:actionSelected('unapprove');" type='button'>
    <?php echo $this->translate("Unapprove Selected") ?>
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
      <?php echo $this->translate("There are no blog entries.") ?>
    </span>
  </div>
<?php endif; ?>

<script type="text/javascript">
 en4.core.runonce.add(function(){
     $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){
         var checked = $(this).checked;
         var checkboxes =$$('td.advgroup_check input[type=checkbox]');
         checkboxes.each(function(item,index){
         item.checked = checked;
        });
     })
 });

function actionSelected(actionType){
    var checkboxes = $$('td.advgroup_check input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('action_selected').action = en4.core.baseUrl +'admin/advgroup/manage/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
  }

function group_good(group_id){
            var element = document.getElementById('advgroup_content_' + group_id);
            var checkbox = document.getElementById('goodgroup_' + group_id);
            var status = 0;

            if(checkbox.checked==true) status = 1;
            else status = 0;
            var content = element.innerHTML;
            element.innerHTML= "<img  src='application/modules/Advgroup/externals/images/loading.gif'></img>";

            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('controller' => 'group', 'action' => 'featured'), 'group_extended') ?>',
              'data' : {
                'format' : 'json',
                'group_id' : group_id,
                'good' : status
              },
              'onRequest' : function(){
              },
              'onSuccess' : function(responseJSON, responseText)
              {
                element.innerHTML = content;
                checkbox = document.getElementById('goodgroup_'+group_id);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
              }
            }).send();

    }

  function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
</script>

<h2>
  <?php echo $this->translate("Groups Plugin") ?>
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
  <?php echo $this->translate("GROUP_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br/>
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br/>
<?php if( count($this->paginator) ): ?>
  <table class='advgroup_admin_tbl admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('group_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Title") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'DESC');"><?php echo $this->translate("Owner") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('is_subgroup', 'DESC');"><?php echo $this->translate("Is Sub-group")?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('number_group_member', 'DESC');"><?php echo $this->translate("Num of Members")?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');"><?php echo $this->translate("Views") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td class="advgroup_check"><input type='checkbox' class='checkbox' value='<?php echo $item->group_id ?>' /></td>
          <td><?php echo $item->group_id ?></td>
          <td><?php echo $item ?></td>
          <td>
            <?php if(!$item->is_subgroup):?>
              <div id='advgroup_content_<?php echo $item->group_id; ?>'>
                  <?php if($item->featured): ?>
                      <input type="checkbox" id='goodgroup_<?php echo $item->group_id; ?>'  onclick="group_good(<?php echo $item->group_id; ?>,this)" checked />
                  <?php else: ?>
                      <input type="checkbox" id='goodgroup_<?php echo $item->group_id; ?>'  onclick="group_good(<?php echo $item->group_id; ?>,this)" />
                  <?php endif; ?>
              </div>
           <?php endif;?>
          </td>
          <td>
          	<?php echo $this->user($item->user_id);?>
          </td>
          <td class="center">
          	<?php
          		if($item->is_subgroup)
          			echo $this->translate("Yes");
                else
                	echo $this->translate("No");
            ?>
          </td>
          <td class="center"><?php echo $item->member_count ?></td>
          <td class="center"><?php echo $item->view_count;?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td>
            <a href="<?php echo $this->url(array('id' => $item->group_id), 'group_profile') ?>">
              <span><?php echo $this->translate("view") ?></span>
            </a>
            |
            <?php
            	echo $this->htmlLink(
                	array('route' => 'default', 'module' => 'advgroup', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->group_id),
                	$this->translate("delete"),
                	array('class' => 'smoothbox'));
			?>
			|
          	<?php
          		echo $this->htmlLink($this->url(array('action' => 'transfer', 'group_id' => $item->getIdentity(), 'class' => 'smoothbox'), 'group_specific', true),
          			$this->translate('change owner'), array('class' => 'smoothbox'));
          	?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='button' onclick="javascript:actionSelected('delete');"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  <br />
  <form id='action_selected' method="post" action="">
       <input type="hidden" id="ids" name="ids" value=""/>
  </form>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no groups posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
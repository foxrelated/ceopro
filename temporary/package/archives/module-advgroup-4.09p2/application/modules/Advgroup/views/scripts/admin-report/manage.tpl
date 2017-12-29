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
    $('action_selected').action = en4.core.baseUrl +'admin/advgroup/report/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
  }

</script>
<h2>
  <?php echo $this->translate("Groups Plugin") ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu

      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<p>
  <?php echo $this->translate("REPORTS_VIEW_GROUPS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br />
<?php if( count($this->paginator) ): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
        <th><?php echo $this->translate("ID") ?></th>
        <th><?php echo $this->translate("Group Title") ?></th>
        <th><?php echo $this->translate("Topic Title") ?></th>
        <th><?php echo $this->translate("Post ID")?> </th>
        <th><?php echo $this->translate("Reporter") ?></th>
        <th style='width:200px;'><?php echo $this->translate("Report") ?></th>
        <th><?php echo $this->translate("Date") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td class="advgroup_check"><input type='checkbox' class='checkbox' value='<?php echo $item->report_id ?>' /></td>
          <td><?php echo $item->report_id ?></td>
          <td>
          	<?php
          		$groupTable = Engine_Api::_()->getItemTable('group');
          		$groupName =$groupTable ->info('name');
          		$select = $groupTable->select()->where("group_id = $item->group_id");
          		$group_title = $groupTable->fetchAll($select);
          		if(count($group_title)==1){
          			echo $group_title[0]['title'];
          		}
          		else
          		{
          			echo $this->translate('N/A');
          		}
          	?></td>
          <td>
          <?php
          		$topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
          		$topicName =$topicTable ->info('name');
          		$select = $topicTable->select()->where("topic_id = $item->topic_id");
          		$topic = $topicTable->fetchAll($select);
          		if(count($topic)==1){
          			echo $topic[0]['title'];
          		}
          		else
          		{
          			echo $this->translate('N/A');
          		}
          	?></td>
          <td><?php if($item->post_id)
          {
          	echo $item->post_id;
          }
          else echo $this->translate('N/A');
          ?></td>

          <td><?php echo $this->user($item->user_id)->getTitle(); ?></td>
          <td><?php echo $this->viewMore($item->content); ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td>
          <?php
          		if(count($topic)==0){
          		// view group
          ?>

            <a href="<?php echo $this->url(array('id' => $item->group_id), 'group_profile') ?>">
             <span><?php echo $this->translate("view") ?></span>

            </a>

            <?php
          		}
            	else {
            	if($item->post_id)
            		{
            			$postTable = Engine_Api::_()->getItemTable(('advgroup_post'));
            			$postName = $postTable->info('name');
            			$select = $postTable->select()->where("post_id = $item->post_id");
            			$post = $postTable->fetchAll($select);
            			if(count($post)==1) echo  $this->htmlLink($post[0]->getHref(),$this->translate('view'));
            		}
					//view topic
            	else	echo $this->htmlLink($topic[0]->getHref(), $this->translate("view"));
            	}

            ?>
            |
            <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'advgroup', 'controller' => 'admin-report', 'action' => 'delete', 'id' => $item->report_id),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
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
      <?php echo $this->translate("There are no reports posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>

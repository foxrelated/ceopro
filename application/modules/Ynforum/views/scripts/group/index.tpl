<?php
	$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynforum/externals/scripts/core.js');   
       ?>
<div class="generic_layout_container layout_middle">
	<?php echo $this->partial('_forum_header.tpl', 'ynforum', array('forum'=>$this -> forum, 'linkedCategories' => $this -> linkedCategories, 'navigationForums' => $this -> navigationForums, 'moderators' => $this -> moderators))?>  
	<!-- Group menus-->
	<div class="tabs navigation-over-list ynforum_intergrate_menus">
	    <ul class="navigation">
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo $this->forum->getHref()?>"><?php echo $this -> translate("Back To Forum");?></a>
		    </li>
		    <li class="active">
		        <a class="buttonlink icon_forum_all_groups" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_group', true)?>"><?php echo $this -> translate("All Groups");?></a>
		    </li>
		    <?php if ($this->forum -> checkPermission($this->viewer, 'forum', 'fgroup.edit')): ?>
		    <li>
		        <a class="buttonlink icon_forum_my_groups" href="<?php echo $this -> url(array('action' => 'manage', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_group', true)?>"><?php echo $this -> translate("My Groups");?></a>
		    </li>
		    <?php endif;?>
		    <?php if ($this->forum -> checkPermission($this->viewer, 'forum', 'fgroup.create')): ?>
		    <li>
		        <a class="buttonlink icon_forum_create_group" href="<?php echo $this -> url(array('action' => 'create', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_group', true)?>"><?php echo $this -> translate("Create Group");?></a>
		    </li>
		    <?php endif;?>
		</ul>
	</div>
<?php if( count($this->paginator) > 0 ): ?>

<ul class='groups_browse'>
  <?php foreach( $this->paginator as $group ): ?>
    <li id="ynforum_group_item_<?php echo $group -> getIdentity()?>">
      <div class="groups_photo">
        <?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.normal')) ?>
      </div>
      <div class="groups_options">
          <?php if(($this->viewer() && $group->isOwner($this->viewer())) || $this->forum -> checkPermission($this->viewer, 'forum', 'fgroup.edit')): ?>
            <?php echo $this->htmlLink(array('route' => 'group_specific', 'action' => 'edit', 'group_id' => $group->getIdentity()), $this->translate('Edit Group'), array(
              'class' => 'buttonlink icon_group_edit'
            )) ?>
            <?php endif; ?>
            <?php if(($this->viewer() && $group->isOwner($this->viewer())) || $this->forum -> checkPermission($this->viewer, 'forum', 'fgroup.delete')): ?>
            <?php echo $this->htmlLink(array('route' => 'group_specific', 'action' => 'delete', 'group_id' => $group->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Group'), array(
                      'class' => 'buttonlink smoothbox icon_group_delete'
                    ));
            ?>
          <?php endif; ?>
           <?php if(($this->viewer() && $group->isOwner($this->viewer())) || $this -> forum -> checkPermission($this -> viewer ,'forum', 'fgroup.hlight')):?>
	            <?php
	            echo $this->htmlLink(array(
	                'route' => 'ynforum_group',
	                'action' => 'highlight',
	                'group_id' => $group->getIdentity(),
	                'forum_id' => $this -> forum ->getIdentity()
	                ), $this -> forum -> checkEventHighlight($group -> getIdentity(), 'group') ? $this->translate('Un-highlight') : $this->translate('Highlight'), 
	                array(
	              'class' => $this -> forum -> checkEventHighlight($group -> getIdentity(), 'group')?'buttonlink ynforum_group_highlight icon_ynforum_group_unhighlight':'buttonlink ynforum_group_highlight icon_ynforum_group_highlight'
	            ));
	            endif;?>
        </div>
      <div class="groups_info">
        <div class="groups_title">
          <h3><?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?></h3>
        </div>
        <div class="groups_members">
          <?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?>
          <?php echo $this->translate('led by');?> <?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle()) ?>
        </div>
        <div class="groups_desc">
          <?php echo $this->viewMore($group->getDescription()) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any groups.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array()); ?>
</div>
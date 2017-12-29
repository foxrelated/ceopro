<?php
	$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynforum/externals/scripts/core.js');   
       ?>
<div class="generic_layout_container layout_middle">
	<?php echo $this->partial('_forum_header.tpl', 'ynforum', array('forum'=>$this -> forum, 'linkedCategories' => $this -> linkedCategories, 'navigationForums' => $this -> navigationForums, 'moderators' => $this -> moderators))?>  
	<div class="tabs navigation-over-list ynforum_intergrate_menus">
	    <ul class="navigation">
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo $this->forum->getHref()?>"><?php echo $this -> translate("Back To Forum");?></a>
		    </li>
		    <li class="active">
		        <a class="buttonlink icon_forum_manage_announcement" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_announcement', true)?>"><?php echo $this -> translate("Manage Announcements");?></a>
		    </li>
		    <?php if ($this->forum -> checkPermission($this->viewer, 'forum', 'ynannoun.create')): ?>
		    <li>
		        <a class="buttonlink icon_forum_create_announcement" href="<?php echo $this -> url(array('action' => 'create', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_announcement', true)?>"><?php echo $this -> translate("Create Announcement");?></a>
		    </li>
		    <?php endif;?>
		</ul>
	</div>
	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="ynforum_announcement_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li id="ynforum_announcement_item_<?php echo $item -> getIdentity()?>">
          <div class='ynforum_announcement_browse_options'>
          	<?php if(($this->viewer() && $item->isOwner($this->viewer())) || $this -> forum -> checkPermission($this -> viewer ,'forum', 'ynannoun.edit')):?>
            <?php
            echo $this->htmlLink(array(
              'route' => 'ynforum_announcement',
              'action' => 'edit',
              'announcement_id' => $item->getIdentity(),
              'forum_id' => $this -> forum ->getIdentity(),
              'reset' => true,
            ), $this->translate('Edit Entry'), array(
              'class' => 'buttonlink icon_forum_post_edit',
            ));
            endif;?>
            <?php if(($this->viewer() && $item->isOwner($this->viewer())) || $this -> forum -> checkPermission($this -> viewer ,'forum', 'ynannoun.delete')):?>
            <?php
            echo $this->htmlLink(array(
                'route' => 'ynforum_announcement',
                'action' => 'delete',
                'announcement_id' => $item->getIdentity(),
                'forum_id' => $this -> forum ->getIdentity(),
                'format' => 'smoothbox'
                ), $this->translate('Delete Entry'), array(
              'class' => 'buttonlink smoothbox icon_forum_post_delete'
            ));
            endif;?>
            <?php if(($this->viewer() && $item->isOwner($this->viewer())) || $this -> forum -> checkPermission($this -> viewer ,'forum', 'ynannoun.hlight')):?>
            <?php
            echo $this->htmlLink(array(
                'route' => 'ynforum_announcement',
                'action' => 'highlight',
                'announcement_id' => $item->getIdentity(),
                'forum_id' => $this -> forum ->getIdentity()
                ), $item->highlight ? $this->translate('Un-highlight') : $this->translate('Highlight'), 
                array(
              'class' => $item->highlight?'buttonlink ynforum_announcement_highlight icon_ynforum_announcement_unhighlight':'buttonlink ynforum_announcement_highlight icon_ynforum_announcement_highlight'
            ));
            endif;?>
          </div>
          <div class='ynforum_announcement_browse_info'>
            <div class='ynforum_announcement_browse_info_title'>
              <b><?php echo $item->getTitle() ?></b>
            </div>
            <p>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle());?>
            <?php if($item->getOwner()):
           		$level = Engine_Api::_()->getItem('authorization_level', $item->getOwner() -> level_id);
				echo "(".$level -> title.")";
			endif;           	
			?>
            </p>
            <p class='ynforum_announcement_browse_info_blurb'>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any announcements.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array()); ?>
</div>
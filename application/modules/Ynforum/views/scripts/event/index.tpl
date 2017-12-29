<?php
	$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynforum/externals/scripts/core.js');   
       ?>
<div class="generic_layout_container layout_middle">
	<?php echo $this->partial('_forum_header.tpl', 'ynforum', array('forum'=>$this -> forum, 'linkedCategories' => $this -> linkedCategories, 'navigationForums' => $this -> navigationForums, 'moderators' => $this -> moderators))?>  
	<!-- Event menus-->
	<div class="tabs navigation-over-list ynforum_intergrate_menus">
	    <ul class="navigation">
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo $this->forum->getHref()?>"><?php echo $this -> translate("Back To Forum");?></a>
		    </li>
		    <li class="active">
		        <a class="buttonlink icon_forum_all_events" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><?php echo $this -> translate("All Events");?></a>
		    </li>
		    <?php if ($this->forum -> checkPermission($this->viewer, 'forum', 'fevent.edit')): ?>
		    <li>
		        <a class="buttonlink icon_forum_my_events" href="<?php echo $this -> url(array('action' => 'manage', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><?php echo $this -> translate("My Events");?></a>
		    </li>
		     <?php endif;?>
		    <?php if ($this->forum -> checkPermission($this->viewer, 'forum', 'fevent.create')): ?>
		    <li>
		        <a class="buttonlink icon_forum_create_event" href="<?php echo $this -> url(array('action' => 'create', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><?php echo $this -> translate("Create Event");?></a>
		    </li>
		    <?php endif;?>
		</ul>
		<div>
			 <?php echo $this->form->render($this) ?> 
		</div>
	</div>
	<?php if( count($this->paginator) > 0 ): ?>
  <ul class='events_browse'>
    <?php foreach( $this->paginator as $event ): ?>
      <li id="ynforum_event_item_<?php echo $event -> getIdentity()?>">
        <div class="events_photo">
          <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
        </div>
        <div class="events_options">
                <?php if (($this->viewer() && $event->isOwner($this->viewer())) || $this->forum -> checkPermission($this->viewer, 'forum', 'fevent.edit')): ?>
                <?php
	                echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'edit', 'event_id' => $event->getIdentity()), $this->translate('Edit Event'), array(
	                'class' => 'buttonlink icon_event_edit'
	                ));
                endif;
                ?>
                <?php
                if(($this->viewer() && $event->isOwner($this->viewer())) || $this->forum -> checkPermission($this->viewer, 'forum', 'fevent.delete')):
	                echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Event'), array(
	                'class' => 'buttonlink smoothbox icon_event_delete'
	                ));
                ?>
                <?php endif; ?>
                 <?php if(($this->viewer() && $event->isOwner($this->viewer())) || $this -> forum -> checkPermission($this -> viewer ,'forum', 'fevent.hlight')):?>
	            <?php
	            echo $this->htmlLink(array(
	                'route' => 'ynforum_event',
	                'action' => 'highlight',
	                'event_id' => $event->getIdentity(),
	                'forum_id' => $this -> forum ->getIdentity()
	                ), $this -> forum -> checkEventHighlight($event -> getIdentity(), 'event') ? $this->translate('Un-highlight') : $this->translate('Highlight'), 
	                array(
	              'class' => $this -> forum -> checkEventHighlight($event -> getIdentity(), 'event')?'buttonlink ynforum_event_highlight icon_ynforum_event_unhighlight':'buttonlink ynforum_event_highlight icon_ynforum_event_highlight'
	            ));
	            endif;?>
            </div>
        <div class="events_info">
          <div class="events_title">
            <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
          </div>
      <div class="events_members">
        <?php echo $this->locale()->toDateTime($event->starttime) ?>
      </div>
          <div class="events_members">
            <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
            <?php echo $this->translate('led by') ?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          </div>
          <div class="events_desc">
            <?php echo $event->getDescription() ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any events.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array()); ?>
</div>
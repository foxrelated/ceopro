<ul class="generic_list_widget">
	<?php if ($this->forum -> checkPermission($this->viewer, 'forum', 'fevent.create')): ?>
	<li class="create_event">
        <a class="buttonlink icon_forum_create_event" href="<?php echo $this -> url(array('action' => 'create', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><?php echo $this -> translate("Create Event");?></a>
    </li>
	 <?php endif;?>
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'thumb')) ?>
      </div>
      <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </div>
        <div class="stats">
          <?php echo $this->timestamp(strtotime($item->starttime)) ?>
          - <?php echo $this->translate('led by %1$s',
              $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>
          <?php if( $this->popularType == 'view' ): ?>
            - <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
          <?php else:?>
            - <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>
          <?php endif; ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
  <?php if($this->paginator -> getTotalItemCount()): ?>
   <li>
        <a class="buttonlink icon_forum_all_events" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><?php echo $this -> translate("View All");?></a>
    </li>
   <?php endif;?>
</ul>

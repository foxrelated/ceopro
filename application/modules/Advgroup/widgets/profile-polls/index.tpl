<div class="group_poll_list">
<?php if( 0 == count($this->paginator) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no polls yet.') ?>
      <?php if( $this->canCreate ): ?>
      	<?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
        	  '<a href="'.$this->url(array('controller'=>'poll','action' => 'create','subject' =>$this->subject()->getGuid()), 'group_extended').'">', '</a>') ?>
      <?php endif; ?>
    </span>
  </div>

<?php else: // $this->polls is NOT empty ?>
	<?php if( $this->canCreate ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'group_extended',
        'controller' => 'poll',
        'action' => 'create',
        'subject' =>  $this->subject()->getGuid(),
      ), $this->translate('Create New Poll'), array(
        'class' => 'buttonlink icon_group_poll_new'
    )) ?>
    <br/>
  <?php endif; ?>

  <ul class="polls_browse">
    <?php foreach ($this->paginator as $poll): ?>
    <li id="poll-item-<?php echo $poll->poll_id ?>">
      <?php echo $this->htmlLink(
        $poll->getHref(),
        $this->itemPhoto($poll->getOwner(), 'thumb.icon', $poll->getOwner()->getTitle()),
        array('class' => 'polls_browse_photo')
      ) ?>
      <div class="polls_browse_info">
        <h3 class="polls_browse_info_title">
          <?php echo $this->htmlLink($poll->getHref(), $poll->getTitle()) ?>
          <?php if( $poll->closed ): ?>
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advgroup/externals/images/poll/close.png' alt="<?php echo $this->translate('Closed') ?>" />
          <?php endif ?>
        </h3>
        <div class="polls_browse_info_date">
          <?php echo $this->translate('Posted by %s', $this->htmlLink($poll->getOwner(), $poll->getOwner()->getTitle())) ?>
          <?php echo $this->timestamp($poll->creation_date) ?>
          -
          <?php echo $this->translate(array('%s vote', '%s votes', $poll->vote_count), $this->locale()->toNumber($poll->vote_count)) ?>
          -
          <?php echo $this->translate(array('%s view', '%s views', $poll->view_count), $this->locale()->toNumber($poll->view_count)) ?>
        </div>
        <?php if (!empty($poll->description)): ?>
          <div class="polls_browse_info_desc">
            <?php echo $poll->description ?>
          </div>
        <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
<?php endif; // $this->polls is NOT empty ?>
</div>
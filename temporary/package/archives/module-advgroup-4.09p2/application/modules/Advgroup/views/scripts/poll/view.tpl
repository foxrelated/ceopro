<!-- Header -->
<h2>
    <?php echo $this->group->__toString();
          echo $this->translate('&#187; Polls');
    ?>
</h2>
<!-- Menu Bar -->
<div class="group_discussions_options">
 <a class="buttonlink icon_back" href="javascript:void(0);" onclick="history.go(-1);"> <b style ="margin-bottom: 5px"><?php echo $this->translate('Back');?></b></a>
 <?php if( $this->canEdit): ?>
    <?php echo $this->htmlLink(array(
            'route' => 'group_extended',
            'controller' => 'poll',
            'action' => 'edit',
            'poll_id' => $this->poll->getIdentity(),
            'reset' => true,
          ), $this->translate('Edit Poll'), array(
            'class' => 'smoothbox buttonlink icon_group_poll_edit'
          )) ?>
 <?php if( !$this->poll->closed ): ?>
            <?php echo $this->htmlLink(array(
              'route' => 'group_extended',
              'controller' => 'poll',
              'action' => 'close',
              'poll_id' => $this->poll->getIdentity(),
              'closed' => 1,
            ), $this->translate('Close Poll'), array(
              'class' => 'smoothbox buttonlink icon_group_poll_close'
            )) ?>

          <?php else: ?>
            <?php echo $this->htmlLink(array(
              'route' => 'group_extended',
              'controller' => 'poll',
              'action' => 'close',
              'poll_id' => $this->poll->getIdentity(),
              'closed' => 0,
            ), $this->translate('Open Poll'), array(
              'class' => 'smoothbox buttonlink icon_group_poll_open'
            )) ?>
          <?php endif; ?>

          <?php echo $this->htmlLink(array(
            'route' => 'group_extended',
            'controller' => 'poll',
            'action' => 'delete',
            'poll_id' => $this->poll->getIdentity(),
          ), $this->translate('Delete Poll'), array(
            'class' => 'smoothbox buttonlink smoothbox icon_group_poll_delete'
          )) ?>
  <?php endif; ?>
</div>
<br/>

<!-- Content -->
<div>
  <h3>
    <?php echo $this->poll->title ?>

    <?php if( $this->poll->closed ): ?>
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advgroup/externals/images/poll/close.png' alt="<?php echo $this->translate('Closed') ?>" />
    <?php endif ?>
  </h3>
  <p><?php echo $this->translate('Created by %s', $this->htmlLink($this->owner, $this->owner->getTitle())) ?></p>
  <br/>
  <div class="poll_desc">
    <?php echo $this->poll->description ?>
  </div>

  <?php
    // poll, pollOptions, canVote, canChangeVote, hasVoted, showPieChart
    echo $this->render('_poll.tpl');
  ?>
</div>
<br/>
<?php echo $this->action("list", "comment", "core", array("type"=>"advgroup_poll", "id"=>$this->poll->getIdentity())); ?>
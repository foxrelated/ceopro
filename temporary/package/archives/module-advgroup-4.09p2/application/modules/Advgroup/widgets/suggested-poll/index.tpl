<ul class = "global_form_box" style="background: none; margin-bottom: 5px;">
  <ul class="polls_browse" >
    <?php $poll = $this->poll;?>
    <li id="poll-item-<?php echo $poll->poll_id ?>" style="padding:0px;">
      <?php echo $this->htmlLink(
        $poll->getHref(),
        $this->itemPhoto($poll->getOwner(), 'thumb.icon', $poll->getOwner()->getTitle()),
        array('class' => 'polls_browse_photo')
      ) ?>
      <div class="polls_browse_info">
        <div class="polls_browse_info_title" style="word-wrap:break-word">
          <?php $poll_name = Engine_Api::_()->advgroup()->subPhrase($poll->getTitle(),50);?>
          <b><?php echo $this->htmlLink($poll->getHref(), $poll_name) ?></b>
        </div>
        <div class="polls_browse_info_date"  style="word-wrap:break-word">
          <?php $owner_name = Engine_Api::_()->advgroup()->subPhrase($poll->getOwner()->getTitle(),13);?>
          <?php echo $this->translate('By %s', $this->htmlLink($poll->getOwner(),$owner_name)) ?>
        </div>
        <div class="polls_browse_info_date">
          <?php echo $this->timestamp($poll->creation_date) ?>
        </div>
        <div class="polls_browse_info_date">
          <?php echo $this->translate(array('%s vote', '%s votes', $poll->vote_count), $this->locale()->toNumber($poll->vote_count)) ?>
          -
          <?php echo $this->translate(array('%s view', '%s views', $poll->view_count), $this->locale()->toNumber($poll->view_count)) ?>
        </div>
      </div>
    </li>
  </ul>
</ul>
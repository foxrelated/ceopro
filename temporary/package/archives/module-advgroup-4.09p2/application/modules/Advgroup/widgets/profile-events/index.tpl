<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_groups_events').getParent();
    $('profile_groups_events_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_groups_events_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_groups_events_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_groups_events_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<div class="group_album_options">
  <?php if( $this->canAdd ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'event_general',
        'controller' => 'event',
        'action' => 'create',
        'parent_type'=> 'group',
        'subject_id' => $this->subject()->getIdentity(),
      ), $this->translate('Add Events'), array(
        'class' => 'buttonlink icon_group_event_new'
    )) ?>
  <?php endif; ?>
</div>
<br/>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <ul id="profile_groups_events">
    <?php foreach( $this->paginator as $event ): ?>
      <li class="events_item">
        <div class='groups_photo'>
          <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
        </div>
        <div class='groups_info'>
          <div class="groups_title">
            <?php $event_name = Engine_Api::_()->advgroup()->subPhrase($event->getTitle() , 50);
                  echo $this->htmlLink($event->getHref(),"<b>".$event_name."</b>") ?>
          </div>
          <span class="groups_members">
            <?php echo $this->translate('By');?>
            <?php $owner_name = Engine_Api::_()->advgroup()->subPhrase($event->getOwner()->getTitle(),25);
                    echo $this->htmlLink($event->getOwner()->getHref(), $owner_name) ?>
          </span>
          -
          <span class="groups_members">
            <?php echo $this->timestamp($event->creation_date) ?>
          </span>
          <p class="groups_desc" style="text-align: justify;">
            <?php echo Engine_Api::_()->advgroup()->subPhrase($event->description,250);?>
          </p>
        </div>
      </li>
    <?php endforeach;?>
  </ul>

    <div>
      <div id="profile_groups_events_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="profile_groups_events_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    </div>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No events have been added to this group yet.');?>
    </span>
  </div>

<?php endif; ?>

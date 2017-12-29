<a id="group_profile_members_anchor"></a>

<script type="text/javascript">
  var groupMemberSearch = '<?php echo $this->search ?>';
  var groupMemberPage = Number(<?php echo sprintf('%d', $this->members->getCurrentPageNumber()) ?>);
  var waiting = '<?php echo $this->waiting ?>';
  en4.core.runonce.add(function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    $('group_members_search_input').addEvent('keypress', function(e) {
      if( e.key != 'enter' ) return;

      en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'subject' : en4.core.subject.guid,
          'search' : this.value
        }
      }), {
        'element' : $('group_profile_members_anchor').getParent()
      });
    });
  });

  var paginateGroupMembers = function(page) {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'search' : groupMemberSearch,
        'page' : page,
        'waiting' : waiting
      }
    }), {
      'element' : $('group_profile_members_anchor').getParent()
    });
  }
</script>

<?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
<script type="text/javascript">
  var showWaitingMembers = function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'waiting' : true
      }
    }), {
      'element' : $('group_profile_members_anchor').getParent()
    });
  }
  var showFullMembers = function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid
      }
    }), {
      'element' : $('group_profile_members_anchor').getParent()
    });
  }
</script>
<?php endif; ?>

<?php if( !$this->waiting ): ?>
  <div class="group_members_info">
    <div class="group_members_search">
      <input id="group_members_search_input" type="text" value="<?php echo $this->translate('Search Members') ?>" onfocus="$(this).store('over', this.value);this.value = '';" onblur="this.value = $(this).retrieve('over');">
    </div>
    <div class="group_members_total">
      <?php if( '' == $this->search ): ?>
        <?php echo $this->translate(array('This wiki space has %1$s member.', 'This group has %1$s members.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
      <?php else: ?>
        <?php echo $this->translate(array('This wiki space has %1$s member that matched the query "%2$s".', 'This wiki space has %1$s members that matched the query "%2$s".', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->search) ?>
      <?php endif; ?>
    </div>
    <?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
      <div class="group_members_total">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="group_members_info">
    <div class="group_members_total">
      <?php echo $this->translate(array('This wiki space has %s member waiting for approval or waiting for an invite response.', 'This wiki space has %s members waiting for approval or waiting for an invite response.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
      <?php if( !empty($this->fullMembers) && $this->fullMembers->getTotalItemCount() > 0 ): ?>
        <div class="group_members_total">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View all approved members'), array('onclick' => 'showFullMembers(); return false;')) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<?php if( $this->members->getTotalItemCount() > 0 ): ?>
  <ul class='group_members'>
    <?php foreach( $this->members as $member ):
      if( !empty($member->resource_id) ) {
        $memberInfo = $member;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->page->membership()->getMemberInfo($member);
      }
      $listItem = $this->list->get($member);
      $isOfficer = ( null !== $listItem );
      ?>

      <li id="group_member_<?php echo $member->getIdentity() ?>">

        <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'group_members_icon')) ?>
        <div class='group_members_options'>

          <?php // Remove/Promote/Demote member ?>
          <?php if( $this->page->isOwner($this->viewer()) ||$this->page->isParentWikiOwner($this->viewer())): ?>

            <?php if( !$this->page->isOwner($member) && $memberInfo->active == true ): ?>
              <?php echo $this->htmlLink(array('route' => 'ynwiki_general', 'action' => 'remove', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove Member'), array(
                'class' => 'buttonlink smoothbox icon_friend_remove'
              )) ?>
            <?php endif; ?>
            <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
              <?php echo $this->htmlLink(array('route' => 'ynwiki_extended', 'controller' => 'member', 'action' => 'approve', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array(
                'class' => 'buttonlink smoothbox icon_group_accept'
              )) ?>
              <?php echo $this->htmlLink(array('route' => 'ynwiki_extended', 'controller' => 'member', 'action' => 'cancel-invite', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array(
                'class' => 'buttonlink smoothbox icon_group_reject'
              )) ?>
            <?php endif; ?>
            <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true && $memberInfo->rejected_ignored == false )
               echo $this->htmlLink(array('route' => 'ynwiki_extended', 'controller' => 'member', 'action' => 'cancel-invite', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
                'class' => 'buttonlink smoothbox icon_group_cancel'
              ));
           
            if($memberInfo->active == false && $memberInfo->rejected_ignored == true){
              	 echo $this->htmlLink(array('route' => 'ynwiki_extended', 'controller' => 'invite-manage', 'action' => 'reinvite', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Resend Invite'), array(
                'class' => 'buttonlink smoothbox icon_group_resend_invite'
             	 ));
              	 echo $this->htmlLink(array('route' => 'ynwiki_extended', 'controller' => 'member', 'action' => 'cancel-invite', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
              	 		'class' => 'buttonlink smoothbox icon_group_cancel'
              	 ));
            }
          
              ?>


            <?php if( $memberInfo->active ): ?>
              <?php if( $isOfficer && !$this->page->isOwner($member) ): ?>
                <?php echo $this->htmlLink(array('route' => 'ynwiki_general', 'action' => 'demote', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Demote Officer'), array(
                  'class' => 'buttonlink smoothbox icon_group_demote'
                )) ?>
              <?php elseif( !$this->page->isOwner($member) ): ?>
                <?php echo $this->htmlLink(array('route' => 'ynwiki_general', 'action' => 'promote', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Make Officer'), array(
                  'class' => 'buttonlink smoothbox icon_group_promote'
                )) ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <div class='group_members_body'>
         
            <span class='group_members_status'>
              <?php echo $this->htmlLink($member->getHref(), $member->getTitle());
             
              
              ?>
              
              <?php // Titles ?>
              <?php if( $this->page->isOwner($member) ): ?>
                (<?php echo ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') ) ?><?php if( $this->page->isOwner($this->viewer()) ): ?><?php echo $this->htmlLink(array('route' => 'ynwiki_extended', 'controller' => 'member', 'action' => 'edit', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity(), 'format' => 'smoothbox'), '&nbsp;', array('class' => 'smoothbox')) ?><?php endif; ?>)

              <?php elseif( $isOfficer ): ?>
                (<?php echo ( $memberInfo->title ? $memberInfo->title : $this->translate('officer') ) ?><?php if( $this->page->isOwner($this->viewer()) ): ?><?php echo $this->htmlLink(array('route' => 'ynwiki_extended', 'controller' => 'member', 'action' => 'edit', 'pageId' => $this->page->getIdentity(), 'user_id' => $member->getIdentity(), 'format' => 'smoothbox'), '&nbsp;', array('class' => 'smoothbox')) ?><?php endif; ?>)
              <?php endif; ?>


            </span>
            <span>
              <?php  if($memberInfo->active == false && $memberInfo->rejected_ignored == true) 
              	echo  " (".$this->translate('Ignored').")"; 
              ?>
            </span>
           
          
        </div>
      </li>
    <?php endforeach;?>
  </ul>


  <?php if( $this->members->count() > 1 ): ?>
    <div>
      <?php if( $this->members->getCurrentPageNumber() > 1 ): ?>
        <div id="user_group_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => 'paginateGroupMembers(groupMemberPage - 1)',
            'class' => 'buttonlink icon_previous'
          )); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->members->getCurrentPageNumber() < $this->members->count() ): ?>
        <div id="user_group_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => 'paginateGroupMembers(groupMemberPage + 1)',
            'class' => 'buttonlink_right icon_next'
          )); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

<?php endif; ?>

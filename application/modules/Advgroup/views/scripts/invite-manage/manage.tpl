<script type="text/javascript">
//check all invite
en4.core.runonce.add(function(){
    $$('th.advgroup_inviteman_table_short input[type=checkbox]').addEvent('click', function(){
        var checked = $(this).checked;
        var checkboxes = $$('td.advgroup_inviteman_check input[type=checkbox]');
        checkboxes.each(function(item){
          item.checked = checked;
        });
    });
});

function actionSelected(actionType){
   var checkboxes = $$('td.advgroup_inviteman_check input[type=checkbox]');
   var selecteditems = [];

   checkboxes.each(function(item){
     var checked = item.checked;
     var value = item.value;
     if (checked == true && value != 'on'){
       selecteditems.push(value);
     }
   });
   $('action_selected').action = en4.core.baseUrl +'groups/invite-manage/' + actionType + '-selected/group_id/'+<?php echo $this->group->getIdentity() ?>;
   $('ids').value = selecteditems;
   $('action_selected').submit();
 }
var resendInvite = function(group_id,user_id) {
    Smoothbox.close();
    en4.core.request.send(
        new Request.HTML({
            url : en4.core.baseUrl + 'groups/invite-manage/ajax-reinvite/group_id/' +group_id+'/user_id/'+user_id+'/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
            data : {
                format : 'html',
            },
            onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                location.reload();
            }
        }));
};
</script>

<!-- Header -->
<h2><?php echo $this->translate('Invitations Management') ?></h2>

<!-- Menu Bar-->
<div class="group_discussions_options">
<?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
</div>

<!-- Content -->
<?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
    <div class="group_members_info">
        <div class="group_members_total">
          <?php echo $this->translate(
                    array('This group has %s invitation waiting for resend.',
                          'This group has %s invitations waiting for resend.',
                          $this->waitingMembers->getTotalItemCount()),
                          $this->locale()->toNumber($this->waitingMembers->getTotalItemCount()
                    )); ?>
    </div>
    </div>
    <br />
    <?php if( count($this->waitingMembers) ): ?>
    <table class='advgroup_inviteman_table'>
        <thead>
            <tr>
                  <th class='advgroup_inviteman_table_short' ><input type='checkbox' class='checkbox' /></th>
                  <th><?php echo $this->translate("User") ?></th>
                  <th><?php echo $this->translate("Status") ?></th>
                  <th><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach( $this->waitingMembers as $member ):
              if( !empty($member->resource_id) ) {
                $memberInfo = $member;
                $member = $this->item('user', $memberInfo->user_id);
              } else {
                $memberInfo = $this->group->membership()->getMemberInfo($member);
              }
          ?>
        <tr>
          <td class="advgroup_inviteman_check" ><input type='checkbox' class='checkbox' value='<?php echo $memberInfo->user_id ?>'></td>
          <td>
             <?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>
          </td>
          <td><?php if($memberInfo->rejected_ignored == true)
                    {
                      echo $this->translate('Rejected / Ignored');
                    }
                    else
                    {
                      echo $this->translate("Not Replied");
                    }
               ?>
          </td>
          <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true && $memberInfo->rejected_ignored == false ) :?>
          <td>
            <?php
                   echo $this->htmlLink(array('route'      => 'group_extended',
                                              'controller' => 'member',
                                              'action'     => 'cancel-oneinvite',
                                              'group_id'   => $this->group->getIdentity(),
                                              'user_id'    => $member->getIdentity()
                                              ),
                                        $this->translate('Cancel Invite'),
                                        array('class' => 'buttonlink smoothbox icon_group_cancel')
                                        );
                   endif;
              ?>
          </td>
          <?php if($memberInfo->active == false && $memberInfo->rejected_ignored == true):?>
          <td>
              <?php
                echo $this->htmlLink(array('route'      => 'group_extended',
                                           'controller' => 'invite-manage',
                                           'action'     => 'reinvite',
                                           'group_id'   => $this->group->getIdentity(),
                                           'user_id'    => $member->getIdentity()
                                            ),
                                     $this->translate('Resend Invite'),
                                     array('class' => 'buttonlink smoothbox icon_group_resend_invite')
                                     );
                endif;
                ?>
           </td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <?php endif;?>

    <!-- Button -->
    <br/>
    <div class='buttons'>
          <button type='button' onclick="javascript:actionSelected('reinvite');"><?php echo $this->translate("Invite Selected") ?></button>
    </div>

    <!-- Form -->
    <br/>
    <form id='action_selected' method="post" action="">
          <input type="hidden" id="ids" name="ids" value=""/>
    </form>
    <!-- Paginator -->
    <div>
        <?php echo $this->paginationControl($this->waitingMembers); ?>
    </div>
<?php else:?>
   <br />
   <div class="tip">
          <span> <?php echo $this->translate("This group don't have any invitations.");?> </span>
   </div>
 <?php endif;?>
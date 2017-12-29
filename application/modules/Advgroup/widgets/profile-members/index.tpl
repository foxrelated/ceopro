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
          'search' : this.value,
          'search_type': this.getAttribute("search_type")
        }
      }), {
        'element' : $('group_profile_members_anchor').getParent()
      });
    });
  });

  var refeshPage =  function (){
	  var r2 = new Request.HTML({
          url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
          data : {
              format : 'html',
              subject : en4.core.subject.guid	              
                         
      	},
      	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
       
      		arrtab = $$(".layout_core_container_tabs > div");
      		
      		for (var i=0;i<arrtab.length;i++)
      		{ 
      			style = arrtab[i].getProperty('style');
					if(style == ""){
						$$(".layout_core_container_tabs > div")[i].set('html', responseHTML);
						number = $$('ul.group_members >li').length;
						$$('ul#main_tabs > li.active > a > span')[0].set('html', '('+number+')');
						
						eval(responseJavaScript);

						smoothboxEvent = $$(".group_members > li > div.group_members_options a.smoothbox  ");
						for(var j=0;j<smoothboxEvent.length;j++)
						{
							smoothboxEvent[j].addEvent('click', function(event){
							    event.stop();
							    Smoothbox.open(this);
							   });
						}							
					}	      			
      		}	                  	
      	}
  	});
	r2.send();	
  };
  var refeshPage2 =  function (responseHTML,responseJavaScript){
	arrtab = $$(".layout_core_container_tabs > div");
      		
      		for (var i=0;i<arrtab.length;i++)
      		{ 
      			style = arrtab[i].getProperty('style');
					if(style == ""){
						$$(".layout_core_container_tabs > div")[i].set('html', responseHTML);
						number = $$('ul.group_members >li').length;
						$$('ul#main_tabs > li.active > a > span')[0].set('html', '('+number+')');
						
						eval(responseJavaScript);

						smoothboxEvent = $$("div.layout_advgroup_profile_members .group_members > li > div.group_members_options a.smoothbox  ")
						
						for(var j=0;j<smoothboxEvent.length;j++)
						{
							smoothboxEvent[j].addEvent('click', function(event){
							    event.stop();
							    Smoothbox.open(this);
							   });
						}	
						var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
					    $('group_members_search_input').addEvent('keypress', function(e) {
					      if( e.key != 'enter' ) return;

					      en4.core.request.send(new Request.HTML({
					        'url' : url,
					        'data' : {
					          'format' : 'html',
					          'subject' : en4.core.subject.guid,
					          'search' : this.value,
					          'search_type': this.getAttribute("search_type")
					        }
					      }), {
					        'element' : $('group_profile_members_anchor').getParent()
					      });
					    });								
					}	      			
      		}	       
  };
  

 	 var removeMember = function(group_id,user_id, ftitle) {
 		Smoothbox.close();
 		en4.core.request.send(
		new Request.HTML({
	        url : en4.core.baseUrl + 'groups/member/ajax-remove/group_id/' +group_id+'/user_id/'+user_id+'/ftitle/'+ftitle+'/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
	        data : {
	            format : 'html',
	            group_id : group_id,
	            user_id : user_id, 
	            ftitle: ftitle    
	    	},
	    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	    	       
	      		refeshPage2(responseHTML,responseJavaScript)  ;            	
	      	}
		}));

 	 }	  
	  var approveRequest = function(group_id,user_id) {
			Smoothbox.close();
			en4.core.request.send(
				new Request.HTML({
		        url : en4.core.baseUrl + 'groups/member/ajax-approve/group_id/' +group_id+'/user_id/'+user_id+'/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
		        data : {
		            format : 'html',		               
		    	},
		    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	    	       
	      		refeshPage2(responseHTML,responseJavaScript)  ;            	
	      	}
			}));
		    
		  };
		  var resendInvite = function(group_id,user_id) {
				Smoothbox.close();
				en4.core.request.send(
					new Request.HTML({
			        url : en4.core.baseUrl + 'groups/invite-manage/ajax-reinvite/group_id/' +group_id+'/user_id/'+user_id+'/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
			        data : {
			            format : 'html',		               
			    	},
			    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
		    	       
		      		refeshPage2(responseHTML,responseJavaScript)  ;            	
		      	}
				}));
			    
			  };
		  var cancelInvite = function(group_id,user_id) {
				Smoothbox.close();
				en4.core.request.send(
					new Request.HTML({
			        url : en4.core.baseUrl + 'groups/member/ajax-cancel-invite/group_id/' +group_id+'/user_id/'+user_id+'/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
			        data : {
			            format : 'html',		               
			    	},
			    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
		    	       
		      		refeshPage2(responseHTML,responseJavaScript)  ;        
			    	}
			    	
				}));
			    
			  };
	  var promoteMember = function(group_id,user_id) {	 
	  	Smoothbox.close(); 		
			en4.core.request.send(
				new Request.HTML({
		        url : en4.core.baseUrl + 'groups/member/ajax-promote/group_id/' +group_id+'/user_id/'+user_id+'/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
		        data : {
		            format : 'html',
		          	          
		    	},	
		    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
		    	       
		      		refeshPage2(responseHTML)  ;        
		    	}	    	
			}));
		    
		  };
		  var demoteMember = function(group_id,user_id) {	 
		  	Smoothbox.close(); 		
				en4.core.request.send(
					new Request.HTML({
			        url : en4.core.baseUrl + 'groups/member/ajax-demote/group_id/' +group_id+'/user_id/'+user_id+'/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
			        data : {
			            format : 'html',
			           	          
			    	},	
			    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
		    	       
		      		refeshPage2(responseHTML)  ;        
		    	}	 	    	
				}));
			    
			  };
  
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
  function openPopup(url)
    {
    	Smoothbox.open(url);     
    }
</script>


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
</script>  

<script type="text/javascript">
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



<script type="text/javascript">
	var showBlackListMembers = function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'blacklist_enable' : true
      }
    }), {
      'element' : $('group_profile_members_anchor').getParent()
    });
  }
</script>



<?php if( $this->blacklist_enable ): ?>	
		<div class="group_members_info">
			<div class="group_members_search">
			<input id="group_members_search_input" type="text" search_type="blacklist"
				value="<?php echo $this->translate('Search Members') ?>"
				onfocus="$(this).store('over', this.value);this.value = '';"
				onblur="this.value = $(this).retrieve('over');">
		</div>
		<div class="group_members_total">
			<?php if( '' == $this->search ): ?>
				<?php echo $this->translate(array('This group has %1$s member in blacklist.', 'This group has %1$s members in blacklist.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
			<?php else: ?>
				<?php echo $this->translate(array('This group has %1$s black member that matched the query "%2$s".', 'This group has %1$s black members that matched the query "%2$s".', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->search) ?>
		    <?php endif; ?>	
		</div>	
			<?php if( !empty($this->fullMembers) && $this->fullMembers->getTotalItemCount() > 0 ): ?>
			<div class="group_members_total">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View all approved members'), array('onclick' => 'showFullMembers(); return false;')) ?>
			</div>
			<?php endif; ?>
			
			<?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
			<div class="group_members_total">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
			</div>
			<?php endif; ?>
			
		
	</div>

<?php else: ?>	
<?php if( !$this->waiting ): ?>
<div class="group_members_info">
	<div class="group_members_search">
		<input id="group_members_search_input" type="text" search_type="approved"
			value="<?php echo $this->translate('Search Members') ?>"
			onfocus="$(this).store('over', this.value);this.value = '';"
			onblur="this.value = $(this).retrieve('over');">
	</div>
	<div class="group_members_total">
		<?php if( '' == $this->search ): ?>
			<?php echo $this->translate(array('This group has %1$s member.', 'This group has %1$s members.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
		<?php else: ?>
			<?php echo $this->translate(array('This group has %1$s member that matched the query "%2$s".', 'This group has %1$s members that matched the query "%2$s".', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->search) ?>
		<?php endif; ?>
	</div>
	
	<?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
	<div class="group_members_total">
		<?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
	</div>
	<?php endif; ?>
	
	<?php if( !empty($this->blacklistMembers) && $this->blacklistMembers->getTotalItemCount() > 0 ): ?>
	<div class="group_members_total">
		<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Users in BlackList'), array('onclick' => 'showBlackListMembers(); return false;')) ?>
	</div>
	<?php endif; ?>
	
</div>
<?php else: ?>
	<div class="group_members_info">
		<div class="group_members_search">
			<input id="group_members_search_input" type="text" search_type="waiting"
				value="<?php echo $this->translate('Search Members') ?>"
				onfocus="$(this).store('over', this.value);this.value = '';"
				onblur="this.value = $(this).retrieve('over');">
		</div>
		<div class="group_members_total">
			<?php if( '' == $this->search ): ?>
				<?php echo $this->translate(array('This group has %s member waiting for approval or waiting for an invite response.', 'This group has %s members waiting for approval or waiting for an invite response.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
			<?php else: ?>
				<?php echo $this->translate(array('This group has %1$s waiting member that matched the query "%2$s".', 'This group has %1$s waiting members that matched the query "%2$s".', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->search) ?>
			<?php endif; ?>
		</div>	
			<?php if( !empty($this->fullMembers) && $this->fullMembers->getTotalItemCount() > 0 ): ?>
			<div class="group_members_total">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View all approved members'), array('onclick' => 'showFullMembers(); return false;')) ?>
			</div>
			<?php endif; ?>
			
			<?php if( !empty($this->blacklistMembers) && $this->blacklistMembers->getTotalItemCount() > 0 ): ?>
			<div class="group_members_total">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Users in BlackList'), array('onclick' => 'showBlackListMembers(); return false;')) ?>
			</div>
			<?php endif; ?>
			
		
	</div>
<?php endif; ?>
<?php endif; ?>
<?php if( $this->members->getTotalItemCount() > 0 ): ?>
<?php
	  $viewer = $this->viewer;
	  if( !empty($viewer->resource_id) ) {
        $memberInfo = $viewer;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->group->membership()->getMemberInfo($viewer);
      }
      $listItem_checkviewer = $this->list->get($viewer);
      $isOfficer_checkviewer = ( null !== $listItem_checkviewer );
 ?>	
<form action="groups/member/action" method="post" id="manage_member" onsubmit="return openPopupConfirm();">
	<input type='hidden' name="group_id" value='<?php echo $this->group->getIdentity()?> '/>
<ul class='group_members'>
	<?php foreach( $this->members as $member ):
	if( !empty($member->resource_id) ) {
        $memberInfo = $member;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->group->membership()->getMemberInfo($member);
      }
      $listItem = $this->list->get($member);
      $isOfficer = ( null !== $listItem );
	  


      //Zend_Registry::get('Zend_Log')->log(print_r($listItem,true),Zend_Log::DEBUG);
      ?>

	<li id="group_member_<?php echo $member->getIdentity() ?>">
		<?php if($this->group->canManageUser($this->viewer())):?>
		<?php if($isOfficer_checkviewer && !$isOfficer || $this->group->isOwner($viewer)):?>
		<?php if($member->getIdentity() != $viewer->getIdentity() ):?>		
		<?php if(!$this->group->isOwner($member)):?>		
			<input class='member_checklist' type="checkbox" name="member_checklist[]" value="<?php echo $member->getIdentity()?>"/>
		<?php endif; ?>
		<?php endif; ?>
		<?php endif; ?>
		<?php endif; ?>
		<div class="content">
			<div class="photo">
				<a href="<?php echo $member->getHref() ?>">
					<?php if($member -> getPhotoUrl("thumb.profile")): ?>
						<span class="image-thumb" style="background-image:url('<?php echo $member -> getPhotoUrl("thumb.profile"); ?>')"></span>
					<?php else: ?>
						<span class="image-thumb" style="background-image:url('<?php echo $this->baseURL(); ?>/application/modules/User/externals/images/nophoto_user_thumb_profile.png')"></span>
					<?php endif; ?>					
				</a>				
			</div>
			<div class='group_members_body'>
				<div class="title">
					<strong><?php echo $this->htmlLink($member->getHref(), $member->getTitle()); ?> </strong>
				</div>
				<div class="stats">
					<span class='group_members_status'>
					<?php if( $this->group->isOwner($member) ): ?>					
						(<?php echo ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') ) ?>
						<?php if( $this->group->isOwner($this->viewer()) ): ?> 
							<?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'edit', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'format' => 'smoothbox'), '&nbsp;', array('class' => 'smoothbox')) ?>
						<?php endif; ?>) 
						<?php elseif( $isOfficer ): ?> (<?php echo ( $memberInfo->title ? $memberInfo->title : $this->translate('officer') ) ?>
						<?php if( $this->group->isOwner($this->viewer()) ): ?> 
							<?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'edit', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'format' => 'smoothbox'), '&nbsp;', array('class' => 'smoothbox')) ?>
						<?php endif; ?>) 						
					<?php endif; ?>	
					</span> 
					<?php if( !$this->blacklist_enable ): ?>
					<?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
						(<?php echo ( $memberInfo->title ? $memberInfo->title : $this->translate('waiting') ) ?>)
					<?php endif; ?> 	
					<?php if( $memberInfo->active == false && $memberInfo->resource_approved == true && $memberInfo->rejected_ignored == false ): ?>
						(<?php echo ( $memberInfo->title ? $memberInfo->title : $this->translate('inviting') ) ?>)
					<?php endif; ?> 
					<?php endif;?>
					<span class='group_members_status'>						
					</span> 
					<?php  if($memberInfo->active == false && $memberInfo->rejected_ignored == true): ?>
					<span>
						<?php echo  "(".$this->translate('ignored').")"; ?>
					</span>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</li>
	<?php endforeach;?>
</ul>
	
	<?php if($this->group->canManageUser($this->viewer())):?>	
	<?php if($isOfficer_checkviewer && !$isOfficer || $this->group->isOwner($viewer)):?>     			
	<?php if( $this->blacklist_enable && $this->members->getTotalItemCount() > 0 ): ?>	
		<p> <input type="checkbox" id='selectall' onclick="selectAll(this)"/>&nbsp;&nbsp;<?php echo $this -> translate("Select all") ?> </p>	
		<select id="actionType" onchange="changeAction(this)">
		  <option value="" selected="true"><?php echo $this->translate("Please choose action...");?></option>
		  <option value="removefromblacklist"><?php echo $this->translate("Remove From Blacklist");?></option>
		</select>
		<button type="submit"><?php echo $this->translate("Submit");?></button>
	<?php else: ?>	
	<?php if( !$this->waiting && $this->members->getTotalItemCount() > 1 ): ?>		
		<p> <input type="checkbox" id='selectall' onclick="selectAll(this)"/>&nbsp;&nbsp;<?php echo $this -> translate("Select all") ?> </p>
		<select id="actionType" onchange="changeAction(this);">
		  <option value="" selected="true"><?php echo $this->translate("Please chose action...");?></option>
		  <option value="remove"><?php echo $this->translate("Remove Member");?></option>
		  <?php if($this->group->isOwner($viewer)) :?>
			  <option value="promote"><?php echo $this->translate("Make Officer");?></option>
			  <option value="demote"><?php echo $this->translate("Demote Officer");?></option>	
		  <?php endif;?>
		  <option value="addtoblacklist"><?php echo $this->translate("Add To Blacklist");?></option>
		  <?php if($this->sub_groups):?>	
		  <option value="addtosubgroup"><?php echo $this->translate("Add To Subgroup");?></option>
		  <?php endif;?>
		</select>
		<button type="submit"><?php echo $this->translate("Submit");?></button>
	<?php else: ?>
	<?php if($this->waiting && $this->members->getTotalItemCount() > 0):?>
		<p> <input type="checkbox" id='selectall' onclick="selectAll(this)"/>&nbsp;&nbsp;<?php echo $this -> translate("Select all") ?> </p>
		<select id="actionType" onchange="changeAction(this)">
		  <option value="" selected="true"><?php echo $this->translate("Please chose action...");?></option>
		  <option value="approve"><?php echo $this->translate("Approve Request");?></option>
		  <option value="reject-invite"><?php echo $this->translate("Reject Request");?></option>
		  <option value="cancel-invite"><?php echo $this->translate("Cancel Membership Request");?></option>
		</select>
		<button type="submit"><?php echo $this->translate("Submit");?></button>
	<?php endif; ?>
	<?php endif; ?>
	<?php endif; ?>
	<?php endif; ?>	
	<?php endif; ?>		
<?php if( !$this->waiting ): ?>
<?php if($this->sub_groups):?>	
<div class="advgroup_sponsor_subgroup" style="display:none;">
	
		<h3><?php echo $this->translate("Add members to sub-group"); ?></h3>
		<div class="form-wrapper" id="name-wrapper">
			<p class="description">
				<?php echo $this->translate("Please select a sub-group"); ?>
			</p>			
			<select id = 'subgroup' name="subgroup"> 	
					<?php foreach($this->sub_groups as $sub_group):	?>
						<option name='subgroup' value="<?php echo $sub_group->group_id ?>"><?php echo $sub_group->getTitle() ?></option>
					<?php endforeach;?>
			</select>		
		</div>
		<div class="form-wrapper" id="button-wrapper">
			<button id="submit" type="submit"><?php echo $this->translate("Add");?></button>						
		</div>
	
</div>

<?php endif; ?>
</form>	
<?php endif; ?>
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
<script type="text/javascript">
	var selectAll = function(obj)
	{
        if(obj.checked) 
        { 
            $$('.member_checklist').each(function(e) { 
                e.checked = true;             
            });
        }else
        {
            $$('.member_checklist').each(function(e) { 
                e.checked = false;                       
	            });         
	        }
	}	
	var changeAction = function(obj)
	{
		var container = $$('.layout_advgroup_profile_members')[0],
		formMems = $$('.layout_advgroup_profile_members > form')[0],
		filterMems = $$('.layout_advgroup_profile_members > form > select')[0];			

		var action = formMems.getProperty('action'),
			valueSelect = obj.getProperty('value');
		if(valueSelect != ''){
			formMems.setProperty('action',action.truncate('14',valueSelect));
			if(valueSelect != 'addtosubgroup'){				
				$$('.advgroup_sponsor_subgroup').hide();
				if($$('#manage_member > button[type="submit"]').isDisplayed())
					$$('#manage_member > button[type="submit"]').show();				
			}
			else{
				$$('.advgroup_sponsor_subgroup').show();
				$$('#manage_member > button[type="submit"]').hide();
			}
		}
		else {
			formMems.setProperty('action',action.truncate('14','action'));
			$$('.advgroup_sponsor_subgroup').hide();
			if($$('#manage_member > button[type="submit"]').isDisplayed())
				$$('#manage_member > button[type="submit"]').show();
		}
	}
	var openPopupConfirm = function()
	{
		if($('actionType').value == "")
		{
			return false;
		}
		
		var url = '<?php echo $this -> url(array('controller' => 'member'), 'group_extended');?>';
		
		var action = $('actionType').value;
		if(action == 'reject-invite')
		{
			// get Action
			url = url + "/" + 'cancel-invite/reject/1';
		}
		else
		{
			// get Action
			url = url + "/" + action;
		}
		
		
		// get Group Id
		url = url + "/group_id/" + '<?php echo $this -> group -> getIdentity()?>';
		
		var strMembers = '';
		// get Member Selected
		$$('.group_members > li [type=checkbox]').each(function (ele)
		{
			if(ele.checked == true)
			{
				strMembers += ',' + ele.value;
			}
		});
		url = url + "/memberIds/" + strMembers;
		
		
		if($('actionType').value == "addtosubgroup")
		{
			var e = document.getElementById("subgroup");
			var strSubgroup = e.options[e.selectedIndex].value;
			url = url + "/subgroup/"  + strSubgroup;
		}
		
		if($('actionType').value == "addtosubgroup")
		{
			
		}
		
		Smoothbox.open(url);
		return false;
	}
</script>
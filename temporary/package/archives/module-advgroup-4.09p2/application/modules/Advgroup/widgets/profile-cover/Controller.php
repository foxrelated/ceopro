<?php

class Advgroup_Widget_ProfileCoverController extends Engine_Content_Widget_Abstract {

     public function indexAction() {
     		
          // Don't render this if not authorized
          $viewer = Engine_Api::_()->user()->getViewer();
          if (!Engine_Api::_()->core()->hasSubject()) {
               return $this->setNoRender();
          }
         
          // Get subject and check auth
          $subject = Engine_Api::_()->core()->getSubject('group');
		  	
          $view = $this->view;
          $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
          $this->view->group = $group = $subject;
          $category = null;
		
          if ($group->category_id)
          {	
          		$category = Engine_Api::_()->getItem('advgroup_category', $group->category_id);
          }
           
          $this->view->category = $category;
          $this->view->user = $user = $group->getOwner();
          $this->view->canComment = $canComment = $group->authorization()->isAllowed($viewer, 'comment');
          $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($group);
        
		// Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject('group');
		
		  $menu = new Advgroup_Plugin_Menus();
		 
		  $aJoinButton = $menu->onMenuInitialize_AdvgroupProfileMember();
          $this->view->aJoinButton = $aJoinButton;
		 
		  
		  $aReportButton = $menu->onMenuInitialize_AdvgroupProfileReport();
          $this->view->aReportButton = $aReportButton; 
		 
		
		  
          $aEditButton = $menu->onMenuInitialize_AdvgroupProfileEdit();
          $this->view->aEditButton = $aEditButton;
          
          $aStyleButton = $menu->onMenuInitialize_AdvgroupProfileStyle();
          $this->view->aStyleButton = $aStyleButton;
          
		  $aDeleteButton = $menu->onMenuInitialize_AdvgroupProfileDelete();
          $this->view->aDeleteButton = $aDeleteButton;
		  	  
		  $aCreateSubGroupButton = $menu->onMenuInitialize_AdvgroupProfileCreateSubGroup();
          $this->view->aCreateSubGroupButton = $aCreateSubGroupButton;
		  
		  $aTrasferButton = $menu->onMenuInitialize_AdvgroupProfileTransfer();
          $this->view->aTrasferButton = $aTrasferButton;
		  
		  $aMessageButton = $menu->onMenuInitialize_AdvgroupProfileMessage();
          $this->view->aMessageButton = $aMessageButton;
		  
          $aInviteButton = $menu->onMenuInitialize_AdvgroupProfileInvite();
          $this->view->aInviteButton = $aInviteButton;
          
		  $aProfileInvitenewButton = $menu->onMenuInitialize_AdvgroupProfileInvitenew();
          $this->view->aProfileInvitenewButton = $aProfileInvitenewButton;
		  		              
		  $aInviteManageButton = $menu->onMenuInitialize_AdvgroupProfileInviteManage();
          $this->view->aInviteManageButton = $aInviteManageButton;
		
		  $aProfileAlbumButton = $menu->onMenuInitialize_AdvgroupProfileAlbum();
          $this->view->aProfileAlbumButton = $aProfileAlbumButton;
		  
		  $aProfileDiscussionButton = $menu->onMenuInitialize_AdvgroupProfileDiscussion();
          $this->view->aProfileDiscussionButton = $aProfileDiscussionButton;
		  
		  $aProfileEventButton = $menu->onMenuInitialize_AdvgroupProfileEvent();
          $this->view->aProfileEventButton = $aProfileEventButton;
		  
		  $aProfilePollButton = $menu->onMenuInitialize_AdvgroupProfilePoll();
          $this->view->aProfilePollButton = $aProfilePollButton;
		  
		  $aProfileVideoButton = $menu->onMenuInitialize_AdvgroupProfileVideo();
          $this->view->aProfileVideoButton = $aProfileVideoButton;
		  
		  $aProfileUsefulLinkButton = $menu->onMenuInitialize_AdvgroupProfileUsefulLink();
          $this->view->aProfileUsefulLinkButton = $aProfileUsefulLinkButton;
		  
		  $aProfileActivityButton = $menu->onMenuInitialize_AdvgroupProfileActivity();
          $this->view->aProfileActivityButton = $aProfileActivityButton;
		 
		 $aProfileMusicButton = $menu->onMenuInitialize_AdvgroupProfileMusic();
          $this->view->aProfileMusicButton = $aProfileMusicButton;
		  
		  $aProfileMp3MusicButton = $menu->onMenuInitialize_AdvgroupProfileMp3Music();
          $this->view->aProfileMp3MusicButton = $aProfileMp3MusicButton;
		
		 $aFileSharingButton = $menu->onMenuInitialize_AdvgroupFileSharing();
          $this->view->aFileSharingButton = $aFileSharingButton;
		  
		  $aWikiButton = $menu->onMenuInitialize_AdvgroupProfileWiki();
          $this->view->aWikiButton = $aWikiButton;
          
          $aProfileListingButton = $menu->onMenuInitialize_AdvgroupProfileListing();
          $this->view->aProfileListingButton = $aProfileListingButton;
		  
		  $aProfileSocialMusicAlbumButton = $menu->onMenuInitialize_AdvgroupProfileSocialMusicAlbum();
          $this->view->aProfileSocialMusicAlbumButton = $aProfileSocialMusicAlbumButton;
		  
		  $aProfileSocialMusicSongButton = $menu->onMenuInitialize_AdvgroupProfileSocialMusicSong();
          $this->view->aProfileSocialMusicSongButton = $aProfileSocialMusicSongButton;

         $aProfileUltimatevideoButton = $menu->onMenuInitialize_AdvgroupProfileUltimatevideo();
         $this->view->aProfileUltimatevideoButton = $aProfileUltimatevideoButton;

         $aProfileVideochannelButton = $menu->onMenuInitialize_AdvgroupProfileVideochannel();
         $this->view->aProfileVideochannelButton = $aProfileVideochannelButton;
		  
		 $aContactButton = $menu->canSendMessageToOwner();
          $this->view->aContactButton = $aContactButton;
		 
          // Get staff
		    $ids = array();
		    $ids[] = $subject->getOwner()->getIdentity();
		    $list = $subject->getOfficerList();
		    foreach( $list->getAll() as $listiteminfo )
		    {
		      $ids[] = $listiteminfo->child_id;
		    }
		
		    $staff = array();
		    foreach( $ids as $id )
		    {
		      $user = Engine_Api::_()->getItem('user', $id);
		      $staff[] = array(
		        'membership' => $subject->membership()->getMemberInfo($user),
		        'user' => $user,
		      );
		    }
		    $this->view->staff = $staff;
            
		
		  
     }

}
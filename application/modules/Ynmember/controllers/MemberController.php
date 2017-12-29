<?php
class Ynmember_MemberController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
	    if(!$require_check)
	    {
	      if(!$this->_helper->requireUser()->isValid()) 
	      	return;
		}
	}

	public function indexAction()
	{

	}
	
	public function birthdayAction()
    {
    	$this->_helper->content->setEnabled();
    	$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
    }
	
	public function ratingAction()
    {
	    $this -> view -> viewer = $viewer = Engine_Api::_()->user() ->getViewer();		
		$this->_helper->content->setNoRender()->setEnabled();
    }
	
	public function featureAction()
    {
	    $this -> view -> viewer = $viewer = Engine_Api::_()->user() ->getViewer();		
		$this->_helper->content->setNoRender()->setEnabled();
    }
	
	public function myfriendAction()
    {
    	if( !$this->_helper->requireUser()->isValid() ) return;
	    $this -> view -> viewer = $viewer = Engine_Api::_()->user() ->getViewer();		
		$this->_helper->content->setNoRender()->setEnabled();
    }
	

	public function getNotificationAction()
	{
		// Get id of friend to add
		$user_id = $this->_getParam('id', null);
		$resourceUser = Engine_Api::_()->getItem('user', $user_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$active = $this->_getParam('active', 0);
		if( !$user_id ) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
			return;
		}
		if( !$resourceUser -> authorization() -> isAllowed($viewer, 'get_notification') )
		{
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('You do not have permission to get notification');
			return;
		}

		// Make form
		$this->view->form = $form = new Ynmember_Form_GetNotification(array('active' => $active));

		if( !$this->getRequest()->isPost() ) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
			return;
		}

		// Process
		try {
			$notificationTbl = Engine_Api::_()->getDbTable('notifications', 'ynmember');
			$notification = $notificationTbl -> getNotificationRow(array('resource_id' => $user_id, 'user_id' => $viewer->getIdentity()));
			if (is_null($notification))
			{
				$notification = $notificationTbl -> createRow();
				$notification -> resource_id = $resourceUser -> getIdentity();
				$notification -> user_id = $viewer -> getIdentity();
			}
			$notification -> active = $active;
			$notification -> save();
			$this->view->status = true;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Apply setting successfully.');

			return $this->_forward('success', 'utility', 'core', array(
		        'smoothboxClose' => true,
		        'parentRefresh' => true,
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Apply setting successfully.'))
			));
		} catch( Exception $e ) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
			$this->view->exception = $e->__toString();
		}
	}

	public function likeAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) {
			return;
		}
		$user_id = $this->_getParam('id', null);
		$user = Engine_Api::_()->getItem('user', $user_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$commentedItem = $subject = $user;
		// Process
		$db = $commentedItem->likes()->getAdapter();
		$db->beginTransaction();
		try
		{
			$commentedItem->likes()->addLike($viewer);
			// Add notification
			$owner = $user;
			$this->view->owner = $owner->getGuid();
			if( $owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity() ) {
				$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
				$notifyApi->addNotification($owner, $viewer, $commentedItem, 'ynmember_liked', array(
	          		'label' => $this->view->translate('you')
				));
			}
			// Stats
			Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');
			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			throw $e;
		}
		return $this->_forward('success', 'utility', 'core', array(
	        'smoothboxClose' => true,
	        'parentRefresh' => true,
	        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Like added successfully!'))
	      ));
		exit;
	}
	
	public function unlikeAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) {
			return;
		}
		$user_id = $this->_getParam('id', null);
		$user = Engine_Api::_()->getItem('user', $user_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$commentedItem = $subject = $user;
		// Process
		$db = $commentedItem->likes()->getAdapter();
		$db->beginTransaction();
		try
		{
			$commentedItem->likes()->removeLike($viewer);
			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			throw $e;
		}
		return $this->_forward('success', 'utility', 'core', array(
	        'smoothboxClose' => true,
	        'parentRefresh' => true,
	        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Unliked Successfully!'))
	      ));
		exit;
	}
	

	/**
	 * @url : http://localhost/pcus907/ynmember/member/share/type/user/id/1/format/smoothbox
	 * Sharing member
	 * @throws Exception
	 */
	public function shareAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;

		$type = $this->_getParam('type');
		$id = $this->_getParam('id');

		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->attachment = $attachment = Engine_Api::_()->getItem($type, $id);
		$this->view->form = $form = new Activity_Form_Share();

		if( !$attachment ) {
			// tell smoothbox to close
			$this->view->status  = true;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
			$this->view->smoothboxClose = true;
			return $this->render('deletedItem');
		}

		// hide facebook and twitter option if not logged in
		$facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
		if( !$facebookTable->isConnected() ) {
			$form->removeElement('post_to_facebook');
		}

		$twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
		if( !$twitterTable->isConnected() ) {
			$form->removeElement('post_to_twitter');
		}

		if( !$this->getRequest()->isPost() ) {
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}

		// Process
		$db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
		$db->beginTransaction();

		try {
			// Get body
			$body = $form->getValue('body');
			// Set Params for Attachment
			$params = array(
          		//'type' => '<a href="'.$attachment->getHref().'">'.$attachment->getMediaType().'</a>',          
			);

			// Add activity
			$api = Engine_Api::_()->getDbtable('actions', 'activity');
			//$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
			$action = $api->addActivity($viewer, $attachment->getOwner(), 'ynmember_share', $body, $params);
			if( $action ) {
				$api->attachActivity($action, $attachment);
			}
			$db->commit();

			// Notifications
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			// Add notification for owner of activity (if user and not viewer)
			if( $action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity() )
			{
				$notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'ynmember_shared', array(
          			//'label' => $attachment->getMediaType(),
          			'label' => $this->view->translate('you')
				));
			}

			// Preprocess attachment parameters
			$publishMessage = html_entity_decode($form->getValue('body'));
			$publishUrl = null;
			$publishName = null;
			$publishDesc = null;
			$publishPicUrl = null;
			// Add attachment
			if( $attachment ) {
				$publishUrl = $attachment->getHref();
				$publishName = $attachment->getTitle();
				$publishDesc = $attachment->getDescription();
				if( empty($publishName) ) {
					$publishName = ucwords($attachment->getShortType());
				}
				if( ($tmpPicUrl = $attachment->getPhotoUrl()) ) {
					$publishPicUrl = $tmpPicUrl;
				}
				// prevents OAuthException: (#100) FBCDN image is not allowed in stream
				if( $publishPicUrl &&
				preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST)) ) {
					$publishPicUrl = null;
				}
			} else {
				$publishUrl = $action->getHref();
			}
			// Check to ensure proto/host
			if( $publishUrl &&
			false === stripos($publishUrl, 'http://') &&
			false === stripos($publishUrl, 'https://') ) {
				$publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
			}
			if( $publishPicUrl &&
			false === stripos($publishPicUrl, 'http://') &&
			false === stripos($publishPicUrl, 'https://') ) {
				$publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
			}
			// Add site title
			if( $publishName ) {
				$publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
				. ": " . $publishName;
			} else {
				$publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
			}


			// Publish to facebook, if checked & enabled
			if( $this->_getParam('post_to_facebook', false) &&
          		'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
			try {

				$facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
				$facebookApi = $facebook = $facebookTable->getApi();
				$fb_uid = $facebookTable->find($viewer->getIdentity())->current();

				if( $fb_uid &&
				$fb_uid->facebook_uid &&
				$facebookApi &&
				$facebookApi->getUser() &&
				$facebookApi->getUser() == $fb_uid->facebook_uid ) {
					$fb_data = array(
              			'message' => $publishMessage,
					);
					if( $publishUrl ) {
						$fb_data['link'] = $publishUrl;
					}
					if( $publishName ) {
						$fb_data['name'] = $publishName;
					}
					if( $publishDesc ) {
						$fb_data['description'] = $publishDesc;
					}
					if( $publishPicUrl ) {
						$fb_data['picture'] = $publishPicUrl;
					}
					$res = $facebookApi->api('/me/feed', 'POST', $fb_data);
				}
			} catch( Exception $e ) {
				// Silence
			}
          } // end Facebook

          // Publish to twitter, if checked & enabled
          if( $this->_getParam('post_to_twitter', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable ) {
          try {
          	$twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          	if( $twitterTable->isConnected() ) {

            // Get attachment info
            $title = $attachment->getTitle();
            $url = $attachment->getHref();
            $picUrl = $attachment->getPhotoUrl();

            // Check stuff
            if( $url && false === stripos($url, 'http://') ) {
            	$url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
            if( $picUrl && false === stripos($picUrl, 'http://') ) {
            	$picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
            }

            // Try to keep full message
            // @todo url shortener?
            $message = html_entity_decode($form->getValue('body'));
            if( strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140 ) {
            	if( $title ) {
            		$message .= ' - ' . $title;
            	}
            	if( $url ) {
            		$message .= ' - ' . $url;
            	}
            	if( $picUrl ) {
            		$message .= ' - ' . $picUrl;
            	}
            } else if( strlen($message) + strlen($title) + strlen($url) + 6 <= 140 ) {
            	if( $title ) {
            		$message .= ' - ' . $title;
            	}
            	if( $url ) {
            		$message .= ' - ' . $url;
            	}
            } else {
            	if( strlen($title) > 24 ) {
            		$title = Engine_String::substr($title, 0, 21) . '...';
            	}
            	// Sigh truncate I guess
            	if( strlen($message) + strlen($title) + strlen($url) + 9 > 140 ) {
            		$message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
            	}
            	if( $title ) {
            		$message .= ' - ' . $title;
            	}
            	if( $url ) {
            		$message .= ' - ' . $url;
            	}
            }

            $twitter = $twitterTable->getApi();
            $twitter->statuses->update($message);
          	}
          } catch( Exception $e ) {
          	// Silence
          }
          }


          // Publish to janrain
          if( //$this->_getParam('post_to_janrain', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable ) {
          try {
          	$session = new Zend_Session_Namespace('JanrainActivity');
          	$session->unsetAll();

          	$session->message = $publishMessage;
          	$session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
          	$session->name = $publishName;
          	$session->desc = $publishDesc;
          	$session->picture = $publishPicUrl;

          } catch( Exception $e ) {
          	// Silence
          }
          }


		} catch( Exception $e ) {
			$db->rollBack();
			throw $e; // This should be caught by error handler
		}
		// Disable layout and viewrenderer
     	//$this -> _helper -> layout -> disableLayout();
		
		return $this->_forward('success', 'utility', 'core', array(
	        'smoothboxClose' => true,
	        'parentRefresh' => false,
			'layout' => 'default-simple',
	        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Shared This Member Successfully!'))
	      ));
		exit;
		
	}


	public function confirmAction()
	{
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		$params = $this -> _getAllParams();
		
		$user_id = $this->_getParam('id', null);
		$user = Engine_Api::_()->user()->getUser($user_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$linkageTbl = Engine_Api::_()->getItemTable('ynmember_linkage');
		if (isset($params['confirm']) && $params['confirm'] == '1')
		{
			$linkageTbl->setUserApproved($viewer, $user);
			$linkageTbl->setResourceApproved($user, $viewer);
		}
		elseif (isset($params['confirm']) && $params['confirm'] == '0')
		{
			$linkageTbl = $linkageTbl->deleteLinkage($viewer, $user);
		}
		echo Zend_Json::encode(array('message' => 'ok'));
		exit;
	}
	
	public function memberLikedAction()
	{
		// Get id of friend to add
		$userId = $this->_getParam('id', null);
		$user = Engine_Api::_()->getItem('user', $userId);
		$userLiked = array();
		if ($user->getIdentity())
		{
			$this-> view-> userLiked = $userLiked = $user -> likes() -> getAllLikesUsers();
		}
	}
	
	public function memberGotNotificationAction()
	{
		// Get id of friend to add
		$userId = $this->_getParam('id', null);
		$user = Engine_Api::_()->getItem('user', $userId);
		$notiTbl = Engine_Api::_()->getDbTable('notifications', 'ynmember');
		$users = array();
		if ($user->getIdentity())
		{
			$this-> view-> users = $users = $notiTbl -> getAllUsers($user);
		}
	}
	
	public function suggestFriendAction()
	{
		// Get id of friend to add
		$userId = $this->_getParam('id', null);
		$searchText = $this->_getParam('text', '');
		$this -> view -> mode = $mode =  $this->_getParam('mode', '');
		if ($mode == 'ajax')
		{
			// Disable layout and viewrenderer
     		$this -> _helper -> layout -> disableLayout();
		}
		$this -> view -> page = $page = $this->_getParam('page', 1);
		$this -> view -> user = $user = Engine_Api::_()->getItem('user', $userId);
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer -> getIdentity() || !$user -> getIdentity())
		{
			return;
		}
		$viewerFriends = $viewer -> membership() -> getMembers();
		$users = array();
		if (count($viewerFriends))
		{
			foreach ($viewerFriends as $u)
			{
				if ($u->isSelf($user))
				{
					continue;
				}
				if (!$u -> membership()-> isMember($user))
				{
					if ($searchText != '')
					{
						if (strpos($u->displayname, $searchText) !== false)
						{
							$users[] = $u;
						}
					}
					else 
					{
						$users[] = $u;
					}					
				}
			}	
		}
		
		if (count($users))
		{
			$paginator = Zend_Paginator::factory($users);
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($page);
			$this->view->paginator = $paginator;
		}
	}
	
	public function suggestAction()
	{
		$suggestedMemberId = $this->_getParam('id', null);
		$fromId = $this->_getParam('from', null);
		$toId = $this->_getParam('to', null);
		$view = Zend_Registry::get('Zend_View');
		if (is_null($suggestedMemberId) || is_null($fromId) || is_null($toId))
		{
			echo Zend_Json::encode(array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get("Zend_Translate")->_("Invalid params") 
			)); exit;
		}
		$fromUser = Engine_Api::_()->user()->getUser($fromId);
		$toUser= Engine_Api::_()->user()->getUser($toId);
		$suggestedUser = Engine_Api::_()->user()->getUser($suggestedMemberId);
		
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		$notifyApi->addNotification($toUser, $fromUser, $suggestedUser, 'ynmember_suggested', array());
		echo Zend_Json::encode(array(
			'error_code' => 0,
			'error_message' => '', 
			'message' => $view -> translate("Sent suggestion successfully.") 
		)); exit;
	}
}
<?php
class Ynmember_Plugin_Menus
{
	
	public function onMenuInitialize_UserProfileFeature($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> getIdentity())
		{
			return false;
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		$label = "Feature Profile";
		if (!$viewer -> isSelf($subject))
		{
			return false;
		}
		
		return array(
			'class' => 'smoothbox',
			'label' => $label,
			'icon' => 'application/modules/Ynmember/externals/images/featured.png',
			'route' => 'ynmember_general',
			'params' => array(
				'controller' => 'index',
				'action' => 'feature-member',
			)
		);
	}
	public function onMenuInitialize_UserEditCover($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> getIdentity())
		{
			return false;
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		$view = Zend_Registry::get('Zend_View');
		$label = $view -> translate("Edit Cover Photo");
		
		// Check authorization to view feedback.
        if (!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth') -> setAuthParams(null, null, 'edit') -> checkRequire())
        {
        	return false;
		}
		return true;
	}
	
   public function onMenuInitialize_UserEditPlace($row)
   {
   		$settings = Engine_Api::_()->getApi('settings', 'core');
		$show = $settings->getSetting('ynmember_allow_add_workplace', 1);
		if(!$show)
		{
			return false;
		}
		if( Engine_Api::_()->core()->hasSubject('user') ) {
		  $user = Engine_Api::_()->core()->getSubject('user');
		} else {
		  $user = Engine_Api::_()->user()->getViewer();
		}
		if( !$user->getIdentity() ) {
		  return false;
		}
		return true;
   }
   
   public function onMenuInitialize_UserEditRelationship($row)
   {
   		$settings = Engine_Api::_()->getApi('settings', 'core');
   		$allow_search_location = $settings->getSetting('ynmember_allow_update_relationship', 1);
		if(!$allow_search_location)
		{
			return false;
		}
		if( Engine_Api::_()->core()->hasSubject('user') ) {
		  $user = Engine_Api::_()->core()->getSubject('user');
		} else {
		  $user = Engine_Api::_()->user()->getViewer();
		}
		if( !$user->getIdentity() ) {
		  return false;
		}
		return true;
   }
   
	public function onMenuInitialize_UserNotificationMember($row)
   {
		$viewer = Engine_Api::_()->user()->getViewer();
		$resourceUser = $user = Engine_Api::_()->core()->getSubject('user');
	   	if( !$viewer->getIdentity() ) 
	   	{
			return false;
		}
		if ($viewer -> isSelf($resourceUser))
		{
			return false;
		}	
		$notificationTbl = Engine_Api::_()->getDbTable('notifications', 'ynmember');
		$notification = $notificationTbl -> getNotificationRow(array(
			'resource_id' => $resourceUser->getIdentity(), 
			'user_id' => $viewer->getIdentity()
		));
		if (is_null($notification) || $notification->active == '0')
		{
			$label = Zend_Registry::get("Zend_Translate")->_("Get Notification");
		}
		else
		{
			$label = Zend_Registry::get("Zend_Translate")->_("Stop Getting Notification");
		}
		
	   	if( $resourceUser->authorization()->isAllowed($viewer, 'get_notification') ) 
	   	{
	    	 return array(
				'class' => 'smoothbox',
				'label' => $label,
				'icon' => 'application/modules/Ynmember/externals/images/get_notifications.png',
				'route' => 'ynmember_extended',
				'params' => array(
					'controller' => 'member',
					'action' => 'get-notification',
					'id' => $user -> getIdentity(),
					'active' => (is_null($notification) || $notification->active == '0') ? 1 : 0,
				)
			);
	    }
	    return false;
   }
   
   
	public function onMenuInitialize_UserRatingMember($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> getIdentity())
		{
			return false;
		}
		
		$requireAuth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
		$can_review_members = ($requireAuth->setAuthParams('ynmember_user', null, 'can_review_members') -> checkRequire());
		$can_review_oneself = ($requireAuth->setAuthParams('ynmember_user', null, 'can_review_oneself') -> checkRequire());
		
		$subject = Engine_Api::_() -> core() -> getSubject();
		$label = "Review & Rate this member";
		if ($viewer -> isSelf($subject))
		{
			if(!$can_review_oneself)
			{
				return false;
			}
			else 
			{
				$label = "Review & Rate yourself";
			}
		}
		else {
			if(!$can_review_members)
			{
				return false;
			}
		}
	  	
		//check hasReviewed
		$tableReview = Engine_Api::_() -> getItemTable('ynmember_review');
		$HasReviewed = $tableReview -> checkHasReviewed($subject -> getIdentity(), $viewer -> getIdentity());
		
		if(!$HasReviewed)
		{
			return array(
				'class' => 'smoothbox',
				'label' => $label,
				'icon' => 'application/modules/Ynmember/externals/images/review_rate_user.png',
				'route' => 'ynmember_general',
				'params' => array(
					'controller' => 'index',
					'action' => 'rate-member',
					'id' => $subject -> getIdentity(),
				)
			);
		}
		else {
			return false;
		}
	}
	
	public function onMenuInitialize_UserLikeMember($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> getIdentity())
		{
			return false;
		}
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return false;
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		if (!($subject instanceof User_Model_User))
		{
			return false;
		}
		$requireAuth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
		$can_like_members = ($requireAuth->setAuthParams('ynmember_user', null, 'can_like_members') -> checkRequire());
		if(!$can_like_members)
		{
			return false;
		}
		if (!$subject->likes()->isLike($viewer))
		{
			$label = ($subject->isSelf($viewer))
			? Zend_Registry::get("Zend_Translate")->_("Like yourself")
			: Zend_Registry::get("Zend_Translate")->_("Like this member");
			$action = 'like';
		}
		else
		{
			$label = ($subject->isSelf($viewer))
			? Zend_Registry::get("Zend_Translate")->_("Unlike yourself")
			: Zend_Registry::get("Zend_Translate")->_("Unlike this member");
			$action = 'unlike';
		}
		return array(
			'class' => 'smoothbox',
			'label' => $label,
			'icon' => 'application/modules/Ynmember/externals/images/like.png',
			'route' => 'ynmember_extended',
			'params' => array(
				'controller' => 'member',
				'action' => $action,
				'type' => 'user',
				'id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_UserShareMember($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return false;
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		if (!($subject instanceof User_Model_User))
		{
			return false;
		}
		$label = ($subject->isSelf($viewer))
			? Zend_Registry::get("Zend_Translate")->_("Share yourself")
			: Zend_Registry::get("Zend_Translate")->_("Share this member");
		return array(
			'class' => 'smoothbox',
			'label' => $label,
			'icon' => 'application/modules/Ynmember/externals/images/share.png',
			'route' => 'ynmember_extended',
			'params' => array(
				'controller' => 'member',
				'action' => 'share',
				'type' => 'user',
				'id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_UserDirectionMember($row)
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return false;
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		if (!($subject instanceof User_Model_User))
		{
			return false;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$tableLive = Engine_Api::_() -> getItemTable('ynmember_liveplace');
	  	$currentliveplaces = $tableLive -> getLiveCurrentPlacesByUserId($subject -> getIdentity());
	  	$currentPlace = null;
	  	if (count($currentliveplaces))
	  	{
	  		foreach($currentliveplaces as $currentliveplace)
	  		{
	  			if($currentliveplace -> isViewable())
	  			{
	  				$currentPlace = $currentliveplace; break;
	  			}
	  		}
	  		if (is_null($currentPlace))
	  		{
	  			return false;
	  		}
	  		$label = Zend_Registry::get("Zend_Translate")->_("Get Direction");
	  		return array(
				'class' => 'smoothbox',
				'label' => $label,
				'icon' => 'application/modules/Ynmember/externals/images/get_directions.png',
				'route' => 'ynmember_general',
				'params' => array(
					'action' => 'direction',
					'type'=>'live',
					'id' => $currentPlace->getIdentity()
				)
			);
	  	}
	  	return false;
	}
	
	public function onMenuInitialize_UserSuggestFriendMember($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return false;
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		if (!($subject instanceof User_Model_User))
		{
			return false;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($subject->isSelf($viewer))
		{
			return false;
		}
		$label = Zend_Registry::get("Zend_Translate")->_("Suggest Friends");
		$class = 'smoothbox';
		$format = 'smoothbox';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';
			$format = '';
		}
		return array(
			'class' => $class,
			'label' => $label,
			'icon' => 'application/modules/Ynmember/externals/images/suggest.png',
			'route' => 'ynmember_extended',
			'params' => array(
				'controller' => 'member',
				'action' => 'suggest-friend',
				'id' => $subject -> getIdentity(),
				'format' => $format
			)
		);
	}
}

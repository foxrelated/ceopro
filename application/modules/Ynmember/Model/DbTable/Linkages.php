<?php

class Ynmember_Model_DbTable_Linkages extends Engine_Db_Table
{
	protected $_rowClass = 'Ynmember_Model_Linkage';
	protected $_name = 'ynmember_linkages';
	protected $_rows = array();

	public function getLinkage(User_Model_User $user, User_Model_User $resource = null, $userApproved = false)
	{
		$select = $this->select();
		if ($user)
		{
			$select -> where("user_id = ? ", $user->getIdentity());
		}
		if ($userApproved === true)
		{
			$select -> where("user_approved = ? ", '1');
		}
		if (!is_null($resource))
		{
			$select -> where("resource_id = ? ", $resource->getIdentity());
		}
		$select -> order("linkage_id DESC") -> limit(1);
		return $this -> fetchRow($select);
	}

	public function getRequest(User_Model_User $user)
	{
		$select = $this->select();
		if ($user)
		{
			$select -> where("user_id = ? ", $user->getIdentity());
			$select -> where("user_approved = ? ", '0');
		}
		return $this -> fetchAll($select);
	}
	
	public function deleteLinkage(User_Model_User $user, User_Model_User $resource = null)
	{
		$linkage = $this->getLinkage($user, $resource);
		$linkage->delete();
	}
	
	public function setResourceApproved(User_Model_User $user , Core_Model_Item_Abstract $resource)
	{
		$row = $this->getLinkage($user, $resource);
		if( !$row->resource_approved )
		{
			$row->resource_approved = true;
			if( $row->resource_approved && $row->user_approved )
			{
				$row->active = true;
			}
			$row->save();
			if( $row->resource_approved && $row->user_approved )
			{
				$this->postLikageFeeds($row, $user);
			}
		}
		
		return $this;
	}

	public function setUserApproved(User_Model_User $user, User_Model_User $resource)
	{
		$row = $this->getLinkage($user, $resource);
		if( !$row->user_approved )
		{
			$row->user_approved = true;
			if( $row->resource_approved && $row->user_approved )
			{
				$row->active = true;
			}
			$row->save();
			if( $row->resource_approved && $row->user_approved )
			{
				$this->postLikageFeeds($row, $user);
			}
		}
		
		/*
		$this->delete(array(
	      'user_id = ?' => $user->getIdentity(),
		  'linkage_id <> ?' => $row->getIdentity()
	    ));
	    */
		return $this;
	}

	protected function _checkActive(User_Model_User $user, User_Model_User $resource)
	{
		$row = $this->getLinkage($user, $resource);
		if( $row->resource_approved && $row->user_approved && !$row->active )
		{
			$row->active = true;
			$row->save();
		}
	}

	public function addLinkage(User_Model_User $user, User_Model_User $resource = null)
	{
		
		$row = $this -> getLinkage($user, $resource);
		if( null === $row )
		{
			$row = $this->createRow();
		}
		
		return $row;
	}
	
	function postLikageFeeds($row, $subject)
	{
		$relationship = Engine_Api::_()->getItem('ynmember_relationship', $row->relationship_id);
		if ($relationship->appear_feed == '1')
	    {
	    	$api = Engine_Api::_()->getDbtable('actions', 'activity');
		    $feedContent = $this -> getLinkageAsString($subject);
			if ($feedContent != '')
			{
				// DELETE OLD ACTIONS
				$select = $api -> select()
					-> where('type = ?', 'ynmember_relationship')
					-> where('subject_type = ?', 'user')
					-> where('subject_id = ?', $subject->getIdentity())
					-> where('object_type = ?', 'ynmember_linkage')
					-> where('object_id = ?', $row->getIdentity());

				$actions = $api -> fetchAll($select);
				foreach ($actions as $action) {
					$action -> deleteItem();
				}

				$api->addActivity($subject, $row , 'ynmember_relationship', $feedContent, array());
			}
	    }
	}
	
	function getLinkageAsString($subject)
    {
    	$relationshipTbl = Engine_Api::_()->getItemTable('ynmember_relationship');
	 	$relationshipTblName = $relationshipTbl -> info ('name');
		$linkageTblName = $this -> info('name');
		$select = $this 
		-> select () -> setIntegrityCheck(false)
		-> from ($linkageTblName)
		-> joinLeft($relationshipTblName, "{$relationshipTblName}.relationship_id = {$linkageTblName}.relationship_id")
		-> where("{$linkageTblName}.user_id = ? ", $subject->getIdentity())
		-> order("{$linkageTblName}.linkage_id DESC ")
		-> limit(1);
		;
    	
	 	$linkage = $this -> fetchRow($select);
    	$str = "";
    	$view = Zend_Registry::get("Zend_View");
    	if($linkage -> isViewable($subject)) 
    	{
    	 	$str .= "<span> is {$linkage -> status}</span>"; 
			if (!is_null($linkage -> with_member == '1')) 
			{
				if ($linkage -> active && $linkage -> resource_approved)
				{
					$member = Engine_Api::_()->user() -> getUser($linkage -> resource_id);
					if ($member->getIdentity())
					{
						$str .= "<span> {$view -> translate('with')} {$view->htmlLink($member->getHref(), $member->getTitle(), array('target' => '_blank'))}</span>";  
					}
				}
			}
    		if (!is_null($linkage -> anniversary)) 
		 	{
		 		$dateObject = new Zend_Date(strtotime($linkage -> anniversary));
		 		$str .= "<span>  {$view -> translate('since')} {$dateObject->toString('MM-dd-y')}</span>";
		 	}
    	 }
    	 return $str;
	}
}

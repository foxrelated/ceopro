<?php
class Ynmember_Model_Review extends Core_Model_Item_Abstract {
	public function getGeneralRating()
	{
		$ratingTbl = Engine_Api::_()->getItemTable("ynmember_rating");
		$ratingSelect = $ratingTbl
		->select()
		->where("review_id = ? ", $this->review_id)
		->where("rating_type = 0 ")
		->limit(1);
		return $ratingTbl->fetchRow($ratingSelect);;
	}

	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
	}
	
	public function getReviewUseful()
	{
		$viewer = Engine_Api::_()->user() -> getViewer();
		$usefulTbl = Engine_Api::_()->getDbTable('usefuls', 'ynmember');
		$select = $usefulTbl->select()->where("review_id = ?", $this->getIdentity());
		$yesCount = 0; $noCount = 0; 
		$checked = false; $checkedValue = null;
		foreach ($usefulTbl -> fetchAll($select) as $useful)
		{
			if ($useful -> value == '1')
			{
				$yesCount++;
			}
			else if ($useful -> value == '0')
			{
				$noCount++;
			}
			if ($useful -> user_id == $viewer -> getIdentity())
			{
				$checked = true;
				$checkedValue = $useful -> value;
			}
		}
		return array(
			'review_id' => $this->getIdentity(),
			'yes_count' => $yesCount,
			'no_count' => $noCount,
			'checked' => $checked,
			'checked_value' => $checkedValue
		);
	}
	
	public function getRating()
	{
		$ratingTbl = Engine_Api::_()-> getItemTable('ynmember_rating');
		$ratingTblName = $ratingTbl -> info('name');
		
		$ratingTypeTbl = Engine_Api::_()-> getItemTable('ynmember_ratingtype');
		$ratingTypeTblName = $ratingTypeTbl -> info('name');
		
		$select = $ratingTbl -> select() -> setIntegrityCheck(false)
			-> from($ratingTblName)
			-> joinleft($ratingTypeTblName, "{$ratingTblName}.rating_type = {$ratingTypeTblName}.ratingtype_id")
			-> where("$ratingTblName.review_id = ?", $this -> getIdentity())
			;
		$ratings = $ratingTbl -> fetchAll($select);
		return $ratings;
	}
	
	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'ynmember_extended',
			'controller' => 'review',
			'action' => 'detail',
			'id' => $this -> review_id,
			'reset' => true,
			'slug' => $this -> getSlug(),
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
}
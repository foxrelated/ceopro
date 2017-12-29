<?php 
class Ynmultilisting_Model_Review extends Core_Model_Item_Abstract {
	
	protected $_parent_type = 'ynmultilisting_listing';
    protected $_owner_type = 'user';
    protected $_type = 'ynmultilisting_review';
    protected $_searchTriggers = false;
	
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
	
	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'ynmultilisting_review',
			'controller' => 'review',
			'action' => 'view',
			'id' => $this -> review_id,
			'reset' => true,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
	
    function isViewable() {
        return $this->getParent()->isAllowed('view'); 
    }
    
	public function getListingType()
	{
		$listing = $this->getParent();
        if ($listing) {
			$listingtype = $listing -> getListingType();
			return $listingtype;
		}
		return false;
	}
	
	public function getRating()
	{
		$ratingValueTbl = Engine_Api::_()-> getDbTable('ratingvalues', 'ynmultilisting');
		$ratingValueTblName = $ratingValueTbl -> info('name');
		
		$ratingTypeTbl = Engine_Api::_() -> getItemTable('ynmultilisting_ratingtype');
		$ratingTypeTblName = $ratingTypeTbl -> info('name');
		
		$select = $ratingValueTbl -> select() -> setIntegrityCheck(false)
			-> from($ratingValueTblName)
			-> joinleft($ratingTypeTblName, "{$ratingValueTblName}.ratingtype_id = {$ratingTypeTblName}.ratingtype_id", "{$ratingTypeTblName}.title")
			-> where("{$ratingValueTblName}.review_id = ?", $this -> getIdentity())
			;
		$ratings = $ratingValueTbl -> fetchAll($select);
		return $ratings;
	}
	
	public function getReview()
	{
		$reviewValueTbl = Engine_Api::_()-> getDbTable('reviewvalues', 'ynmultilisting');
		$reviewValueTblName = $reviewValueTbl -> info('name');
		
		$reviewTypeTbl = Engine_Api::_() -> getItemTable('ynmultilisting_reviewtype');
		$reviewTypeTblName = $reviewTypeTbl -> info('name');
		
		$select = $reviewValueTbl -> select() -> setIntegrityCheck(false)
			-> from($reviewValueTblName)
			-> joinleft($reviewTypeTblName, "{$reviewValueTblName}.reviewtype_id = {$reviewTypeTblName}.reviewtype_id", "{$reviewTypeTblName}.title")
			-> where("{$reviewValueTblName}.review_id = ?", $this -> getIdentity())
			;
		$reviews = $reviewValueTbl -> fetchAll($select);
		return $reviews;
	}
	
	function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit'); 
    }
	
    function isDeletable() {
        return $this->authorization()->isAllowed(null, 'delete'); 
    }

    public function getReviewUseful()
    {
        $viewer = Engine_Api::_()->user() -> getViewer();
        $usefulTbl = Engine_Api::_()->getDbTable('usefuls', 'ynmultilisting');
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
}
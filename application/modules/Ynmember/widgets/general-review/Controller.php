<?php
class Ynmember_Widget_GeneralReviewController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        $values = $this->_getAllParams();
		$resource = $user = Engine_Api::_()->core()->getSubject();
		$reviewTbl = Engine_Api::_()->getItemTable("ynmember_review");
		$params = array(
			'resource_id' => $resource->getIdentity()
		);
		if (!empty($values['itemCountPerPage']))
		{
			$params['limit'] = $values['itemCountPerPage'];
		}
		$this -> view -> reviews = $reviews = $reviewTbl->getReviewPaginator($params);
		$this -> view -> can_edit_own_review = $can_edit_own_review = (Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynmember_review', null, 'can_edit_own_review') -> checkRequire());
	}
}
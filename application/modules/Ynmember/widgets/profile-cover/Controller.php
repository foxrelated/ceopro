<?php
class Ynmember_Widget_ProfileCoverController extends Engine_Content_Widget_Abstract
{
  	public function indexAction()
  	{
  		// Don't render this if not authorized
	    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	
	    // Get subject and check auth
	    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
	    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
	      return $this->setNoRender();
	    }
	
	    // Member type
	    $subject = Engine_Api::_()->core()->getSubject();
	    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
	
	    if( !empty($fieldsByAlias['profile_type']) )
	    {
	      $optionId = $fieldsByAlias['profile_type']->getValue($subject);
	      if( $optionId ) {
	        $optionObj = Engine_Api::_()->fields()
	          ->getFieldsOptions($subject)
	          ->getRowMatching('option_id', $optionId->value);
	        if( $optionObj ) {
	          $this->view->memberType = $optionObj->label;
	        }
	      }
	    }
		 // Friend count
		 $select = $subject->membership()->getMembersSelect();
    	 $friends = Zend_Paginator::factory($select);
   		 $this->view->friendCount = $friends->getTotalItemCount();
	}
}
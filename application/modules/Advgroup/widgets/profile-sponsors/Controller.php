<?php

class Advgroup_Widget_ProfileSponsorsController extends Engine_Content_Widget_Abstract {
	
	protected $_childCount;
	
	public function indexAction() {
		$params = $this -> _getAllParams();
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
		// Don't render this if not authorized
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('group');
		if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return $this->setNoRender();
		}
		// Get params
		$this->view->page = $page = $this->_getParam('page', 1);
		$this->view->search = $search = $this->_getParam('search');

		// Prepare data
		$this->view->group = $group = $subject;
		
		
		//check auth if can manage
		$is_Owner = ($group->user_id == $viewer->getIdentity()) ? true : false;
	  	$canAdd = $subject -> authorization() -> isAllowed(null, 'sponsor');
		$allow_manage = "";
		$levelAdd = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'sponsor');
		if ($is_Owner || ($canAdd && $levelAdd)) {
			$this -> view -> canAdd =  $canAdd = true;
		} else {
			$this -> view -> canAdd =  $canAdd = false;
		}
		
		$this->view->form = $form = new Advgroup_Form_Sponsor_Create();
		
		// Get paginator
	    $table = Engine_Api::_()->getItemTable('advgroup_sponsor');
		
	    $select = $table->select()
	      ->where('group_id = ?', $subject->getIdentity())
	      ->order('sponsor_id DESC')
	      ;
		$number = 8;
		if(empty($params['number']))
		{
			$number = 8;
		}
		else {
			$number = $params['number'];
		}
	    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
	    // Set item count per page and current page number
	    $paginator->setItemCountPerPage($number);
	
	    // Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
	      $this->_childCount = $paginator->getTotalItemCount();
	    }
				
		$request = Zend_Controller_Front::getInstance()->getRequest();
		// Not post/invalid
		if ( !$request->isPost( )) { return; }

		if ( !$form->isValid($request->getPost()) ) { return; }
		
		// Process
		$values = $form->getValues();
		$this->view->formValues = $values ;
		$values['event_id'] = $event->getIdentity();
		$db = Engine_Api::_()->getDbtable('sponsors', 'advgroup')->getAdapter();
		$db->beginTransaction();
		
		try {
			$table = Engine_Api::_()->getDbtable('sponsors', 'advgroup');
			$sponsor = $table->createRow();
			$sponsor->setFromArray($values);
			$sponsor->save();
			
			// Add photo
			if (!empty($values['photo'])) {
				$sponsor->setPhoto($form->photo);
			}
			// Commit
			$db->commit();
		} catch (Engine_Image_Exception $e) {
			$db->rollBack();
			$form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
		
     }
     
	public function getChildCount()
	{
	    return $this->_childCount;
	}
}
?>
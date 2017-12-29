<?php 
/**
 * SocialEngine
 *
 * @package    Advanced Group
 * @copyright  Copyright 2008-2012 YouNet Company
 * @license    http://www.socialengine.net/license/
 * @author     HuyNA
 * @todo 	   Controller of report button
 */
class Advgroup_ReportController extends Core_Controller_Action_Standard
{

	public function addAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->form = $form = new Advgroup_Form_Report();
		if( !$this->getRequest()->isPost() ) {
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
		$table = Engine_Api::_()->getItemTable('advgroup_report');
		$db = $table->getAdapter();
		$db->beginTransaction();
		
		try 
		{
			$values = array('user_id'=>$viewer->getIdentity(), 'group_id' =>$this->_getParam('group_id',0),
					'topic_id'=>$this->_getParam('topic_id',0),'post_id'=>$this->_getParam('post_id',0),
					'content'=>$form->getValue('body'));
			
			$report = $table->createRow();
      		$report->setFromArray($values);
      		$report->save();
      		$db->commit();
		} 
		catch( Exception $e ) {
			$db->rollBack();
      		throw $e; // This should be caught by error handler
		}
		
		// Redirect if in normal context
		 if( 'smoothbox' === $this->_helper->contextSwitch->getCurrentContext() ) {
			$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh'=> false,
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('The report will be sent to admin.'))
			));
		}
	}
}
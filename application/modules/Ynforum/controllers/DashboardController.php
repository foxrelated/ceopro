<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     LuanND
 */
class Ynforum_DashboardController extends Core_Controller_Action_Standard
{

	public function init()
	{
		$this -> view -> callback_url = $this -> _getParam('callback_url', "");
		
		
	}
	public function signatureAction()
	{
		if (!$this->_helper->requireUser->isValid()) {
            return;
        }
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->form = $form = new Ynforum_Form_Dashboard_Signature;

		$signatureTable = Engine_Api::_()->getItemTable('ynforum_signature');
		$signatureSelect = $signatureTable->select()->where('user_id = ?', $viewer->getIdentity());
		$signature = $signatureTable->fetchRow($signatureSelect);
		
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
		{
			$values = $form->getValues();

			if ($signature == null)
			{
				$signature = $signatureTable->createRow(array(
					'user_id'       => $viewer->getIdentity(),
					'body'          => '',
					'creation_date' => date('Y-m-d H:i:s'),
					'post_count'    => 0,
					'thanked_count' => 0,
					'thanks_count'  => 0,
					'reputation'    => 0,
					'signature'     => $values['body']
				));
			}
			else
			{
				$signature->signature = $values['body'];
				$signature->modified_date = $signature->creation_date;
			}
			$signature->save();

			$form->addNotice('Your changes have been saved.');
			return;
		}

		if ($signature != null) {
		    $form->getElement('body')->setValue($signature->signature);
		}
		
	}
	public function manageAttachmentsAction()
	{
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynforum/externals/scripts/datepicker.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Ynforum/externals/styles/datepicker_jqui/datepicker_jqui.css');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynforum/externals/scripts/ynforum_date.js');
		
		$page = $this -> _getParam('page', 1);		
		$viewer = Engine_Api::_() -> user() -> getViewer();			
		$this->view->form = $form = new Ynforum_Form_Dashboard_Attachment();		
				
		$values = array();		
		
		if ($this -> getRequest() -> isPost()) {					
			$values = $this -> getRequest() -> getPost();				
			foreach ($values as $key => $value) {
				if ($key == 'delete_' . $value) {
					
					$attach = Engine_Api::_()->getDbtable('attachments', 'ynforum')->find($value)->current();
					
					$attach -> delete();
				}
			}
		}	
		
		$form->populate($values);			
		
		$attachTable = Engine_Api::_()->getDbtable('attachments', 'ynforum');
		$Name = $attachTable->info('name');		
		$attachSelect = $attachTable->select() -> from($Name, "$Name.*") -> setIntegrityCheck(false);		
		$attachSelect -> joinLeft("engine4_forum_posts", "engine4_forum_posts.post_id = $Name.post_id" , "");	
		
		// From date
		if (!empty($values['From_Date']) && empty($values['To_Date'])) {
			$fromdate = Engine_Api::_() -> ynforum() -> getFromDaySearch($values['From_Date']);			
			if ($fromdate) {
				$attachSelect -> where("($Name.modified_date >= ?)", $fromdate);
			}	
		}		
		// To date
		if (!empty($values['To_Date']) && empty($values['From_Date'])) {
			$todate = Engine_Api::_() -> ynforum() -> getToDaySearch($values['To_Date']);
			if ($todate) {
				$attachSelect -> where("($Name.modified_date <= ?)", $todate);
			}	
		}
		if (!empty($values['From_Date']) && !empty($values['To_Date'])) {
			$fromdate = Engine_Api::_() -> ynforum() -> getFromDaySearch($values['From_Date']);
			$todate = Engine_Api::_() -> ynforum() -> getToDaySearch($values['To_Date']);
			$attachSelect -> where("$Name.modified_date between '$fromdate' and '$todate'");
		}
		
		if(isset($values['title']) && $values['title'] !='')
		{
			$attachSelect -> where("engine4_forum_posts.title LIKE ?", "%".$values['title']."%");
		}
		
		$attachSelect -> where("$Name.user_id = ?", $viewer -> getIdentity()) ->order("$Name.modified_date DESC")->limit(10);
				
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($attachSelect);
		$this -> view -> paginator = $paginator -> setCurrentPageNumber($page);
		$this -> view -> paginator -> setItemCountPerPage(10);		
			
	}

	public function myWatchTopicAction()
	{			
		$this -> view -> viewer =  $viewer = Engine_Api::_() -> user() -> getViewer();
		
		$page = $this -> getRequest() -> getParam('page', 1);
		
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$topicLength = $settings->getSetting('forum_newest_topic_length', 10);
		$topicsTable = Engine_Api::_()->getDbtable('topics', 'ynforum');
		$Name = $topicsTable->info('name');
		
		$topicsSelect = $topicsTable->select() -> from($Name, "$Name.*") -> setIntegrityCheck(false);
		
		$topicsSelect -> joinRight("engine4_forum_topicwatches", "engine4_forum_topicwatches.topic_id = $Name.topic_id" , "engine4_forum_topicwatches.*");
		$topicsSelect -> where('approved = ?', 1) -> where ("engine4_forum_topicwatches.user_id = ?", $viewer -> getIdentity())-> where("engine4_forum_topicwatches.watch = 1")->order('modified_date DESC')->limit($topicLength);
				
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($topicsSelect);
		$this -> view -> paginator = $paginator -> setCurrentPageNumber($page);
		$this -> view -> paginator -> setItemCountPerPage($topicLength);		
	}

	public function deleteattachmentAction()
	{	
		$this -> view -> form = $form = new Ynforum_Form_Dashboard_Deleteattach();
		
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$post_id = $this -> _getParam('post_id', 0);
		if($post_id)
		{
			$post = Engine_Api::_()->getItemTable('ynforum_post')->find($post_id)->current();		
			$post -> deleteAttach();
		}
		return $this -> _forward('success', 'utility', 'core', 
						array('smoothboxClose' => true, 
						'parentRefresh' => true, 'format' => 
						'smoothbox', 
						'messages' => array(Zend_Registry::get('Zend_Translate')->_('Attachment deleted.'))));	
	}
}

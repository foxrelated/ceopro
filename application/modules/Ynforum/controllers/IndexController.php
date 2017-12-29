<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_IndexController extends Core_Controller_Action_Standard
{

	public function indexAction()
	{
		if (!$this->_helper->requireAuth()->setAuthParams('forum', null, 'view')->isValid())
		{
			return;
		}

		$categoryTable = Engine_Api::_()->getItemTable('ynforum_category');
		$topicTable = Engine_Api::_()->getItemTable('ynforum_topic');
		$postTable = Engine_Api::_()->getItemTable('ynforum_post');
		$forumTable = Engine_Api::_()->getItemTable('ynforum_forum');

		$this->view->categories = $categoryTable->getCategoriesOrderByLevel();

		$forums = $forumTable->fetchAllAndOrderByHierachy();
		
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$this->view->check_permission = $check_permission = $settings->getSetting('forum_permission_see_forum',0);
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
	
		foreach ($forums as $forumCategory)
		{
			foreach ($forumCategory as $forum)
			{
				$lastPostIds[] = $forum->lastpost_id;
			}
		}

		$lastTopicIds = array();
		$lastPosts = array();
		foreach ($postTable->find($lastPostIds) as $post)
		{
			$lastPosts[$post->getIdentity()] = $post;
			$lastTopicIds[] = $post->topic_id;
		}
		$lastTopics = array();
		foreach ($topicTable->find($lastTopicIds) as $lastTopic)
		{
			$lastTopics[$lastTopic->getIdentity()] = $lastTopic;
		}

		$this->view->lastTopics = $lastTopics;
		$this->view->lastPosts = $lastPosts;
		$this->view->forums = $forums;

		// Render
		$this->_helper->content->setEnabled();
	}

	public function uploadAction()
	{
		$this->_helper->layout->disableLayout();
		
		if (!$this->_helper->requireAuth()->setAuthParams('forum', null, 'view')->isValid())
		{
			return;
		}
		Zend_Registry::get('Zend_Log')->log(print_r('uploadPhoto', true), Zend_Log::DEBUG);

		if (!isset($_FILES['userfile']))
		{
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
			return;
		}
		if (0 !== ($forum_id = (int) $this->_getParam('forum_id')) && null !== ($forum = Engine_Api::_()->getItem('ynforum_forum', $forum_id)) && $forum instanceof Ynforum_Model_Forum)
		{
			Engine_Api::_()->core()->setSubject($forum);
		}

		$this->view->forum = $forum = Engine_Api::_()->core()->getSubject();

		if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.create')->isValid())
		{
			return;
		}

		if (!$this->_helper->requireUser()->checkRequire())
		{
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
			return;
		}
		$destination = "public/ynforum/";
		if (!is_dir($destination))
		{
			mkdir($destination);
		}
		
		$upload = new Zend_File_Transfer_Adapter_Http();
		$upload->setDestination($destination);
		$file_info = pathinfo($upload -> getFileName('userfile', false));
        $fullFilePath = $destination . time() . '.' . $file_info['extension'];

		$image = Engine_Image::factory();
		$image->open($_FILES['userfile']['tmp_name'])->resize(720, 720)->write($fullFilePath);

		$this->view->status = true;
		$this->view->name = $_FILES['userfile']['name'];
		$this->view->photo_url = Zend_Registry::get('StaticBaseUrl') . $fullFilePath;
		$this->view->photo_width = $image->getWidth();
		$this->view->photo_height = $image->getHeight();
	}

	public function uploadPhotoAction()
	{
		Zend_Registry::get('Zend_Log')->log(print_r('uploadPhoto', true), Zend_Log::DEBUG);
		$this->view->forum = $forum = Engine_Api::_()->core()->getSubject();
		if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.create')->isValid())
		{
			return;
		}

		if (!$this->_helper->requireUser()->checkRequire())
		{
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
			return;
		}

		if (!$this->getRequest()->isPost())
		{
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
			return;
		}

		$values = $this->getRequest()->getPost();
		if (empty($values['Filename']))
		{
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
			return;
		}

		if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']))
		{
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
			return;
		}

		$db = Engine_Api::_()->getDbtable('photos', 'ynforum')->getAdapter();
		$db->beginTransaction();

		try
		{
			$viewer = Engine_Api::_()->user()->getViewer();

			$photoTable = Engine_Api::_()->getDbtable('photos', 'ynforum');
			$photo = $photoTable->createRow();
			$photo->setFromArray(array(
				'owner_type' => 'user',
				'owner_id'   => $viewer->getIdentity()
			));
			$photo->save();

			$photo->setPhoto($_FILES['Filedata']);
			$photo->save();

			$this->view->status = true;
			$this->view->name = $_FILES['Filedata']['name'];
			$this->view->photo_id = $photo->photo_id;

			$db->commit();
		}
		catch (Ynforum_Model_Exception $e)
		{
			$db->rollBack();
			$this->view->status = false;
			$this->view->error = $e->getMessage();
			throw $e;
			return;
		}
		catch (Exception $e)
		{
			$db->rollBack();
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
			throw $e;
			return;
		}
	}

	public function signatureAction()
	{
	    if (!$this->_helper->requireUser->isValid()) {
            return;
        }
         // Can specifiy custom id
        $id = $this->_getParam('id', null);
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation(
			'user_settings', ($id ? array('params' => array('id' => $viewer->getIdentity())) : array()));

		$this->view->form = $form = new Ynforum_Form_User_Signature;

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
}

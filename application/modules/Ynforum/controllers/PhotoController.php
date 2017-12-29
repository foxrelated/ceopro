<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: PhotoController.php 7244 2011-07-2- 01:49:53Z john $
 * @author     Luan Nguyen
 */
class Ynforum_PhotoController extends Core_Controller_Action_Standard {
	public function init() {		
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('ynforum_photo', $photo_id))) {
				Engine_Api::_() -> core() -> setSubject($photo);
			} else if (0 !== ($post_id = (int)$this -> _getParam('post_id')) && null !== ($post = Engine_Api::_() -> getItem('ynforum_post', $post_id))) {
				Engine_Api::_() -> core() -> setSubject($post);
			}
		}
	}
	public function uploadPhotoAction() {

		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
		}

		if (!$this -> getRequest() -> isPost()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
		}
		
		$post = Engine_Api::_() -> getItem('ynforum_post', (int)$_REQUEST['post_id']);
		
		// @todo check auth
		//$post

		if (empty($_FILES['files'])) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $error)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
		}

		$db = Engine_Api::_() -> getDbtable('photos', 'Ynforum') -> getAdapter();
		$db -> beginTransaction();

		try {
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$album = $post -> getSingletonAlbum();

			$params = array(
			// We can set them now since only one album is allowed
				'collection_id' => $album -> getIdentity(), 'album_id' => $album -> getIdentity(), 'post_id' => $post -> post_id, 'user_id' => $viewer -> getIdentity(), );
			$temp_file = array('type' => $_FILES['files']['type'][0], 'tmp_name' => $_FILES['files']['tmp_name'][0], 'name' => $_FILES['files']['name'][0]);
			$photo_id = Engine_Api::_() -> Ynforum() -> createPhoto($params, $temp_file) -> photo_id;

			if (!$post -> photo_id) {
				$post -> photo_id = $photo_id;
				$post -> save();
			}

			$db -> commit();

			$status = true;
			$name = $_FILES['files']['name'][0];
	
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $name, 'photo_id' => $photo_id)))));

		} catch( Exception $e ) {
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
		}
	}
	public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('ynfundraising_photo', $this -> getRequest() -> getParam('photo_id'));
		
		if (!$photo)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'Ynforum') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function editAction() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireAuth -> forward();
		}
		$photo = Engine_Api::_() -> core() -> getSubject();

		$this -> view -> form = $form = new Ynfundraising_Form_Photo_Edit();

		if (!$this -> getRequest() -> isPost()) {
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'Ynforum') -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> setFromArray($form -> getValues()) -> save();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array('Changes saved'), 'layout' => 'default-simple', 'parentRefresh' => true, 'closeSmoothbox' => true, ));
	}

	public function removeAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$photo_id = (int)$this -> _getParam('photo_id');
		$photo = Engine_Api::_() -> getItem('ynfundraising_photo', $photo_id);

		$db = $photo -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> delete();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
	}

}

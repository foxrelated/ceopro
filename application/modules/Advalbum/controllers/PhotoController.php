<?php

class Advalbum_PhotoController extends Core_Controller_Action_Standard
{

	public function init()
	{
		// die('asdasd');
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'view') -> isValid())
			return;

		if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('advalbum_photo', $photo_id)))
		{
			Engine_Api::_() -> core() -> setSubject($photo);
		}
	}

	// download resize photo
	public function downloadPhotoAction()
	{
		// tat di layout
		$this -> _helper -> layout -> disableLayout();
		// khong su dung view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		//$params = $this->_getAllParams();
		$photo_id = $this -> _getParam('photo_id', 0);
		$photo_type = $this -> _getParam('photo_type', '');
		$photo = Engine_Api::_() -> getItem('advalbum_photo', $photo_id);
		if (!$photo)
		{
			exit();
		}

		if ($photo_type)
		{
			$file = Engine_Api::_() -> getApi('storage', 'storage') -> get($photo -> file_id, "thumb.normal");
		}
		else
		{
			$file = Engine_Api::_() -> getApi('storage', 'storage') -> get($photo -> file_id, "");
		}
		if (!$file)
		{
			exit();
		}
		// to remove params from file url
		if ($file -> service_id == 1)
		{
			$file_path = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $file -> getHref();
		}
		else
		{
			$file_path = $file -> getHref();
		}
		$info = pathinfo($file -> getHref());
		$title = trim($photo -> getTitle());
		if (empty($title))
		{
			$title = Zend_Registry::get('Zend_Translate') -> _('[Untitled]');
		}
		//$file_name =  $photo->getTitle() . '.' . $info['extension'];
		$file_name = $title . '.' . $info['extension'];
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
			$file_name = rawurlencode($file_name);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		//header ( 'Cache-Control: must-revalidate' );
		header('Pragma: public');
		ob_clean();
		ob_flush();
		echo file_get_contents($file_path);
		exit();
	}

	public function setAlbumCoverAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		// User checking
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// edit permission
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', $viewer, 'edit') -> isValid())
		{
			return;
		}
		$params = $this -> _getAllParams();
		$album = Engine_Api::_() -> getItem('advalbum_album', $params['album_id']);
		if (!$album || !$album -> isOwner($viewer))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		$album -> photo_id = $params['photo_id'];
		$album -> save();
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => false,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Set album cover successfully'))
		));
	}

	/*
	 * Edit photo location
	 */
	public function changeLocationAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_photo') -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', $viewer, 'edit') -> isValid())
		{
			return;
		}
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$params = $this -> _getAllParams();
		$photo = Engine_Api::_() -> core() -> getSubject();
		if (!$photo)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// Make form
		$form = null;
		$form = new Advalbum_Form_Photo_Location();
		$this -> view -> form = $form;
		$form -> populate($photo -> toArray());
		if (!$form)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$values = $form -> getValues();
		$photo -> location = $values['location'];
		$photo -> save();

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => false,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Location is saved successfully.'))
		));
	}

	public function editTitleAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_photo') -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', $viewer, 'edit') -> isValid())
		{
			return;
		}
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$params = $this -> _getAllParams();
		$photo = Engine_Api::_() -> core() -> getSubject();
		if (!$photo)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// Make form
		$form = null;
		$message = null;
		if (isset($params['type']))
		{
			if ($params['type'] == 'title')
			{
				$form = new Advalbum_Form_Photo_EditTitle();
				$message = $this -> view -> translate('Title is saved successfully.');
			}
			if ($params['type'] == 'taken_date')
			{
				$form = new Advalbum_Form_Photo_EditDate();
				$message = $this -> view -> translate('Date is saved successfully.');
			}
		}
		$this -> view -> form = $form;
		$form -> populate($photo -> toArray());
		if (!$form)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$values = $form -> getValues();
		$photo -> setFromArray($values);
		$photo -> save();

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => ($params['type'] != 'title') ? false : true,
			'format' => 'smoothbox',
			'messages' => array($message)
		));
	}

	public function deletePhotoAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_photo') -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', $viewer, 'delete') -> isValid())
		{
			return;
		}
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$params = $this -> _getAllParams();
		$photo = Engine_Api::_() -> core() -> getSubject();
		if (!$photo)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// Make form
		$this -> view -> form = new Advalbum_Form_Photo_DeletePhoto();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$photo -> delete();

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Delete successfully'))
		));
	}

	public function viewAction()
	{
		$photo = '';
		if (Engine_Api::_() -> core() -> hasSubject())
			$photo = Engine_Api::_() -> core() -> getSubject();
		if (!$photo) {
			if ($this->_getParam('format', '') == 'smoothbox') {
				return $this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true
				));
			} else if ($this->_getParam('album_id', 0)){
				return $this->_forward('success' ,'utility', 'core', array(
					'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('album_id' => $this->_getParam('album_id')), 'album_specific', true),
				));
			}
		}
		if (!$this -> _helper -> requireSubject('advalbum_photo') -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = Engine_Api::_() -> getItem('advalbum_album', $photo -> album_id);

		if(!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'view') -> setNoForward()->isValid()) {
			$this->renderScript('photo/show_message.tpl');
			return;
		}

		$this -> _helper -> content -> setEnabled();
		// Get tags
		$tags = array();
		foreach ($photo->tags()->getTagMaps() as $tagmap)
		{
			$tags[] = array_merge($tagmap -> toArray(), array(
				'id' => $tagmap -> getIdentity(),
				'text' => $tagmap -> getTitle(),
				'href' => $tagmap -> getHref(),
				'guid' => $tagmap -> tag_type . '_' . $tagmap -> tag_id
			));
		}
		$this -> view -> tags = $tags;
		// if this is sending a message id, the user is being directed from a conversation
		// check if member is part of the conversation
		$message_id = $this -> getRequest() -> getParam('message');
		$message_view = false;
		if ($message_id)
		{
			$conversation = Engine_Api::_() -> getItem('messages_conversation', $message_id);
			if ($conversation -> hasRecipient(Engine_Api::_() -> user() -> getViewer()))
				$message_view = true;
		}
		$this -> view -> message_view = $message_view;

		if (!$message_view && @!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'view') -> isValid())
			return;

		$checkAlbum = Engine_Api::_() -> getItem('advalbum_album', $this -> _getParam('album_id'));
		if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum -> getIdentity() || $checkAlbum -> album_id != $photo -> album_id)
		{
			$this -> _forward('requiresubject', 'error', 'core');
			return;
		}

		$this -> view -> rating_count = Engine_Api::_() -> advalbum() -> countRating($photo -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_PHOTO);
		$this -> view -> is_rated = Engine_Api::_() -> advalbum() -> checkRated($photo -> getIdentity(), $viewer -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_PHOTO);
		$this -> view -> canTag = $canTag = $album -> authorization() -> isAllowed($viewer, 'tag');
		$this -> view -> canUntagGlobal = $canUntag = $album -> isOwner($viewer);

		$this -> view -> can_edit = $album -> authorization() -> isAllowed($viewer, 'edit');

		if (!$viewer || !$viewer -> getIdentity() || !$album -> isOwner($viewer))
		{
			$photo -> view_count = new Zend_Db_Expr('view_count + 1');
			$photo -> save();
		}

		$format = strtolower(trim($this -> _getParam('format')));

		$popup_view = FALSE;
		if ($format == 'smoothbox')
		{
			$popup_view = TRUE;
		}
		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name)
			-> where("owner_id  = ?", $album -> owner_id)
			-> where("album_id  <> ?", $album -> album_id)
			-> where("search = ?", "1")
			-> order("view_count DESC");
		if($this->_getParam('virtual'))
		{
			$select -> where("`virtual` = ?", "1");
		}
		$paginator = $this -> view -> paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage(1000);
		$paginator -> setPageRange(4);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));

		if($this->_getParam('virtual'))
		{
			$table = Engine_Api::_() -> getDbtable('virtualphotos', 'advalbum');
		}
		else {
			$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		}
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("album_id = ?", $album -> album_id) -> order("order");
		$paginatorp = $this -> view -> paginatorp = Zend_Paginator::factory($select);
		$paginatorp -> setItemCountPerPage(1000);

		$this -> view -> album_virtual = $album_virtual = $this -> _getParam('album_virtual', 0);
		if($album_virtual)
		{
			$this -> view -> album = Engine_Api::_() -> getItem('advalbum_album', $album_virtual);
		}
		$this -> view -> nextPhoto = $photo -> getNextPhoto($album_virtual);
		$this -> view -> previousPhoto = $photo -> getPreviousPhoto($album_virtual);

		$featured = false;
		if ($this -> _getParam('featured') && $this -> _getParam('featured') == true)
		{
			$featured = $this -> _getParam('featured');
		}
		$this -> view -> featured_view = $featured;

		// clear the rotate / flip session variables
		$session_AdvAlbumRotate = new Zend_Session_Namespace('AdvAlbumRotate');
		$session_AdvAlbumRotate -> dest = null;
		$session_AdvAlbumRotate -> photo_id = null;
		$session_AdvAlbumFlip = new Zend_Session_Namespace('AdvAlbumFlip');
		$session_AdvAlbumFlip -> dest = null;
		$session_AdvAlbumFlip -> photo_id = null;

		if ($popup_view)
		{
			$this -> renderScript('photo/view_popup.tpl');
		}

        // Set meta
        $og = '<meta property="og:image" content="' . $this->finalizeUrl($photo->getPhotoUrl()) . '" />';
        $og .= '<meta property="og:image:width" content="640" />';
        $og .= '<meta property="og:image:height" content="442" />';
        $og .= '<meta property="og:title" content="' . $photo->getTitle() . '" />';
        $og .= '<meta property="og:description" content="' . $this->view->string()->striptags($photo->getDescription()) . '" />';
        $og .= '<meta property="og:url" content="' . $this->finalizeUrl($photo->getHref()) . '" />';
        $og .= '<meta property="og:type" content="website" />';
        $this->view->layout()->headIncludes .= $og;
	}

    private function finalizeUrl($url)
    {
        if ($url)
        {
            if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
            {
                $pageURL = 'http';
                if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
                {
                    $pageURL .= "s";
                }
                $pageURL .= "://";
                $pageURL .= $_SERVER["SERVER_NAME"];
                $url = $pageURL . '/'. ltrim( $url, '/');
            }
        }

        return $url;
    }

	public function frameviewAction()
	{
		if (!$this -> _helper -> requireSubject('advalbum_photo') -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = Engine_Api::_() -> getItem('advalbum_album', $photo -> album_id);

		// if this is sending a message id, the user is being directed from a
		// coversation
		// check if member is part of the conversation
		$message_id = $this -> getRequest() -> getParam('message');
		$message_view = false;
		if ($message_id)
		{
			$conversation = Engine_Api::_() -> getItem('messages_conversation', $message_id);
			if ($conversation -> hasRecipient(Engine_Api::_() -> user() -> getViewer()))
				$message_view = true;
		}
		$this -> view -> message_view = $message_view;

		// if( !$this->_helper->requireAuth()->setAuthParams(null, null,
		// 'view')->isValid() ) return;
		if (!$message_view && @!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'view') -> isValid())
			return;

		$checkAlbum = Engine_Api::_() -> getItem('advalbum_album', $this -> _getParam('album_id'));
		if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum -> getIdentity() || $checkAlbum -> album_id != $photo -> album_id)
		{
			$this -> _forward('requiresubject', 'error', 'core');
			return;
		}

		if (!$viewer || !$viewer -> getIdentity() || !$album -> isOwner($viewer))
		{
			$photo -> view_count = new Zend_Db_Expr('view_count + 1');
			$photo -> save();
			$this -> view -> do_count = $photo -> view_count;
		}

		$this -> view -> canTag = $canTag = $album -> authorization() -> isAllowed($viewer, 'tag');
		$this -> view -> canUntagGlobal = $canUntag = $album -> isOwner($viewer);
		$this -> view -> can_edit = $can_edit = @$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'edit') -> checkRequire();

		// $this->_helper->layout->disableLayout();
		$this -> _helper -> layout -> setLayout('default-simple');
	}

	public function slideviewAction()
	{
		if (!$this -> _helper -> requireSubject('advalbum_photo') -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		if (!$photo)
		{
			$this -> _forward('requiresubject', 'error', 'core');
			return;
		}
		$this -> view -> album = $album = Engine_Api::_() -> getItem('advalbum_album', $photo -> album_id);
		if (@!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'view') -> isValid())
			return;

		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$tableName = $table -> info('name');
		$select = $table -> select() -> from($tableName) -> where("album_id = ?", $photo -> album_id) -> order("order");
		$this -> view -> photo_list = $table -> fetchAll($select);
		$this -> _helper -> layout -> setLayout('default-simple');
	}

	public function popupviewAction()
	{
		if (!$this -> _helper -> requireSubject('advalbum_photo') -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		if (!$photo)
		{
			$this -> _forward('requiresubject', 'error', 'core');
			return;
		}
		$this -> view -> album = $album = Engine_Api::_() -> getItem('advalbum_album', $photo -> album_id);
		if (@!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'view') -> isValid())
			return;

		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$tableName = $table -> info('name');
		$select = $table -> select() -> from($tableName) -> where("album_id = ?", $photo -> album_id) -> order("order");
		$this -> view -> photo_list = $table -> fetchAll($select);
		$this -> _helper -> layout -> setLayout('default-simple');
	}

	public function deleteAction()
	{
		if (!$this -> _helper -> requireSubject('advalbum_photo') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'delete') -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$photo = Engine_Api::_() -> core() -> getSubject('advalbum_photo');

		$db = $photo -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();

			$db -> commit();
		}

		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function sendToFriendsAction() {

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		if (!$photo) {
			return $this->_helper->requireSubject()->forward();
		}

		$this->view->form = $form = new Advalbum_Form_Photo_SendToFriends();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$values = $form -> getValues();
		$viewerTitle = $viewer->getIdentity() ? $viewer->getTitle() : $values['send_name'];
		$send_emails = $values['recipients'];
		$send_message = @$values['message'];

		$send_name = $viewerTitle;

//		$server_array = explode ( "/", $_SERVER ['PHP_SELF'] );
//		$server_info = implode ( "/", $server_array );
//		$selfURL =  "http://" . $_SERVER ['HTTP_HOST'] . $server_info . "/";
		$url_back = $photo -> getHref();
		$photo_title = $photo->getTitle();
		$sender_href = $viewer -> getHref();
//		$header = "From: " . $send_name . "\r\n";
//		$header .= "Content-type: text/html; charset=utf-8";
		$emailsCount = sizeof(explode(",", $send_emails));

		// Main params
		$defaultParams = array(
			'host' => $_SERVER['HTTP_HOST'],
			'object_title' => $photo_title,
			'sender_title' => $send_name,
			'message' => $send_message,
			'object_link' => $url_back,
			'sender_link' => $sender_href,
		);
		$send_emails = explode(",", $send_emails);
		// Send
		try {
			Engine_Api::_()->getApi('mail', 'core')->sendSystem(
				$send_emails,
				'send_image',
				$defaultParams
			);
		} catch (Exception $e) {
			$error_message = Zend_Registry::get('Zend_Translate')->_("Mail sending failed.");
		}

		$message = Zend_Registry::get('Zend_Translate') -> _("$emailsCount email(s) have been sent.");
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => false,
			'smoothboxClose' => true,
			'messages' => $message
		));
	}

	public function sendImageAction()
	{
		$error_message = '';
		$result = '';
		$send_emails = $this -> getRequest() -> getParam('send_emails');
		$pattern = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
		$emails = split(",", $send_emails);
		$count = 0;
		$str = Zend_Registry::get('Zend_Translate') -> _("Email invalid:");
		foreach ($emails as $email)
		{
			if (preg_match($pattern, $email) == 0)
			{
				$count++;
				$str .= "'" . $email . "' ";
			}
		}
		if ($count > 0)
		{
			$error_message = $str;
		}
		else
		{
			$send_name = $this -> getRequest() -> getParam("send_name");
			$send_message = $this -> getRequest() -> getParam("send_message");
			$url_back = $this -> getRequest() -> getParam("url_send");
			$photo_title = $this -> getRequest() -> getParam("photo_title");
			$sender_href = $this -> getRequest() -> getParam("sender_href");
			$header = "From: " . $send_name . "\r\n";
			$header .= "Content-type: text/html; charset=utf-8";
			if ($send_name == "")
				$error_message = "Please enter your name or your email!";
			else
				if (trim($send_emails) == "")
					$error_message = "Please enter at least a email!";
				else
					if (sizeof(explode(",", $send_emails)) > 5)
						$error_message = "You are allowed to send email to up to 5 persons at a time.";
					else
					{

						// Main params
						$defaultParams = array(
							'object_title' => $photo_title,
							'sender_title' => $send_name,
							'message' => $send_message,
							'object_link' => $url_back,
							'sender_link' => $sender_href,
						);
						$send_emails = explode(",", $send_emails);
						// Send
						try {
							Engine_Api::_()->getApi('mail', 'core')->sendSystem(
								$send_emails,
								'send_image',
								$defaultParams
							);
							$result = Zend_Registry::get('Zend_Translate') -> _("Your picture has been sent.");
						} catch( Exception $e ) {
							$error_message = Zend_Registry::get('Zend_Translate') -> _("Mail sending failed.");
						}
					}
		}
		$result_response = null;
		$this -> view -> result = $result;
		$this -> view -> error_message = $error_message;
		return;
	}

	public function rotateAction()
	{
		$album_id = $this -> _getParam('album_id', 0);
		$photo_id = $this -> _getParam('photo_id', 0);
		$dest = $this -> _getParam('dest', 'left');

		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$can_edit = FALSE;
		if ($photo)
		{
			$this -> view -> album = $album = Engine_Api::_() -> getItem('advalbum_album', $photo -> album_id);
			$can_edit = $this -> _helper -> requireAuth() -> setAuthParams($album, null, 'edit') -> checkRequire();
		}
		$is_login = $this -> _helper -> requireUser() -> isValid();

		$parentRefresh = false;
		$first_call = false;
		$session_AdvAlbumFlip = new Zend_Session_Namespace('AdvAlbumFlip');
		if (!isset($session_AdvAlbumFlip -> dest) || $session_AdvAlbumFlip -> dest != $dest || !isset($session_AdvAlbumFlip -> photo_id) || $session_AdvAlbumFlip -> photo_id != $photo_id)
		{
			$parentRefresh = true;
			$first_call = true;
			$session_AdvAlbumFlip -> dest = $dest;
			$session_AdvAlbumFlip -> photo_id = $photo_id;
		}

		if (!$photo || !$is_login || !$can_edit || !$first_call)
		{
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => false,
				'format' => 'smoothbox',
				'messages' => array('No permission!')
			));
			return;
		}
		// Get file
		$file = Engine_Api::_() -> getItem('storage_file', $photo -> file_id);
		if (!($file instanceof Storage_Model_File))
		{
			$this -> view -> status = false;
			$this -> view -> error = $this -> view -> translate('Could not retrieve file');
			return;
		}

		// Pull photo to a temporary file
		$tmpFile = $file -> temporary();

		if ($dest == 'left')
		{
			$angle = 90;
		}
		else
		{
			$angle = 270;
		}
		// Operate on the file
		$image = Engine_Image::factory();
		$image -> open($tmpFile) -> rotate($angle) -> write() -> destroy();

		// Set the photo
		$db = $photo -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$params = array(
				'owner_type' => 'user',
				'owner_id' => $viewer -> getIdentity()
			);
			Engine_Api::_() -> advalbum() -> createPhoto($params, $tmpFile, $photo);
			@unlink($tmpFile);
			$db -> commit();
		}
		catch( Exception $e )
		{
			@unlink($tmpFile);
			$db -> rollBack();
			throw $e;
		}

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => $parentRefresh,
			'format' => 'smoothbox',
			'messages' => array('Your changes have been saved.')
		));
	}

	public function flipAction()
	{
		$album_id = $this -> _getParam('album_id', 0);
		$photo_id = $this -> _getParam('photo_id', 0);
		$dest = $this -> _getParam('dest', 'vertical');

		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$can_edit = FALSE;
		if ($photo)
		{
			$this -> view -> album = $album = Engine_Api::_() -> getItem('advalbum_album', $photo -> album_id);
			$can_edit = $this -> _helper -> requireAuth() -> setAuthParams($album, null, 'edit') -> checkRequire();
		}
		$is_login = $this -> _helper -> requireUser() -> isValid();

		$parentRefresh = false;
		$first_call = false;
		$session_AdvAlbumFlip = new Zend_Session_Namespace('AdvAlbumFlip');
		if (!isset($session_AdvAlbumFlip -> dest) || $session_AdvAlbumFlip -> dest != $dest || !isset($session_AdvAlbumFlip -> photo_id) || $session_AdvAlbumFlip -> photo_id != $photo_id)
		{
			$parentRefresh = true;
			$first_call = true;
			$session_AdvAlbumFlip -> dest = $dest;
			$session_AdvAlbumFlip -> photo_id = $photo_id;
		}

		if (!$photo || !$is_login || !$can_edit || !$first_call)
		{
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => false,
				'format' => 'smoothbox',
				'messages' => array('No permission!')
			));
			return;
		}

		if (!in_array($dest, array(
			'vertical',
			'horizontal'
		)))
		{
			$this -> view -> status = false;
			$this -> view -> error = $this -> view -> translate('Invalid direction');
			return;
		}

		// Get file
		$file = Engine_Api::_() -> getItem('storage_file', $photo -> file_id);
		if (!($file instanceof Storage_Model_File))
		{
			$this -> view -> status = false;
			$this -> view -> error = $this -> view -> translate('Could not retrieve file');
			return;
		}

		// Pull photo to a temporary file
		$tmpFile = $file -> temporary();

		// Operate on the file
		$image = Engine_Image::factory();
		$image -> open($tmpFile) -> flip($dest != 'vertical') -> write() -> destroy();

		// Set the photo
		$db = $photo -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$params = array(
				'owner_type' => 'user',
				'owner_id' => $viewer -> getIdentity()
			);
			Engine_Api::_() -> advalbum() -> createPhoto($params, $tmpFile, $photo);
			@unlink($tmpFile);
			$db -> commit();
		}
		catch( Exception $e )
		{
			@unlink($tmpFile);
			$db -> rollBack();
			throw $e;
		}

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => $parentRefresh,
			'format' => 'smoothbox',
			'messages' => array('Your changes have been saved.')
		));
	}

	public function addToVirtualAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$params = $this -> _getAllParams();

		// Make form
		$this -> view -> form = $form = new Advalbum_Form_Photo_AddToVirtual();
		$error_message = "";
		$message = $this -> view -> translate('Added photo successfully.');
		$virtualAlbumAssoc = $form->getElement("album_id")->getMultiOptions();

		// NO VIRTUAL ALBUMS
		if (!count($virtualAlbumAssoc))
		{
			$this -> view -> error_message = $error_message = $this -> view -> translate("No Virtual Albums found.");
		}

		if (!$form)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		$virtualPhotoTbl = Engine_Api::_()->getDbTable("virtualphotos", "advalbum");
		// ADDED THIS PHOTO TO SPECIFIC ALBUM ALREADY
		if (!$virtualPhotoTbl->checkPhoto($values['album_id'],$params['photo_id']))
		{
			$this -> view -> error_message = $error_message = $this -> view -> translate("This photo was added before!");
			return;
		}
		$virtualPhoto = $virtualPhotoTbl->createRow();
		$virtualPhoto->photo_id = $params['photo_id'];
		$virtualPhoto->album_id = $values['album_id'];
		$virtualPhoto->save();

		$album = Engine_Api::_()->getItem("advalbum_album", $values['album_id']);
		if (!$album->photo_id)
		{
			$album->photo_id = $params['photo_id'];
			$album->save();
		}

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => false,
			'format' => 'smoothbox',
			'messages' => array($message)
		));
	}

	public function deleteVirtualPhotoAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', $viewer, 'delete') -> isValid())
		{
			return;
		}
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$params = $this -> _getAllParams();
		$virtualPhotoTbl = Engine_Api::_()->getDbTable("virtualphotos", "advalbum");
		$select = $virtualPhotoTbl->select()->where("album_id = ?", $params['album_id'])->where("photo_id = ?", $params['photo_id']);
		$virtualPhoto = $virtualPhotoTbl->fetchRow($select);
		if (!$virtualPhoto)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// Make form
		$this -> view -> form = new Advalbum_Form_Photo_DeletePhoto();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$album = Engine_Api::_() -> getItem('advalbum_album', $virtualPhoto -> album_id);
		if ($album->photo_id == $virtualPhoto->photo_id)
		{
			$album->photo_id = 0;
			$album->save();
		}
		$virtualPhoto -> delete();

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Deleted successfully'))
		));
	}

	public function exportUrlAction()
	{
		$server_array = explode ( "/", $_SERVER ['PHP_SELF'] );
		$server_array_mod = array_pop ( $server_array );
		if ($server_array [count ( $server_array ) - 1] == "admin") {
			$server_array_mod = array_pop ( $server_array );
		}
		$server_info = implode ( "/", $server_array );

		$selfURL =  "http://" . $_SERVER ['HTTP_HOST'] . $server_info . "/";
		$serverURL = "http://" . $_SERVER ['HTTP_HOST'];

		$photo = Engine_Api::_() -> core() -> getSubject();
		$album_id = $this->_getParam('album_id');
		$photo_id = $this->_getParam('photo_id');
		$mode = $this->_getParam('mode');
		// Get embed code
		$url = '';
		switch ($mode) {
			case 'url':
				$url = $selfURL . 'albums/photo/view/album_id/' . $album_id . '/photo_id/' . $photo_id;
				$title = 'URL';
				break;
			case 'html':
				$url = '<a href = "' . $selfURL . 'albums/photo/view/album_id/' . $album_id . '/photo_id/"><img src = "' . $serverURL . $photo->getPhotoUrl() . '"></a>';
				$title = 'HTML code';
				break;
			case 'forum':
				$title = 'Forum code';
				$url = '[URL=' . $selfURL . 'albums/photo/view/album_id/' . $album_id . '/photo_id/' . $photo_id . '][IMG]' . $serverURL . $photo->getPhotoUrl() . '[/IMG][/URL]';
				break;
		}

		$this -> view -> title = $title;
		$this -> view -> htmlCode = $url;
	}
}

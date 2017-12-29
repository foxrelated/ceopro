<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_Model_Post extends Core_Model_Item_Abstract
{

	protected $_parent_type = 'ynforum_topic';
	protected $_owner_type = 'user';
	protected $_type = 'forum_post';
	public $deletingTopic;

	public function getDescription()
	{
		//        return strip_tags($this->body);
		// @todo decide how we want to handle multibyte string functions
		$tmpBody = strip_tags($this -> body);
		$tmpBody = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $tmpBody);
		return (Engine_String::strlen($tmpBody) > 350 ? Engine_String::substr($tmpBody, 0, 350) . '...' : $tmpBody);
	}
	
	public function getSingletonAlbum() {
		$table = Engine_Api::_ ()->getItemTable ( 'ynforum_postalbum' );
		$select = $table->select ()->where ( 'post_id = ?', $this->getIdentity () )->order ( 'postalbum_id ASC' )->limit ( 1 );
		
		$album = $table->fetchRow ( $select );

		if (null === $album) {
			$album = $table->createRow ();
			$album->setFromArray ( array (
					'title' => $this->getTitle (),
					'post_id' => $this->getIdentity ()
			) );
			$album->save ();
		}

		return $album;
	}

	public function getHref($params = array())
	{
		$topic = $this -> getParent();
		$params = array_merge(array(
			'route' => 'ynforum_topic',
			'reset' => true,
			'topic_id' => $this -> topic_id,
			'slug' => $topic -> getSlug(),
			'post_id' => $this -> getIdentity(),
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	protected function _insert()
	{
		if (empty($this -> topic_id))
		{
			throw new Ynforum_Model_Exception('Cannot have a post without a topic');
		}

		if (empty($this -> user_id))
		{
			throw new Ynforum_Model_Exception('Cannot have a post without a user');
		}

		// if the post's owner is the forum's moderator, his post will be approved.
		$forum = $this -> getParent() -> getParent();
		if (!$forum -> isModerator($this -> getOwner()))
		{
			$settings = Engine_Api::_() -> getApi('settings', 'core');
			$this -> approved = $settings -> getSetting('forum_approve_topic', 0);
		}
		else
		{
			$this -> approved = 1;
		}

		parent::_insert();
	}

	private function _updateStatisticWhenAPostIsSaved()
	{
		$table = Engine_Api::_() -> getItemTable('ynforum_signature');
		$select = $table -> select() -> where('user_id = ?', $this -> user_id) -> limit(1);
		$row = $table -> fetchRow($select);

		if (null == $row)
		{
			$row = $table -> createRow();
			$row -> user_id = $this -> user_id;
			if ($this -> approved)
			{
				$row -> approved_post_count = 1;
			}
			else
			{
				$row -> approved_post_count = 0;
			}
			$row -> post_count = 1;
		}
		else
		{
			if (empty($this -> _cleanData))
			{
				// when a post is created, increase the post count
				$row -> post_count = new Zend_Db_Expr('post_count + 1');
			}
			// when a post is approved, increase the approved post count
			// because there is no case to unapprove a post -> do not take care about decrease the approved post count
			if ($this -> approved)
			{
				$row -> approved_post_count = new Zend_Db_Expr('approved_post_count + 1');
			}
		}
		$row -> save();

		// Update topic post count
		$topic = $this -> getParent();
		if ($this -> approved)
		{
			$topic -> approved_post_count = new Zend_Db_Expr('approved_post_count + 1');
		}

		// just increase the topic's post count when creating a new post
		if (!$this -> getIdentity())
		{
			$topic -> post_count = new Zend_Db_Expr('post_count + 1');
		}
		$topic -> modified_date = $this -> creation_date;
		if ($topic -> lastpost_id < $this -> post_id && $this -> approved)
			$topic -> lastpost_id = $this -> post_id;
		$topic -> lastposter_id = $this -> user_id;
		$topic -> save();

		// Update forum post count
		$forum = $topic -> getParent();
		if ($this -> approved)
		{
			$forum -> approved_post_count = new Zend_Db_Expr('approved_post_count + 1');
			$forum -> lastpost_id = $this -> post_id;
			$forum -> lastposter_id = $this -> user_id;
		}
		// just increase the forum's post count when creating a new post
		if (!$this -> getIdentity())
		{
			$forum -> post_count = new Zend_Db_Expr('post_count + 1');
		}
		$forum -> modified_date = $this -> creation_date;
		$forum -> save();
	}

	protected function _postInsert()
	{
		// statistic the post count for the topic, forum and user when the post is approved
		$this -> _updateStatisticWhenAPostIsSaved();
		parent::_postInsert();
	}

	protected function _postUpdate()
	{
		// statistic the post count for the topic, forum and user when the post is approved
		if (array_key_exists('approved', $this -> _modifiedFields))
		{
			$this -> _updateStatisticWhenAPostIsSaved();
		}

		parent::_postUpdate();
	}

	protected function _update()
	{
		if (empty($this -> topic_id))
		{
			throw new Forum_Model_Exception('Cannot have a post without a topic');
		}

		$this -> modified_date = date('Y-m-d H:i:s');

		parent::_update();
	}

	protected function _delete()
	{
		$this -> deletePhoto();

		$topic = $this -> getParent();
		$forum = $topic -> getParent();
		$signatureTbl = Engine_Api::_() -> getItemTable('ynforum_signature');
		if (!$this -> approved)
		{
			// Decrement user post count
			$signatureTbl -> update(array('post_count' => new Zend_Db_Expr('post_count - 1')), array(
				'user_id = ?' => $this -> user_id,
				'post_count > 0'
			));
			if (!$this -> deletingTopic)
			{
				$topic -> post_count = new Zend_Db_Expr('post_count - 1');
			}
			// Update forum post count
			// TODO : check again the condition here
			if ($forum -> post_count > 0)
			{
				$forum -> post_count = new Zend_Db_Expr('post_count - 1');
			}
			$topic -> save();
			$forum -> save();
		}
		else
		{
			// Decrement user post count
			$signatureTbl -> update(array(
				'approved_post_count' => new Zend_Db_Expr('approved_post_count - 1'),
				'post_count' => new Zend_Db_Expr('post_count - 1'),
			), array(
				'user_id = ?' => $this -> user_id,
				// only decrement if the post_count is greater than 0
				'approved_post_count > 0',
				'post_count > 0',
			));

			// delete thanks to this post
			Engine_Api::_() -> getItemTable('ynforum_thank') -> delete(array('post_id = ?' => $this -> getIdentity()));

			if (!$this -> deletingTopic)
			{
				$topic -> approved_post_count = new Zend_Db_Expr('approved_post_count - 1');
				$topic -> post_count = new Zend_Db_Expr('post_count - 1');

				// Update topic last post
				if ($topic -> lastpost_id == $this -> post_id)
				{
					$olderTopicLastPost = $this -> getTable() -> select() -> where('topic_id = ?', $this -> topic_id) -> where('post_id != ?', $this -> post_id) -> where('approved = 1') -> order('post_id DESC') -> limit(1) -> query() -> fetch();

					if ($olderTopicLastPost)
					{
						$topic -> lastpost_id = $olderTopicLastPost['post_id'];
						$topic -> lastposter_id = $olderTopicLastPost['user_id'];
					}
					else
					{
						$topic -> lastpost_id = null;
						$topic -> lastposter_id = null;
					}
				}

				// Update forum post count
				if ($forum -> approved_post_count > 0)
				{
					$forum -> approved_post_count = new Zend_Db_Expr('approved_post_count - 1');
				}
				if ($forum -> post_count > 0)
				{
					$forum -> post_count = new Zend_Db_Expr('post_count - 1');
				}

				// Update forum last post
				if ($forum -> lastpost_id == $this -> post_id)
				{
					$olderForumLastPost = $this -> getTable() -> select() -> where('forum_id = ?', $this -> forum_id) -> where('post_id != ?', $this -> post_id) -> order('post_id DESC') -> limit(1) -> query() -> fetch();

					if ($olderForumLastPost)
					{
						$forum -> lastpost_id = $olderForumLastPost['post_id'];
						$forum -> lastposter_id = $olderForumLastPost['user_id'];
					}
					else
					{
						$forum -> lastpost_id = null;
						$forum -> lastposter_id = null;
					}
				}

				$topic -> save();
				$forum -> save();
			}
		}

		if (!$this -> deletingTopic)
		{
			$table = $this -> getTable();
			$select = new Zend_Db_Select($table -> getAdapter());
			$select -> from($table -> info('name'), 'COUNT(*) AS count');
			$select -> where('topic_id = ?', $this -> topic_id);
			$count = $select -> query() -> fetchColumn(0);

			if ($count == 1)
			{
				//            if( $topic->post_count - 1 == 0 ) {
				$this -> deletingTopic = true;
				$topic -> delete();
			}
		}

		parent::_delete();
	}

	public function getLastCreatedPost()
	{
		return $this -> getChildren('ynforum_post', array(
			'limit' => 1,
			'order' => 'creation_date DESC'
		));
	}

	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_id' => $this -> getIdentity(),
			'parent_type' => 'forum_post'
		);

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(2000, 2000) -> write($path . '/m_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);

		// Remove temp files
		@unlink($path . '/m_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> file_id = $iMain -> getIdentity();
		$this -> save();

		return $this;
	}

	public function getSignature()
	{
		$user_id = $this -> user_id;
		$table = Engine_Api::_() -> getItemTable('ynforum_signature');
		$select = $table -> select() -> where("user_id = ?", $user_id) -> limit(1);
		return $table -> fetchRow($select);
	}

	public function getPhotoUrl($type = null)
	{
		$photo_id = $this -> file_id;
		if (!$photo_id)
		{
			return null;
		}

		$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($photo_id, $type);
		if (!$file)
		{
			return null;
		}

		return $file -> map();
	}

	public function getPostIndex()
	{
		$table = $this -> getTable();

		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), new Zend_Db_Expr('COUNT(post_id) as count')) -> where('topic_id = ?', $this -> topic_id) -> where('post_id < ?', $this -> getIdentity()) -> order('post_id ASC');

		$data = $select -> query() -> fetch();
		return (int)$data['count'];
	}

	public function deletePhoto()
	{
		if (empty($this -> file_id))
		{
			return;
		}
		// This is dangerous, what if something throws an exception in postDelete
		// after the files are deleted?
		try
		{
			$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($this -> file_id);
			if ($file)
			{
				$file -> remove();
			}
			$this -> file_id = null;
		}
		catch (Exception $e)
		{
			// @todo completely silencing them probably isn't good enough
			throw $e;
		}
	}

	public function getPhotos()
	{
		$photoTable = Engine_Api::_() -> getItemTable('ynforum_photo');
		$select = $photoTable -> select() -> where('post_id = ?', $this -> getIdentity());

		return $photoTable -> fetchAll($select);
	}

	/**
	 * check that weather a user thanked to a post or not
	 * @param type $user_id
	 * @return boolean, true if this post was thanked by this user, otherwise, return false
	 */
	public function isThanked($user_id)
	{
		$thankTable = Engine_Api::_() -> getItemTable('ynforum_thank');
		$thankSelect = $thankTable -> select();
		$thankSelect -> where('post_id = ?', $this -> getIdentity());
		$thankSelect -> where('user_id = ?', $user_id);
		$thank = $thankTable -> fetchRow($thankSelect);

		return $thank != null;
	}

	public function thank($user_id)
	{
		if (!$this -> isThanked($user_id))
		{
			$thankTable = Engine_Api::_() -> getItemTable('ynforum_thank');
			$thankTable -> giveThank($user_id, $this -> user_id, $this -> getIdentity());
			$this -> thanked_count = new Zend_Db_Expr('thanked_count + 1');
			if ($this -> save())
			{
				return true;
			}
		}

		return false;
	}

	public function getThankedUserIds()
	{
		$thankTable = Engine_Api::_() -> getItemTable('ynforum_thank');
		$thankSelect = $thankTable -> select();
		$thankSelect -> where('post_id = ?', $this -> getIdentity());
		$userIds = array();
		foreach ($thankTable->fetchAll($thankSelect) as $thank)
		{
			$userIds[] = $thank -> user_id;
		}
		return $userIds;
	}

	public function isAddedReputationBy($user_id)
	{
		$reputationTable = Engine_Api::_() -> getItemTable('ynforum_reputation');
		$reputationSelect = $reputationTable -> select();
		$reputationSelect -> where('post_id = ?', $this -> getIdentity());
		$reputationSelect -> where('user_id = ?', $user_id);
		$reputation = $reputationTable -> fetchRow($reputationSelect);

		return $reputation != null;
	}

	public function getTitle()
	{
		if ($this -> topic_id)
		{
			$topic = $this -> getParent();
			if ($topic)
			{
				return $topic -> getTitle();
			}
		}
		return parent::getTitle();
	}

	/**
	 *
	 * Save attach
	 */
	public function saveAttach($name, $title)
	{
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynforum_attach',
			'parent_id' => $this -> getIdentity()
		);

		// Save
		$storage = Engine_Api::_() -> storage();

		// Store
		$aMain = $storage -> create($path . '/' . $name, $params);

		// Remove temp files
		@unlink($path . '/' . $name);

		// save row
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$attach_table = Engine_Api::_() -> getDbTable('attachments', 'ynforum');
		$attachment = $attach_table -> createRow();
		$attachment -> post_id = $this -> getIdentity();
		$attachment -> user_id = $viewer -> getIdentity();
		$attachment -> file_id = $aMain -> getIdentity();
		$attachment -> title = $title;
		$attachment -> creation_date = date('Y-m-d H:i:s');
		$attachment -> modified_date = date('Y-m-d H:i:s');
		$attachment -> save();

		return $this;
	}

	/**
	 * get Attach
	 */
	public function getAttachments()
	{
		$attachTable = Engine_Api::_() -> getDbtable('attachments', 'ynforum');
		$select = $attachTable -> select() -> where('post_id = ?', $this -> getIdentity()) -> order('creation_date DESC');
		$attachments = $attachTable -> fetchAll($select);
		return $attachments;
	}

	/*
	 *
	 * delete Attach
	 */
	public function deleteAttach()
	{
		$attachTable = Engine_Api::_() -> getDbtable('attachments', 'ynforum');
		$select = $attachTable -> select() -> where('post_id = ?', $this -> getIdentity()) -> limit(1);
		$attachment = $attachTable -> fetchRow($select);
		if($attachment)
			$attachment->delete();
		return true;
	}
	public function getMediaType()
	{
		return 'post';
	}

}

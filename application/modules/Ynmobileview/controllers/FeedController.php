<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_FeedController extends Core_Controller_Action_Standard
{
	public function likeAction()
	{
		// Make sure user exists
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		// Collect params
		$action_id = $this -> _getParam('action_id');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Start transaction
		$db = Engine_Api::_() -> getDbtable('likes', 'activity') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> getActionById($action_id);
			// Action
			// Check authorization
			if ($action && !Engine_Api::_() -> authorization() -> isAllowed($action -> getObject(), null, 'comment'))
			{
				throw new Engine_Exception('This user is not allowed to like this item');
			}

			$action -> likes() -> addLike($viewer);

			// Add notification for owner of activity (if user and not viewer)
			if ($action -> subject_type == 'user' && $action -> subject_id != $viewer -> getIdentity())
			{
				$actionOwner = Engine_Api::_() -> getItemByGuid($action -> subject_type . "_" . $action -> subject_id);

				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($actionOwner, $viewer, $action, 'liked', array('label' => 'post'));
			}

			// Stats
			Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('core.likes');

			$db -> commit();
			$data = array('title' => $this->view->translate('Like'),
						  'str_totalLike' => $this->view->translate(array('%s Like','%s Likes', $action -> likes() -> getLikeCount()), $action -> likes() -> getLikeCount())
						);
			if($action -> comments() -> getCommentCount() && $action -> likes() -> getLikeCount()) 
			{
				$data['str_totalLike'] = $data['str_totalLike'].' <span class="ynmb_dot">·</span>';
			}
			$this -> _helper -> layout() -> disableLayout();
			$this -> _helper -> viewRenderer -> setNoRender(true);
			echo Zend_Json::encode($data);
			exit;
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function unlikeAction()
	{
		// Make sure user exists
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		// Collect params
		$action_id = $this -> _getParam('action_id');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Start transaction
		$db = Engine_Api::_() -> getDbtable('likes', 'activity') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> getActionById($action_id);
			// Check authorization
			if (!Engine_Api::_() -> authorization() -> isAllowed($action -> getObject(), null, 'comment'))
			{
				throw new Engine_Exception('This user is not allowed to unlike this item');
			}

			$action -> likes() -> removeLike($viewer);
			$db -> commit();
			$data = array('title' => $this->view->translate('Like'),
						  'str_totalLike' => $this->view->translate(array('%s Like','%s Likes', $action -> likes() -> getLikeCount()), $action -> likes() -> getLikeCount())
						);
			if($action -> likes() -> getLikeCount() == 0)
			{
				$data['str_totalLike'] = '';
			}
			else if($action -> comments() -> getCommentCount() && $action -> likes() -> getLikeCount()) 
			{
				$data['str_totalLike'] = $data['str_totalLike'].' <span class="ynmb_dot">·</span>';
			}
			$this -> _helper -> layout() -> disableLayout();
			$this -> _helper -> viewRenderer -> setNoRender(true);
			echo Zend_Json::encode($data);
			exit;
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}
	public function statusAction()
	{
		$action_id = $this -> _getParam('action_id');
		$this->view->play = false;
		if($this -> _getParam('play') == 'on')
		{
			$this->view->play = true;
		}
		$form = new Activity_Form_Comment();
		$this->view->commentForm = $form;
		$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> getActionById($action_id);
		$this->view->action = $action;
		if(!$this -> _getParam('play'))
		{
			$this->view->viewAllComments  = true; 
		}
		$this->view->viewAllLikes  = $this->_getParam('viewAllLikes', $this->_getParam('show_likes', false));
	}
}
?>
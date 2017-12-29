<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_MusicController extends Core_Controller_Action_Standard
{
	public function profileAction()
	{
		// Check auth
		if (!$this -> _helper -> requireAuth() -> setAuthParams('music_playlist', null, 'view') -> isValid())
		{
			return;
		}

		// Get viewer info
		$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();

		// Get subject
		if (null !== ($playlist_id = $this -> _getParam('playlist_id')) && null !== ($playlist = Engine_Api::_() -> getItem('music_playlist', $playlist_id)) && $playlist instanceof Music_Model_Playlist && !Engine_Api::_() -> core() -> hasSubject())
		{
			Engine_Api::_() -> core() -> setSubject($playlist);
		}
		// Check subject
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}

		// Get viewer/subject
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> playlist = $playlist = Engine_Api::_() -> core() -> getSubject('music_playlist');

		// Increment view count
		if (!$viewer -> isSelf($playlist -> getOwner()))
		{
			$playlist -> view_count++;
			$playlist -> save();
		}

		// if this is sending a message id, the user is being directed from a coversation
		// check if member is part of the conversation
		$message_view = false;
		if (null !== ($message_id = $this -> _getParam('message')))
		{
			$conversation = Engine_Api::_() -> getItem('messages_conversation', $message_id);
			$message_view = $conversation -> hasRecipient($viewer);
		}
		$this -> view -> message_view = $message_view;

		// Check auth
		if (!$message_view && !$this -> _helper -> requireAuth() -> setAuthParams($playlist, $viewer, 'view') -> isValid())
		{
			return;
		}

		// Render
		$this -> _helper -> content
		// -> setNoRender()
		-> setEnabled();
	}

}

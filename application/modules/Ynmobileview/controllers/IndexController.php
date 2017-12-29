<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_IndexController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		if (Engine_Api::_() -> user() -> getViewer() -> getIdentity())
		{
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'home'), 'user_general', true);
		}
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function userhomeAction()
	{
		if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity())
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
		}

		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function profileAction()
	{
		// @todo this may not work with some of the content stuff in here, double-check
		$subject = null;
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			$id = $this -> _getParam('id');
			if (null !== $id)
			{
				$subject = Engine_Api::_() -> user() -> getUser($id);
				if ($subject -> getIdentity())
				{
					Engine_Api::_() -> core() -> setSubject($subject);
				}
			}
		}

		$this -> _helper -> requireSubject('user');
		$this -> _helper -> requireAuth() -> setNoForward() -> setAuthParams($subject, Engine_Api::_() -> user() -> getViewer(), 'view');

		$subject = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// check public settings
		$require_check = Engine_Api::_() -> getApi('settings', 'core') -> core_general_profile;
		if (!$require_check && !$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		// Check enabled
		if (!$subject -> enabled && !$viewer -> isAdmin())
		{
			return $this -> _forward('requireauth', 'error', 'core');
		}

		// Check block
		if ($viewer -> isBlockedBy($subject))
		{
			return $this -> _forward('requireauth', 'error', 'core');
		}

		// Increment view count
		if (!$subject -> isSelf($viewer))
		{
			$subject -> view_count++;
			$subject -> save();
		}

		// Check to see if profile styles is allowed
		$style_perm = Engine_Api::_() -> getDbtable('permissions', 'authorization') -> getAllowed('user', $subject -> level_id, 'style');
		if ($style_perm)
		{
			// Get styles
			$table = Engine_Api::_() -> getDbtable('styles', 'core');
			$select = $table -> select() -> where('type = ?', $subject -> getType()) -> where('id = ?', $subject -> getIdentity()) -> limit();

			$row = $table -> fetchRow($select);
			if (null !== $row && !empty($row -> style))
			{
				$this -> view -> headStyle() -> appendStyle($row -> style);
			}
		}

		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function loginAction()
	{
		// Already logged in
		if (Engine_Api::_() -> user() -> getViewer() -> getIdentity())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('You are already signed in.');
			if (null === $this -> _helper -> contextSwitch -> getCurrentContext())
			{
				$this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
			}
			return;
		}
		$this -> _helper -> content -> setEnabled();

		//Facebook Connect
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		if ('none' != $settings -> getSetting('core_facebook_enable', 'none') && $settings -> core_facebook_secret)
		{
			$this -> view -> fblogin = new Engine_Form_Element_Dummy('facebook', array(
				'content' => User_Model_DbTable_Facebook::loginButton(),
				'decorators' => array('ViewHelper'),
			));
			$this -> view -> fbLoginEnabled = true;
		}
		else
		{
			$this -> view -> fbLoginEnabled = false;
			$this -> view -> fblogin = new Engine_Form_Element_Dummy('facebook', array(
				'content' => 'fblogin here',
				'decorators' => array('ViewHelper'),
			));
		}
		//Facebook Connect end

		//Twitter Connect
		// Init twitter login link
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		if ('none' != $settings -> getSetting('core_twitter_enable', 'none') && $settings -> core_twitter_secret)
		{
			$this -> view -> twlogin = new Engine_Form_Element_Dummy('twitter', array(
				'content' => User_Model_DbTable_Twitter::loginButton(),
				'decorators' => array('ViewHelper'),
			));
			$this -> view -> TwLoginEnabled = true;
		}
		else
		{
			$this -> view -> TwLoginEnabled = false;
			$this -> view -> twlogin = new Engine_Form_Element_Dummy('twitter', array(
				'content' => 'twlogin here',
				'decorators' => array('ViewHelper'),
			));
		}

		//Twitter Connect end

		//Janrain Connect
		// Init janrain login link
		if ('none' != $settings -> getSetting('core_janrain_enable', 'none') && $settings -> core_janrain_key)
		{
			$this -> view -> jrlogin = new Engine_Form_Element_Dummy('janrain', array(
				'content' => User_Model_DbTable_Janrain::loginButton('page'),
				'decorators' => array('ViewHelper'),
			));
			$this -> view -> JrLoginEnabled = true;
		}
		else
		{
			$this -> view -> JrLoginEnabled = false;
			$this -> view -> jrlogin = new Engine_Form_Element_Dummy('janrain', array(
				'content' => 'janrain here',
				'decorators' => array('ViewHelper'),
			));
		}
		//Janrain Connect end

		// Return URL
		$this -> view -> return_url = $this -> _getParam('return_url');
		// Not a post
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$form = new User_Form_Login();
		$emailName = $form -> getEmailElementFieldName();
		$posts = $this->getRequest()->getPost();
		$posts[$emailName] = $posts['email'];
		unset($posts['email']);
		// Form not valid
	    if( !$form->isValid($posts) ) {
	      $this->view->status = false;
	      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
	      return;
	    }
		$remember = null;
		extract($form->getValues());
		$user_table = Engine_Api::_() -> getDbtable('users', 'user');
		$user_select = $user_table -> select() -> where('email = ?', $email);
		// If post exists
		$user = $user_table -> fetchRow($user_select);

		// Get ip address
		$db = Engine_Db_Table::getDefaultAdapter();
		$ipObj = new Engine_IP();
		$ipExpr = new Zend_Db_Expr($db -> quoteInto('UNHEX(?)', bin2hex($ipObj -> toBinary())));

		// Check if user exists
		if (empty($user))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('No record of a member with that email was found.');

			// Register login
			Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
				'email' => $email,
				'ip' => $ipExpr,
				'timestamp' => new Zend_Db_Expr('NOW()'),
				'state' => 'no-member',
			));

			return;
		}

		// Check if user is verified and enabled
		if (!$user -> enabled)
		{
			if (!$user -> verified)
			{
				$this -> view -> status = false;

				$resend_url = $this -> _helper -> url -> url(array(
					'action' => 'resend',
					'email' => $email
				), 'user_signup', true);
				$translate = Zend_Registry::get('Zend_Translate');
				$error = $translate -> translate('This account still requires either email verification.');
				$error .= ' ';
				$error .= sprintf($translate -> translate('Click <a href="%s">here</a> to resend the email.'), $resend_url);
				$this -> view -> error = $error;

				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $email,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'disabled',
				));

				return;
			}
			else
			if (!$user -> approved)
			{
				$this -> view -> status = false;

				$translate = Zend_Registry::get('Zend_Translate');
				$error = $translate -> translate('This account still requires admin approval.');
				$this -> view -> error = $error;

				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $email,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'disabled',
				));

				return;
			}
			// Should be handled by hooks or payment
			//return;
		}

		// Handle subscriptions
		if (Engine_Api::_() -> hasModuleBootstrap('payment'))
		{
			// Check for the user's plan
			$subscriptionsTable = Engine_Api::_() -> getDbtable('subscriptions', 'payment');
			if (!$subscriptionsTable -> check($user))
			{
				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $email,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'unpaid',
				));
				// Redirect to subscription page
				$subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
				$subscriptionSession -> unsetAll();
				$subscriptionSession -> user_id = $user -> getIdentity();
				return $this -> _helper -> redirector -> gotoRoute(array(
					'module' => 'payment',
					'controller' => 'subscription',
					'action' => 'index'
				), 'default', true);
			}
		}

		// Run pre login hook
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('onUserLoginBefore', $user);
		foreach ((array) $event->getResponses() as $response)
		{
			if (is_array($response))
			{
				if (!empty($response['error']) && !empty($response['message']))
				{
					$this -> view -> error = $response['message'];
				}
				else
				if (!empty($response['redirect']))
				{
					$this -> _helper -> redirector -> gotoUrl($response['redirect'], array('prependBase' => false));
				}
				else
				{
					continue;
				}

				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $email,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'third-party',
				));

				// Return
				return;
			}
		}

		// Version 3 Import compatibility
		if (empty($user -> password))
		{
			$compat = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.compatibility.password');
			$migration = null;
			try
			{
				$migration = Engine_Db_Table::getDefaultAdapter() -> select() -> from('engine4_user_migration') -> where('user_id = ?', $user -> getIdentity()) -> limit(1) -> query() -> fetch();
			}
			catch( Exception $e )
			{
				$migration = null;
				$compat = null;
			}
			if (!$migration)
			{
				$compat = null;
			}

			if ($compat == 'import-version-3')
			{

				// Version 3 authentication
				$cryptedPassword = self::_version3PasswordCrypt($migration['user_password_method'], $migration['user_code'], $password);
				if ($cryptedPassword === $migration['user_password'])
				{
					// Regenerate the user password using the given password
					$user -> salt = (string) rand(1000000, 9999999);
					$user -> password = $password;
					$user -> save();
					Engine_Api::_() -> user() -> getAuth() -> getStorage() -> write($user -> getIdentity());
					// @todo should we delete the old migration row?
				}
				else
				{
					$this -> view -> status = false;
					$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid credentials');
					return;
				}
				// End Version 3 authentication

			}
			else
			{
				$this -> view -> error = 'There appears to be a problem logging in. Please reset your password with the Forgot Password link.';

				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $email,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'v3-migration',
				));

				return;
			}
		}

		// Normal authentication
		else
		{
			$authResult = Engine_Api::_() -> user() -> authenticate($email, $password);
			$authCode = $authResult -> getCode();
			Engine_Api::_() -> user() -> setViewer();

			if ($authCode != Zend_Auth_Result::SUCCESS)
			{
				$this -> view -> status = false;
				$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid credentials supplied');

				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $email,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'bad-password',
				));

				return;
			}
		}

		// -- Success! --

		// Register login
		$loginTable = Engine_Api::_() -> getDbtable('logins', 'user');
		$loginTable -> insert(array(
			'user_id' => $user -> getIdentity(),
			'email' => $email,
			'ip' => $ipExpr,
			'timestamp' => new Zend_Db_Expr('NOW()'),
			'state' => 'success',
			'active' => true,
		));
		$_SESSION['login_id'] = $login_id = $loginTable -> getAdapter() -> lastInsertId();

		// Remember
		if ($remember)
		{
			$lifetime = 1209600;
			// Two weeks
			Zend_Session::getSaveHandler() -> setLifetime($lifetime, true);
			Zend_Session::rememberMe($lifetime);
		}
		// Increment sign-in count
		Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('user.logins');

		// Test activity @todo remove
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$viewer -> lastlogin_date = date("Y-m-d H:i:s");
			if ('cli' !== PHP_SAPI)
			{
				$viewer -> lastlogin_ip = $ipExpr;
			}
			$viewer -> save();
			Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $viewer, 'login');
		}

		// Assign sid to view for json context
		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Login successful');
		$this -> view -> sid = Zend_Session::getId();
		$this -> view -> sname = Zend_Session::getOptions('name');

		// Run post login hook
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('onUserLoginAfter', $viewer);

		// Do redirection only if normal context
		if (null === $this -> _helper -> contextSwitch -> getCurrentContext())
		{
			// Redirect by form
			$uri = $this -> _getParam('return_url');
			if ($uri)
			{
				if (substr($uri, 0, 3) == '64-')
				{
					$uri = base64_decode(substr($uri, 3));
				}
				return $this -> _redirect($uri, array('prependBase' => false));
			}

			// Redirect by session
			$session = new Zend_Session_Namespace('Redirect');
			if (isset($session -> uri))
			{
				$uri = $session -> uri;
				$opts = $session -> options;
				$session -> unsetAll();
				return $this -> _redirect($uri, $opts);
			}
			else
			if (isset($session -> route))
			{
				$session -> unsetAll();
				return $this -> _helper -> redirector -> gotoRoute($session -> params, $session -> route, $session -> reset);
			}

			// Redirect by hook
			foreach ((array) $event->getResponses() as $response)
			{
				if (is_array($response))
				{
					if (!empty($response['error']) && !empty($response['message']))
					{
						return $this -> view -> error = $response['message'];
					}
					else
					if (!empty($response['redirect']))
					{
						return $this -> _helper -> redirector -> gotoUrl($response['redirect'], array('prependBase' => false));
					}
				}
			}

			// Just redirect to home
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'home'), 'user_general', true);
		}
	}

	// Notification
	public function cancelFriendAction()
	{
		// Get Viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			// Set Norender if user is not logged on
			return false;
		}
		$resource_id = $this -> getRequest() -> getParam('resource_id');
		$user = Engine_Api::_() -> getItem('user', $resource_id);
		$this -> view -> status = false;
		$this -> view -> resource_id = $resource_id;
		if ($resource_id)
		{
			$uid = $viewer -> getIdentity();
			$userTb = Engine_Api::_() -> getDbTable('membership', 'user');
			$db = $userTb -> getAdapter();
			$db -> beginTransaction();
			$select = $userTb -> select() -> where("(user_id = $uid AND resource_id = $resource_id)
                                    OR (user_id = $resource_id AND resource_id = $uid)") -> where("active = 0");
			$rows = $userTb -> fetchAll($select);
			try
			{
				if (count($rows))
				{
					foreach ($rows as $row)
					{
						$row -> delete();
					}
					// Set the requests as handled
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$db -> commit();
					$this -> view -> status = true;
				}
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
		}
	}

	public function confirmFriendAction()
	{
		// Get Viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			// Set Norender if user is not logged on
			return false;
		}
		$resource_id = $this -> getRequest() -> getParam('resource_id');
		$this -> view -> status = false;
		$this -> view -> resource_id = $resource_id;
		$user = Engine_Api::_() -> getItem('user', $resource_id);
		if ($resource_id)
		{
			$uid = $viewer -> getIdentity();
			$userTb = Engine_Api::_() -> getDbTable('membership', 'user');
			$db = $userTb -> getAdapter();
			$db -> beginTransaction();
			$select = $userTb -> select() -> where("(user_id = $uid AND resource_id = $resource_id)
                                    OR (user_id = $resource_id AND resource_id = $uid)") -> where("active = 0");
			$rows = $userTb -> fetchAll($select);
			try
			{
				if (count($rows))
				{
					foreach ($rows as $row)
					{
						$row -> active = 1;
						$row -> user_approved = 1;
						$row -> resource_approved = 1;
						$row -> save();
					}
					// Add activity
					if (!$user -> membership() -> isReciprocal())
					{
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.');
					}
					else
					{
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
					}

					// Add notification
					if (!$user -> membership() -> isReciprocal())
					{
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_follow_accepted');
					}
					else
					{
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_accepted');
					}
					// Set the requests as handled
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$db -> commit();
					$this -> view -> status = true;
				}
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
		}
	}

	/**
	 * Message alert
	 * @todo get message
	 * @access public
	 */
	public function messageAction()
	{
		// Get Viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			// Set Norender if user is not logged on
			return false;
		}
		$notificationsTable = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$db = $notificationsTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Get notification
			$notifications = $notificationsTable -> fetchAll("`user_id` = {$viewer->getIdentity()} AND `type` = 'message_new' AND `mitigated` = 0");
			if ($notifications)
			{
				foreach ($notifications as $notification)
				{
					$notification -> mitigated = 1;
					$notification -> save();
				}
				$db -> commit();
			}
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		// Get Message
		$table = Engine_Api::_() -> getItemTable('messages_conversation');
		$rName = Engine_Api::_() -> getDbtable('recipients', 'messages') -> info('name');
		$cName = $table -> info('name');
		$select = $table -> select() -> from($cName) -> joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null) -> where("`{$rName}`.`user_id` = ?", $viewer -> getIdentity()) -> where("`{$rName}`.`inbox_deleted` = ?", 0) -> order('inbox_read ASC') -> order(new Zend_Db_Expr('inbox_updated DESC')) -> limit(5);
		$this -> view -> messages = $messages = $table -> fetchAll($select);

		$this -> _helper -> layout -> disableLayout();
	}

	/**
	 * Notification alert
	 * @todo get notification
	 * @access public
	 */
	public function notificationAction()
	{
		// Get Viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			// Set Norender if user is not logged on
			return false;
		}
		$notificationsTable = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$db = $notificationsTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Get notification
			$notifications = $notificationsTable -> fetchAll("`user_id` = {$viewer->getIdentity()} AND `type` NOT  IN ('friend_request','message_new') AND `mitigated` = 0");
			if ($notifications)
			{
				foreach ($notifications as $notification)
				{
					$notification -> mitigated = 1;
					$notification -> save();
				}
				$db -> commit();
			}
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		// Get notifications
		$select = Engine_Api::_() -> getDbTable('notifications', 'activity') -> select() -> where("`user_id` = ?", $viewer -> getIdentity()) -> where("`type` NOT IN ('friend_request','message_new')") -> order('read ASC') -> order('notification_id DESC') -> limit(5);
		$this -> view -> updates = Engine_Api::_() -> getDbTable('notifications', 'activity') -> fetchAll($select);
		$this -> _helper -> layout -> disableLayout();
	}

	public function markreadAction()
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$action_id = $request -> getParam('actionid', 0);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$notificationsTable = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$db = $notificationsTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$notification = Engine_Api::_() -> getItem('activity_notification', $action_id);
			$notification -> read = 1;
			$notification -> save();
			// Commit
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	/**
	 * Friend request alert
	 * @todo get friend request
	 * @access public
	 */
	public function friendAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		// Mark read action
		$notificationsTable = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$db = $notificationsTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Get friend request notifications
			$notifications = $notificationsTable -> fetchAll("`user_id` = {$viewer->getIdentity()} AND `type` = 'friend_request' AND `read` = 0");
			if ($notifications)
			{
				foreach ($notifications as $notification)
				{
					$notification -> read = 1;
					$notification -> save();
				}
				$db -> commit();
			}
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		// Get Friend request but not confirm yet
		$userTb = Engine_Api::_() -> getDbTable('membership', 'user');
		$select = $userTb -> select() -> where("user_id = ?", $viewer -> getIdentity()) -> where("active = 0 AND resource_approved = 1 AND user_approved = 0");
		$this -> view -> freqs = $userTb -> fetchAll($select);
		$this -> _helper -> layout -> disableLayout();
	}

	public function friendRequestsAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		$notificationsTable = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$notifi_name = $notificationsTable -> info('name');
		$select = $notificationsTable -> select() -> from($notifi_name) -> where("$notifi_name.`user_id` = {$viewer->getIdentity()} AND `type` = 'friend_request' AND `mitigated` = 0") -> order('notification_id DESC');
		$this -> view -> requests = $requests = Zend_Paginator::factory($select);
		$requests -> setCurrentPageNumber($this -> _getParam('page'));
	}

	public function notificationsAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$select = Engine_Api::_() -> getDbTable('notifications', 'activity') -> select() -> where("`user_id` = ?", $viewer -> getIdentity()) -> where("`type` NOT IN ('friend_request','message_new')") -> order('read ASC') -> order('notification_id DESC');
		$this -> view -> notifications = $notifications = Zend_Paginator::factory($select);
		$notifications -> setCurrentPageNumber($this -> _getParam('page'));

		// Force rendering now
		$this -> _helper -> viewRenderer -> postDispatch();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		$this -> view -> hasunread = false;

		// Now mark them all as read
		$ids = array();
		foreach ($notifications as $notification)
		{
			$ids[] = $notification -> notification_id;
		}
	}

	public function pulldownAction()
	{
		$page = $this -> _getParam('page');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$select = Engine_Api::_() -> getDbTable('notifications', 'activity') -> select() -> where("`user_id` = ?", $viewer -> getIdentity()) -> where("`type`NOT IN ('friend_request','message_new')") -> order('notification_id DESC');
		$this -> view -> notifications = $notifications = Zend_Paginator::factory($select);
		$notifications -> setCurrentPageNumber($page);

		if ($notifications -> getCurrentItemCount() <= 0 || $page > $notifications -> getCurrentPageNumber())
		{
			$this -> _helper -> viewRenderer -> setNoRender(true);
			return;
		}
		// Force rendering now
		$this -> _helper -> viewRenderer -> postDispatch();
		$this -> _helper -> viewRenderer -> setNoRender(true);
	}

	public function editCoverAction()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			$subject = Engine_Api::_() -> user() -> getViewer();
			Engine_Api::_() -> core() -> setSubject($subject);
		}
		$this -> view -> user = $user = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		// Get form
		$this -> view -> form = $form = new User_Form_Edit_Photo();
		
		$form -> current -> setLabel('Current Cover');
		$form -> Filedata -> setLabel('Choose New Cover');
		$form -> done -> setLabel('Save Cover');
		
		if (empty($user -> cover_id))
		{
			$form -> removeElement('remove');
		}
		else 
		{
			$form -> remove -> setAttrib('href', Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          		), 'ynmobi_remove_cover'));
			$form -> remove -> setLabel('remove cover');
		}
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Uploading a new photo
		if ($form -> Filedata -> getValue() !== null)
		{
			$db = $user -> getTable() -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$fileElement = $form -> Filedata;
				$user = Engine_Api::_() -> ynmobileview() -> setUserPhoto($user, $fileElement);
				$iMain = Engine_Api::_() -> getItem('storage_file', $user -> cover_id);
				$db -> commit();
			}
			// If an exception occurred within the image adapter, it's probably an invalid image
			catch( Engine_Image_Adapter_Exception $e )
			{
				$db -> rollBack();
				$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The uploaded file is not supported or is corrupt.'));
			}
			// Otherwise it's probably a problem with the database or the storage system (just throw it)
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
		}
	}

	public function removeCoverAction()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			$subject = Engine_Api::_() -> user() -> getViewer();
			Engine_Api::_() -> core() -> setSubject($subject);
		}
		// Get form
		$this -> view -> form = $form = new User_Form_Edit_RemovePhoto();
		$form -> setTitle('Remove Cover');
		$form -> setDescription('Do you want to remove your profile cover?  Doing so will set your cover back to the default cover.');
		$form -> submit -> setLabel('Remove Cover');
		if (!$this -> getRequest() -> isPost() || !$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$user = Engine_Api::_() -> core() -> getSubject();
		$user -> cover_id = 0;
		$user -> save();

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your cover has been removed.');

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your cover has been removed.'))
		));
	}

}

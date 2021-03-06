<?php
/**
 *
 */

require_once(APPLICATION_PATH . '/application/modules/User/controllers/AuthController.php');

class DisableHelperContent
{
    public function setEnabled()
    {
        return null;
    }
}

class Advmenusystem_LoginController extends User_AuthController
{

    function indexAction()
    {
        $this->_helper->layout->setLayout('default-simple');
        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $this->view->status = false;
            return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,));
        }

        // Make form
        $this->view->form = $form = new User_Form_Login();
        $form->setDescription('');
        $form->setAction($this->view->url(array('return_url' => null)));
        $form->populate(array(
            'return_url' => $this->_getParam('return_url'),
        ));

        $content = Zend_Registry::get('Zend_Translate')->_("<span><a href='%s' target='_blank'>Forgot Password?</a></span>");
        $content= sprintf($content, Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth', 'action' => 'forgot'), 'default', true));
        $form->getElement('forgot')->setContent($content);

        $this->view->socialconnect_enable = Engine_Api::_()->hasModuleBootstrap("social-connect");
        // Facebook login
//    if( User_Model_DbTable_Facebook::authenticate($form) ) {
//      // Facebook login succeeded, redirect to home
//      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
//    }

        // Render
        // $this->_helper->content
        //     //->setNoRender()
        //     ->setEnabled()
        //     ;

        // Not a post
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
            return;
        }

        // Form not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        // Check login creds
        extract($form->getValues()); // $email, $password, $remember
        $user_table = Engine_Api::_()->getDbtable('users', 'user');
        $user_select = $user_table->select()
            ->where('email = ?', $email);          // If post exists
        $user = $user_table->fetchRow($user_select);

        // Get ip address
        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

        // Check if user exists
        if (empty($user)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.');
            $form->addError(Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.'));

            // Register login
            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                'email' => $email,
                'ip' => $ipExpr,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'no-member',
            ));

            return;
        }

        // Check if user is verified and enabled
        if (!$user->enabled) {
            if (!$user->verified) {
                $this->view->status = false;

                $resend_url = $this->_helper->url->url(array('action' => 'resend', 'email' => $email), 'user_signup', true);
                $translate = Zend_Registry::get('Zend_Translate');
                $error = $translate->translate('This account still requires either email verification.');
                $error .= ' ';
                $error .= sprintf($translate->translate('Click <a href="%s">here</a> to resend the email.'), $resend_url);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            } else if (!$user->approved) {
                $this->view->status = false;

                $translate = Zend_Registry::get('Zend_Translate');
                $error = $translate->translate('This account still requires admin approval.');
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
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
        if (Engine_Api::_()->hasModuleBootstrap('payment')) {
            // Check for the user's plan
            $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            if (!$subscriptionsTable->check($user)) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'unpaid',
                ));
                // Redirect to subscription page
                $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
                $subscriptionSession->unsetAll();
                $subscriptionSession->user_id = $user->getIdentity();
                return $this->_helper->redirector->gotoRoute(array('module' => 'payment',
                    'controller' => 'subscription', 'action' => 'index'), 'default', true);
            }
        }

        // Run pre login hook
        $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginBefore', $user);
        foreach ((array)$event->getResponses() as $response) {
            if (is_array($response)) {
                if (!empty($response['error']) && !empty($response['message'])) {
                    $form->addError($response['message']);
                } else if (!empty($response['redirect'])) {
                    $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
                } else {
                    continue;
                }

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
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
        if (empty($user->password)) {
            $compat = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.compatibility.password');
            $migration = null;
            try {
                $migration = Engine_Db_Table::getDefaultAdapter()->select()
                    ->from('engine4_user_migration')
                    ->where('user_id = ?', $user->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetch();
            } catch (Exception $e) {
                $migration = null;
                $compat = null;
            }
            if (!$migration) {
                $compat = null;
            }

            if ($compat == 'import-version-3') {

                // Version 3 authentication
                $cryptedPassword = self::_version3PasswordCrypt($migration['user_password_method'], $migration['user_code'], $password);
                if ($cryptedPassword === $migration['user_password']) {
                    // Regenerate the user password using the given password
                    $user->salt = (string)rand(1000000, 9999999);
                    $user->password = $password;
                    $user->save();
                    Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                    // @todo should we delete the old migration row?
                } else {
                    $this->view->status = false;
                    $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));
                    return;
                }
                // End Version 3 authentication

            } else {
                $form->addError('There appears to be a problem logging in. Please reset your password with the Forgot Password link.');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'v3-migration',
                ));

                return;
            }
        } // Normal authentication
        else {
            $authResult = Engine_Api::_()->user()->authenticate($email, $password);
            $authCode = $authResult->getCode();
            Engine_Api::_()->user()->setViewer();

            if ($authCode != Zend_Auth_Result::SUCCESS) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
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
        $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
        $loginTable->insert(array(
            'user_id' => $user->getIdentity(),
            'email' => $email,
            'ip' => $ipExpr,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'state' => 'success',
            'active' => true,
        ));
        $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();

        // Remember
        if ($remember) {
            $lifetime = 1209600; // Two weeks
            Zend_Session::getSaveHandler()->setLifetime($lifetime, true);
            Zend_Session::rememberMe($lifetime);
        }

        // Increment sign-in count
        Engine_Api::_()->getDbtable('statistics', 'core')
            ->increment('user.logins');

        // Test activity @todo remove
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $viewer->lastlogin_date = date("Y-m-d H:i:s");
            if ('cli' !== PHP_SAPI) {
                $viewer->lastlogin_ip = $ipExpr;
            }
            $viewer->save();
            Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($viewer, $viewer, 'login');
        }

        // Assign sid to view for json context
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Login successful');
        $this->view->sid = Zend_Session::getId();
        $this->view->sname = Zend_Session::getOptions('name');

        // Run post login hook
        $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);

        // Do redirection only if normal context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            // Redirect by form
            $uri = $form->getValue('return_url');
            if ($uri) {
                if (substr($uri, 0, 3) == '64-') {
                    $uri = base64_decode(substr($uri, 3));
                }
                return $this->_redirect($uri, array('prependBase' => false));
            }

            // Redirect by session
            $session = new Zend_Session_Namespace('Redirect');
            if (isset($session->uri)) {
                $uri = $session->uri;
                $opts = $session->options;
                $session->unsetAll();
                return $this->_redirect($uri, $opts);
            } else if (isset($session->route)) {
                $session->unsetAll();
                return $this->_helper->redirector->gotoRoute($session->params, $session->route, $session->reset);
            }

            // Redirect by hook
            foreach ((array)$event->getResponses() as $response) {
                if (is_array($response)) {
                    if (!empty($response['error']) && !empty($response['message'])) {
                        return $form->addError($response['message']);
                    } else if (!empty($response['redirect'])) {
                        return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
                    }
                }
            }

            // Just redirect to home
            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
    }
}
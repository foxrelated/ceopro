<?php

class Advgroup_SignupController extends Core_Controller_Action_Standard {

    public function init() {
        
    }

    public function signupAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer && $viewer->getIdentity()) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $session = new Zend_Session_Namespace('invite_nonmembers');
        $inviteSession = new Zend_Session_Namespace('invite');
        $inviteSession->invite_email = $this->_getParam('email');
        $inviteSession->invite_code = $this->_getParam('code');
        $session->verified = 1;

        $session->invite_code = $this->_getParam('code');
        $session->invite_email = $this->_getParam('email');
        $session->group_id = $this->_getParam('group_id');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        if ($settings->getSetting('user.signup.inviteonly') > 0) {
            if (empty($session->invite_code)) {
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            }

            $inviteTable = Engine_Api::_()->getDbtable('invites', 'advgroup');
            $inviteSelect = $inviteTable->select()
                    ->where('code = ?', $session->invite_code);

            if ($settings->getSetting('user.signup.checkemail')) {
                if (empty($session->invite_email)) {
                    return $this->_helper->redirector->gotoRoute(array(), 'default', true);
                }
                $inviteSelect
                        ->where('recipient = ?', $session->invite_email);
            }

            $inviteRow = $inviteTable->fetchRow($inviteSelect);

            // No invite or already signed up
            if (!$inviteRow || $inviteRow->new_user_id) {
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            }
        }

        return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
    }

}
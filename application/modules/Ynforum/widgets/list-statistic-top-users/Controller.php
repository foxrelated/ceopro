<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */

class Ynforum_Widget_ListStatisticTopUsersController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $userLength = $settings->getSetting('forum_top_thanked_user_length', 10);
        $signaturesTable = Engine_Api::_()->getDbtable('signatures', 'ynforum');
        
        $signaturesThankedSelect = $signaturesTable->select()->order('thanked_count DESC')->limit($userLength);
        $this->view->thankedSignatures = $signaturesTable->fetchAll($signaturesThankedSelect);
        
        $signaturesPostedSelect = $signaturesTable->select()->order('approved_post_count DESC')->limit($userLength);
        $this->view->postedSignatures = $signaturesTable->fetchAll($signaturesPostedSelect);
		
		if(count($this->view->thankedSignatures) == 0 && count($this->view->postedSignatures) == 0)
		{
			return $this->setNoRender();
		}
    }
}
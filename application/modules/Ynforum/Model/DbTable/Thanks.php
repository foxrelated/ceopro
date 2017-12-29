<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Categories.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Model_DbTable_Thanks extends Engine_Db_Table {

    protected $_rowClass = 'Ynforum_Model_Thank';
    protected $_name = 'forum_thanks';
    
    public function giveThank($subject_id, $object_id, $post_id) {
        $newThank = $this->createRow();
        $newThank->user_id = $subject_id;
        $newThank->post_id = $post_id;
        $newThank->save();
        
        $signatureTable = Engine_Api::_()->getItemTable('ynforum_signature');
        $selectThank = $signatureTable->select()->where('user_id = ?', $subject_id)->limit(1);
        $rowThank = $signatureTable->fetchRow($selectThank);
        if ($rowThank == null) {
            $rowThank = $signatureTable->createRow();
            $rowThank->user_id = $subject_id;
            $rowThank->thanks_count = 1;
            $rowThank->thanked_count = 0;
        } else {
            $rowThank->thanks_count = new Zend_Db_Expr('thanks_count + 1');
        }
        $rowThank->save();
        
        $selectThanked = $signatureTable->select()->where('user_id = ?', $object_id)->limit(1);
        $rowThanked = $signatureTable->fetchRow($selectThanked);
        if ($rowThanked == null) {
            $rowThanked = $signatureTable->createRow();
            $rowThanked->user_id = $object_id;
            $rowThanked->thanked_count = 1;
            $rowThanked->thanks_count = 0;
        } else {
            $rowThanked->thanked_count = new Zend_Db_Expr('thanked_count + 1');
        }
        $rowThanked->save();       
    }
}
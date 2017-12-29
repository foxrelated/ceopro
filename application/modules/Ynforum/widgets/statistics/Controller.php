<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     MinhNC
 */

class Ynforum_Widget_StatisticsController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
    	$forumTable = Engine_Api::_()->getItemTable('ynforum_forum');
        $select = new Zend_Db_Select($forumTable->getAdapter());
		$select->from(Engine_Api::_()->getItemTable('ynforum_topic')->info('name'), 'COUNT(*) as count');
		$this->view->topicCount = $select->query()->fetchColumn(0);
		$select->reset();
		$select->from(Engine_Api::_()->getItemTable('ynforum_post')->info('name'), 'COUNT(*) as count');
		$this->view->postCount = $select->query()->fetchColumn(0);
    }
}
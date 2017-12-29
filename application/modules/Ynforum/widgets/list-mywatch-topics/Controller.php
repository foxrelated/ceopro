<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Adv Forum
 * @copyright  YouNetCo Company
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9245 2011-09-07 22:41:17Z shaun $
 * @author     LuanND
 */

/**
 * @category   Application_Extensions
 * @package    Adv Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Widget_ListMywatchTopicsController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> getIdentity())
		{
			return $this->setNoRender();
		}
		
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $topicLength = $settings->getSetting('forum_newest_topic_length', 10);
        $topicsTable = Engine_Api::_()->getDbtable('topics', 'ynforum');
		$Name = $topicsTable->info('name');
		
		$topicsSelect = $topicsTable->select() -> from($Name, "$Name.*") -> setIntegrityCheck(false);
		
		$topicsSelect -> joinRight("engine4_forum_topicwatches", "engine4_forum_topicwatches.topic_id = $Name.topic_id" , "engine4_forum_topicwatches.watch");
        $topicsSelect 	-> where("$Name.approved = ?", 1) 
        				-> where("engine4_forum_topicwatches.user_id = ?", $viewer -> getIdentity()) 
						-> where("engine4_forum_topicwatches.watch = 1")
        				->order("$Name.modified_date DESC")->limit($topicLength);
        $this->view->topics = $topicsTable->fetchAll($topicsSelect);
		
		if(count($this->view->topics)==0)
		{
			return $this->setNoRender();
		}
    }
}
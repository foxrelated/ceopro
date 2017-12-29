<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9245 2011-09-07 22:41:17Z shaun $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Widget_ListMostViewedTopicsController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $topicLength = $settings->getSetting('forum_most_viewed_topic_length', 10);
        $topicsTable = Engine_Api::_()->getDbtable('topics', 'ynforum');
        $topicsSelect = $topicsTable->select()->where('approved = ?', 1)->order('view_count DESC')->limit($topicLength);
        $this->view->topics = $topicsTable->fetchAll($topicsSelect);
		
		if(count($this->view->topics)==0)
		{
			return $this->setNoRender();
		}
    }
}
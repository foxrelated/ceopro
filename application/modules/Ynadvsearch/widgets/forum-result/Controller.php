<?php

class Ynadvsearch_Widget_ForumResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        if (!Engine_Api::_() -> hasItemType('forum_topic')) {
            $this->setNoRender();
        }
        $ynforum_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynforum');
        $this->view->route = ($ynforum_enable) ? 'ynforum_general' : 'forum_general';
        $this->view->ynforum_enable = $ynforum_enable;
        if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('forum', null, 'view')->checkRequire()) $this->setNoRender();
        $values = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        unset($values['controller']);
        unset($values['action']);
        unset($values['name']);
        unset($values['module']);
        $this->view->formValues = $values;
        $topic_table = Engine_Api::_()->getItemTable('forum_topic');
        $topic_tbl_name = $topic_table->info('name');
        $forum_table = Engine_Api::_()->getItemTable('forum_forum');
        $forum_tbl_name = $forum_table->info('name');
        $select = $topic_table -> select();
        $select -> setIntegrityCheck(false);
        $select -> from("$topic_tbl_name as topic", "topic.*");
        $select -> joinLeft("$forum_tbl_name","$forum_tbl_name.forum_id = topic.forum_id", "");
        if (isset($values['search']) && $values['search'] != '') {
            $select->where('topic.title LIKE  ?', '%'.$values['search'].'%');
        }
        $select->order("$forum_tbl_name.title ASC");
        $this -> view -> topics = $topics = $topic_table->fetchAll($select);
        $this -> view -> paginator = $paginator = Zend_Paginator::factory($topics);
        $page = (isset($values['page'])) ? $values['page'] : 1;
        $paginator -> setCurrentPageNumber($page);
        $settings = Engine_Api::_() -> getApi('settings', 'core');
        $paginator -> setItemCountPerPage($settings->getSetting('forum_topic_pagelength'));
        $this -> view -> numberOfPostOfHotTopic = $settings -> getSetting('forum_minimum_post_of_hot_topic', 25);
        
        //count result
        $ori_select = $topic_table -> select();
        $ori_topics = $topic_table->fetchAll($ori_select);
        $total_content = 0;
        foreach ($ori_topics as $topic) {
            if ($topic->forum_id != $forum_id) {
                $forum_id = $topic->forum_id;
                $forum = Engine_Api::_()->getItem('forum_forum', $forum_id);
                $forum_auth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($forum, null, 'view') -> checkRequire();
            }
            if ($forum_auth) $total_content++;
        }
        $this->view->total_content = $total_content;
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('forum_topic');
        if ($contentType) $this->view->label_content = $contentType->title;
    }
}

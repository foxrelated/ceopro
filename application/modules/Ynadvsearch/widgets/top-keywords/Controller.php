<?php

class Ynadvsearch_Widget_TopKeywordsController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $num_of_keywords = $this->_getParam('num_of_keywords', 5);
        $table = Engine_Api::_()->getDbTable('keywords', 'ynadvsearch');
        $select = $table->select()->order('count DESC')->order('modified_date DESC')->limit($num_of_keywords);
        $keywords = $table->fetchAll($select);
        if (!count($keywords)) {
            $this->setNoRender();
        }
        else {
            $this->view->keywords = $keywords;
        }
    }

}

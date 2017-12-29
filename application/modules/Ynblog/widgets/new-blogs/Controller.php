<?php

class Ynblog_Widget_NewBlogsController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;

    public function indexAction()
    {
        //Get number of blogs display
        if ($this->_getParam('max') != '' && $this->_getParam('max') >= 0) {
            $limitNblog = $this->_getParam('max');
        } else {
            $limitNblog = 6;
        }
        $view_mode = $this->_getParam('view_mode', 'list');
        $mode_enabled = array();
        if ($this->_getParam('mode_grid', 0)) {
            $mode_enabled[] = 'grid';
        }
        if ($this->_getParam('mode_list', 0)) {
            $mode_enabled[] = 'list';
        }
        if (!in_array($view_mode, $mode_enabled) && $mode_enabled) {
            $view_mode = $mode_enabled[0];
        }
        $this->view->mode_enabled = $mode_enabled;
        $this->view->view_mode = $view_mode;
        //List params for getting new blogs
        $params = array();
        $params['visible'] = 1;
        $params['draft'] = 0;
        $params['is_approved'] = 1;
        $params['orderby'] = 'creation_date';
        $params['limit'] = $limitNblog;
        //Select blogs
        $table = Engine_Api::_()->getItemTable('blog');
        $select = Engine_Api::_()->ynblog()->getBlogsSelect($params);
        $this->view->blogs = $blogs = $table->fetchAll($select);
        $this->view->limit = $limitNblog;
        $this->_childCount = count($blogs);
    }

    public function getChildCount()
    {
        return $this->_childCount;
    }
}

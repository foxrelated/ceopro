<?php

class Ynblog_Widget_MostViewedBlogsController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;

    public function indexAction()
    {
        //Get number of blogs display
        if ($this->_getParam('max') != '' && $this->_getParam('max') >= 0) {
            $limitMVblog = $this->_getParam('max');
        } else {
            $limitMVblog = 6;
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

        //Select glogs
        $table = Engine_Api::_()->getItemTable('blog');
        $name = $table->info('name');
        $select = $table->select()->from($name)
            ->order('view_count DESC')
            ->where("search = 1")
            ->where("draft = 0")
            ->where('is_approved = 1')
            ->where("view_count > 0")
            ->limit($limitMVblog);

        $this->view->blogs = $blogs = $table->fetchAll($select);
        $this->view->limit = $limitMVblog;
        $this->_childCount = count($blogs);
    }

    public function getChildCount()
    {
        return $this->_childCount;
    }
}

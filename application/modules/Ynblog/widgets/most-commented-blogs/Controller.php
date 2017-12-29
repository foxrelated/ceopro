<?php

class Ynblog_Widget_MostCommentedBlogsController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;

    public function indexAction()
    {
        //Get number of blogs display
        if ($this->_getParam('max') != '' && $this->_getParam('max') >= 0) {
            $limitMCblog = $this->_getParam('max');
        } else {
            $limitMCblog = 6;
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
        //Get blogs
        $b_table = Engine_Api::_()->getItemtable('blog');
        $bName = $b_table->info('name');
        $select = $b_table->select()->from($bName)
            ->order('comment_count DESC')
            ->where("search = ?", "1")
            ->where("draft = ?", "0")
            ->where("is_approved = ?", "1")
            ->where("comment_count > ?", "0")
            ->limit($limitMCblog);

        $this->view->blogs = $blogs = $b_table->fetchAll($select);
        $this->view->limit = $limitMCblog;
        $this->_childCount = count($blogs);
    }

    public function getChildCount()
    {
        return $this->_childCount;
    }
}

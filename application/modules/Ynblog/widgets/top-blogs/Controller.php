<?php

class Ynblog_Widget_TopBlogsController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;

    public function indexAction()
    {
        //Get number of blogs display
        if ($this->_getParam('max') != '' && $this->_getParam('max') >= 0) {
            $limit = $this->_getParam('max');
        } else {
            $limit = 6;
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
        //Select blogs
        $btable = Engine_Api::_()->getItemTable('blog');
        $ltable = Engine_Api::_()->getDbtable('likes', 'core');
        $bName = $btable->info('name');
        $lName = $ltable->info('name');

        $select = $btable->select()
            ->from($bName)
            ->setIntegrityCheck(FALSE)
            ->joinLeft($lName, "resource_id = blog_id", "COUNT(resource_id) as total_like")
            ->where("resource_type  LIKE 'blog'")
            ->group("resource_id")
            ->where("search = 1")
            ->where("draft = 0")
            ->where("is_approved = 1")
            ->order("Count(resource_id) DESC")
            ->limit($limit);
        $this->view->blogs = $blogs = $btable->fetchAll($select);
        $this->view->limit = $limit;
        $this->_childCount = count($blogs);
    }

    public function getChildCount()
    {
        return $this->_childCount;
    }
}
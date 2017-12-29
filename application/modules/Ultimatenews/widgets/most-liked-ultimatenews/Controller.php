<?php

class Ultimatenews_Widget_MostLikedUltimatenewsController extends Ultimatenews_Content_Widget_Base
{

    public function indexAction()
    {
        $table = Engine_Api::_()->getDbtable('Contents', 'ultimatenews');
        $limit = $this->_getParam('max');

        if (!isset($limit) || $limit <= 0)
            $limit = 5;
        
        $selectTop = $table->select('engine4_ultimatenews_contents')->setIntegrityCheck(false)
                ->joinLeft("engine4_ultimatenews_categories", "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id", array('logo' => 'engine4_ultimatenews_categories.category_logo', 'logo_icon' => 'engine4_ultimatenews_categories.logo', 'display_logo' => 'engine4_ultimatenews_categories.display_logo', 'mini_logo' => 'engine4_ultimatenews_categories.mini_logo'))
                ->joinLeft("engine4_core_likes", "engine4_core_likes.resource_id = engine4_ultimatenews_contents.content_id", array('count_like' => 'Count(resource_id)'))
                ->where('engine4_core_likes.resource_type = ?', 'ultimatenews_content')
                ->where('engine4_ultimatenews_categories.is_active= ? ', 1)
                ->where('engine4_ultimatenews_categories.approved= ? ', 1)
                ->group("resource_id")
                ->order("Count(resource_id) DESC")
                ->limit($limit);

        $likedultimatenews = $table->fetchAll($selectTop);

        if (count($likedultimatenews) <= 0)
        {
            return $this->setNoRender();
        }
        $this->view->likedultimatenews = $this->_prepareContent($likedultimatenews);
    }

}
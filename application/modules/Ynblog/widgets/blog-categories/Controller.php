<?php
class Ynblog_Widget_BlogCategoriesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
      $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') .
          'application/modules/Ynblog/externals/scripts/collapsible.js');

      $params = array();
      if(Engine_Api::_()->core()->hasSubject('user')){
        $user =  Engine_Api::_()->core()->getSubject('user');
        $params['mode'] = '1';
        $params['user_id']  = $user->getIdentity();
        $categories = Engine_Api::_()->getItemTable('blog_category')->getUserCategories($user->getIdentity());
      }
      else if(Engine_Api::_()->core()->hasSubject('blog')){
        $blog =  Engine_Api::_()->core()->getSubject('blog');
        $user = $blog->getOwner();
        $params['mode'] = '1';
        $params['user_id']  = $user->getIdentity();
        $categories = Engine_Api::_()->getItemTable('blog_category')->getUserCategories($user->getIdentity());
      }
      else{
        $params['mode'] = '0';
        $categories = Engine_Api::_()->getItemtable('blog_category')->getAllCategories();
      }

      $this->view->params = $params;
      $this->view->categories = $categories;
  }
}

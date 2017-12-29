<?php
class Advgroup_Widget_GroupsCategorySearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction(){
    $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') .
                'application/modules/Advgroup/externals/scripts/collapsible.js');
    
    $this->view->categories = $parent_categories = Engine_Api::_()->getDbTable('categories','advgroup')->getParentCategories();
  }
}
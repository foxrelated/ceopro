<?php
class Ynadvsearch_Widget_YnBlogSearchController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
    $ynblog_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynblog');
    if(!$ynblog_enable) {
        return $this -> setNoRender();
    }
    //Data preload
    $viewer = Engine_Api::_()->user()->getViewer();
    $params = array();
    
    //Get search form
    $this->view->form = $form = new Ynadvsearch_Form_YnBlogSearch();

    //Get search action type
    if(Engine_Api::_()->core()->hasSubject('user')){
       $user = Engine_Api::_()->core()->getSubject('user');
      $form->removeElement('show');
      $form->setAction($this->view->url(array(),'default') . "blogs/".$user->getIdentity());
    }
    else if( Engine_Api::_()->core()->hasSubject('blog') ) {
      $blog = Engine_Api::_()->core()->getSubject('blog');
      $user = $blog->getOwner();
      $form->setAction($this->view->url(array(),'default')."blogs/".$user->getIdentity());
      $form->removeElement('show');
     }
     else{
        $form->setAction($this->view->url(array(),'default'). "blogs/listing");
      }
      
    $form->removeElement('mode');
    if( !$viewer->getIdentity() )
    {
      $form->removeElement('show');
    }

    //Get category array
    $cat_arrays = Engine_Api::_()->getItemTable('blog_category')->getCategoriesAssoc();
    $form->category->addMultiOptions($cat_arrays);

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $params = $request->getParams();

    $form->populate($params);
 
  }
}
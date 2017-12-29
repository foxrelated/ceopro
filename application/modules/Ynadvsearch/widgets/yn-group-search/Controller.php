<?php
class Ynadvsearch_Widget_YnGroupSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction(){
    // Get quick navigation.
    $advgroup_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('advgroup');
    if(!$advgroup_enable) {
        return $this -> setNoRender();
    }
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advgroup_quick');
    
	$viewer = Engine_Api::_()->user()->getViewer();
	
    // Create search form.
    $search_form = $this->view->form = new Ynadvsearch_Form_YnGroupSearch();
	
	if( !$viewer || !$viewer->getIdentity() ) {
      $search_form ->removeElement('view_group');
    }
	
    $request = Zend_Controller_Front::getInstance() -> getRequest();
    $params = $request->getParams();
    $search_form->populate($params);
    }
}
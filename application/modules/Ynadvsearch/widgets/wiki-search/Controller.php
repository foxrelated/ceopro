<?php
class Ynadvsearch_Widget_WikiSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
       $this->view->form = $form = new Ynadvsearch_Form_WikiSearch(); 

       $request = Zend_Controller_Front::getInstance() -> getRequest();
       $params = $request->getParams();
       $form->populate($params); 
  }
}

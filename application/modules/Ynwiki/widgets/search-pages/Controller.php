<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    wiki
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: menu wikis
 * @author     Minh Nguyen
 */
class Ynwiki_Widget_SearchPagesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
       $this->view->form = $form = new Ynwiki_Form_Search(); 
       $form->setAction($this->view->baseUrl() . "/wiki/listing");

       $request = Zend_Controller_Front::getInstance() -> getRequest();
       $params = $request->getParams();
       $form->populate($params); 
  }
}

<?php

class Advgroup_Widget_GroupsSearchController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        // Get quick navigation.
        $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('advgroup_quick');
        $viewer = Engine_Api::_()->user()->getViewer();
        $location = $request -> getParam('location', '');
        $search_form = $this->view->form = new Advgroup_Form_Search(array('location' => strip_tags($location)));
        $search_form->setAction($this->view->url(array('action' => 'listing'), 'group_general'));
        if ($request->getActionName() == 'manage')
            $search_form->setAction($this->view->url(array('action' => 'manage'), 'group_general'));

        if (!$viewer || !$viewer->getIdentity()) {
            $search_form->removeElement('view');
        }
        $params = $request->getParams();
        if(!$search_form->isValid($params) ) {
            return;
        }
    }
}
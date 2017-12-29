<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
class Ynadvsearch_Widget_FilesharingSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Data preload
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$params = array ();

		// Get search form
		$this->view->form = $form = new Ynadvsearch_Form_FilesharingSearch ();

		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$params = $request->getParams ();
		$form->populate ( $params );
	}
}
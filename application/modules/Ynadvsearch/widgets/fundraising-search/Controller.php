<?php
class Ynadvsearch_Widget_FundraisingSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Data preload
		if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynfundraising')) {
            $this->setNoRender();
        }
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$params = array ();
        

		// Get search form
		$this->view->form = $form = new Ynadvsearch_Form_FundraisingSearch ();

		// Get search action type
		if (Engine_Api::_ ()->core ()->hasSubject ( 'user' )) {
			$form->removeElement ( 'status' );
			$form->removeElement ( 'type' );
		}
		else {
			//$form->setAction($this->view->url(array(),'default')."fundraising/list");
			$form->removeElement ( 'show' );
		}

		$request = Zend_Controller_Front::getInstance()->getRequest();
		$module = $request->getParam('module');
		$controller = $request->getParam('controller');
		$action = $request->getParam('action');
		$forwardListing = true;
		if ($module == 'ynfundraising') {
			if ( ($controller == 'index' && $action == 'past-campaigns')
					|| ($controller == 'campaign' && $action == 'index')
				) {
				$forwardListing = false;
				if ($controller == 'index' && $action == 'past-campaigns') {
					unset($form->status->options['ongoing']);
				}
			}
		}

		$request = Zend_Controller_Front::getInstance ()->getRequest ();

		$params = $request->getParams ();

		$form->populate ( $params );
	}
}
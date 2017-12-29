<?php

class Ynadvsearch_Widget_YnFundraisingResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
    	$ynfundraising_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynfundraising');
		if(!$ynfundraising_enable)
		{
			return $this -> setNoRender();
		}
		
		$values = Zend_Controller_Front::getInstance()->getRequest()->getParams();;
		$this->view->formValues = array_filter ( $values );
	
		$values ['published'] = 1;
		
		 //count result
        $this->view->total_content = Engine_Api::_ ()->ynfundraising ()->getCampaignPaginator ( array('published' => 1) )->getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynfundraising_campaign');
        if ($contentType) $this->view->label_content = $contentType->title;
		
		
		// Get campaign paginator
		$items_count = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.page', 10 );
		$paginator = Engine_Api::_ ()->ynfundraising ()->getCampaignPaginator ( $values );
		$paginator->setItemCountPerPage ( $items_count );
		$this->view->paginator = $paginator;
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Ynfundraising/views/helpers','Ynfundraising_View_Helper');
		
	}
}

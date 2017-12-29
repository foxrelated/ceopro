<?php

class Ynadvsearch_Widget_AlbumResultController extends Engine_Content_Widget_Abstract {

  public function indexAction()
  {
 	$album_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('album');
	if(!$album_enable)
	{
		return $this -> setNoRender();
	}
	
    $settings = Engine_Api::_()->getApi('settings', 'core');
	$values = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    // Get params
    $sort = 'recent';
	if(!empty($values['sort']))
	{
		$sort = $values['sort'];
	}
    switch($sort) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }

    // Prepare data
    $table = Engine_Api::_()->getItemTable('album');
    if( !in_array($order, $table->info('cols')) ) {
      $order = 'modified_date';
    }
    
    $select = $table->select()
      ->where("search = 1")
      ->order($order . ' DESC');

    $user_id = $values['user'];
    if ($user_id) $select->where("owner_id = ?", $user_id);
    if (!empty($values['category_id'])) $select->where("category_id = ?", $values['category_id']);

    if (!empty($values['search'])) {
      $select->where('title LIKE ? OR description LIKE ?', '%'.$values['search'].'%');
    }
    
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    
	 //count result
    $this->view->total_content = Zend_Paginator::factory($table->select()->where("search = 1")->order($order . ' DESC'))->getTotalItemCount();
    $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
    $contentType = $table->getContentType('album');
    if ($contentType) $this->view->label_content = $contentType->title;
	
    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($settings->getSetting('album_page', 6));
	$page = 1;
	if(!empty($values['page']))
	{
		$page = $values['page'];
	}
    $paginator->setCurrentPageNumber( $page );
    
    $searchForm = new Album_Form_Search();
    $searchForm->getElement('sort')->setValue($sort);
    $searchForm->getElement('search')->setValue($values['search']);
    $category_id = $searchForm->getElement('category_id');
    if ($category_id && !empty($values['category_id'])) {
      $category_id->setValue($values['category_id']);
    }
    $this->view->searchParams = $searchForm->getValues();

  }
}

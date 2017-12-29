<?php 
/*
 * Controller for Group Directory widget
 */
class Advgroup_Widget_GroupsDirectoryController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      // clear title of widget
      if ($request->isPost()) {
      	$this->getElement()->setTitle('');
      }

      $table = Engine_Api::_()->getItemTable('group');
      $tableName = $table -> info('name');
      if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
          $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('advgroup', 'groups');
      }
      else
      {
          $select = $table->select()->from("$tableName", array("$tableName.*"));
      }
      $select
          ->where('search = ?', 1)
          ->where('is_subgroup = ?', 0)
          ->order('title ASC');

      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		
      // Set item count per page and current page number
      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      if( count($paginator) <= 0 ) {
      	return $this->setNoRender();
      }
	}
}
?>
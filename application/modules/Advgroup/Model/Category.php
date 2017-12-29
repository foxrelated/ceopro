<?php
class Advgroup_Model_Category extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  public function getHref($params = array()){
      $params = array_merge(array(
            'route' => 'group_general',
            'action'=> 'listing',
            'reset' => true,
            'category_id' => $this->category_id,
            ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, $route, $reset);
  }
  
  public function getTable()
  {
    if( null === $this->_table ) {
      $this->_table = Engine_Api::_()->getDbtable('categories', 'advgroup');
    }

    return $this->_table;
  }

  public function getUsedCount()
  {
    $eventTable = Engine_Api::_()->getItemTable('group');
    return $eventTable->select()
        ->from($eventTable, new Zend_Db_Expr('COUNT(group_id)'))
        ->where('category_id = ?', $this->category_id)
        ->query()
        ->fetchColumn();
  }

  public function getTitle() {
    return strip_tags(Zend_Registry::get('Zend_Translate')->_($this->title));
  }
  public function isOwner($owner)
  {
    return false;
  }

  public function getOwner()
  {
    return $this;
  }

  public function getSubCategories($parent_id = 0){
    $table = Engine_Api::_()->getDbTable('categories','advgroup');
    $select = $table->select()->where('parent_id = ?',$this->category_id) ->order('title ASC');
    return $table->fetchAll($select);
  }

  public function getSubCategoriesAssoc($parent_id = 0){
    $table = Engine_Api::_()->getDbTable('categories','advgroup');
    $select = $table->select()->where('parent_id = ?',$parent_id) ->order('title ASC')->query();
    $data = array();
    foreach( $select->fetchAll() as $category ) {
      $data[$category['category_id']] = $category['title'];
    }
    return $data;
  }
}

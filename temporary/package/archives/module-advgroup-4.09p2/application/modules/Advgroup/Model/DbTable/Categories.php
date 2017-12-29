<?php
class Advgroup_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_name = 'group_categories';
  protected $_rowClass = 'Advgroup_Model_Category';

  public function getCategories(){
    $select = $this->select()->order("title ASC");
    return $this->fetchAll($select);
  }
  public function getCategoriesAssoc()
  {
    $stmt = $this->select()
        ->from($this, array('category_id', 'title'))
        ->order('title ASC')
        ->query();
    
    $data = array();
    $data[0] ="";
    foreach( $stmt->fetchAll() as $category ) {
      $data[$category['category_id']] = $category['title'];
    }
    
    return $data;
  }

  public function getAllCategoriesAssoc(){
    $parent_categories = $this->getParentCategories();
    $data = array();
    $data[0] ="";
    foreach($parent_categories as $category){
        $data[$category['category_id']] = Zend_Registry::get('Zend_Translate')->_($category['title']);
        $sub_categories = $category->getSubCategories();
        if(count($sub_categories)>0){
          foreach($sub_categories as $sub_category){
            $data[$sub_category['category_id']] = "---".Zend_Registry::get('Zend_Translate')->_($sub_category['title']);
          }
        }
    }
    return $data;
  }

  public function getArraySearch($category_id = 0){
    $category = Engine_Api::_()->getItem('advgroup_category',$category_id);
    $cat_array = array();
    $cat_array[] = $category->category_id;
    $sub_cats = $category->getSubCategories();
    if(count($sub_cats)>0){
      foreach($sub_cats as $sub_cat){
        $cat_array[] = $sub_cat->category_id;
      }
    }
    return $cat_array;
  }

  public function getParentCategories(){
    $select = $this->select()->where('parent_id = 0')->order('title ASC');
    return $this->fetchAll($select);
  }
  
  public function getParentCategoriesAssoc(){
    $select = $this->select()->where('parent_id = 0')->order('title ASC')->query();
    $data = array();
    foreach( $select->fetchAll() as $category ) {
      $data[$category['category_id']] = $category['title'];
    }
    return $data;
  }

}
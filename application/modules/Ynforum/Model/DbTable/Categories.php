<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    YnForum
 * @author     DangTH
 */
class Ynforum_Model_DbTable_Categories extends Engine_Db_Table {
    protected $_rowClass = 'Ynforum_Model_Category';
    protected $_name = 'forum_categories';

    public function getCategoriesOrderByLevel() {        
        $categories = $this->fetchAll($this->select()->order(array('level ASC', 'order DESC')));
        $orderCategories = array();
        
        foreach($categories as $category) {
            if (!$category->parent_category_id) {
                array_splice($orderCategories, 0, 0, array($category));
            } else {
                $index = 0;
                foreach($orderCategories as $index => $orderCategory) {                
                    if ($category->parent_category_id == $orderCategory->getIdentity()) {
                        break;
                    }                
                }
                array_splice($orderCategories, $index + 1, 0, array($category));
            }
        }
        return $orderCategories;
    }   
    
    public function getCategories($catIds) {
        $select = $this->select()->where('category_id in (?)', array($video->category_id, $video->subcategory_id));
        $categories = $this->fetchAll($select);
        $arrCats = array();
        foreach($categories as $category) {
            $arrCats[$category->getIdentity()] = $category;
        }
        return $arrCats;
    }
}
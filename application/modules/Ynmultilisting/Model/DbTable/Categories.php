<?php
class Ynmultilisting_Model_DbTable_Categories extends Ynmultilisting_Model_DbTable_Nodes {
	protected $_rowClass = 'Ynmultilisting_Model_Category';
	
	public function getCategoryByOptionId($option_id)
	{
		$select = $this->select();
		$select -> where('option_id = ?', $option_id);
		$select -> limit(1);
		$item  = $this->fetchRow($select);
		return $item;
	}
	
	public function getCategoryArray(){
		$select = $this -> select();
		$select -> where('parent_id IS NOT NULL')
				-> order('title ASC');
		$categories = $this -> fetchAll($select);
		$arrValue = array();
		foreach($categories as $category) {
			$arrValue[$category -> getIdentity()] = $category -> getTitle();
		}
		return $arrValue;
	}
	
	public function getFirstCategory()
	{
		$select = $this->select();
		$select -> order('category_id ASC');
		$select -> limit(2);
		$select -> where('category_id <> 1');
		$item  = $this->fetchRow($select);
		return $item;
	}
	
	public function deleteNode(ynmultilisting_Model_Node $node, $node_id = NULL) {
		parent::deleteNode($node);
	}

    public function getCategories() {
        $table = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting');
        $tree = array();
        $node = $table -> getNode(1);
        $this->appendChildToTree($node, $tree);
        return $tree;
    }

    public function appendChildToTree($node, &$tree, $topCategory = false, $moreCategory = false) {
        if ($topCategory) {
            if ($node->top_category) array_push($tree, $node);   
        }
        else if ($moreCategory) {
            if ($node->more_category) array_push($tree, $node);    
        }
        else array_push($tree, $node);
        $children = $node->getChilren();
        foreach ($children as $child_node) {
            $this->appendChildToTree($child_node, $tree, $topCategory, $moreCategory);
        }
    }
	
	public function getAllCategories()
	{
		$select = $this -> select() -> order('title') -> where('category_id <> 1');
		return $this -> fetchAll($select);
	}
    
    public function getListingTypeCategories($listingtype_id, $top = false, $more = false) {
        $topCategory = $this->getListingTypeTopCategory($listingtype_id);
        $tree = array();
        $node = $this -> getNode($topCategory->getIdentity());
        $this->appendChildToTree($node, $tree, $top, $more);
        return $tree;
    }
    
    public function getListingTypeTopCategory($listingtype_id) {
        $select = $this->select()->where('listingtype_id = ?', $listingtype_id)->where('level = ?', 0);
        $category = $this->fetchRow($select);
        if (!$category) {
            $category = $this->createRow();
            $category->listingtype_id = $listingtype_id;
            $category->level = 0;
            $category->title = 'Top Category';
            $category->save();
        }
        return $category;
    }
    
    public function updateListingTypeMenu($listingtype_id, $top_categories = array(), $more_categories = array()) {
        $where = array(
            $this->getAdapter()->quoteInto('listingtype_id = ?', $listingtype_id),
            $this->getAdapter()->quoteInto('level <> ?', 0)
        );
        $data = array(
            'top_category' => 0,
            'more_category' => 0
        );
        $this->update($data, $where);
        if ($top_categories) {
            $top_where = $where;
            $top_where[] = $this->getAdapter()->quoteInto('category_id IN (?)', $top_categories);
            $this->update(array('top_category' => 1), $top_where);
        }
        if ($more_categories) {
            $more_where = $where;
            $more_where[] = $this->getAdapter()->quoteInto('category_id IN (?)', $more_categories);
            $this->update(array('more_category' => 1), $more_where);
        }
    }
	
	//HOANGND get categories level 1 of listing type
	public function getListingTypeCategoriesLevel1($listingtype_id, $from = null, $limit = null) {
		$select = $this->select()->where('listingtype_id = ?', $listingtype_id)->where('level = ?', 1);
		if (!is_null($from) && $limit) {
			$select->limit($limit+1, $from);
		}
        $categories = $this->fetchAll($select);
        return $categories;
	}
}

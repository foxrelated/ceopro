<?php
class Ynblog_Model_DbTable_Categories extends Engine_Db_Table
{
  /*----- Properties -----*/
  protected $_rowClass = 'Ynblog_Model_Category';
  protected $_name = 'blog_categories';
  protected $_type = 'blog_category';
  
  /*----- Get Category Function -----*/
  public function getCategory($category_id){
    return $this->find($category_id)->current();
  }
  /*----- Get Category List Function -----*/
  public function getCategories($params = array()){
    $select = $this->select()->order('category_name ASC');
    if (is_numeric($params['parent_id']))
      $select->where('parent_id = ?', $params['parent_id']);
    $select->where('category_id <> ? ', 0);
    $result = $this->fetchAll($select);
    return $result;
  }
  /*----- Get Categories Array -----*/
  public function getCategoriesAssoc(){
    $categories = $this->getAllCategories();
    $data = array();
    $data[0] ="";
    foreach($categories as $category){
      $data[$category->category_id] = str_repeat('--', $category->level - 1) .$category->category_name;
    }
    return $data;
  }
  /*----- Get User Categories List -----*/
  public function getUserCategories($user_id = 0){
    //Get table name
    $blog_table_name = Engine_Api::_()->getItemTable('blog')->info('name');
    $cat_table_name = $this->info('name');

    // Query
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->from($cat_table_name, array('category_name'))
        ->joinLeft($blog_table_name, "$blog_table_name.category_id = $cat_table_name.category_id")
        ->group("$cat_table_name.category_id")
        ->where($blog_table_name.'.owner_id = ?', $user_id)
        ->where($blog_table_name.'.draft = ?', "0")
        ->where($blog_table_name.'.search = ?',"1")
        ->where($blog_table_name.'.is_approved = ?',"1");

    return $this->fetchAll($select);
  }

    public function getCategoriesParent() {
        $stmt = $this -> select() -> from($this, array('category_id', 'category_name')) -> where('parent_id=?', 0) -> order('category_name ASC') -> query();
        $data = array();
        foreach ($stmt->fetchAll() as $category) {
            $data[$category['category_id']] = $category['category_name'];
        }
        return $data;
    }

    public function getAllCategories($showAllCates = 1) {
        $tree = array();
        $cates_parent = $this->getCategoriesParent();
        foreach ($cates_parent as $id => $cate_name) {
            $node = Engine_Api::_()->getItem('blog_category', $id);
            $this->appendChildToTree($node, $tree);
        }
        if (!$showAllCates) {
            unset($tree[0]);
        }
        return $tree;
    }

    public function appendChildToTree($node, &$tree) {
        array_push($tree, $node);
        $children = $node->getChilren();
        foreach ($children as $child_node) {
            $this->appendChildToTree($child_node, $tree);
        }
    }

    public function getAllChildrenBlogsByCategory($node)
    {
        $return_arr = array();
        $cur_arr = array();
        $list_categories = array();
        Engine_Api::_() -> getItemTable('blog_category') -> appendChildToTree($node, $list_categories);
        $tableBlogs = Engine_Api::_()->getItemTable('blog');
        foreach($list_categories as $category)
        {
            $select = $tableBlogs -> select() -> where('category_id = ?', $category -> category_id);
            $cur_arr = $tableBlogs -> fetchAll($select);
            if(count($cur_arr) > 0)
            {
                $return_arr[] = $cur_arr;
            }
        }
        return $return_arr;
    }
}
?>

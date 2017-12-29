<?php
class Ynblog_Model_Category extends Core_Model_Item_Abstract
{
  protected $_type = "blog_category";

  /*----- Get Category Link Fucntion -----*/
  public function getHref($params = array()) {
        $params = array_merge(array(
                    'category' => $this->category_id,
                    'reset'    => true,
                  ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, $route, $reset);
    }

  /*----- Category Used Count Function -----*/
  public function getUsedCount(){
    $table  = Engine_Api::_()->getItemTable('blog');
    $rName = $table->info('name');
    $select = $table->select()
                    ->from($rName)
                    ->where($rName.'.category_id = ?', $this->category_id);
    $row = $table->fetchAll($select);
    $total = count($row);
    return $total;
  }

  /*----- Ownership Function -----*/

  public function isOwner($owner)
  {
    if( $owner instanceof Core_Model_Item_Abstract )
    {
      return ( $this->getIdentity() == $owner->getIdentity() && $this->getType() == $owner->getType() );
    }

    else if( is_array($owner) && count($owner) === 2 )
    {
      return ( $this->getIdentity() == $owner[1] && $this->getType() == $owner[0] );
    }

    else if( is_numeric($owner) )
    {
      return ( $owner == $this->getIdentity() );
    }

    return false;
  }

  public function getChildCount()
  {
      $table = Engine_Api::_()->getItemTable('blog_category');
      return count($table->select()->from($table->info('name'), 'category_id')->where('parent_id = ?', $this->getIdentity())->query()->fetchAll(Zend_Db::FETCH_COLUMN));
  }

  public function getParent()
  {
      return Engine_Api::_()->getItem('blog_category', $this->parent_id);
  }

  public function shortTitle()
  {
      return strlen($this -> category_name) > 20 ? (substr($this -> category_name, 0, 17) . '...') : $this -> category_name;
  }

    public function getChilren() {
        $table = Engine_Api::_()->getItemTable('blog_category');
        $select = $table -> select() -> where('parent_id = ?', $this -> getIdentity()) -> order('category_name asc');
        return $table -> fetchAll($select);
    }

    public function getBreadCrumNode() {
        $result = array($this);
        $parent = $this -> getParent();
        while ($parent->level) {
            $result[] = $parent;
            $parent = $parent -> getParent();
        }
        return array_reverse($result);
    }

    public function checkHasBlogs()
    {
        $list_categories = array();
        $table = Engine_Api::_() -> getItemTable('blog');
        Engine_Api::_()->getItemTable('blog_category') -> appendChildToTree($this, $list_categories);
        foreach($list_categories as $category)
        {
            $select = $table -> select() -> where('category_id = ?', $category -> category_id) -> limit(1);
            $row = $table -> fetchRow($select);
            if($row)
                return $category -> category_id;
        }
        return false;
    }
}
?>

<?php
class Advgroup_Model_DbTable_Polls extends Engine_Db_Table
{
   protected $_name = 'group_polls';
   protected $_rowClass = 'Advgroup_Model_Poll';

   public function getPollSelect($params = array())
  {
    $table = Engine_Api::_()->getItemTable('advgroup_poll');
    $tableName = $table->info('name');

    $select = $table
      ->select()
      ->from($tableName)
      ;

    // Browse
    if( isset($params['browse']) ) {
      $select->where('search = ?', (int) (bool) $params['browse']);
    }

    //Search
    if(!empty($params['search'])){
      $select->where('title LIKE ? OR description LIKE ?',"%".$params['search']."%");
    }

    // Closed
    if(isset($params['closed']) && $params['closed'] != '') {
    
    $select
      ->where('closed = ?', $params['closed']);
    }
    
    // User
    if( !empty($params['user_id']) ) {
      $select
        ->where('user_id = ?', $params['user_id']);
    }

    //Group
    if(isset ($params['group_id'])){
        $select
        ->where('group_id = ?', $params['group_id']);
    }
    // Order
    switch( $params['order'] ) {
      case 'popular':
        $select
          ->order('vote_count DESC')
          ->order('view_count DESC');
        break;
      case 'recent':
      default:
        $select
          ->order('creation_date DESC');
        break;
    }

    return $select;
  }

  public function getPollsPaginator($params = array())
  {
    return Zend_Paginator::factory($this->getPollSelect($params));
  }
}
?>

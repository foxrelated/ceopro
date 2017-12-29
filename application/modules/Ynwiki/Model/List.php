<?php
class Ynwiki_Model_List extends Core_Model_List
{
  protected $_owner_type = 'page';

  protected $_child_type = 'user';
  
  //protected $_type = 'ynwiki_list';

  public $ignorePermCheck = true;

  public function getListItemTable()
  {
    return Engine_Api::_()->getItemTable('ynwiki_list_item');
  }
}
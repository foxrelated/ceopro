<?php
class Advgroup_Model_List extends Core_Model_List
{
  protected $_owner_type = 'group';

  protected $_child_type = 'user';

  public $ignorePermCheck = true;

  public function getListItemTable()
  {
    return Engine_Api::_()->getItemTable('advgroup_list_item');
  }
}
<?php
class Ynforum_Model_Userview extends Core_Model_List
{
  protected $_owner_type = 'ynforum';
  protected $_child_type = 'user';

  public function getUserviewItemTable()
  {
    return Engine_Api::_()->getItemTable('ynforum_userview_item');
  }
}
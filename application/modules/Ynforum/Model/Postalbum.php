<?php
class Ynforum_Model_Postalbum extends Core_Model_Item_Collection
{
  protected $_searchTriggers = false;
  protected $_parent_type = 'post';

  protected $_owner_type = 'post';

  protected $_children_types = array('ynforum_postphoto');

  protected $_collectible_type = 'ynforum_postphoto';

  public function getHref($params = array())
  {
    return $this->getCampaign()->getHref($params);
  }

  public function getCampaign()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('post');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('ynforum_postphoto');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $postPhoto ) {
      $postPhoto->delete();
    }

    parent::_delete();
  }
}
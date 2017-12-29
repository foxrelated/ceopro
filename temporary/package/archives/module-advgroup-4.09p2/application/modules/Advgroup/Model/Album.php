<?php
class Advgroup_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'group';

  protected $_owner_type = 'user';

  protected $_children_types = array('advgroup_photo');

  protected $_collectible_type = 'advgroup_photo';

//  public function getHref_1($params = array())
//  {
//    $params = array_merge(array(
//      'route' => 'group_profile',
//      'reset' => true,
//      'id' => $this->getGroup()->getIdentity(),
//      //'album_id' => $this->getIdentity(),
//    ), $params);
//    $route = $params['route'];
//    $reset = $params['reset'];
//    unset($params['route']);
//    unset($params['reset']);
//    return Zend_Controller_Front::getInstance()->getRouter()
//      ->assemble($params, $route, $reset);
//  }
  public function getHref($params = array())
    {
    $params = array_merge(array(
      'route' => 'group_extended',
      'reset' => true,
      'controller' => 'album',
      'action' => 'view',
      'group_id' => $this->getGroup()->getIdentity(),
      'album_id' => $this->album_id,
      //'album_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getAlbumHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'group_extended',
      'reset' => true,
      'controller' => 'album',
      'action' => 'view',
      'group_id' => $this->getGroup()->getIdentity(),
      'album_id' => $this->album_id,
      //'album_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  public function getGroup()
  {
//    return $this->getOwner();
      return Engine_Api::_()->getItem('group', $this->group_id);
  }

  public function getMemberOwner(){
    return Engine_Api::_()->user()->getUser($this->user_id);
  }
  
  public function getAuthorizationItem()
  {
    return $this->getParent('group');
  }

   public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function getPhotoCount($user_id){
    $table = Engine_Api::_()->getItemTable('advgroup_photo');
    $name = $table->info('name');
    $select = $table->select()
                    ->from($name, 'COUNT(*) AS count')
                    ->where("album_id = $this->album_id")
                    ->where("user_id = $user_id");
    return $select->query()->fetchColumn(0);
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('advgroup_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $groupPhoto ) {
      $groupPhoto->delete();
    }

    parent::_delete();
  }

 //overwrite function getCollectiblesPaginator
  public function getCollectiblesPaginator($params)
  {
  	$tbl_photos = Engine_Api::_()->getItemTable('advgroup_photo');
	$photoSelect = $tbl_photos->select()->where('album_id = ?', $this->getIdentity());
	
	if(isset($params['is_featured']))
	{
		 $photoSelect-> where("is_featured = ?", $params['is_featured'] );
	}
	
	return Zend_Paginator::factory($photoSelect);
  }

  public function count()
  {
    $photoTable = Engine_Api::_()->getItemTable('advgroup_photo');
    return $photoTable->select()
        ->from($photoTable, new Zend_Db_Expr('COUNT(photo_id)'))
        ->where('album_id = ?', $this->album_id)
        ->limit(1)
        ->query()
        ->fetchColumn();
  }
}
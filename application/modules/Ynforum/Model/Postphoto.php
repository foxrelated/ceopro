<?php
class Ynforum_Model_Postphoto extends Core_Model_Item_Collectible
{
  protected $_searchTriggers = false;
  protected $_parent_type = 'ynforum_postalbum';

  protected $_owner_type = 'user';

  protected $_collection_type = 'ynforum_postalbum';
  
  public function getPhotoUrl($type = null)
  {
    if( empty($this->file_id) ) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function getPost()
  {
    return Engine_Api::_()->getItem('ynforum_post', $this->post_id);
  }

  public function isSearchable()
  {
    $collection = $this->getCollection();
    if( !$collection instanceof Core_Model_Item_Abstract )
    {
      return false;
    }
    return $collection->isSearchable();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('ynforum_post');
  }


  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  protected function _postDelete()
  {
  	if($this->parent_type == 'forum_post')
	{	
	
	    if( $this->_disableHooks ) return;
	
	    // This is dangerous, what if something throws an exception in postDelete
	    // after the files are deleted?
	    try
	    {
	      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
		  if(is_object($file)){
	      	$file->remove();
		  }
	      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');
	      if(is_object($file)){
	      	$file->remove();
		  }
	
	      $album = $this->getCollection();
	
	      if( (int) $album->photo_id == (int) $this->getIdentity() )
	      {
			$album->photo_id = $this->getNextCollectible()->getIdentity();
			$album->save();
	      }
	    }
	    catch( Exception $e )
	    {
	      // @todo completely silencing them probably isn't good enough
	      //throw $e;
	    }
	}
  }
}
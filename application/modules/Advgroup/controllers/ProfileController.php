<?php
class Advgroup_ProfileController extends Core_Controller_Action_Standard
{
  public function init()
  {
  	$viewer = Engine_Api::_() -> user() -> getViewer();
  	
  	    $blacklists = Engine_Api::_()->getDbTable('blacklists','advgroup');
		
		$check = $blacklists->checkBlackListMembers($this -> _getParam('id'),$viewer->getIdentity());
		
		if($check == "true"){
			return;
		}
		
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('id');
      if( null !== $id )
      {
        $subject = Engine_Api::_()->getItem('group', $id);
        if( $subject && $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
        else return;
      }
    }
    $this->_helper->requireSubject('group');
  }
  
public function indexAction()
  {
  	
    if(!Engine_Api::_()->core()->hasSubject()) return $this->renderScript("_error.tpl");
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

    // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $subject->getType())
      ->where('id = ?', $subject->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    if( null !== $row && !empty($row->style) ) {
      $this->view->headStyle()->appendStyle($row->style);
    }

    $coverPhotoUrl = "";
    if ($subject->cover_photo)
    {
      $coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($subject->cover_photo)->current();
      if($coverFile)
          $coverPhotoUrl = $coverFile->map();
    }
    // Set meta
    $og = '<meta property="og:image" content="' . $this->finalizeUrl($subject->getPhotoUrl()) . '" />';
    $og .= '<meta property="og:image:width" content="640" />';
    $og .= '<meta property="og:image:height" content="442" />';
    $og .= '<meta property="og:image" content="' . $this->finalizeUrl($coverPhotoUrl) . '" />';
    $og .= '<meta property="og:title" content="' . $subject->getTitle() . '" />';
    $og .= '<meta property="og:description" content="' . $this->view->string()->striptags($subject->getDescription()) . '" />';
    $og .= '<meta property="og:url" content="' . $this->finalizeUrl($subject->getHref()) . '" />';
    $og .= '<meta property="og:updated_time" content="' . strtotime($subject->modified_date) . '" />';
    $og .= '<meta property="og:type" content="website" />';
    $this->view->layout()->headIncludes .= $og;

      // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
  private function finalizeUrl($url)
  {
    if ($url)
    {
      if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
        {
          $pageURL = 'http';
          if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
            {
              $pageURL .= "s";
            }
            $pageURL .= "://";
            $pageURL .= $_SERVER["SERVER_NAME"];
            $url = $pageURL . '/'. ltrim( $url, '/');
        }
    }

    return $url;
  }
}

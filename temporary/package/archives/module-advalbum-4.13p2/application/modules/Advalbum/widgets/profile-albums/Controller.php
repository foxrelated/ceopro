<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Advalbum_Widget_ProfileAlbumsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
  	$params = $this -> _getAllParams();

  	$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
	
	$mode_list = $mode_grid = $mode_pinterest = 1;
			$mode_enabled = array();
			$view_mode = 'list';
			
			if(isset($params['mode_list']))
			{
				$mode_list = $params['mode_list'];
			}
			if($mode_list)
			{
				$mode_enabled[] = 'list';
			}	
			if(isset($params['mode_grid']))
			{
				$mode_grid = $params['mode_grid'];
			}
			if($mode_grid)
			{
				$mode_enabled[] = 'grid';
			}			
			if(isset($params['mode_pinterest']))
			{
				$mode_pinterest = $params['mode_pinterest'];
			}
			if($mode_pinterest)
			{
				$mode_enabled[] = 'pinterest';
			}
			if(isset($params['view_mode']))
			{
				$view_mode = $params['view_mode'];
			}			
			if($mode_enabled && !in_array($view_mode, $mode_enabled))
			{
				$view_mode = $mode_enabled[0];
			}		
				
			$this -> view -> mode_enabled = $mode_enabled;
		
			$class_mode = "ynalbum-list-view";
			switch ($view_mode) 
			{
				case 'grid':
					$class_mode = "ynalbum-grid-view";
					break;
				case 'pinterest':
					$class_mode = "ynalbum-pinterest-view";
					break;
				default:
					$class_mode = "ynalbum-list-view";
					break;
			}
			$this -> view -> class_mode = $class_mode;
			$this -> view -> view_mode = $view_mode;
			
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
		{
			$limit = 8;
		}
		$session = new Zend_Session_Namespace('mobile');
		if($session -> mobile)
		{
        	$limit = $limit * 4;
		}
	
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'advalbum')
      ->getAlbumPaginator(array('owner' => $subject, 'search' => 1));
    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
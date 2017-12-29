<?php

/**
 * SocialEngine - Search Widget Controller
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2012 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     Matthew
 */
class Core_Widget_SearchMiniController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $requireCheck = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if( !$requireCheck && !Zend_Controller_Action_HelperBroker::getStaticHelper('RequireUser')->checkRequire() ) {
      $this->setNoRender();
      return;
    }
  }

}
?>

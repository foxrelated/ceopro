<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_BrowseController extends Core_Controller_Action_Standard
{
  public function browseAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('mobi_browse');
  }
}
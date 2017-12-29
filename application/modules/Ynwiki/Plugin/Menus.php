<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Wiki
 * @copyright  Copyright 2011 YouNet Company
 * @license    http://www.modules2buy.com/
 * @version    $Id: Menus.php
 * @author     Minh Nguyen
 */
class Ynwiki_Plugin_Menus
{
  public function canCreateSpace()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    // Must be able to create Page
    if( !Engine_Api::_()->authorization()->isAllowed('ynwiki_page', $viewer, 'createspase') ) {
      return false;
    }
    return true;
  }

  public function canViewWiki()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Must be able to view Page
    if( !Engine_Api::_()->authorization()->isAllowed('ynwiki_page', $viewer, 'view') ) {
      return false;
    }
    return true;
  }
  public function canFollowWiki()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    return true;
  }
  public function canFavouriteWiki()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Must be able to follow Page
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    return true;
  }
  public function canFaqs()
  {
    //$viewer = Engine_Api::_()->user()->getViewer();
    //if( !$viewer || !$viewer->getIdentity() ) {
    //  return false;
    //}
    return true;
  }
  public function canHelp()
  {
    //$viewer = Engine_Api::_()->user()->getViewer();
    //if( !$viewer || !$viewer->getIdentity() ) {
    //  return false;
    //}
    return true;
  }
}

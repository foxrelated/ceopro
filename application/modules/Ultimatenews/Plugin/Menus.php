<?php
class Ultimatenews_Plugin_Menus
{
	public function canManageFeeds()
    {
    	$viewer = Engine_Api::_()->user()->getViewer();
    	return (bool)Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'manage_feed');
	}
	public function canCreateFeeds()
    {
    	$viewer = Engine_Api::_()->user()->getViewer();
    	return (bool)Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_feed');
	}
	
	public function canManageNews()
    {
    	$viewer = Engine_Api::_()->user()->getViewer();
    	return (bool)Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'manage_news');
	}
	public function canCreateNews()
    {
    	$viewer = Engine_Api::_()->user()->getViewer();
    	return (bool)Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_news');
	}
    public function onMenuInitialize_UltimatenewsMainFavoritenews($row)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer->getIdentity() ) {
            return false;
        }
        return true;
    }
    public function onMenuInitialize_UltimatenewsMainRsssubscription($row)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer->getIdentity() ) {
            return false;
        }
        return true;
    }
}
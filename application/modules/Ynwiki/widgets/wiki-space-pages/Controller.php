<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Wiki
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: browse auctions
 * @author     Minh Nguyen
 */
class Ynwiki_Widget_WikiSpacePagesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
         if($this->_getParam('number') != ''  && $this->_getParam('number') >= 0){
                $limit = $this->_getParam('number');
            }
            else $limit = 10;
         $this->view->space = $pages = Engine_Api::_()->ynwiki()->getSpaces($limit);
         $css = "";
         $page_listing_id = 'wiki_space';
         $no_pages_message = $this->view->translate('Nobody has posted a space yet.');
         $this->view->arr_pages = $pages; 
         $this->view->page_listing_id = $page_listing_id;
         $this->view->no_pages_message = $no_pages_message;
         $this->view->css = $css; 
    }
}
?>
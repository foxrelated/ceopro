<?php
class Ynwiki_Widget_ProfileEditPagesController extends Engine_Content_Widget_Abstract
{
    protected $_childCount; 
    public function indexAction()
    {
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
        
        $values['profile'] = Engine_Api::_()->core()->getSubject()->getIdentity();
        $values['edit'] = true;
        $values['orderby'] = "engine4_ynwiki_edits.creation_date";
        $this->view->pages = $paginator = Engine_Api::_()->ynwiki()->getPagesPaginator($values);
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->ynwiki_page;
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber(1);
        
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
?>
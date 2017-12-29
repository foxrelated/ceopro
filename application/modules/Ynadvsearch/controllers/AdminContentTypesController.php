<?php

class Ynadvsearch_AdminContentTypesController extends Core_Controller_Action_Admin
{
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynadvsearch_admin_main', array(), 'ynadvsearch_admin_main_contenttypes');
    }

    public function indexAction() 
    {
        $this -> view -> form = $form = new Ynadvsearch_Form_Admin_Content_Create();
        
        $type_element = $form->getElement('type');
        $this -> view -> canCreate =  true;
        if(!$type_element)
        {
            $this -> view -> canCreate =  false;
        }
        
        $this -> view -> page = $page = $this->_getParam('page',1);
        $this->view->paginator = Engine_Api::_()->getItemTable('ynadvsearch_contenttype')->getContentTypesPaginator();
        $this->view->paginator->setItemCountPerPage(20);
        $this->view->paginator->setCurrentPageNumber($page);
    }
    
     public function searchBarAction() 
     {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $content_type = Engine_Api::_()->getItem('ynadvsearch_contenttype', $id);
        if ($content_type) {
            $content_type->search = $value;
            $content_type->save();
        }
    }
     
     public function showAction() 
     {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $content_type = Engine_Api::_()->getItem('ynadvsearch_contenttype', $id);
        if ($content_type) {
            $content_type->show = $value;
            $content_type->save();
        }
    }
     
     public function styleAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $content_type = Engine_Api::_()->getItem('ynadvsearch_contenttype', $id);
        if ($content_type) {
            $content_type->original_style = $value;
            $content_type->save();
        }
     }
     
     public function multiselectedAction() 
      {
        $action = $this -> _getParam('select_action', 'Delete');
        $this->view->action = $action;
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $confirm = $this -> _getParam('confirm', false);

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true) {
            $ids_array = explode(",", $ids);
            switch ($action) {
                case 'Delete':
                    foreach ($ids_array as $id) {
                        $content_type = Engine_Api::_()->getItem('ynadvsearch_contenttype', $id);
                        if ($content_type) {
                            if ($content_type->photo_id) {
                                Engine_Api::_()->getItem('storage_file', $content_type->photo_id)->remove();
                            }
                            Engine_Api::_()->ynadvsearch()->removeContentPage($content_type->type);
                            $content_type->delete();
                        }
                    }
                    break;
            }
            $this -> _helper -> redirector -> gotoRoute(array('action' => ''));
        }
    }
    
    public function createAction()
    {
        // Get form
        $this -> view -> form = $form = new Ynadvsearch_Form_Admin_Content_Create();
        
        // Check stuff
        if (!$this -> getRequest() -> isPost())
        {
            return;
        }
        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }
        
        // Save
         $values = $form->getValues();
         
         $content = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype') -> createRow();
         $content-> type = $values['type'];
         $content-> title  = $values['title'];
         $content-> search = $values['search'];
         $content-> show = $values['show'];
         $content->save();
    
        if (!empty($values['photo']))
            $content -> setPhoto($form->photo);
        switch ($values['type']) {
            case 'event':
                Engine_Api::_()-> ynadvsearch() -> addEventPage();
                $content -> module = 'Events';
                break;
            case 'user':
                Engine_Api::_()-> ynadvsearch() -> addMemberPage();
                $content -> module = 'Members';
                break;  
            case 'blog':
                Engine_Api::_()-> ynadvsearch() -> addBlogPage();
                $content -> module = 'Blogs';
                break;  
            case 'classified':
                Engine_Api::_()-> ynadvsearch() -> addClassifiedPage();
                $content -> module = 'Classifieds';
                break;
            case 'poll':
                Engine_Api::_()-> ynadvsearch() -> addPollPage();
                $content -> module = 'Polls';
                break;
            case 'ynauction_product':
                Engine_Api::_()-> ynadvsearch() -> addAuctionPage();
                $content -> module = 'Auction';
                break; 
            case 'yncontest_contest':
                Engine_Api::_()-> ynadvsearch() -> addContestPage();
                $content -> module = 'Contest';
                break;
            case 'forum_topic':
                Engine_Api::_()-> ynadvsearch() -> addForumPage();
                $content -> module = 'Forum';
                break;
            case 'group':
                Engine_Api::_()-> ynadvsearch() -> addGroupPage();
                $content -> module = 'Group';
                break;
            case 'ynwiki_page':
                Engine_Api::_()-> ynadvsearch() -> addWikiPage();
                $content -> module = 'Wiki';
                break;
            case 'social_store':
                Engine_Api::_()-> ynadvsearch() -> addStoreStorePage();
                $content -> module = 'Store';
                break;  
            case 'social_product':
                Engine_Api::_()-> ynadvsearch() -> addStoreProductPage();
                $content -> module = 'Store';
                break;                 
            case 'video':
                Engine_Api::_()-> ynadvsearch() -> addVideoPage();
                $content -> module = 'Videos';
                break;
            case 'album':
                Engine_Api::_()-> ynadvsearch() -> addAlbumPage();
                $content -> module = 'Albums';
                break;
            case 'advalbum_photo':  
                Engine_Api::_()-> ynadvsearch() -> addPhotoPage();
                $content -> module = 'Albums';
                break;
            case 'ynfilesharing_folder':    
                Engine_Api::_()-> ynadvsearch() -> addFileSharingPage();
                $content -> module = 'File Sharing';    
                break;
            case 'groupbuy_deal':
                Engine_Api::_()-> ynadvsearch() -> addGroupBuyPage();
                $content -> module = 'Group Buy';
                break;
            case 'music_playlist':
                Engine_Api::_()-> ynadvsearch() -> addMusicPage();  
                $content -> module = 'Music';
                break;
            case 'mp3music_playlist':
                Engine_Api::_()-> ynadvsearch() -> addMp3MusicPage();
                Engine_Api::_()-> ynadvsearch() -> addMp3MusicAlbumsPage();
                Engine_Api::_()-> ynadvsearch() -> addMp3MusicPlaylistsPage();
                $content -> module = 'Mp3 Music';
                break;  
            case 'ynfundraising_campaign':
                Engine_Api::_()-> ynadvsearch() -> addFundraisingPage();    
                $content -> module = 'Fundraising';
                break;
            case 'ynlistings_listing':
                Engine_Api::_()-> ynadvsearch() -> addListingPage();    
                $content -> module = 'Listing';
                break;
            case 'ynjobposting_job':
                Engine_Api::_()-> ynadvsearch() -> addJobPostingJobPage();    
                $content -> module = 'Job Posting';
                break;
            case 'ynjobposting_company':
                Engine_Api::_()-> ynadvsearch() -> addJobpostingCompanyPage();    
                $content -> module = 'Job Posting';
                break;
            case 'ynbusinesspages_business':
                Engine_Api::_()-> ynadvsearch() -> addBusinessPage();    
                $content -> module = 'Business Pages';
                break; 
			case 'ynresume_resume':
                Engine_Api::_()-> ynadvsearch() -> addResumePage();    
                $content -> module = 'Resume';
                break;
			case 'ynmultilisting_listing':
                Engine_Api::_()-> ynadvsearch() -> addMultilistingPage();    
                $content -> module = 'Multilple Listing';
                break;
			case 'ynmusic_song':
                Engine_Api::_()-> ynadvsearch() -> addSocialmusicPage();    
                $content -> module = 'Social Music';
                break;  
			case 'ynultimatevideo_video':
                Engine_Api::_()-> ynadvsearch() -> addUltimateVideoPage();
                $content -> module = 'Ultimate Video';
                break;
			case 'ynvideochannel_video':
                Engine_Api::_()-> ynadvsearch() -> addVideochannelPage();
                $content -> module = 'Video Channel';
                break;
            default:
                break;
        }
        
        $content->save();
        
        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Content Type Added.')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }
    
    public function editAction()
    {
        $content = Engine_Api::_() -> getItem('ynadvsearch_contenttype', $this->_getParam('id'));
        // Get form
        $this -> view -> form = $form = new Ynadvsearch_Form_Admin_Content_Edit();
        $form -> removeElement('type');
        $form -> populate($content -> toArray());
        // Check stuff
        if (!$this -> getRequest() -> isPost())
        {
            return;
        }
        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }
        // Save
         $values = $form->getValues();
         if(!empty($values['title']))
             $content-> title = $values['title'];
         if(!empty($values['module']))
            $content-> module  = $values['module'];
         if(!empty($values['search']))
             $content-> search = $values['search'];
         if(!empty($values['show']))
             $content-> show = $values['show'];
         $content->save();
    
        if (!empty($values['photo']))
            $content -> setPhoto($form->photo);
        
        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Content Type Edited.')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }
    
    public function sortAction()
    {
        $params['page'] = $this->getRequest()->getParam('page',1);
        $content_types = Engine_Api::_()->getItemTable('ynadvsearch_contenttype')->getContentTypesPaginator($params);
        $order = explode(',', $this->getRequest()->getParam('order'));
        foreach( $order as $i => $item ) {
          $contenttype_id = substr($item, strrpos($item, '_')+1);
          foreach( $content_types as $content_type ) {
            if( $content_type->contenttype_id == $contenttype_id ) {
              $content_type->order = $i;
              $content_type->save();
            }
        }
    }
  }
}

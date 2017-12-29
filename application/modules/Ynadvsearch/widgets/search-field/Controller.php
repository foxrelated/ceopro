<?php

class Ynadvsearch_Widget_SearchFieldController extends Engine_Content_Widget_Abstract
{
    public function indexAction() {
        $values = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if (isset($values['submit']) && isset($values['query']) && $values['query'] != '') {
            $this->view->query = $values['query'];
        }
        elseif (isset($values['is_search']) && $values['is_search'] == 1 && isset($values['query']) && $values['query'] != '') {
            if (isset($values['is_hashtag']) && $values['is_hashtag'] == 1) {
                $text = str_replace('#', '', $values['query']);
            }
            else {
                $text = $values['query'];
            }
            $this->view->query = $text;
        }
        elseif (isset($values['phrase']) && $values['phrase'] != '') {
            $this->view->query = $values['phrase'];
        }
        $this->view->action = $values['action'];
        if (isset($values['page']))
            $this->view->page = $values['page'];
        else
            $this->view->page = 1;
        $this->view->module = $values['module'];

        //Check YouNet Profile Search plugin enabled for displaying link button
        $module_table = Engine_Api::_()->getDbTable('modules','core');
        if($module_table->isModuleEnabled('ynprofilesearch')){
            $this->view->profile_search_enabled = true;
        }
        else {
            $this->view->profile_search_enabled = false;
        }
    
        //add sugestion
        $this->view->maxRe = $this->view->maxRe =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynadvsearch_num_searchitem', 10);
        
        //tab menu
        $tabs = array();    
        $menu = new Ynadvsearch_Plugin_Menus();
          
        $aAllButton = $menu -> onMenuInitialize_YnadvsearchAll();
        array_push($tabs, $aAllButton);
          
        $table_contentType = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $content_types =$table_contentType->fetchAll($table_contentType -> getContentTypesSelect());
        foreach($content_types as $item) {
            switch ($item -> type) {
                case 'event':
                    $aEventButton = $menu -> onMenuInitialize_YnadvsearchEvent();
                    array_push($tabs, $aEventButton);
                    break;
                case 'user':
                    $aMemberButton = $menu -> onMenuInitialize_YnadvsearchMember();
                    array_push($tabs, $aMemberButton);
                    break;  
                case 'blog':
                    $aBlogButton = $menu -> onMenuInitialize_YnadvsearchBlog();
                    array_push($tabs, $aBlogButton);
                    break;  
                case 'classified':
                    $aClassifiedButton = $menu -> onMenuInitialize_YnadvsearchClassified();
                    array_push($tabs, $aClassifiedButton);
                    break;
                case 'poll':
                    $aPollButton = $menu -> onMenuInitialize_YnadvsearchPoll();
                    array_push($tabs, $aPollButton);
                    break;
                case 'ynauction_product':
                    $aAuctionButton = $menu -> onMenuInitialize_YnadvsearchAuction();
                    array_push($tabs, $aAuctionButton);
                    break; 
                case 'yncontest_contest':
                    $aContestButton = $menu -> onMenuInitialize_YnadvsearchContest();
                    array_push($tabs, $aContestButton);
                    break;
                case 'forum_topic':
                    $aForumButton = $menu -> onMenuInitialize_YnadvsearchForum();
                    array_push($tabs, $aForumButton);
                    break;
                case 'group':
                    $aGroupButton = $menu -> onMenuInitialize_YnadvsearchGroup();
                    array_push($tabs, $aGroupButton);
                    break;
                case 'ynwiki_page':
                    $aWikiButton = $menu -> onMenuInitialize_YnadvsearchWiki();
                    array_push($tabs, $aWikiButton);
                    break;
                case 'social_store':
                    $aStoreStoreButton = $menu -> onMenuInitialize_YnadvsearchStoreStore();
                    array_push($tabs, $aStoreStoreButton);
                    break;  
                case 'social_product':
                    $aStoreProductButton = $menu -> onMenuInitialize_YnadvsearchStoreProduct();
                    array_push($tabs, $aStoreProductButton);
                    break;                 
                case 'video':
                    $aVideoButton = $menu -> onMenuInitialize_YnadvsearchVideo();
                    array_push($tabs, $aVideoButton);
                    break;
                case 'album':
                    $aAlbumButton = $menu -> onMenuInitialize_YnadvsearchAlbum();
                    array_push($tabs, $aAlbumButton);
                    break;
                case 'advalbum_photo':  
                    $aPhotoButton = $menu -> onMenuInitialize_YnadvsearchPhoto();
                    array_push($tabs, $aPhotoButton);
                    break;
                case 'ynfilesharing_folder':    
                    $aFileSharingButton = $menu -> onMenuInitialize_YnadvsearchFileSharing();
                    array_push($tabs, $aFileSharingButton); 
                    break;
                case 'groupbuy_deal':
                    $aGroupBuyButton = $menu -> onMenuInitialize_YnadvsearchGroupBuy();
                    array_push($tabs, $aGroupBuyButton);
                    break;
                case 'music_playlist':
                    $aMusicButton = $menu -> onMenuInitialize_YnadvsearchMusic();
                    array_push($tabs, $aMusicButton);
                    break;
                case 'mp3music_playlist':
                    $aMp3MusicButton = $menu -> onMenuInitialize_YnadvsearchMp3Music();
                    array_push($tabs, $aMp3MusicButton);
                    break;  
                case 'ynfundraising_campaign':
                    $aFundraisingButton = $menu -> onMenuInitialize_YnadvsearchFundraising();
                    array_push($tabs, $aFundraisingButton);
                    break;
                case 'ynlistings_listing':
                    $aListingButton = $menu -> onMenuInitialize_YnadvsearchListing();
                    array_push($tabs, $aListingButton);
                    break;
                case 'ynjobposting_job':
                    $aJobButton = $menu -> onMenuInitialize_YnadvsearchJobPostingJob();
                    array_push($tabs, $aJobButton);
                    break;
                case 'ynjobposting_company':
                    $aCompanyButton = $menu -> onMenuInitialize_YnadvsearchJobPostingCompany();
                    array_push($tabs, $aCompanyButton);
                    break;
                case 'ynbusinesspages_business':
                    $aBusinessButton = $menu -> onMenuInitialize_YnadvsearchBusiness();
                    array_push($tabs, $aBusinessButton);
                    break;
				case 'ynresume_resume':
                    $aResumeButton = $menu -> onMenuInitialize_YnadvsearchResume();
                    array_push($tabs, $aResumeButton);
                    break;
				case 'ynmultilisting_listing':
                    $aMultilistingButton = $menu -> onMenuInitialize_YnadvsearchMultilisting();
                    array_push($tabs, $aMultilistingButton);
                    break; 
				case 'ynmusic_song':
                    $aSocialMusicButton = $menu -> onMenuInitialize_YnadvsearchSocialmusic();
                    array_push($tabs, $aSocialMusicButton);
                    break;
                case 'ynultimatevideo_video':
                    $aUltimateVideoButton = $menu -> onMenuInitialize_YnadvsearchUltimateVideo();
                    array_push($tabs, $aUltimateVideoButton);
                    break;
                case 'ynvideochannel_video':
                    $aVideoChannelButton = $menu -> onMenuInitialize_YnadvsearchVideoChannel();
                    array_push($tabs, $aVideoChannelButton);
                    break;
                default:
                    break;
            }
        }
          
        $this->view->tabs = $tabs;
        $session = new Zend_Session_Namespace('mobile');
        $this->view->isMobile = $session->mobile;
    }
}

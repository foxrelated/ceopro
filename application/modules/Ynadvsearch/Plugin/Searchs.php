<?php

class Ynadvsearch_Plugin_Searchs {
    
    public function onMenuInitialize_YnadvsearchMember() {
        $member_enable = Engine_Api::_() -> hasItemType('user');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('user');
        $view = Zend_Registry::get('Zend_View');
         
        if($member_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'user-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchBlog() {
        $blog_enable = Engine_Api::_() -> hasItemType('blog');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('blog');
        $view = Zend_Registry::get('Zend_View');
         
        if($blog_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'blog-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchClassified() {
        $classified_enable = Engine_Api::_() -> hasItemType('classified');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('classified');
        $view = Zend_Registry::get('Zend_View');
         
        if($classified_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'classified-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }

    public function onMenuInitialize_YnadvsearchPoll() {
        $poll_enable = Engine_Api::_() -> hasItemType('poll');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('poll');
        $view = Zend_Registry::get('Zend_View');
         
        if($poll_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'poll-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchAuction() {
        $auction_enable = Engine_Api::_() -> hasItemType('ynauction_product');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('ynauction_product');
        $view = Zend_Registry::get('Zend_View');
         
        if($auction_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'auction-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchContest() {
        $contest_enable = Engine_Api::_() -> hasItemType('yncontest_contest');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('yncontest_contest');
        $view = Zend_Registry::get('Zend_View');
         
        if($contest_enable && $row && $row -> search) {
            
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'contest-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchForum() {
        
        $forum_enable = Engine_Api::_() -> hasItemType('forum_topic');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('forum_topic');
        $view = Zend_Registry::get('Zend_View');
         
        
        if($forum_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'forum-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchGroup() {
        
        $group_enable = Engine_Api::_() -> hasItemType('group');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('group');
        $view = Zend_Registry::get('Zend_View');
         
        
        if($group_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'group-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchWiki() {
        
        $wiki_enable = Engine_Api::_() -> hasItemType('ynwiki_page');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('ynwiki_page');
        $view = Zend_Registry::get('Zend_View');
         
        
        if($wiki_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'wiki-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchStoreStore() {
        
        $store_store_enable = Engine_Api::_() -> hasItemType('social_store');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('social_store');
        $view = Zend_Registry::get('Zend_View');
         
        
        if($store_store_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'store-store-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchStoreProduct() {
        
        $store_product_enable = Engine_Api::_() -> hasItemType('social_product');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType ('social_product');
        $view = Zend_Registry::get('Zend_View');
         
        
        if($store_product_enable && $row && $row -> search) {
            return array(
                'label' => $row->title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'store-product-search',
                    
                )
            );
        }
        else {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchEvent()
    {
        $event_enable = Engine_Api::_() -> hasItemType('event');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('event');
        $view = Zend_Registry::get('Zend_View');
        if($event_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'event-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchVideo()
    {
        $video_enable = Engine_Api::_() -> hasItemType('video');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('video');
        $view = Zend_Registry::get('Zend_View');
         
        if($video_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'video-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchAlbum()
    {
        $album_enable = Engine_Api::_() -> hasItemType('album');
        $advalbum_enable = Engine_Api::_() -> hasItemType('advalbum_album');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('album');
        $view = Zend_Registry::get('Zend_View');
         
        if(($album_enable || $advalbum_enable) && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'album-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchPhoto()
    {
        $photo_enable = Engine_Api::_() -> hasItemType('advalbum_photo');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('advalbum_photo');
        $view = Zend_Registry::get('Zend_View');
         
        if($photo_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'photo-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }

    public function onMenuInitialize_YnadvsearchFileSharing()
    {
        $filesharing_enable = Engine_Api::_() -> hasItemType('ynfilesharing_folder');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynfilesharing_folder');
        $view = Zend_Registry::get('Zend_View');
         
        if($filesharing_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'filesharing-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchGroupBuy()
    {
        $groupbuy_enable = Engine_Api::_() -> hasItemType('groupbuy_deal');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('groupbuy_deal');
        $view = Zend_Registry::get('Zend_View');
         
        if($groupbuy_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'groupbuy-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }

    public function onMenuInitialize_YnadvsearchMusic()
    {
        $music_enable = Engine_Api::_() -> hasItemType('music_playlist');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('music_playlist');
        $view = Zend_Registry::get('Zend_View');
         
        if($music_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'music-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchMp3Music()
    {
        $mp3music_enable = Engine_Api::_() -> hasItemType('mp3music_playlist');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('mp3music_playlist');
        $view = Zend_Registry::get('Zend_View');
         
        if($mp3music_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'mp-music-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }

    public function onMenuInitialize_YnadvsearchFundraising()
    {
        $ynfundraising_enable = Engine_Api::_() -> hasItemType('ynfundraising_campaign');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynfundraising_campaign');
        $view = Zend_Registry::get('Zend_View');
         
        if($ynfundraising_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'fundraising-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchListing()
    {
        $listing_enable = Engine_Api::_() -> hasItemType('ynlistings_listing');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynlistings_listing');
        $view = Zend_Registry::get('Zend_View');
         
        if($listing_enable && $row && $row -> search)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                
                'params' => array(
                    'action' => 'listing-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchJobPostingJob() {
        $job_enable = Engine_Api::_() -> hasItemType('ynjobposting_job');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynjobposting_job');
        $view = Zend_Registry::get('Zend_View');
        if($job_enable && $row && $row -> show)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'jobposting-job-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }

    public function onMenuInitialize_YnadvsearchJobPostingCompany() {
        $company_enable = Engine_Api::_() -> hasItemType('ynjobposting_company');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynjobposting_company');
        $view = Zend_Registry::get('Zend_View');
         
        if($company_enable && $row && $row -> show)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'jobposting-company-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
    
    public function onMenuInitialize_YnadvsearchBusiness() {
        $business_enable = Engine_Api::_() -> hasItemType('ynbusinesspages_business');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynbusinesspages_business');
        $view = Zend_Registry::get('Zend_View');
         
        if($business_enable && $row && $row -> show)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'business-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
	
	public function onMenuInitialize_YnadvsearchResume() {
        $resume_enable = Engine_Api::_() -> hasItemType('ynresume_resume');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynresume_resume');
        $view = Zend_Registry::get('Zend_View');
         
        if($resume_enable && $row && $row -> show)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'resume-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }

	public function onMenuInitialize_YnadvsearchMultilisting() {
        $multilisting_enable = Engine_Api::_() -> hasItemType('ynmultilisting_listing');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynmultilisting_listing');
        $view = Zend_Registry::get('Zend_View');
         
        if($multilisting_enable && $row && $row -> show)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'multilisting-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }
	
	public function onMenuInitialize_YnadvsearchSocialmusic() {
        $socialmusic_enable = Engine_Api::_() -> hasItemType('ynmusic_song');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynmusic_song');
        $view = Zend_Registry::get('Zend_View');
         
        if($socialmusic_enable && $row && $row -> show)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'socialmusic-search',
                    
                )
            );
        }
        else 
        {
            return false;
        }
    }

    public function onMenuInitialize_YnadvsearchUltimateVideo() {
        $ultimatevideo_enable = Engine_Api::_() -> hasItemType('ynultimatevideo_video');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynultimatevideo_video');
        $view = Zend_Registry::get('Zend_View');

        if($ultimatevideo_enable && $row && $row -> show)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'ultimatevideo-search',

                )
            );
        }
        else
        {
            return false;
        }
    }
    public function onMenuInitialize_YnadvsearchVideochannel() {
        $videochannel_enable = Engine_Api::_() -> hasItemType('ynvideochannel_video');
        $table_contentType = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_contentType -> getContentType('ynvideochannel_video');
        $view = Zend_Registry::get('Zend_View');
        if($videochannel_enable && $row && $row -> show)
        {
            return array(
                'label' => $row -> title,
                'icon' => $view->itemPhoto($row, 'thumb.icon'),
                'route' => 'ynadvsearch_search',
                'params' => array(
                    'action' => 'videochannel-search',
                )
            );
        }
        else
        {
            return false;
        }
    }
}

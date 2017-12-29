<?php
class Ynmultilisting_Widget_ListingProfileCoverStyle1Controller extends Engine_Content_Widget_Abstract {
  
    public function indexAction() {
        if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject() -> forward();
		}
		$this -> view -> listing = $subject = Engine_Api::_() -> core() -> getSubject();
        
		if(!Engine_Api::_() -> ynmultilisting() -> isMobile()){
			//full size
			if ($subject->theme != 'theme1')
			{
				return $this -> setNoRender();
			}
		} else {
			//mobile
			$session = new Zend_Session_Namespace('mobile'); 
    		if(!$session -> mobile) {
    			//if not mobile view
    			if ($subject->theme != 'theme1')
				{
					return $this -> setNoRender();
				}
    		}
		}
		
		//get viewer 
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		//get photos
		$this -> view -> album = $album = $subject -> getSingletonAlbum();
		$this -> view -> photos = $photos = $album -> getCollectiblesPaginator();
		$photos -> setCurrentPageNumber(1);
		$photos -> setItemCountPerPage(100);
		//get videos
		$data_videos = array();
		if (Engine_Api::_() -> hasModuleBootstrap('video') || Engine_Api::_() -> hasModuleBootstrap('ynvideo')) {
			$tableMappings = Engine_Api::_() -> getDbTable('mappings', 'ynmultilisting');
			$params['listing_id'] = $subject -> getIdentity();
			$videos= $tableMappings -> getVideosPaginator($params);
			$videos -> setCurrentPageNumber(1);
			$videos -> setItemCountPerPage(100);

			foreach ($videos as $video) {
				$data_videos[] = $video;
			}
		}

		if (Engine_Api::_()->hasModuleBootstrap('ynultimatevideo')) {
            if (Engine_Api::_()->hasModuleBootstrap('ynultimatevideo')) {
                $tableMappings = Engine_Api::_() -> getDbTable('mappings', 'ynmultilisting');
                $params['listing_id'] = $subject -> getIdentity();
                $videos= $tableMappings -> getUltimatevideoPaginator($params);
                $videos -> setCurrentPageNumber(1);
                $videos -> setItemCountPerPage(100);

                foreach ($videos as $video) {
                    $data_videos[] = $video;
                }
            }
		}

		$this->view->videos = $data_videos;
		$this->view->module_video = $module_video = Engine_Api::_()->hasModuleBootstrap('ynultimatevideo') ? 'ynultimatevideo' : 'video';
		$this -> view -> listing = $subject;
		if (!$subject -> isOwner($viewer) && $subject -> status == 'open' && $subject -> approved_status == 'approved') {
			$now = new DateTime();
			$subject -> view_time = $now -> format('y-m-d H:i:s');
			$subject -> view_count++;
			$subject -> save();
		}
		$can_review = false;
		$can_review = $subject -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'review');
		if ($can_review) {
			$reviewTable = Engine_Api::_() -> getItemTable('ynmultilisting_review');
			$reviewSelect = $reviewTable -> select() -> where('listing_id = ?', $subject -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity());
			$this->view->my_review = $my_review = $reviewTable -> fetchRow($reviewSelect);
			if ($my_review) {
				$this -> view -> has_review = true;
				$can_review = false;
			}
		}
		$this -> view -> can_review = $can_review;
    }

}
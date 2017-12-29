<?php
class Ynmultilisting_Plugin_Core
{
	public function onItemDeleteAfter($event)
	{
		$payload = $event -> getPayload();
        
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$subject_id = $request -> getParam("id_subject", null);
			$type = $request -> getParam("type_parent", null);
			if ($type == 'ynmultilisting_listing')
			{
				if ($subject_id)
				{
					$type = $request -> getParam("case", null);
					switch ($type) {
					case 'video':
							$ynvideo_enabled = Engine_Api::_()->hasModuleBootstrap('ynvideo');
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$key = 'ynmultilisting_predispatch_url:' . $module_video . '.index.manage';
							$value = $view -> url(array(
								'controller' => 'video',
								'action' => 'manage',
								'listing_id' => $subject_id,
							), 'ynmultilisting_extended', true);
							$_SESSION[$key] = $value;
							break;			
					}
				}
			}
		}
	}	
    
    public function onItemDeleteBefore($event) {
        //HOANGND
        //for update comment count     
        $payload = $event->getPayload();
        $arrTypes = array('status', 'post', 'post_self');
        if ($payload->getType() == 'activity_action' && $payload->object_type == 'ynmultilisting_listing' && in_array($payload->type, $arrTypes)) {
            $listing_id = $payload->object_id;
            $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $listing_id);
            if ($listing) {
                $listing->comment_count = $listing->comment_count - 1;
                $listing->save();
            }   
        }
    }

	public function onItemUpdateAfter($event)
	{
		$payload = $event->getPayload();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$viewer = Engine_Api::_()->user()->getViewer();
		if (is_object($request)) {
			$view = Zend_Registry::get('Zend_View');
			$subject_id = $request->getParam("id_subject", null);
			$type = $request->getParam("type_parent", null);
			if ($type == 'ynmultilisting_listing') {
				if ($subject_id) {
					$type = $payload->getType();
					switch ($type) {
						case 'video':
							$ynvideo_enabled = Engine_Api::_()->hasModuleBootstrap('ynvideo');
							if ($ynvideo_enabled) {
								$module_video = "ynvideo";
							} else {
								$module_video = "video";
							}
							$profile = $request->getParam("profile", null);
							if (!empty($profile)) {
								$video_type = 'profile_video';
							} else {
								$video_type = 'video';
							}
							$table = Engine_Api::_()->getDbTable('mappings', 'ynmultilisting');
							$select = $table->select()->where('listing_id =?', $subject_id)->where('item_id = ?', $payload->getIdentity())->limit(1);
							$video_row = $table->fetchRow($select);
							if (!$video_row) {
								$db = $table->getAdapter();
								$db->beginTransaction();
								$row = $table->createRow();
								$row->setFromArray(array(
									'listing_id' => $subject_id,
									'item_id' => $payload->getIdentity(),
									'user_id' => $viewer->getIdentity(),
									'type' => $video_type,
									'creation_date' => date('Y-m-d H:i:s'),
									'modified_date' => date('Y-m-d H:i:s'),
								));
								$row->save();
								$listing = Engine_Api::_()->getItem('ynmultilisting_listing', $subject_id);
								$video = Engine_Api::_()->getItem('video', $payload->getIdentity());
								$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
								$action = $activityApi->addActivity($viewer, $listing, 'ynmultilisting_video_create');
								if ($action) {
									$activityApi->attachActivity($action, $video);
								}

								$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
								$notifyApi->addNotification($listing->getOwner(), $video, $listing, 'ynmultilisting_listing_add_item', array('label' => 'video'));

								$db->commit();
								$key = 'ynmultilisting_predispatch_url:' . $module_video . '.index.manage';
								if (!empty($profile)) {
									$value = $view->url(array(
										'controller' => 'video',
										'action' => 'list',
										'listing_id' => $subject_id,
									), 'ynmultilisting_extended', true);
									$_SESSION[$key] = $value;
								} else {
									$value = $view->url(array(
										'controller' => 'video',
										'action' => 'manage',
										'listing_id' => $subject_id,
									), 'ynmultilisting_extended', true);
									$_SESSION[$key] = $value;
								}
							} else {
								$key = 'ynmultilisting_predispatch_url:' . $module_video . '.index.manage';
								if ($video_type == 'profile_video') {
									$value = $view->url(array(
										'controller' => 'video',
										'action' => 'list',
										'listing_id' => $subject_id,
									), 'ynmultilisting_extended', true);
									$_SESSION[$key] = $value;
								} else {
									$value = $view->url(array(
										'controller' => 'video',
										'action' => 'manage',
										'listing_id' => $subject_id,
									), 'ynmultilisting_extended', true);
									$_SESSION[$key] = $value;
								}
							}
							break;
						case 'ynultimatevideo_video':
							$profile = $request->getParam("profile", null);
							if (!empty($profile)) {
								$video_type = 'profile_video';
							} else {
								$video_type = 'video';
							}
							$module_video = 'ynultimatevideo';
							$table = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
							$select = $table->select()->where('parent_id =?', $subject_id)->where('video_id = ?', $payload->getIdentity())->limit(1);
							$video_row = $table->fetchRow($select);
							if (!$video_row) {
								$ultimate_video = Engine_Api::_() -> getItem('ynultimatevideo_video', $payload -> getIdentity());
								$ultimate_video->parent_type = 'ynmultilisting_listing';
								$ultimate_video->parent_id = $subject_id;
								$ultimate_video->save();
								$listing = Engine_Api::_()->getItem('ynmultilisting_listing', $subject_id);

								$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
								$action = $activityApi->addActivity($viewer, $listing, 'ynmultilisting_video_create');
								if($action) {
									$activityApi->attachActivity($action, $ultimate_video);
								}

								$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
								$notifyApi -> addNotification($listing -> getOwner(), $ultimate_video, $listing, 'ynmultilisting_listing_add_item', array('label' => 'ynultimatevideo_video'));

								$key = 'ynmultilisting_predispatch_url:' . $module_video . '.index.view';
								if (!empty($profile)) {
									$value = $view->url(array(
										'controller' => 'ultimate-videos',
										'action' => 'list',
										'listing_id' => $subject_id,
									), 'ynmultilisting_extended', true);
									$_SESSION[$key] = $value;
								} else {
									$value = $view->url(array(
										'controller' => 'ultimate-videos',
										'action' => 'manage',
										'listing_id' => $subject_id,
									), 'ynmultilisting_extended', true);
									$_SESSION[$key] = $value;
								}
							} else {
								$key = 'ynmultilisting_predispatch_url:' . $module_video . '.index.view';
								if ($video_type == 'profile_video') {
									$value = $view->url(array(
										'controller' => 'ultimate-videos',
										'action' => 'list',
										'listing_id' => $subject_id,
									), 'ynmultilisting_extended', true);
									$_SESSION[$key] = $value;
								} else {
									$value = $view->url(array(
										'controller' => 'ultimate-videos',
										'action' => 'manage',
										'listing_id' => $subject_id,
									), 'ynmultilisting_extended', true);
									$_SESSION[$key] = $value;
								}
							}
							break;

					}
				}
			}
		}
	}	
	
	public function onItemCreateAfter($event)
	{
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$subject_id = $request -> getParam("id_subject", null);
			$type = $request -> getParam("type_parent", null);
			if ($type == 'ynmultilisting_listing')
			{
				if ($subject_id)
				{
					$type = $payload -> getType();
					switch ($type) {
						case 'video':
							$profile = $request -> getParam("profile", null);
							if(!empty($profile)){
								$video_type = 'profile_video';
							}
							else {
								$video_type = 'video';
							}
							$table = Engine_Api::_() -> getDbTable('mappings', 'ynmultilisting');
							$db = $table->getAdapter();
    						$db->beginTransaction();
								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'listing_id' => $subject_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $viewer -> getIdentity(),				       
							       'type' => $video_type,
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
							    $row -> save();
								$listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $subject_id);
								$video = Engine_Api::_() -> getItem('video', $payload -> getIdentity());
								$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
								$action = $activityApi->addActivity($viewer, $listing, 'ynmultilisting_video_create');
								if($action) {
									$activityApi->attachActivity($action, $video);
								}
								
								$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
        						$notifyApi -> addNotification($listing -> getOwner(), $video, $listing, 'ynmultilisting_listing_add_item', array('label' => 'video'));
								
							$db -> commit();	
							$ynvideo_enabled = Engine_Api::_()->hasModuleBootstrap('ynvideo');
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$key = 'ynmultilisting_predispatch_url:' . $module_video . '.index.view';
							if(!empty($profile)){
								$value = $view -> url(array(
									'controller' => 'video',
									'action' => 'list',
									'listing_id' => $subject_id,
								), 'ynmultilisting_extended', true);
								$_SESSION[$key] = $value;
							}
							else
							{
								$value = $view -> url(array(
									'controller' => 'video',
									'action' => 'manage',
									'listing_id' => $subject_id,
								), 'ynmultilisting_extended', true);
								$_SESSION[$key] = $value;
							}
							break;
                        case 'ynultimatevideo_video':
                            $profile = $request -> getParam("profile", null);
                            if(!empty($profile)){
                                $video_type = 'profile_ynultimatevideo_video';
                            }
                            else {
                                $video_type = 'ynultimatevideo_video';
                            }
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynmultilisting');
                            $db = $table->getAdapter();
                            $db->beginTransaction();
                            $row = $table -> createRow();
                            $row -> setFromArray(array(
                                'listing_id' => $subject_id,
                                'item_id' => $payload -> getIdentity(),
                                'user_id' => $viewer -> getIdentity(),
                                'type' => $video_type,
                                'creation_date' => date('Y-m-d H:i:s'),
                                'modified_date' => date('Y-m-d H:i:s'),
                            ));
                            $row -> save();
                            $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $subject_id);
                            $video = Engine_Api::_() -> getItem('ynultimatevideo_video', $payload -> getIdentity());
                            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
                            $action = $activityApi->addActivity($viewer, $listing, 'ynmultilisting_video_create');
                            if($action) {
                                $activityApi->attachActivity($action, $video);
                            }

                            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                            $notifyApi -> addNotification($listing -> getOwner(), $video, $listing, 'ynmultilisting_listing_add_item', array('label' => 'ynultimatevideo_video'));

                            $db -> commit();
                            $module_video = 'ynultimatevideo';
                            $key = 'ynmultilisting_predispatch_url:' . $module_video . '.index.view';
                            if(!empty($profile)){
                                $value = $view -> url(array(
                                    'controller' => 'ultimate-videos',
                                    'action' => 'list',
                                    'listing_id' => $subject_id,
                                ), 'ynmultilisting_extended', true);
                                $_SESSION[$key] = $value;
                            }
                            else
                            {
                                $value = $view -> url(array(
                                    'controller' => 'ultimate-videos',
                                    'action' => 'manage',
                                    'listing_id' => $subject_id,
                                ), 'ynmultilisting_extended', true);
                                $_SESSION[$key] = $value;
                            }
                            break;
					}
				}
			}
		}
        //HOANGND
        //for update comment count
        $arrTypes = array('status', 'post', 'post_self');
        if ($payload->getType() == 'activity_action' && $payload->object_type == 'ynmultilisting_listing' && in_array($payload->type, $arrTypes)) {
            $listing_id = $payload->object_id;
            $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $listing_id);
            if ($listing) {
                $listing->comment_count = $listing->comment_count + 1;
                $listing->save();
            }   
        }
	}
    
    public function onStatistics($event) {
        $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), 'COUNT(*) AS count');
        $event->addResponse($select->query()->fetchColumn(0), 'listings');
    }
    
    //HOANGND
    public function onRenderLayoutDefault($event) {
        // Arg should be an instance of Zend_View
        $view = $event->getPayload();
        $request = Zend_Controller_Front::getInstance() -> getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
		
		//for render compare bar on pages
		$isMobile = Engine_Api::_()->ynmultilisting()->isMobile();
		$isComparePage = ($module == 'ynmultilisting') && ($controller == 'compare') && ($action == 'index');
        if(!$isMobile && ($view instanceof Zend_View) && !$isComparePage && ($module == 'ynmultilisting')) {
            $view->headScript()->prependFile($view->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/render-compare-bar.js');
        }
		if($module == 'ynmultilisting') {
			$listingtype_id = Engine_Api::_() -> ynmultilisting() -> getCurrentListingTypeId();
			$script = $view -> partial('_listingTypeScript.tpl', 'ynmultilisting', array('listingtype_id' => $listingtype_id));
			$view -> headScript() -> appendScript($script);
		}
		
    }
}

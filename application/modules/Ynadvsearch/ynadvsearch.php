<?php

ini_set('max_execution_time', 3000);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('error_reporting', E_STRICT);

// Create application, translate, view
$view = Zend_Registry::get('Zend_View');
$application = Engine_Api::getInstance()->getApplication();
$application->getBootstrap()->bootstrap('translate');

Zend_Controller_Front::getInstance()->setBaseUrl(
    base64_decode($_REQUEST['static_base_url']));

// TO DO HERE
try {
    $coresearchApi = Engine_Api::_()->getApi('search', 'core');
    $searchModulesTable = new Ynadvsearch_Model_DbTable_Modules();
    $values = $_REQUEST;
    $types = array();
    $action = $values['action'];
    if ($action) {
        $type_arr = Engine_Api::_()->ynadvsearch()->getTypesOfAction($action);
        $types = array_merge($types, $type_arr);
    }    $module_str = $values['modules'];
    if ($module_str) {
        $modules = explode(',', $module_str);
        foreach ($modules as $module) {
            $types = array_merge($types, $searchModulesTable->getTypes($module));
        }
    }
    $query = $values['search'];
    $trimquery = trim($query);
    $showquery = strip_tags($trimquery);
    $item_per_page = $values['maxre'];
    // get types from selected modules in back-end
    $enabled_types = $searchModulesTable->getAllEnabledTypes();

    if (count($types) > 0) {
    }
    else {
        foreach ($enabled_types as $module_types) {
            foreach ($module_types as $module_type) {
                $types[] = $module_type;
            }
        }
    }

    $paginator = Engine_Api::_()->getApi('search', 'ynadvsearch')->getPaginator($query, $types, null, null);
    $data = array();
    $types = Engine_Api::_() -> getItemTypes();
    if(!empty($trimquery))
    {
        if (count($paginator) == 0) {
            $data[] = array(
                'key' => $query,
                'label' => translate('No results were found'),
                'type' => 'no_result_found_link',
                'url' => $view->url(
                        array(
                            'controller' => 'search',
                            'module' => 'ynadvsearch'
                        ), 'default', true) . '?is_search=1&query=' . $query
            );
        } else {
            $data[] = array(
                'key' => $query,
                'label' => 'Hidden choice',
                'url' => $view->url(
                        array(
                            'controller' => 'search',
                            'module' => 'ynadvsearch'
                        ), 'default', true) . '?is_search=1&query=' . $query,
                'type' => 'hidden_link'
            );
            if (count($paginator) < $item_per_page) {

                $temp_data = array();
                foreach ($paginator as $item) {
                    if (! $item) {
                        continue;
                    }
                    $type = $item['type'];
                    if (!in_array($type, $types))
                    {
                        continue;
                    }
                    $item = Engine_Api::_()->getItem($type, $item['id']);

                    if (! $item) {
                        continue;
                    }
                    if (! is_object($item)) {
                        continue;
                    }
                    if (! $item instanceof Core_Model_Item_Abstract) {
                        continue;
                    }
                    if (!$item->getIdentity()) {
                        continue;
                    }
                    if ($type == 'user') {
                        if ($item->verified != 1 || $item->enabled != 1 ||
                            $item->approved != 1) {
                            continue;
                        }
                    }
                    if (! isset($temp_data[$type])) {
                        $temp_data[$type] = array();
                    }
                    if ($type == 'activity_action' || $type  == 'advgroup_post' || $type == 'group_post') {
                        $label = $view -> string() -> truncate(strip_tags($item->body), 80);
                    } else {
                        $label = $item->getTitle();
                    }
                    $photo_url = $view->itemPhoto($item, 'thumb.icon');
                    $photo_url = preg_replace('/\/index.php/', '', $photo_url);

                    $temp_data[$type][] = array(
                        'photo' => $photo_url,
                        'label' => $label,
                        'url' => $item->getHref(),
                        'type' => $type,
                        'type_label' => translate(getLabelType($type))
                    );
                }

                foreach ($temp_data as $item_type) {
                    foreach ($item_type as $item) {
                        $data[] = $item;
                    }
                }
            } else {
                $temp_data = array();

                foreach ($paginator as $item) {
                    if (! $item) {
                        continue;
                    }
                    $type = $item['type'];
                    if (!in_array($type, $types))
                    {
                        continue;
                    }
                    $item = Engine_Api::_()->getItem($type, $item['id']);
                    if (! $item) {
                        continue;
                    }
                    if (! is_object($item)) {
                        continue;
                    }
                    if (! $item instanceof Core_Model_Item_Abstract) {
                        continue;
                    }
                    if (!$item->getIdentity()) {
                        continue;
                    }
                    if ($type == 'user') {
                        if ($item->verified != 1 || $item->enabled != 1 ||
                            $item->approved != 1) {
                            continue;
                        }
                    }
                    if (! isset($temp_data[$type])) {
                        $temp_data[$type] = array();
                    }
                    if ($type == 'activity_action') {
                        $label = $item->body;
                    } else {
                        $label = $item->getTitle();
                    }

                    $photo_url = $view->itemPhoto($item, 'thumb.icon');
                    $photo_url = preg_replace('/\/index.php/', '', $photo_url);
                    $temp_data[$type][] = array(
                        'photo' => $photo_url,
                        'label' => $label,
                        'url' => $item->getHref(),
                        'type' => $type,
                        'type_label' => translate(getLabelType($type))
                    );
                }

                foreach ($temp_data as $item_type) {
                    foreach ($item_type as $item) {
                        $data[] = $item;
                    }
                }

                // $itemnum = $item_per_page;
                $itemnum = ($item_per_page > count($data)) ? count($data) : $item_per_page;
                while (count($data) < $item_per_page) {
                    $itemnum ++;
                    $paginator->setItemCountPerPage($itemnum);
                    $paginator->setCurrentPageNumber(1);
                    $item = $paginator->getItem($itemnum);
                    if (! $item) {
                        break;
                    }
                    $type = $item['type'];
                    if (!in_array($type, $types))
                    {
                        continue;
                    }
                    $item = Engine_Api::_()->getItem($type, $item['id']);
                    if (! $item) {
                        continue;
                    }
                    if (!$item->getIdentity()) {
                        continue;
                    }
                    if ($type == 'user') {
                        if ($item->verified != 1 || $item->enabled != 1 ||
                            $item->approved != 1) {
                            continue;
                        }
                    }
                    if (! isset($temp_data[$type])) {
                        $temp_data[$type] = array();
                    }
                    if ($type == 'activity_action') {
                        $label = $item->body;
                    } else {
                        $label = $item->getTitle();
                    }

                    $photo_url = $view->itemPhoto($item, 'thumb.icon');
                    $photo_url = preg_replace('/\/index.php/', '', $photo_url);
                    $data[] = array(
                        'photo' => $photo_url,
                        'label' => $label,
                        'url' => $item->getHref(),
                        'type' => $type,
                        'type_label' => translate(getLabelType($type))
                    );
                }
            }
        }
    }
    $tabs = getMenu();
    foreach ($tabs as $tab)
    {
        if($tab)
        {
            if(!empty($trimquery))
            {
                $tab_url =  $view->url($tab['params'], $tab['route']);
                $data[] = array(
                    'key' => $query,
                    'label' => translate('Find all ').translate($tab['label']).translate(' named ').'"'.$showquery.'"',
                    'photo' => $tab['icon'],
                    'type' => 'menu',
                    'url' => $tab_url.'/query/'.$query,
                );
            }
            else
            {
                $tab_url =  $view->url($tab['params'], $tab['route']);
                $data[] = array(
                    'key' => $query,
                    'label' => translate('Find in ').translate($tab['label']),
                    'photo' => $tab['icon'],
                    'type' => 'menu',
                    'url' => $tab_url,
                );
            }
        }
    }
    if(!empty($trimquery))
    {
        $data[] = array(
            'key' => $query,
            'label' => translate('Search more results for ').'"'.$showquery.'"',
            'url' => $view->url(
                    array(
                        'controller' => 'search',
                        'module' => 'ynadvsearch'
                    ), 'default', true) . '?is_search=1&query=' . $query,
            'type' => 'see_more_link'
        );
    }
    echo Zend_Json::encode($data);

} catch (Exception $e) {
    echo $e;
}

function getLabelType ($type)
{
    return strtoupper('ITEM_TYPE_' . $type);
}

function getMenu()
{
    $tabs = array();
    $menu = new Ynadvsearch_Plugin_Searchs();

    $table_contentType = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
    $content_types =$table_contentType->fetchAll($table_contentType -> getContentTypesSelect());
    foreach($content_types as $item)
    {
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
                $aSocialmusicButton = $menu -> onMenuInitialize_YnadvsearchSocialmusic();
                array_push($tabs, $aSocialmusicButton);
                break;
            case 'ynultimatevideo_video':
                $aUltimatevideoButton = $menu -> onMenuInitialize_YnadvsearchUltimateVideo();
                array_push($tabs, $aUltimatevideoButton);
                break;
            case 'ynvideochannel_video':
                $aVideochannelButton = $menu -> onMenuInitialize_YnadvsearchVideochannel();
                array_push($tabs, $aVideochannelButton);
                break;
            default:
                break;
        }
    }
    return $tabs;
}
function translate($key)
{
    $viewer   = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
        $locale = $viewer->language;
    } else if( !empty($_COOKIE['en4_language']) && !empty($_COOKIE['en4_locale']) ) {
        $locale = $_COOKIE['en4_language'];
    } else if( !empty($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ) {
        $l = new Zend_Locale(Zend_Locale::BROWSER);
        $locale = $l->getLanguage();
    } else {
        $locale = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
    }
    $translate = Zend_Registry::get('Zend_Translate');
    $value = $translate->translate($key, $locale);
    return $value;
}

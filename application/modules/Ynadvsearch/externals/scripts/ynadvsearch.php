<?php

define('DEBUG', true);

ini_set('max_execution_time', 3000);

if(DEBUG) {
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	ini_set('error_reporting', -1);
}else{
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	ini_set('error_reporting', E_STRICT);

}

define('_ENGINE_CUR_PATH', dirname(__FILE__));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
// Config
if(!defined('_ENGINE_R_MAIN')) {
	define('_ENGINE_R_REWRITE', true);
	define('_ENGINE_R_CONF', true);
	define('_ENGINE_R_INIT', true);
	//$indexFile = dirname(dirname(_ENGINE_CUR_PATH)) . DIRECTORY_SEPARATOR . 'index.php';
	$indexFile = APPLICATION_PATH. '/application/index.php';
	//exit($indexFile);

	include_once $indexFile;
}

// Create application, bootstrap, and run
$application = Engine_Api::getInstance()->getApplication();

$application -> getBootstrap() -> bootstrap('frontcontroller');
$application -> getBootstrap() -> bootstrap('cache');
$application -> getBootstrap() -> bootstrap('db');
$application -> getBootstrap() -> bootstrap('log');
$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('frontcontrollermodules');
$application -> getBootstrap() -> bootstrap('session');
$application -> getBootstrap() -> bootstrap('manifest');
$application -> getBootstrap() -> bootstrap('router');
$application -> getBootstrap() -> bootstrap('view');
$application -> getBootstrap() -> bootstrap('layout');
$application -> getBootstrap() -> bootstrap('modules');
$application -> getBootstrap() -> bootstrap('locale');
$application -> getBootstrap() -> bootstrap('hooks');


$view = Zend_Registry::get('Zend_View');
//$view->layout()->staticBaseUrl =  $_REQUEST['static_base_url'];
$view->layout()->staticBaseUrl =  base64_decode($_REQUEST['static_base_url']);
Zend_Controller_Front::getInstance()->setBaseUrl(base64_decode($_REQUEST['static_base_url']));
// TO DO HERE
try {
	$coresearchApi = Engine_Api::_()->getApi('search', 'core');
	$values = $_REQUEST;

	$query = $values['search'];
	$trimquery = trim($query);
	if ($trimquery == null)
	exit;
	$item_per_page = $values['maxre'];

	$other = array();
	$user = array();
	$group = array();
	$page = array();
	$directory = array();

	$user[] = $values['user_name'];

	//$user[] = $values['user_description'];
	$group[] = $values['group_name'];
	$group[] = $values['group_description'];
	$group[] = $values['group_tag'];
	//$group[] = $values['group_category'];
	$page[] = $values['page_name'];
	$page[] = $values['page_description'];
	//$page[] = $values['page_city'];
	//$page[] = $values['page_state'];
	//$page[] = $values['page_phone'];
	$other[] = $values['other_name'];
	$other[] = $values['other_description'];
	$directory[] = $values['directory_name'];
	$directory[] = $values['directory_description'];


	$paginator = getPaginator($query,null,$user,$group,$page,$directory,$other);

	$paginator->setItemCountPerPage($item_per_page);
	$data = array();
	$availableTypes = Engine_Api::_()->getItemTypes();
	$availableTypes = array_diff ( $availableTypes, array (
			"groupbuy_category",
			"groupbuy_location",
			"groupbuy_param",
			"groupbuy_album",
			"groupbuy_photo",
			"music_playlist_song",
			"ynshare",
			"ynsharebutton",
			"classified_album"
	) );
	if(count($paginator) == 0){

		$data[] = array(
    			'key'=> $query,
	    		'label'=>$view->translate('No results were found'),
	    		'type'=>'no_result_found_link',
    			'url'=>$view->url(array('controller' => 'search','module'=>'ynadvsearch'), 'default', true) .'?is_search=1&query='.$query,
		);
	}
	elseif (count($paginator) < $item_per_page){

		$temp_data = array();
		foreach($paginator as $item){
			if (!$item){
				continue;
			}
			$type = $item['type'];

			if (!in_array($type, $availableTypes)) {
				continue;
			}
			$item = Engine_Api::_()->getItem($type, $item['id']);

			if(!isset($temp_data[$type])){
				$temp_data[$type] = array();
			}
			if (!$item) {
				continue;
			}
			if (!is_object($item)){
				continue;
			}
			if (($item->getTitle() == "<i>Deleted Member</i>")) {
				continue;
			}
			if (!$item instanceof Core_Model_Item_Abstract) {
				continue;
			}
			if ($type == 'activity_action') {
				$label = $item->body;
			} else {
				$label = $item->getTitle();
			}
			$photo_url = $view->itemPhoto($item, 'thumb.icon');
			$photo_url = preg_replace('/\/index.php/', '', $photo_url);

			$temp_data[$type][]= array(
	    		'photo'=> $photo_url,
	    		'label'=>$label,
	    		'url'=>$item->getHref(),
	    		'type'=>$type,
	    		'type_label'=>$view->translate(getLabelType($type))
			);
		}

		foreach($temp_data as $item_type){
			foreach($item_type as $item){
				$data[] = $item;
			}
		}
	}
	else {
		$temp_data = array();

		foreach($paginator as $item){
			if (!$item) {
				continue;
			}
			$type = $item['type'];

			if (!in_array($type, $availableTypes)) {
				continue;
			}
			$item = Engine_Api::_()->getItem($type, $item['id']);
			if(!isset($temp_data[$type])){
				$temp_data[$type] = array();
			}
			if (!$item){
				continue;
			}
			if (!is_object($item)){
				continue;
			}
			if (($item->getTitle() == "<i>Deleted Member</i>")) {
				continue;
			}
			if (!$item instanceof Core_Model_Item_Abstract) {
				continue;
			}
			if ($type == 'activity_action') {
				$label = $item->body;
			} else {
				$label = $item->getTitle();
			}

			$photo_url = $view->itemPhoto($item, 'thumb.icon');
			$photo_url = preg_replace('/\/index.php/', '', $photo_url);
			$temp_data[$type][]= array(
	    		'photo'=> $photo_url,
	    		'label'=>$label,
	    		'url'=>$item->getHref(),
	    		'type'=>$type,
	    		'type_label'=>$view->translate(getLabelType($type))
			);
		}

		foreach($temp_data as $item_type){
			foreach($item_type as $item){
				$data[] = $item;
			}
		}

		//$itemnum = $item_per_page;
		$itemnum = ($item_per_page > count($data))?count($data):$item_per_page;

		while(count($data) < $item_per_page) {

			$itemnum++;
			$paginator->setItemCountPerPage($itemnum);
			$paginator->setCurrentPageNumber(1);
			$item = $paginator->getItem($itemnum);
			if (!$item) {
				break;
			}
			$type = $item['type'];
			if (!in_array($type, $availableTypes)) {
				continue;
			}
			$item = Engine_Api::_()->getItem($type, $item['id']);
			if (!$item){
				continue;
			}
			if (($item->getTitle() == "<i>Deleted Member</i>")) {
				continue;
			}
			if(!isset($temp_data[$type])){
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
	    		'photo'=> $photo_url,
	    		'label'=>$label,
	    		'url'=>$item->getHref(),
	    		'type'=>$type,
	    		'type_label'=>$view->translate(getLabelType($type))
			);

		}
	}


	$data[]= array(
	    		'key'=> $query,
	    		'label'=>$view->translate('Search more results',$query),
	//'label2'=>$this->view->translate('Displaying top %s results',$item_per_page),
	    		'url'=>$view->url(array('controller' => 'search','module'=>'ynadvsearch'), 'default', true) .'?is_search=1&query='.$query,
	    		'type'=>'see_more_link'
	    		);

	    		echo Zend_Json::encode($data);
}
catch(Exception $e){
	throw $e;
}
function getPaginator($text, $type = null, $user,$group,$page,$directory,$other) {
	$select = getSelect ($text, $type, $user,$group,$page,$directory,$other);
	return Zend_Paginator::factory ( $select );
}
function getSelect($text, $type = null, $user,$group,$page,$directory,$other)
{
	$is_user = 	in_array('1',$user);
	$is_group = in_array('1',$group);
	$is_page = 	in_array('1',$page);
	$is_directory = in_array('1',$directory);
	$is_other = in_array('1',$other);


	$table = Engine_Api::_()->getDbtable('search', 'core');
	$table_name = $table->info('name');

	//$user_membership_tbl = Engine_Api::_ ()->getDbtable ( 'membership', 'user' );
	//$user_membership_tbl_name = $user_membership_tbl->info('name');
	//$network_membership_tbl = Engine_Api::_ ()->getDbtable ( 'membership', 'network' );
	//$network_membership_tbl_name = $network_membership_tbl->info('name');

	//if ($viewer->getIdentity()) {
	//	$viewer_network = $network_membership_tbl->fetchRow('user_id = '.$viewer->getIdentity());
	//}
	//else  {
	//	$viewer_network = $network_membership_tbl->fetchRow('user_id = '. '1');
		//}
		$db = $table->getAdapter();
		$select = $db->select();
		$select->from($table_name);

		if (!$is_user && !$is_group && !$is_page && !$is_directory && !$is_other)
		{

			$select->where("$table_name.id < 0");
			return $select;
		}

		/*if ($viewer->getIdentity())
		 $select->joinLeft($user_membership_tbl_name,"$user_membership_tbl_name.user_id = $table_name.id AND $table_name.type='user' AND $user_membership_tbl_name.resource_id =".$viewer->user_id, array('is_friend'=>'IF('.$user_membership_tbl_name.'.user_id is NULL,1, 999)'));
		 else
		 $select->joinLeft($user_membership_tbl_name,"$user_membership_tbl_name.user_id = $table_name.id AND $table_name.type='user' AND $user_membership_tbl_name.resource_id =1", array('is_friend'=>'IF('.$user_membership_tbl_name.'.user_id is NULL,1, 999)'));
		 if($viewer_network){
			$select->joinLeft($network_membership_tbl_name,"$network_membership_tbl_name.user_id = $table_name.id AND $table_name.type='user' AND $network_membership_tbl_name.resource_id =".$viewer_network->resource_id,array('same_network'=>'IF('.$network_membership_tbl_name.'.user_id is NULL,1, 999)'));
			}*/


		//$table_pages =  Engine_Api::_()->getDbtable('pages', 'page');
		//$table_page_name = $table_pages->info('name');
		//$select->joinLeft($table_page_name,"$table_page_name.page_id = $table_name.id",array("$table_page_name.state","$table_page_name.city","$table_page_name.phone"));



		$text = '%'.preg_replace('/\s+/', '%',$text).'%';
		$select->where("$table_name.id < 0");
		if ($is_user)
		{
			if ($user[0] == '1')
			{
				$select->orWhere("$table_name.type = 'user' AND $table_name.title like N?",$text);
			}

		}
		if ($is_group)
		{
			if ($group[0] == '1')
			{
				$select->orWhere("$table_name.type = 'group' AND $table_name.title like ?",$text);
			}

			if ($group[1] == '1')
			{
				$select->orWhere("$table_name.type = 'group' AND $table_name.description like ?",$text);
			}

			if($group[2] == '1')
			{
				$select->orWhere("$table_name.type = 'group' AND $table_name.keywords like ?",$text);
			}
			/*
				if ($group[2] == '1')
				{

				$table_group_category =  Engine_Api::_()->getDbtable('categories', 'group');
				$result = $table_group_category->fetchAll($table_group_category->select()->where("title LIKE ?",$text));

				$categories = array();
				foreach($result as $category)
				{
				$categories[] = $category['category_id'];
				}
				if (!empty($categories))
				{
				$table_group =  Engine_Api::_()->getDbtable('groups', 'group');
				$table_group_name = $table_group->info('name');
				$select->joinLeft($table_group_name,"$table_group_name.group_id = $table_name.id",array("$table_group_name.category_id"));

				$select->orWhere("$table_group_name.category_id IN(?)", $categories);
				}
				}
				*/
		}
		if ($is_page)
		{
			//$table_pages =  Engine_Api::_()->getDbtable('pages', 'page');
			//$table_page_name = $table_pages->info('name');
			//$select->joinLeft($table_page_name,"$table_page_name.page_id = $table_name.id",array("$table_page_name.state","$table_page_name.city","$table_page_name.phone"));
			if ($page[0] == '1')
			{
				//$select->orWhere("$table_name.type = 'page' AND $table_page_name.title like ?",$text);
				$select->orWhere("$table_name.type = 'page' AND $table_name.title like ?",$text);
			}
			if ($page[1] == '1')
			{
				//$select->orWhere("$table_name.type = 'page' AND $table_page_name.description like ?",$text);
				$select->orWhere("$table_name.type = 'page' AND $table_name.description like ?",$text);
			}
			/*
				if ($page[1] == '1')
				{
				$select->orWhere("$table_name.type = 'page' AND $table_page_name.city like ?",$text);
				}
				if ($page[2] == '1')
				{
				$select->orWhere("$table_name.type = 'page' AND $table_page_name.state like ?",$text);
				}
				if ($page[3] == '1')
				{
				$select->orWhere("$table_name.type = 'page' AND $table_page_name.phone like ?",$text.'%');
				}
				*/
		}
		if ($is_directory)
		{

			if ($directory[0] == '1')
			{
				$select->orWhere("$table_name.type = 'directory' AND $table_name.title like ?",$text);
			}
			if ($directory[1] == '1')
			{
				$select->orWhere("$table_name.type = 'directory' AND $table_name.description like ?",$text);
			}

		}

		if ($is_other)
		{
			$array = array();
			$array[] = 'group';
			$array[] = 'page';
			$array[] = 'user';
			if ($other[0] == '1')
			{
				$select->orWhere("$table_name.type <> 'user' AND  $table_name.type <> 'group' AND $table_name.type <> 'page' AND $table_name.type <> 'directory' AND $table_name.title like ?",$text);


			}
			if ($other[1] == '1')
			{
				$select->orWhere("$table_name.type <> 'user' AND  $table_name.type <> 'group' AND $table_name.type <> 'page' AND $table_name.type <> 'directory' AND $table_name.description like ?",$text);
			}
		}


		//$select->where("$table_name.title LIKE ?",$text);
		//$select->where("$table_name.title LIKE ?",$text);
		/*if($viewer_network) {
		$select->order(array(
		'is_friend DESC','same_network DESC',
		new Zend_Db_Expr($db->quoteInto('MATCH('.$table_name.'.`title`, '.$table_name.'.`description`, '.$table_name.'.`keywords`, '.$table_name.'.`hidden`) AGAINST (?) DESC', $text))
		));
		}
		// remove order by same_network
		else {
		$select->order(array(
		'is_friend DESC',
		new Zend_Db_Expr($db->quoteInto('MATCH('.$table_name.'.`title`, '.$table_name.'.`description`, '.$table_name.'.`keywords`, '.$table_name.'.`hidden`) AGAINST (?) DESC', $text))
		));
		}*/
		// Filter by item types

		//$availableTypes = Engine_Api::_()->getItemTypes();

		$availTypes[] = 'user';
		$availTypes[] = 'group';
		$availTypes[] = 'page';

		if( $type && in_array($type, $availTypes) ) {

			$select->where('type = ?', $type);
		} else {
			//$select->where('type IN(?)', $availTypes);
		}

		//Zend_Registry::get('Zend_Log')->log(echo $select, Zend_Log::DEBUG);

		return $select;
	}

	function getLabelType($type){
		return strtoupper('ITEM_TYPE_' . $type);
	}

	function checkModuleExist($module_name)
	{
		// check module exist
		$modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
		$mselect = $modulesTable->select()
		->where('enabled = ?', 1)
		->where('name  = ?', $module_name);
		if(count($modulesTable->fetchAll($mselect)) <= 0)
		{
			return false;
		}
		return true;
	}
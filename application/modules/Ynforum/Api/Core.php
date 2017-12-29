<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Api_Core extends Core_Api_Abstract {
	public function getMaxCategoryOrder() {
		$table = Engine_Api::_()->getItemTable('ynforum_category');
		$select = new Zend_Db_Select($table->getAdapter());
		$select = $select->from($table->info('name'), new Zend_Db_Expr('MAX(`order`) as max_order'));
		$data = $select->query()->fetch();
		$order = (int) @$data['max_order'];
		return $order;
	}

	public function getItemTable($type) {
		if ($type == 'forum_post') {
			return Engine_Loader::getInstance()->load('Ynforum_Model_DbTable_Posts');
		} else if ($type == 'forum_topic') {
			return Engine_Loader::getInstance()->load('Ynforum_Model_DbTable_Topics');
		} else if ($type == 'forum_forum') {
			return Engine_Loader::getInstance()->load('Ynforum_Model_DbTable_Forums');
		} else {
			$class = Engine_Api::_()->getItemTableClass($type);
			return Engine_Api::_()->loadClass($class);
		}
	}
	/**
	 *
	 * Start rate topic
	 */
	public function checkTopicRating($topic_id = 0, $viewer_id = 0)
	{
		$topicRatingTbl = Engine_Api::_ ()->getDbTable ( 'topicRatings', 'ynforum' );
		$topicRatingTblName = $topicRatingTbl->info ( 'name' );
		$select = $topicRatingTbl->select()->from($topicRatingTblName)->where("topic_id = ?",$topic_id)
		->where('poster_id = ?', $viewer_id);
		$rate = $topicRatingTbl->fetchRow($select);
		if($rate)
		{
			return false;
		}
		else
			return true;

	}
	public function getAvgTopicRating($topic_id = 0)
	{
		$topicRatingTbl = Engine_Api::_ ()->getDbTable ( 'topicRatings', 'ynforum' );
		$topicRatingTblName = $topicRatingTbl->info ( 'name' );
		$select = $topicRatingTbl->select()->from($topicRatingTblName, new Zend_Db_Expr("SUM(rate_number)/Count(*) as avg"))->where("topic_id = ?",$topic_id);
		$avg = $topicRatingTbl->fetchRow($select);
		if($avg['avg'])
		{
			return $avg['avg'];
		}
		else
			return 0;

	}
	public function getTotalRatingTopic($topic_id = 0)
	{
		$topicRatingTbl = Engine_Api::_ ()->getDbTable ( 'topicRatings', 'ynforum' );
		$topicRatingTblName = $topicRatingTbl->info ( 'name' );
		$select = $topicRatingTbl->select()->from($topicRatingTblName, new Zend_Db_Expr("Count(*) as total"))->where("topic_id = ?",$topic_id);
		$avg = $topicRatingTbl->fetchRow($select);
		if($avg['total'])
		{
			return $avg['total'];
		}
		else
			return 0;

	}
	/**
	 *
	 * end rate topic
	 */
	/*
	 * check attachment
	*
	*/
	public function checkAttach($topic)
	{
		$table = Engine_Api::_()->getItemTable('ynforum_post');
		$select = $topic->getChildrenSelect('ynforum_post', array('order' => 'post_id ASC'));
		$posts = $table->fetchAll($select);
		foreach($posts as $post)
		{
			if(count($post->getAttachments()) > 0)
				return true;
		}
		return false;
	}
	public function getToDaySearch($day)
	{
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		$oldTz = date_default_timezone_get();
		//user time zone
		date_default_timezone_set($user_tz);
		$d_temp = strtotime($day);
		if ($d_temp == false)
		{
			return null;
		}
		$toDateObject = new Zend_Date(strtotime($day));
		$toDateObject -> add('1', Zend_Date::DAY);
		$toDateObject -> sub('1', Zend_Date::SECOND);
		date_default_timezone_set($oldTz);
		$toDateObject -> setTimezone(date_default_timezone_get());
		
		return $todate = $toDateObject -> get('yyyy-MM-dd HH:mm:ss');
	}

	public function getFromDaySearch($day)
	{
		$day = $day . " 00:00:00";
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;

		}
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($user_tz);
		$start = strtotime($day);
		date_default_timezone_set($oldTz);
		$fromdate = date('Y-m-d H:i:s', $start);
		return $fromdate;
	}

	// LUANND START
	public function createPhoto($params, $file)
	{
		 
		if( $file instanceof Storage_Model_File )
		{
			$params['file_id'] = $file->getIdentity();
		}

		else
		{
			// Get image info and resize
			$name = basename($file['tmp_name']);
			$path = dirname($file['tmp_name']);
			$extension = ltrim(strrchr($file['name'], '.'), '.');

			$mainName = $path.'/m_'.$name . '.' . $extension;
			$profileName = $path.'/p_'.$name . '.' . $extension;
			$thumbName = $path.'/in_'.$name . '.' . $extension;
			$iSquare = $path.'/is_'.$name . '.' . $extension;

			$image = Engine_Image::factory();
			$image->open($file['tmp_name'])
			->resize(720, 720)
			->write($mainName)
			->destroy();
			// Resize image (profile)
			$image = Engine_Image::factory();
			$image->open($file['tmp_name'])
			->resize(240, 240)
			->write($profileName)
			->destroy();
			$image = Engine_Image::factory();
			$image->open($file['tmp_name'])
			->resize(190,190)
			->write($thumbName)
			->destroy();

			$image = Engine_Image::factory();
			$image->open($file['tmp_name'])
			->resize(48, 48)
			->write($iSquare)
			->destroy();

			// Store photos
			$photo_params = array(
					'parent_id' => $params['post_id'],
					'parent_type' => 'forum_post',
			);
			$photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
			$profileFile = Engine_Api::_()->storage()->create($profileName, $photo_params);
			$thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
			$iSquare = Engine_Api::_()->storage()->create($iSquare, $photo_params);
			$photoFile->bridge($profileFile, 'thumb.profile');
			$photoFile->bridge($iSquare, 'thumb.icon');
			$photoFile->bridge($thumbFile, 'thumb.normal');
			$params['file_id'] = $photoFile->file_id; // This might be wrong			

			// Remove temp files
			@unlink($mainName);
			@unlink($profileName);
			@unlink($thumbName);
			@unlink($iSquare);

		}

		$row = Engine_Api::_()->getItemTable('ynforum_postphoto')->createRow();
		$row->setFromArray($params);
		$row->save();
		return $row;
	}	
	// LUANND END
}
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_CategoryController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (0 !== ($category_id = (int)$this -> _getParam('category_id')) && null !== ($category = Engine_Api::_() -> getItem('ynforum_category', $category_id)))
		{
			if (!Engine_Api::_() -> core() -> hasSubject($category -> getType()))
			{
				Engine_Api::_() -> core() -> setSubject($category);
			}
		}
		else
		if (0 !== ($category_id = (int)$this -> _getParam('category_id')) && null !== ($category = Engine_Api::_() -> getItem('ynforum_category', $category_id)))
		{
			Engine_Api::_() -> core() -> setSubject($category);
		}
	}

	public function indexAction()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('forum', null, 'view') -> isValid())
		{
			return;
		}
		
		if (!$this -> _helper -> requireSubject('ynforum_category') -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$category = Engine_Api::_() -> core() -> getSubject();
		if(!Engine_Api::_()->authorization()->isAllowed($category, $viewer, 'forumcat.view'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		$categoryTable = Engine_Api::_() -> getItemTable('ynforum_category');
		$postTable = Engine_Api::_() -> getItemTable('ynforum_post');
		$topicTable = Engine_Api::_() -> getItemTable('ynforum_topic');
		$forumTable = Engine_Api::_() -> getItemTable('ynforum_forum');
		$forums = $forumTable -> fetchAllAndOrderByHierachy();
		foreach ($forums as $forumCategory)
		{
			foreach ($forumCategory as $forum)
			{
				$lastPostIds[] = $forum -> lastpost_id;
			}
		}
		$lastTopicIds = array();
		$lastPosts = array();
		foreach ($postTable->find($lastPostIds) as $post)
		{
			$lastPosts[$post -> getIdentity()] = $post;
			$lastTopicIds[] = $post -> topic_id;
		}

		$lastTopics = array();
		foreach ($topicTable->find($lastTopicIds) as $lastTopic)
		{
			$lastTopics[$lastTopic -> getIdentity()] = $lastTopic;
		}

		$cats = $categoryTable -> getCategoriesOrderByLevel();
		$categories = array();
		foreach ($cats as $cat)
		{
			$categories[$cat -> getIdentity()] = $cat;
		}

		$this -> view -> lastTopics = $lastTopics;
		$this -> view -> lastPosts = $lastPosts;
		$this -> view -> forums = $forums;
		$this -> view -> categories = $categories;
		$this -> view -> category = $category;

		$curCat = $category;
		$linkedCategories = array($category);
		while ($curCat -> parent_category_id != null && $curCat -> parent_category_id != 0)
		{
			$curCat = $categories[$curCat -> parent_category_id];
			$linkedCategories[] = $curCat;
		}
		$this -> view -> linkedCategories = $linkedCategories;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> check_permission = $check_permission = $settings -> getSetting('forum_permission_see_forum', 0);
	}

}

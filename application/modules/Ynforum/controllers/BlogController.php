<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     LuanND
 */
class Ynforum_BlogController extends Core_Controller_Action_Standard {

	public function init() {
		if (0 !== ($post_id = (int)$this -> _getParam('post_id')) && null !== ($post = Engine_Api::_() -> getItem('ynforum_post', $post_id)) && $post instanceof Ynforum_Model_Post) {
			Engine_Api::_() -> core() -> setSubject($post);
		}
	}

	public function createAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer->getIdentity()) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		$post = Engine_Api::_() -> core() -> getSubject('forum_post');
		$arrPost = array(
			'title' => $post->title,
			'body'  => $post->body,
			
		);
		
		// Process
		$table = Engine_Api::_() -> getItemTable('blog');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create blog
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$values = array_merge($arrPost, array('owner_type' => $viewer -> getType(), 'owner_id' => $viewer -> getIdentity()));
			if(empty($values['title']))
			{
				$values['title'] = $this -> view -> translate("Untitled");
			}
			$blog = $table -> createRow();
			$blog -> setFromArray($values);
			$blog -> save();
			// Commit
			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		return $this -> _helper -> redirector -> gotoRoute(array('action'=>'edit','blog_id'=> $blog -> getIdentity()), 'blog_specific', true);
		
		
	}

}

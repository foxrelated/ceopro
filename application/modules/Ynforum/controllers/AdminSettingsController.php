<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynforum_admin_main', array(), 'ynforum_admin_main_settings');

		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> form = $form = new Ynforum_Form_Admin_Settings_Global();

		$form -> bbcode -> setValue($settings -> getSetting('forum_bbcode', 1));
		$form -> html -> setValue($settings -> getSetting('forum_html', 0));
		$form -> topic_length -> setValue($settings -> getSetting('forum_topic_pagelength'));
		$form -> forum_length -> setValue($settings -> getSetting('forum_forum_pagelength'));
		$form -> approve_topics -> setValue($settings -> getSetting('forum_approve_topic', 0));
		$form -> permission_see_forum -> setValue($settings -> getSetting('forum_permission_see_forum', 0));
		$form -> detect_link -> setValue($settings -> getSetting('forum_detect_link', 1));
		//$form->max_photo_per_post->setValue($settings->getSetting('forum_max_photo_per_post', 5));
		$form -> maxFileSizeAttach -> setValue($settings -> getSetting('forum_maxFileSizeAttach', 10000));
		$form -> fileType -> setValue($settings -> getSetting('forum_fileType', 'gif, png, jpg, jpeg, doc, docx, xls, xlsx, ppt, pptx, pdf, zip, rar, mp3, wav, mpeg, mpg, mpe, mov, avi, tar, tgz, tar.gz, txt, html, php, tpl'));
		$form -> hottest_topic_length -> setValue($settings -> getSetting('forum_hottest_topic_length', 10));
		$form -> most_viewed_topic_length -> setValue($settings -> getSetting('forum_most_viewed_topic_length', 10));
		$form -> newest_topic_length -> setValue($settings -> getSetting('forum_newest_topic_length', 10));
		$form -> top_thanked_user_length -> setValue($settings -> getSetting('forum_top_thanked_user_length', 10));
		$form -> hot_topic_posts -> setValue($settings -> getSetting('forum_minimum_post_of_hot_topic', 25));

		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			$settings -> setSetting('forum_topic_pagelength', $values['topic_length']);
			$settings -> setSetting('forum_forum_pagelength', $values['forum_length']);
			$settings -> setSetting('forum_bbcode', $values['bbcode']);
			$settings -> setSetting('forum_html', $values['html']);
			$settings -> setSetting('forum_approve_topic', $values['approve_topics']);
			$settings -> setSetting('forum_permission_see_forum', $values['permission_see_forum']);
			$settings -> setSetting('forum_detect_link', $values['detect_link']);
			//$settings->setSetting('forum_max_photo_per_post', $values['max_photo_per_post']);
			$settings -> setSetting('forum_maxFileSizeAttach', $values['maxFileSizeAttach']);
			$settings -> setSetting('forum_fileType', $values['fileType']);
			$settings -> setSetting('forum_hottest_topic_length', $values['hottest_topic_length']);
			$settings -> setSetting('forum_most_viewed_topic_length', $values['most_viewed_topic_length']);
			$settings -> setSetting('forum_newest_topic_length', $values['newest_topic_length']);
			$settings -> setSetting('forum_top_thanked_user_length', $values['top_thanked_user_length']);
			$settings -> setSetting('forum_minimum_post_of_hot_topic', $values['hot_topic_posts']);

			$form -> addNotice('Your changes have been saved.');
		}
	}

}

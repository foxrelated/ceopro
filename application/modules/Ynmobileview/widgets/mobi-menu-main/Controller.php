<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_Widget_MobiMenuMainController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Menu Main
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'ynmobileview') -> getNavigation('core_main');
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		$require_check = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.general.browse', 1);

		if (!$require_check && !$viewer -> getIdentity())
		{
			$navigation -> removePage($navigation -> findOneBy('route', 'user_general'));
		}

		// Search
		$require_check = Engine_Api::_() -> getApi('settings', 'core') -> core_general_search;
		if (!$require_check)
		{
			if ($viewer -> getIdentity())
			{
				$this -> view -> search_check = true;
			}
			else
			{
				$this -> view -> search_check = false;
			}
		}
		else
			$this -> view -> search_check = true;
		// Languages
		$translate = Zend_Registry::get('Zend_Translate');
		$languageList = $translate -> getList();

		// Prepare default langauge
		$defaultLanguage = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.locale.locale', 'en');
		if (!in_array($defaultLanguage, $languageList))
		{
			if ($defaultLanguage == 'auto' && isset($languageList['en']))
			{
				$defaultLanguage = 'en';
			}
			else
			{
				$defaultLanguage = null;
			}
		}

		// Prepare language name list
		$languageNameList = array();
		$languageDataList = Zend_Locale_Data::getList(null, 'language');
		$territoryDataList = Zend_Locale_Data::getList(null, 'territory');

		foreach ($languageList as $localeCode)
		{
			$languageNameList[$localeCode] = Zend_Locale::getTranslation($localeCode, 'language', $localeCode);
			if (empty($languageNameList[$localeCode]))
			{
				list($locale, $territory) = explode('_', $localeCode);
				$languageNameList[$localeCode] = "{$territoryDataList[$territory]} {$languageDataList[$locale]}";
			}
		}
		$languageNameList = array_merge(array($defaultLanguage => $defaultLanguage), $languageNameList);
		$this -> view -> languageNameList = $languageNameList;
	}

	public function getCacheKey()
	{
	}

}

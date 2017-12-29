<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Phpfoximporter
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    UserJanrain.php 2015-07-30 00:00:00Z john $
 * @author     John
 */
class Install_Import_Phpfox_UserJanrain extends Install_Import_Phpfox_Abstract
{

  protected $_fromTable = '';
  protected $_toTable = '';
  protected $_settingEnable = null;

  protected function _initPre()
  {
    $this->_fromTable = $this->getFromPrefix() . 'janrain';
    $this->_toTable = 'engine4_user_janrain';

    //GET JANRAIN VALUE
    $value = $this->getToDb()->select()
      ->from('engine4_core_settings', 'value')
      ->where('name = ?', 'core.janrain.enable')
      ->query()
      ->fetchColumn();
    $this->_settingEnable = $value;
  }

  protected function _translateRow(array $data, $key = null)
  {

    //RETURN FALSE IF JANRAIN LOGIN DISABLED
    if( empty($this->_settingEnable) || $this->_settingEnable == 'none' ) {
      return false;
    }

    //MAKING ARRAY FOR INSERTION OF JANRAIN USER ID
    $newData = array();
    $newData['user_id'] = $data['user_id'];
    $newData['identifier'] = $data['identifier'];

    return $newData;
  }
}

/*
 * CREATE TABLE IF NOT EXISTS `phpfox_janrain` (
  `user_id` int(10) unsigned NOT NULL,
  `identifier` char(32) NOT NULL,
  `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
 */

/*
 * CREATE TABLE IF NOT EXISTS `engine4_user_janrain` (
  `user_id` int(11) unsigned NOT NULL,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 */
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Photos.php 9071 2011-07-20 23:43:30Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Model_DbTable_Photos extends Engine_Db_Table {
    protected $_rowClass = 'Ynforum_Model_Photo';
    protected $_name = 'forum_photos';
}

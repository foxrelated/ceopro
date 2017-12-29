<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2016 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Item.php 9747 2016-12-06 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2016 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Activity_View_Helper_GetActionContent extends Zend_View_Helper_Abstract
{

  public function getActionContent(Activity_Model_Action $action, $similarActivities = array() )
  {
    $similarFeedType = $action->type . '_' . $action->getObject()->getGuid();
    if( empty($similarActivities) || !isset($similarActivities[$similarFeedType]) ) {
      return $action->getContent();
    }

    $actionSubject = $action->getSubject();
    $otherItems = array();
    foreach( $similarActivities[$similarFeedType] as $activity ) {
      $activitySubject = $activity->getSubject();
      if( $activity->getSubject() === $actionSubject ) {
        continue;
      }
      $otherItems[$activitySubject->getGuid()] = $activitySubject;
    }

    return $action->getContent($otherItems);
  }
}

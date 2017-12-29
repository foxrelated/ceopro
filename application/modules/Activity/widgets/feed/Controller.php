<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9806 2012-10-30 23:54:12Z matthew $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Activity_Widget_FeedController extends Engine_Content_Widget_Abstract
{
  private $_blockedUserIds = array();

  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
        return $this->setNoRender();
      }
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

    // Get some options
    $this->view->feedOnly         = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length           = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit  = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

    $this->view->updateSettings   = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes     = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
    $this->view->viewAllComments  = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $this->view->getUpdate        = $request->getParam('getUpdate');
    $this->view->checkUpdate      = $request->getParam('checkUpdate');
    $this->view->action_id        = (int) $request->getParam('action_id');
    $this->view->post_failed      = (int) $request->getParam('pf');
    $this->view->viewMaxPhoto      = (int) $this->_getParam('max_photo', 8);

    if( $feedOnly ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    if( $length > 50 ) {
      $this->view->length = $length = 50;
    }

    if( $viewer && !$viewer->isAdmin() ) {
      $this->_blockedUserIds = $viewer->getAllBlockedUserIds();
    }

    // Get all activity feed types for custom view?
    // $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'activity');
    // $this->view->groupedActionTypes = $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes();
    // $actionTypeGroup = $request->getParam('actionFilter');
    // $actionTypeFilters = array();
    // if( $actionTypeGroup && isset($groupedActionTypes[$actionTypeGroup]) ) {
    // $actionTypeFilters = $groupedActionTypes[$actionTypeGroup];
    //  }
    
    // Get config options for activity
    $config = array(
      'action_id' => (int) $request->getParam('action_id'),
      'max_id'    => (int) $request->getParam('maxid'),
      'min_id'    => (int) $request->getParam('minid'),     
      'limit'     => (int) $length,
      //'showTypes' => $actionTypeFilters,
    );

    // Pre-process feed items
    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array();
    $itemActionCounts = array();
    $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $similarActivities = array();
    
    $activityCount = 0;
    do {
      // Get current batch
      $actions = null;
      
      // Where the Activity Feed is Fetched
      if( !empty($subject) ) {
        $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
      } else {
        $actions = $actionTable->getActivity($viewer, $tmpConfig);
      }
      $selectCount++;
      
      // Are we at the end?
      if( count($actions) < $length || count($actions) <= 0 ) {
        $endOfFeed = true;
      }
      
      // Pre-process
      if( count($actions) > 0 ) {
        foreach( $actions as $action ) {
          // get next id
          if( null === $nextid || $action->action_id <= $nextid ) {
            $nextid = $action->action_id - 1;
          }
          // get first id
          if( null === $firstid || $action->action_id > $firstid ) {
            $firstid = $action->action_id;
          }
          // skip disabled actions
          if( !$action->getTypeInfo() || !$action->getTypeInfo()->enabled ) continue;
          // skip items with missing items
          if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
          if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
          // track/remove users who do too much (but only in the main feed)
          $actionObject = $action->getObject();
          if( empty($subject) ) {
            $actionSubject = $action->getSubject();
            if( !isset($itemActionCounts[$actionSubject->getGuid()]) ) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } elseif( $itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit ) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()]++;
            }
          }

          if( $this->isBlocked($action) ) {
            continue;
          }

          // remove duplicate friend requests
          if( $action->type == 'friends' ) {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if( in_array($id, $friendRequests) || in_array($rev_id, $friendRequests) ) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          // remove items with disabled module attachments
          try {
            $attachments = $action->getAttachments();
          } catch( Exception $e ) {
            // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
            continue;
          }
            $similarFeedType = $action->type . '_' . $actionObject->getGuid();
            if( $action->canMakeSimilar() ) {
              $similarActivities[$similarFeedType][] = $action;
            }
            if( isset($similarActivities[$similarFeedType]) && count($similarActivities[$similarFeedType]) > 1 ) {
              continue;
            }
          // add to list
          if( $activityCount < $length ) {
            $activity[] = $action;
            $activityCount = count($activity);
            if( $activityCount == $length ) {
              break;
            }
          }
        }
      }
      
      // Set next tmp max_id
      if( $nextid ) {
        $tmpConfig['max_id'] = $nextid;
      }
      if( !empty($tmpConfig['action_id']) ) {
        $actions = array();
      }
    } while( $activityCount < $length && $selectCount <= 6 && !$endOfFeed );

    $this->view->activity = $activity;
    $this->view->activityCount = $activityCount;
    $this->view->similarActivities = $similarActivities;
    $this->view->nextid = $nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;


    // Get some other info
    if( !empty($subject) ) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->enableComposer = false;
    if( $viewer->getIdentity() && !$this->_getParam('action_id') && !$this->view->action_id ) {
      if( !$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer)) ) {
        if( Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status') ) {
          $this->view->enableComposer = true;
        }
      } elseif( $subject ) {
        if( Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment') ) {
          $this->view->enableComposer = true;
        }

        $postActionType = 'post_' . $subject->getType();
        $actionType = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionType($postActionType);
        if( $actionType && !$actionType->enabled ) {
          $this->view->enableComposer = false;
        }
      }
    }

    // Assign the composing values
    $composePartials = array();
    foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
      if( empty($data['composer']) ) {
        continue;
      }
      foreach( $data['composer'] as $type => $config ) {
        if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
          continue;
        }
        $composePartials[] = $config['script'];
      }
    }
    $this->view->composePartials = $composePartials;

    $this->view->formToken = $this->view->activityFormToken()->createToken();

  }

  private function isBlocked($action)
  {

    if( empty($this->_blockedUserIds) ) {
      return false;
    }
    $actionObjectOwner = $action->getObject()->getOwner();
    $actionSubjectOwner = $action->getSubject()->getOwner();
    if( $actionSubjectOwner instanceof User_Model_User && in_array($actionSubjectOwner->getIdentity(), $this->_blockedUserIds) ) {
      return true;
    }
    if( $actionObjectOwner instanceof User_Model_User && in_array($actionObjectOwner->getIdentity(), $this->_blockedUserIds) ) {
      return true;
    }
    return false;
  }
}

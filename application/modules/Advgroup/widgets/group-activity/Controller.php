<?php
class Advgroup_Widget_GroupActivityController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Check if the group item is available
    if(!Engine_Api::_()->hasItemType('group'))
    {
        return;
    }

    // Get some options
    $params      = array();
    $first_id    = null;
    $next_id     = null;
    $action_id   = null;
    $end_of_feed = false;
    $limit       = $this->_getParam('itemCountPerPage',10);

    if( $limit > 50 ) {
      $this->view->limit = $limit = 50;
    }
    
    // Load configuration options for getting activity actions here
     $request = Zend_Controller_Front::getInstance()->getRequest();
     $params['order'] = 'date';
     $params['limit'] = $limit;
     $params['minId'] =  $request->getParam('minid',null);
     $params['maxId'] =  $request->getParam('maxid',null);
     $params['action_types'] = null;
     $this->view->feed_only  = $feed_only = $request->getParam('feed_only', false);

     if( $feed_only ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
   
     $actions = Engine_Api::_()->getApi('activity','advgroup')->getActionsByObject($params);
     // Are we at the end?
      if( count($actions) < $limit || count($actions) <= 0 ) {
        $end_of_feed = true;
      }
      
     if( count($actions) > 0 ) {
         foreach( $actions as $action ) {
            // get next id
            if( null === $next_id || $action->action_id <= $next_id ) {
              $next_id = $action->action_id - 1;
            }
            // get first id
            if( null === $first_id || $action->action_id > $first_id ) {
              $first_id = $action->action_id;
            }

            // skip disabled actions
            if( !$action->getTypeInfo() || !$action->getTypeInfo()->enabled ) continue;
            
            // skip items with missing items
            if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
            if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
         }
     }

    $widget_height = $this->_getParam('widget_height', '');
    if (!empty($widget_height))
    {
        $this->view->widget_height = $widget_height;
    }
    else
    {
        $this->view->widget_height = 0;
    }
    $this->view->group_actions = $actions;
    $this->view->activity_count = count($actions);
    $this->view->next_id = $next_id;
    $this->view->first_id = $first_id;
    $this->view->end_of_feed = $end_of_feed;
  }
}
?>

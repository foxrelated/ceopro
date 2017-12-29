<?php
/**
 * @category   Adv.Group Widget
 * @package    Adv.Group
 * @copyright  Copyright 2013-2014 YouNet Developments
 * @author     trunglt
 */
?>
<?php
class Advgroup_Widget_GroupTopMembersController extends Engine_Content_Widget_Abstract
{

    public function indexAction ()
    {
        if (! Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        if (! $group = Engine_Api::_()->core()->getSubject('group')) {
            return $this->setNoRender();
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if(!$group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
        if ($this->_getParam ( 'number' ) != '' && $this->_getParam ( 'number' ) >= 0) {
            $limit = $this->_getParam ( 'number' );
        } else {
            $limit = 5;
        }
        $db = Engine_Db_Table::getDefaultAdapter ();
        $db->beginTransaction ();
        $select = "SELECT count(*) as post_count, `result`.subject_id FROM ((SELECT type1.`action_id`  , type1.`subject_id` , 'action' as 'type'
                    FROM engine4_activity_actions type1
                    WHERE object_type = 'group' and object_id = {$group->getIdentity()})
                    UNION (
                    SELECT like1.`action_id` , like2.`poster_id` as 'subject_id' , 'like' as 'type'
                    FROM engine4_activity_likes like2
                    JOIN engine4_activity_actions like1 ON like2.resource_id = like1.action_id
                    WHERE like1.object_type =  'group'
                    AND like1.object_id = {$group->getIdentity()})
                    UNION (
                    SELECT com1.`action_id`, com2.`poster_id` as 'subject_id' , 'comment' as 'type'
                    FROM engine4_activity_comments com2
                    JOIN engine4_activity_actions com1 ON com2.resource_id = com1.action_id
                    WHERE com1.object_type =  'group'
                    AND com1.object_id = {$group->getIdentity()}
                    )
                    ) as `result`
                    GROUP BY `result`.subject_id
                    ORDER BY post_count DESC";

        $results = $db->fetchAll($select);

        $this->view->limit = $limit;
        $this->view->group = $group;
        $this->view->results = $results;
        // Do not render if nothing to show
        if (count($results) <= 0) {
            return $this->setNoRender();
        }
    }
}

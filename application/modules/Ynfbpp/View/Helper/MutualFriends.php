<?php

class Ynfbpp_View_Helper_MutualFriends extends Zend_View_Helper_Abstract
{

    public function mutualFriends($subject, $viewer = null, $limit = 10, $mess1= '%s mutual friend', $mess2='%s mutual friends')
    {
        if (null === $viewer)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!$viewer || !$viewer -> getIdentity() || $subject -> isSelf($viewer))
        {
            return '';
        }

        // check if a & b has mutuals friend.
        $subject_id = (int)$subject -> getIdentity();
        $viewer_id = (int)$viewer -> getIdentity();

        if ($viewer_id == 0 || $subject_id == 0)
        {
            return '';
        }

        $isFriend = $subject -> membership() -> isMember($viewer);


        // Diff friends
        $friendsTable = Engine_Api::_()->getDbtable('membership', $subject->getModuleName());
        $friendsName = $friendsTable->info('name');

        // Mutual friends/following mode

        $sql = "SELECT `user_id` FROM `{$friendsName}` WHERE (`active`=1 and `resource_id`={$subject_id})
        and `user_id` in (select `resource_id` from `engine4_user_membership` where (`user_id`={$viewer_id} and `active`=1))";


        $db = Engine_Db_Table::getDefaultAdapter();
        $friends = $friendsTable->getAdapter() -> fetchcol($sql);

        if (empty($friends))
        {
            return;
        }

        // Get paginator
        $usersTable = Engine_Api::_()->getItemTable('user');


        $select = $usersTable->select()
          ->where('user_id IN(?)', $friends)->order('photo_id desc')
          ;

        $paginator = Zend_Paginator::factory($select);

        /**
         * @var int
         * number of mutual friends to show here
         */
        $paginator->setItemCountPerPage($limit);

        $totalFriends = $paginator->getTotalItemCount();
        if( $totalFriends <= 0){
            return '';
        }

        $xhtml = array();

        $xhtml[] =  '<div class="uiYnfbpp_manual_friend">';
        $xhtml[] = '<div><a href="' . $subject -> getHref() . '">'
        .$this -> view -> translate(array(
            $mess1,
            $mess2,
            $totalFriends
        ), $this -> view -> locale() -> toNumber($totalFriends))
        .'</a></div>';

        $xhtml[] = '<ul class="uiYnfbpp_manual_friend_list clearfix">';

        // save a space for right arrow
        $count = ($totalFriends > $limit) ? $limit - 1 : $limit;

        foreach ($paginator as $resource)
        {
            $xhtml[] = '<li>'.$this -> view -> htmlLink($resource -> getHref(), $this -> view -> itemPhoto($resource, 'thumb.icon'),array('title'=>$resource->getTitle())). '</li>';
            if (--$count <= 0)
            {
                break;
            }
        }

        if ($totalFriends > $limit) {
            $xhtml[] = '<li class="uiYnfbpp-arrow"><a href="' . $subject->getHref() . '">
                            <span class="ynicon yn-arr-right"></span>
                        </a></li>';
        }

        $xhtml[] = '</ul></div>';

        return implode(PHP_EOL, $xhtml);
    }
}

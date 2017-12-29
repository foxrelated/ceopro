<?php

class Ynfbpp_View_Helper_ItemMembers extends Zend_View_Helper_Abstract
{

    public function itemMembers($subject, $viewer = null, $limit = 8, $mess1= '%s member joined', $mess2='%s members joined')
    {
        if (null === $viewer)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        // check if a & b has mutuals friend.
        $subject_id = (int)$subject -> getIdentity();
        $viewer_id = (int)$viewer -> getIdentity();

        if ($viewer_id == 0 || $subject_id == 0)
        {
            return '';
        }

        // Diff friends
        $membershipTable = Engine_Api::_()->getDbtable('membership', $subject->getModuleName());
        $membershipTableName = $membershipTable->info('name');

        // Mutual friends/following mode

        $sql = "SELECT `user_id` FROM `{$membershipTableName}` WHERE (`active`=1 and `resource_id`={$subject_id})";

        $memberIds = $membershipTable->getAdapter() -> fetchcol($sql);

        if (empty($memberIds))
        {
            return '';
        }

        // Get paginator
        $usersTable = Engine_Api::_()->getItemTable('user');

        $select = $usersTable->select()
          ->where('user_id IN(?)', $memberIds)
          ;

        $paginator = Zend_Paginator::factory($select);

        /**
         * @var int
         * number of mutual friends to show here
         */
        $paginator->setItemCountPerPage($limit);

        $totalMembers = $paginator->getTotalItemCount();

        if( $totalMembers <= 0){
            return '';
        }

        $xhtml = array();

        $xhtml[] =  '<div class="uiYnfbpp_manual_friend">';
        $xhtml[] = '<div><a href="' . $subject -> getHref() . '">'
        .$this -> view -> translate(array(
            $mess1,
            $mess2,
            $totalMembers
        ), $this -> view -> locale() -> toNumber($totalMembers))
        .'</a></div>';

        $xhtml[] = '<ul class="uiYnfbpp_manual_friend_list clearfix">';

        // save a space for right arrow
        $count = ($totalMembers > $limit) ? $limit - 1 : $limit;

        foreach ($paginator as $resource)
        {
            $xhtml[] = '<li>'.$this -> view -> htmlLink($resource -> getHref(), $this -> view -> itemPhoto($resource, 'thumb.icon'),array('title'=>$resource->getTitle())). '</li>';
            if (--$count <= 0)
            {
                break;
            }
        }

        if ($totalMembers > $limit) {
            $xhtml[] = '<li class="uiYnfbpp-arrow"><a href="' . $subject->getHref() . '">
                            <span class="ynicon yn-arr-right"></span>
                        </a></li>';
        }

        $xhtml[] = '</ul></div>';

        return implode(PHP_EOL, $xhtml);
    }
}

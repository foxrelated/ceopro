<?php
class Ynblog_Model_DbTable_Links extends Engine_Db_Table {
    protected $_rowClass = 'Ynblog_Model_Link';
    protected $_name = 'blog_links';

    /*----- Get Specific link according to the user id -----*/
    public function getLink($user_id = 0) {
        if ($user_id == 0)
            return '';
        $select = $this -> select() -> where('user_id = ?', $user_id);
        $result = $this -> fetchRow($select);
        return $result;
    }
    
    /*----- Get Link Paginator Function -----*/
    public function getLinksPaginator($params = array()) {
        //Get links paginator
        $link_select = $this -> getLinksSelect($params);
        $paginator = Zend_Paginator::factory($link_select);

        //Set current page
        if (!empty($params['page'])) {
            $paginator -> setCurrentPageNumber($params['page'], 1);
        }
        //Item per page
        $itemPerPage = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('blog.page', 10);
        $paginator -> setItemCountPerPage($itemPerPage);
        if (!empty($params['limit'])) 
        {
            $paginator -> setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    /*----- Get Link Selection Query Function -----*/
    public function getLinksSelect($params = array()) {
        //Select link
        if (!isset($params['direction']))
            $params['direction'] = "DESC";
        $link_name = $this -> info('name');
        //Order by filter
        if (isset($params['orderby']) && $params['orderby'] == 'displayname') 
        {
            $select = $this -> select() -> from($link_name) -> setIntegrityCheck(false) -> join('engine4_users as u', "u.user_id = $link_name.user_id", '') -> order("u.displayname " . $params['direction']);
        } else 
        {
            $select = $this -> select() -> from($link_name) -> order(!empty($params['orderby']) ? $link_name . "." . $params['orderby'] . ' ' . $params['direction'] : $link_name . '.link_id ' . $params['direction']);
        }
        if(isset($params['enable']) && $params['enable'])
        {
            $select -> where('cronjob_enabled = 1');
        }
        return $select;
    }

}
?>

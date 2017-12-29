<?php
class Ynblog_Model_DbTable_Blogs extends Engine_Db_Table {
    /*----- Properties -----*/
    protected $_rowClass = "Ynblog_Model_Blog";
    protected $_name = 'blog_blogs';

    /*----- Checking Maximum Number Of Blogs -----*/
    public function checkMaxBlogs($user = NULL) {
        //Get user and maximum number of blogs
        if(!$user)
            $user = Engine_Api::_() -> user() -> getViewer();

        $permission_table = Engine_Api::_() -> getDbtable('permissions', 'authorization');
        $select = $permission_table -> select() -> where("type = 'blog'") -> where("level_id = ?", $user -> level_id) -> where("name = 'max'");
        $max_allowed = $permission_table -> fetchRow($select);
        $max_blogs = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('blog', $user, 'max');

        //Check when user set max blog equal 3
        if ($max_blogs == "") {
            if (!empty($max_allowed))
                $max_blogs = $max_allowed['value'];
        }
        return $max_blogs;
    }

    /*----- Count Number of Blogs Function -----*/
    public function getCountBlog($user) {
        $select = $this -> select() -> where('owner_id = ?', $user -> getIdentity());
        return count($this -> fetchAll($select));
    }

}
?>

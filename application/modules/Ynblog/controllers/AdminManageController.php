<?php
class Ynblog_AdminManageController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        // Get navigation bar
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynblog_admin_main', array(), 'ynblog_admin_main_manage');

        $this->view->form = $form = new Ynblog_Form_Admin_Search;
        $form->isValid($this->_getAllParams());
        $params = $form->getValues();
        if(empty($params['orderby'])) $params['orderby'] = 'blog_id';
        if(empty($params['direction'])) $params['direction'] = 'DESC';
        $this->view->formValues = $params;

        //  Filter type of search
        switch ($params['filter']){
            case '0': $params['featured']    = 1;
                break;
            case '1': $params['featured']    = 0;
                break;
            case '2': $params['is_approved'] = 1;
                break;
            case '3': $params['is_approved'] = 0;
                break;
        }
        $this->view->moderation = Engine_Api::_()->getApi('settings','core')->getSetting('ynblog.moderation',0);

        // Get Blog Paginator
        $this->view->paginator = Engine_Api::_()->ynblog()->getBlogsPaginator($params);

        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.page',10);
        $this->view->paginator->setItemCountPerPage($items_per_page);
        if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);
    }

    /*----- Delete Blog Function-----*/
    public function deleteAction()
    {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->blog_id=$id;

        // Check post
        if( $this->getRequest()->isPost() )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            //Process delete action
            try
            {
                $blog = Engine_Api::_()->getItem('blog', $id);
                // delete the blog entry into the database
                $blog->delete();
                $db->commit();
            }

            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }

            // Refresh parent page
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }

        // Output
        $this->renderScript('admin-manage/delete.tpl');
    }

    /*----- Delete Selected Blogs Function -----*/
    public function deleteSelectedAction()
    {
        $this->view->ids = $ids = $this->_getParam('ids', null);
        $confirm = $this->_getParam('confirm', false);
        $this->view->count = count(explode(",", $ids));

        // Check post
        if( $this->getRequest()->isPost() && $confirm == true )
        {
            //Process delete
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try{
                $ids_array = explode(",", $ids);
                foreach( $ids_array as $id ){
                    $blog = Engine_Api::_()->getItem('blog', $id);
                    if( $blog ) $blog->delete();
                }
                $db->commit();
            }
            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }

            $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        }
    }

    /*----- Set Featured Blog Function -----*/
    public function featureAction()
    {
        //Get params
        $blog_id = $this->_getParam('blog_id');
        $featured = $this->_getParam('good');

        //Get blog need to set featured
        $table = Engine_Api::_()->getItemTable('blog');
        $select = $table->select()->where("blog_id = ?",$blog_id);
        $blog = $table->fetchRow($select);

        //Set featured/unfeatured
        if($blog){
            $blog->is_featured =  $featured;
            $blog->save();
        }
    }

    /*----- Set Approve Blog Function -----*/
    public function approveAction(){
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->blog_id=$id;

        // Check post
        if( $this->getRequest()->isPost() )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            // Change the blog approved field in the database
            try
            {
                $blog = Engine_Api::_()->getItem('blog', $id);

                //Check if got object
                if($blog){
                    $blog->is_approved = 1;

                    //Add activity if the blog is approved at the first time
                    if(!$blog->add_activity && !$blog->draft){
                        $owner = $blog->getParent();

                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $blog, 'ynblog_new');

                        // Make sure action exists before attaching the blog to the activity
                        if( $action ) {
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                        }

                        // Send notifications for blog ownner
                        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
                        $notificationTable->addNotification($owner, $blog, $blog, 'ynblog_approved');

                        // Send notifications for subscribers
                        Engine_Api::_()->getDbtable('subscriptions', 'ynblog')
                            ->sendNotifications($blog);

                        $blog->add_activity = 1;
                    }
                    $blog->save();
                    $db->commit();
                }
            }
            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }
    }

    /*----- Set Unapprove Blog Function -----*/
    public function unapproveAction(){
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->blog_id=$id;

        // Check post
        if( $this->getRequest()->isPost() )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            // Change the blog approved field in the database
            try{
                $blog = Engine_Api::_()->getItem('blog', $id);
                if($blog){
                    $blog->is_approved = 0;

                    // Send notifications for blog owner
                    $owner = $blog->getParent();
                    $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
                    $notificationTable->addNotification($owner, $blog, $blog, 'ynblog_unapproved');

                    $blog->save();
                    $db->commit();
                }
            }
            catch( Exception $e ){
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }
    }

    /*----- Aprrove Selected Blogs Function -----*/
    public function approveSelectedAction()
    {
        $this->view->ids = $ids = $this->_getParam('ids', null);
        $confirm = $this->_getParam('confirm', false);
        $this->view->count = count(explode(",", $ids));

        // Save values
        if( $this->getRequest()->isPost() && $confirm == true )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try{
                $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
                $ids_array = explode(",", $ids);
                foreach( $ids_array as $id ){
                    $blog = Engine_Api::_()->getItem('blog', $id);
                    if( $blog ) {
                        $blog->is_approved = 1;

                        //Add activity if the blog is approved at the first time
                        if(!$blog->add_activity && !$blog->draft){
                            $owner = $blog->getParent();
                            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $blog, 'ynblog_new');

                            // Make sure action exists before attaching the blog to the activity
                            if( $action ) {
                                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                            }

                            // Send notifications for blog ownner
                            $notificationTable->addNotification($owner, $blog, $blog, 'ynblog_approved');
                            // Send notifications for subscribers
                            Engine_Api::_()->getDbtable('subscriptions', 'ynblog')->sendNotifications($blog);

                            $blog->add_activity = 1;
                        }
                        $blog->save();
                    }
                }
                $db->commit();
            }
            catch( Exception $e ){
                $db->rollBack();
                throw $e;
            }

            // Redirect to admin manage index page

            $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        }
    }

    /*----- Unapprove Selected Blogs Function -----*/
    public function unapproveSelectedAction()
    {
        $this->view->ids = $ids = $this->_getParam('ids', null);
        $confirm = $this->_getParam('confirm', false);
        $this->view->count = count(explode(",", $ids));

        // Save values
        if( $this->getRequest()->isPost() && $confirm == true )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try{
                $ids_array = explode(",", $ids);
                $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
                foreach( $ids_array as $id ){
                    $blog = Engine_Api::_()->getItem('blog', $id);
                    if( $blog ) {
                        // Send notifications for blog owner
                        $owner = $blog->getParent();
                        $notificationTable->addNotification($owner, $blog, $blog, 'ynblog_unapproved');
                        $blog->is_approved = 0;
                        $blog->save();
                    }
                }
                $db->commit();
            }
            catch( Exception $e ){
                $db->rollBack();
                throw $e;
            }
            //Redirect to admin manage index page
            $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        }
    }

    /*--- Manage URLs ---*/
    public function urlsAction()
    {
        // Get navigation bar
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynblog_admin_main', array(), 'ynblog_admin_main_manageurl');

        $this->view->form = $form = new Ynblog_Form_Admin_SearchURL;
        $form->isValid($this->_getAllParams());
        $params = $form->getValues();
        if(empty($params['orderby'])) $params['orderby'] = 'link_id';
        if(empty($params['direction'])) $params['direction'] = 'DESC';
        $this->view->formValues = $params;

        // Get Link Paginator
        $this->view->paginator = Engine_Api::_ ()->getDbTable ( 'links', 'ynblog' ) -> getLinksPaginator($params);
        if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);
    }
    /*----- Delete Link Function-----*/
    public function deleteLinkAction()
    {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->link_id=$id;

        // Check post
        if( $this->getRequest()->isPost() )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            //Process delete action
            try
            {
                $link = Engine_Api::_ ()->ynblog ()-> getLink($id);
                // delete the link into the database
                $link->delete();
                $db->commit();
            }

            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }

            // Refresh parent page
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }

        // Output
        $this->renderScript('admin-manage/delete-link.tpl');
    }

    /*----- Delete Selected Links Function -----*/
    public function deleteLinkSelectedAction()
    {
        $this->view->ids = $ids = $this->_getParam('ids', null);
        $confirm = $this->_getParam('confirm', false);
        $this->view->count = count(explode(",", $ids));

        // Check post
        if( $this->getRequest()->isPost() && $confirm == true )
        {
            //Process delete
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try{
                $ids_array = explode(",", $ids);
                foreach( $ids_array as $id ){
                    $link = Engine_Api::_ ()->ynblog ()-> getLink($id);
                    if( $link ) $link->delete();
                }
                $db->commit();
            }
            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }

            $this->_helper->redirector->gotoRoute(array('action' => 'urls'));
        }
    }

    /*----- Set enable link Function -----*/
    public function enableCronAction(){
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->link_id = $id;

        // Check post
        if( $this->getRequest()->isPost() )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            // Change the link enable field in the database
            try
            {
                $link = Engine_Api::_ ()->ynblog ()-> getLink($id);
                //Check if got object
                if($link)
                {
                    $link -> cronjob_enabled = 1;
                    $link->save();
                    $db->commit();
                }
            }
            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }
    }

    /*----- Set Disable Link Function -----*/
    public function disableCronAction(){
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->link_id=$id;

        // Check post
        if( $this->getRequest()->isPost() )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try{
                $link = Engine_Api::_ ()->ynblog ()-> getLink($id);
                if($link){
                    $link->cronjob_enabled = 0;
                    $link -> save();
                    $db->commit();
                }
            }
            catch( Exception $e ){
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }
    }

    /*----- Enable Selected Links Function -----*/
    public function enableLinkSelectedAction()
    {
        $this->view->ids = $ids = $this->_getParam('ids', null);
        $confirm = $this->_getParam('confirm', false);
        $this->view->count = count(explode(",", $ids));

        // Save values
        if( $this->getRequest()->isPost() && $confirm == true )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try{
                $ids_array = explode(",", $ids);
                foreach( $ids_array as $id ){
                    $link = Engine_Api::_ ()->ynblog ()-> getLink($id);
                    if( $link )
                    {
                        $link->cronjob_enabled = 1;
                        $link->save();
                    }
                }
                $db->commit();
            }
            catch( Exception $e ){
                $db->rollBack();
                throw $e;
            }

            $this->_helper->redirector->gotoRoute(array('action' => 'urls'));
        }
    }

    /*----- Disable Selected Links Function -----*/
    public function disableLinkSelectedAction()
    {
        $this->view->ids = $ids = $this->_getParam('ids', null);
        $confirm = $this->_getParam('confirm', false);
        $this->view->count = count(explode(",", $ids));

        // Save values
        if( $this->getRequest()->isPost() && $confirm == true )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try{
                $ids_array = explode(",", $ids);
                foreach( $ids_array as $id )
                {
                    $link = Engine_Api::_ ()->ynblog ()-> getLink($id);
                    if( $link )
                    {
                        $link->cronjob_enabled = 0;
                        $link->save();
                    }
                }
                $db->commit();
            }
            catch( Exception $e ){
                $db->rollBack();
                throw $e;
            }
            $this->_helper->redirector->gotoRoute(array('action' => 'urls'));
        }
    }
}
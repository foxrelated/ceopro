<?php
// wget -O - "http://<yoursite>/application/lite.php?module=ynblog&name=task"

$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$application -> getBootstrap() -> bootstrap('hooks');

$allowCron = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.cron', 1);

if($allowCron)
{
    //log
    $strLog = "";
    $strLog .= "\n###############################################\n";
    $strLog .= "Start get data\n";
    $linkTbl = Engine_Api::_()->getDbTable('links', 'ynblog');
    $links = $linkTbl -> getLinksPaginator(array('limit' => 2, 'orderby' => 'last_run', 'direction' => 'ASC', 'enable' => 1));
    if($links -> getTotalItemCount())
    {
        require_once (APPLICATION_PATH.'/application/modules/Ynblog/controllers/YnsRSSFeed/YnsRSS.php');
        set_time_limit(0);
        $rss = new YnsRSS ();
        $is_approved = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.moderation', 0 ) ? 0 : 1;
        foreach ($links as $link) 
        {
            $user = Engine_Api::_ ()-> getItem('user', $link -> user_id);
            if(!$user)
            {
                continue;
            }
            $user_id = $link -> user_id;
            $max_blogs = Engine_Api::_ ()->getItemTable ( 'blog' )->checkMaxBlogs($user);
            $blog_number = Engine_Api::_ ()->getItemTable ( 'blog' )->getCountBlog ($user);
            
            $link -> last_run = new Zend_Db_Expr('NOW()');
            $link->save();
            
            $feed = $rss->getParse ( null, $link -> link_url, null );
            if (empty ( $feed ['entries'] )) 
            {
                continue;
            }
            $feeds = array_reverse ( $feed ['entries'] );
            // count blogs
            $count = $blog_number;
            foreach ( $feeds as $entry ) 
            {
                $a = date ( 'Y-m-d', $entry ['pubDate'] );
                $pubdate = strtotime ( $a );
                // insert data to database
                $db = Engine_Api::_ ()->getItemTable ( 'blog' )->getAdapter();
                $db->beginTransaction ();
    
                try {
                    // check news exist by link
                    $blog_table = Engine_Api::_ ()->getItemTable ( 'blog' );
                    $blog_select = $blog_table->select ()->where ( 'link_detail = ?', $entry ['link_detail'] );
                    $blog = $blog_table->fetchRow ( $blog_select );
                    if ($blog) 
                    {
                        $blog->title = $entry ['title'];
                        $blog->pub_date = $pubdate;
                        $blog->modified_date = date ( 'Y-m-d H:i:s' );
                        if (! empty ( $entry ['content'] )) {
                            $blog->body = $entry ['content'];
                        } else {
                            $blog->body = $entry ['description'];
                        }
                        $blog->is_approved = $is_approved;
                        $blog->save ();
                    } 
                    else 
                    {
                        if ($max_blogs != 0 && $count >= $max_blogs) 
                        {
                            break;
                        }
                        $blog = $blog_table->createRow ();
                        $blog->owner_type = "user";
                        $blog->owner_id = $user_id;
                        $blog->category_id = 0;
                        $blog->creation_date = date ( 'Y-m-d H:i:s' );
                        $blog->modified_date = date ( 'Y-m-d H:i:s' );
                        $blog->pub_date = $pubdate;
                        $blog->link_detail = $entry ['link_detail'];
                        if (! empty ( $entry ['title'] )) {
                            $blog->title = $entry ['title'];
                        } else {
                            $blog->title = 'Untitled Blog';
                        }
                        if (! empty ( $entry ['content'] )) {
                            $blog->body = $entry ['content'];
                        } else {
                            $blog->body = $entry ['description'];
                        }
                        $blog->is_approved = $is_approved;
                        if ($blog->is_approved) {
                            $blog->add_activity = 1;
                        }
    
                        $blog->save ();
                        $count ++;
    
                        // set auth
                        $auth = Engine_Api::_ ()->authorization ()->context;
                        $roles = array (
                                'owner',
                                'owner_member',
                                'owner_member_member',
                                'owner_network',
                                'everyone'
                        );
                        $auth_view = "everyone";
                        $auth_comment = "everyone";
                        $viewMax = array_search ( $auth_view, $roles );
                        $commentMax = array_search ( $auth_comment, $roles );
                        foreach ( $roles as $i => $role )
                            $auth->setAllowed ( $blog, $role, 'view', ($i <= $viewMax) );
                        $auth->setAllowed ( $blog, $role, 'comment', ($i <= $commentMax) );
    
                        if ($blog->is_approved) {
                            $owner = $blog->getParent ();
                            $action = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $owner, $blog, 'ynblog_import' );
    
                            // Make sure action exists before attaching the blog
                            // to the activity
                            if ($action) {
                                Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $blog );
                            }
    
                            // Send notifications for subscribers
                            Engine_Api::_ ()->getDbtable ( 'subscriptions', 'ynblog' )->sendNotifications ( $blog );
    
                        }
                    }
                    $db->commit ();
                } catch ( Exception $e ) {
                    throw $e;
                    $db->rollBack ();
                }
            }
           
        }
    }
    $strLog .= "End get data\n";
    $resource_path = APPLICATION_PATH . "/temporary/log/ynblog.cronjob.log";
    $writer = new Zend_Log_Writer_Stream($resource_path);
    $logger = new Zend_Log($writer);
    $logger->info($strLog);
    exit;
}
?>

<?php
// http://<your site>/?m=lite&name=task&module=ultimatenews

$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$application -> getBootstrap() -> bootstrap('hooks');

$parser = Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.parser');

$serviceLink = ($parser) 
    ? "http://newsservice.younetco.com/v1.1"
    : Engine_Api::_()->ultimatenews()->getCurrentHost() . "application/modules/Ultimatenews/Api/newsservice";
// Constants
defined('YOUNET_NEWS_FEED_PARSER') or define('YOUNET_NEWS_FEED_PARSER', "{$serviceLink}/getfeed.php");
defined('YOUNET_NEWS_HOST') or define('YOUNET_NEWS_HOST', "{$serviceLink}/parser.php");
date_default_timezone_set('UTC');

$days = Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.days', 7);
$now = date('Y-m-d');
$contentTbl = Engine_Api::_()->getDbTable('contents', 'ultimatenews');
$select = $contentTbl -> select() -> where("DATEDIFF('$now', posted_date) >= $days");
$oldNews = $contentTbl -> fetchAll($select);
foreach($oldNews as $oldNew)
{
	$oldNew -> delete();
}
//log
$strLog = "";
$strLog .= "\n###############################################\n";
$strLog .= "Start get data\n";

//get feeds
$numberOfFeeds = Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.feeds');
$categories_arr = Engine_Api::_()->ultimatenews()->getAllCategories(array('category_active' => '1', 'approved' => 1, 'limit' => $numberOfFeeds), $returnModel = true);
set_time_limit(0);

if (count($categories_arr) > 0)
{
    foreach ($categories_arr as $category)
    {
        //get data from remote server
        try
        {
            //get user subscribe;
            $users = Zend_Json::decode($category -> subscribe);
            
            $option = array();
            $option['uri'] = urlencode($category->url_resource);
            
            $contentSetting = $category->full_content;
            
            if ($contentSetting == '0') 
                $option['rssfeed'] = 1;
                
            $feed = Engine_Api::_()->ultimatenews()->getData(YOUNET_NEWS_HOST . '?' . http_build_query($option, null, '&'));
            if (null !== $feed) {
                $feed = Zend_Json::decode($feed);
            }
            else {
                continue;
            }
            
            // Get feed category
            $tagStr = '';
            foreach( $category -> tags() -> getTagMaps() as $tagMap ) 
            {
              $tag = $tagMap->getTag();
              if( !isset($tag->text) ) continue;
              if( '' !== $tagStr ) $tagStr .= ', ';
              $tagStr .= $tag->text;
            }
            
            if (is_array($feed['rows']) && !empty($feed['rows']))
            {
                foreach ($feed['rows'] as $entry)
                {
                    $pubdate = time();
                            if ($entry['item_pubDate'])
                                $pubdate = strtotime($entry['item_pubDate']);
                            else 
                                $entry['item_pubDate'] = date('Y-m-d H:i:s');
                                
                    $edata = array(
                        'category_id' => $category->category_id,
                        'owner_type' => "user",
                        'owner_id' => $category -> owner_id,
                        'title' => $entry['item_title'],
                        'description' => $entry['item_description'],
                        'content' => $entry['item_content'],
                        'image' => $entry['item_image'],
                        'link_detail' => $entry['item_url_detail'],
                        'author' => '',
                        'pubDate' => $pubdate,
                        'pubDate_parse' => $entry['item_pubDate'],
                        'posted_date' => date('Y-m-d H:i:s'),
                        'is_active' => "1"
                    );
                    
                    if ($entry['item_image'] != '')
                    {
                        $storage_file = Engine_Api::_() -> ultimatenews() -> saveImg(Engine_Api::_() -> ultimatenews() -> getImageURL($edata['image']), md5($edata['image']), $category -> owner_id);
                        $edata['image'] = $storage_file -> storage_path;
                        $edata['photo_id'] = $storage_file -> file_id;
                    }
                    //insert news to database
                    $db = Engine_Api::_()->getDbtable('contents', 'ultimatenews')->getAdapter();
                    $db->beginTransaction();
                    try
                    {
                        //check Ultimatenews exist by link and title
                        $content = Engine_Api::_()->ultimatenews()->getAllContent(array('link_detail' => $edata['link_detail'], 'title' => $edata['title']));
    
                        if (count($content) == 0)
                        {
                            // Create content
                            $table = Engine_Api::_()->getDbtable('contents', 'ultimatenews');
                            $content = $table->createRow();
                            $content->setFromArray($edata);
                            $content->save();
                            
                            // add tags
                            $owner = Engine_Api::_() -> getItem('user', $category -> owner_id);
                            $tags = preg_split('/[,]+/', $tagStr);
                            $content->tags()->setTagMaps($owner, $tags);
                            
                            //set auth
                            $auth = Engine_Api::_()->authorization()->context;
                            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                            $auth_view = "everyone";
                            $viewMax = array_search($auth_view, $roles);
                            foreach ($roles as $i => $role)
                                $auth->setAllowed($content, $role, 'view', ($i <= $viewMax));
                        }
    
                        $db->commit();
                        $edata = null;
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                    }
                }
            }
            $category->updated_at = time();
            $category->save();
        }
        catch (exception $ex)
        {
            //var_dump($ex->getTrace());
        }
    }
}

$strLog .= "End get data\n";
try
{
    $resource_path = APPLICATION_PATH . "/temporary/log/Ultimatenews.cronjob.log";
    $writer = new Zend_Log_Writer_Stream($resource_path);
    $logger = new Zend_Log($writer);
    $logger->info($strLog);
}
catch(exception $exc)
{
    print_r("Can not write cron log");
}

echo("Get news successfully!");
?>
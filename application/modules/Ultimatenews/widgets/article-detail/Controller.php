<?php
include_once APPLICATION_PATH. '/application/modules/Ultimatenews/Api/phpCutHtml/cutstring.php';

class Ultimatenews_Widget_ArticleDetailController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $request =  Zend_Controller_Front::getInstance()->getRequest();
        $content_id = $request -> getParam('id');
        //inactive jobs If I click to a inactive jobs link on activity feed
        $table = Engine_Api::_() -> getDbtable('contents', 'ultimatenews');
        $select = $table -> select('engine4_ultimatenews_contents') -> setIntegrityCheck(false) -> joinLeft("engine4_ultimatenews_categories", "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id") -> where('engine4_ultimatenews_contents.content_id= ? ', $content_id) -> where('engine4_ultimatenews_categories.is_active= ? ', 1) -> limit(1);
        $item = $table -> fetchRow($select);
        if (!is_object($item))
        {
        	echo Zend_Registry::get("Zend_Translate")->_("This news is not existed!");
           	return ;
        }
        else
        {
            $photoUrl = $item->getPhotoUrl();
            $size_img = getimagesize($photoUrl);
			$view = Zend_Registry::get('Zend_View');
			$og = '<meta property="og:image" content="' . $this -> finalizeUrl($photoUrl) . '" />';
            if (!empty($size_img)) {
                $og .= '<meta property="og:image:width" content="'.$size_img[0].'" />';
                $og .= '<meta property="og:image:height" content="'.$size_img[1].'" />';
            } else {
                $og .= '<meta property="og:image:width" content="640" />';
                $og .= '<meta property="og:image:height" content="442" />';
            }
			$og .= '<meta property="og:title" content="' . $item -> getTitle() . '" />';
			$og .= '<meta property="og:url" content="' . $this -> finalizeUrl($item->getHref()) . '" />';
			$og .= '<meta property="og:updated_time" content="' . $item->pubDate . '" />';
			if($item -> description)
				$og .= '<meta property="og:description" content="' . $view -> string() -> truncate(strip_tags($item -> description), 300) . '" />';
			$og .= '<meta property="og:type" content="article" />';
			$view -> layout() -> headIncludes .= $og;
        	if (!$item->image)
			{
				preg_match('/img[^>]*src="([^"]*)"/i', $item->content, $matches );
				
				if($matches)
				{
					if($matches[1])
					{
						list($width, $height, $type, $attr) = getimagesize($matches[1]);	
						if ( ($width > 48) && ($height > 48) )
						{
							try
							{
								$storage_file = $this->saveImg($matches[1], md5($matches[1]));	
							}
							catch (Exception $e) {
							    echo $e;exit;
							}
							$item->image = $storage_file->storage_path;
							$item->photo_id = $storage_file->file_id;
							$item->save();
						}
					}
				}
			}
			
			if(!Engine_Api::_() -> core() -> hasSubject())
			{
                Engine_Api::_() ->core() ->setSubject($item);
            }
            $item -> count_view = $item -> count_view + 1;
            $item -> save();
            
            //cut content
            $newsContent = $item -> content;
            $feed = Engine_Api::_()->getItem('ultimatenews_category', $item->category_id);
            if (is_object($feed)){
            	$length = $feed ->characters;
	            if($feed ->full_content == 1 && $length >0) {
					$cutstrObj = new HtmlCutString($newsContent, $length);
					$item -> content = $cutstrObj->cut();
	        	}
            }
				
			//Get tags
		  	$t_table = Engine_Api::_()->getDbtable('tags', 'core');
		  	$tm_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
			$tName = $t_table->info('name');
  			$tmName = $tm_table->info('name');
			$filter_select = $tm_table->select()->from($tmName,"$tmName.*")
			  	->setIntegrityCheck(false)
			  	->where("$tmName.resource_id = ?",$item->getIdentity());
			  
		  	$select = $t_table->select()->from($tName,array("$tName.*","Count($tName.tag_id) as count"));
		  	$select->joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
		  	$select  ->order("$tName.text");
		  	$select  ->group("$tName.text");
		  	$select  ->where("t.resource_type = ?","ultimatenews_content");
		  	$this->view->tags = $tags = $t_table->fetchAll($select);
			
            $this -> view -> content = $item;
            $this -> view -> is_commment = $request -> getParam('commentdetail');
			$category = Engine_Api::_()->ultimatenews()->getAllCategories(array(
            	'category_id' => $item->category_id,
			));
            $this -> view -> category = $category[0];
			
			//Display related news
			$max = (int)$this->_getParam('max',10);
	        $select2 = $table -> select('engine4_ultimatenews_contents') 
	        	-> setIntegrityCheck(false) 
	        	-> joinLeft("engine4_ultimatenews_categories", "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id") 
	        	-> where('engine4_ultimatenews_contents.content_id != ? ', $content_id) 
	        	-> where('engine4_ultimatenews_categories.is_active = ? ', 1)
				-> where('engine4_ultimatenews_contents.category_id = ?', $item->category_id)
				-> order('content_id DESC')
				-> limit($max);
			
	        $this->view->relatedNews = $table -> fetchAll($select2);
			
        }
		
    }
	
	public function finalizeUrl($url)
	{
		if ($url)
		{
			if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
			{
				$pageURL = 'http';
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
				{
					$pageURL .= "s";
				}
				$pageURL .= "://";
				$pageURL .= $_SERVER["SERVER_NAME"];
				$url = $pageURL . '/'. ltrim( $url, '/');
			}
		}
	
		return $url;
	}
	
	public function saveImg($url, $name)
    {
    	$adminArr = Engine_Api::_()->user()->getSuperAdmins()->toArray();
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'category_logo',
			'parent_id' => $adminArr[0]['user_id'],
			'user_id' => $adminArr[0]['user_id']
        );
		$gis = getimagesize($url);
		$type = $gis[2];
		switch($type) {
			case "1": $imorig = imagecreatefromgif($url); break;
			case "2": $imorig = imagecreatefromjpeg($url);break;
			case "3": $imorig = imagecreatefrompng($url); break;
			default: $imorig = imagecreatefromjpeg($url);
		}
		
        // Save
        $storage = Engine_Api::_()->storage();
        $filename = $path . DIRECTORY_SEPARATOR . $name . '.png';
 
		$im = imagecreatetruecolor(150,112);
		$x = imagesx($imorig);
		$y = imagesy($imorig);
		if (imagecopyresampled($im,$imorig , 0,0,0,0,150,112,$x,$y)) 
		{
			imagejpeg($im, $filename);
		}
        $iMain = $storage->create($path . '/' . $name . '.png', $params);
        @unlink($filename);
        return $iMain;
        // die();
    }
	
	public function getImageURL($url)
	{
		 if(strpos($url, '-/h') > 0)
		 {
		 	$type = substr($url, strrpos($url, '.'));
	     	$image_url = substr($url,strpos($url, '-/h') + 2,strrpos($url, '.') - (strpos($url, '-/h') + 2)) . $type;
			$image_url = str_replace("%3A", ":", $image_url);
			return $image_url;
		 }
		 else
		 {
	     	return $url;
		 }
  	}
}

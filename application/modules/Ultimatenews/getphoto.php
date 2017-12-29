<?php

set_time_limit(0);

$table = Engine_Api::_() -> getDbtable('contents', 'ultimatenews');
$select = $table->select()->where("is_got_thumb = 0")->limit(50);

$items = $table -> fetchAll($select);

if (count($items))
{
	$count = 0;
	foreach ($items as $item)
	{
		if (!$item->image)
		{
			preg_match('/img[^>]*src="([^"]*)"/i', $item->content, $matches );
			if ( count($matches) && isset($matches[1]) )
			{
				list($width, $height, $type, $attr) = getimagesize($matches[1]);
				if ( ($width > 48) && ($height > 48) )
				{
					try{
						$storage_file = Engine_Api::_()->getApi('core', 'ultimatenews') ->saveImg($matches[1], md5($matches[1]));
					}
					catch (Exception $e) {
						echo $e;
						exit;
					}
					$item->image = $storage_file->storage_path;
					$item->photo_id = $storage_file->file_id;
					
					$count++;
				}
			}
		}
		
		$item -> is_got_thumb = 1;
		$item -> save();
	}
	
	echo "Got thumbnail sucessfully for {$count} news";
}

echo "<br />DONE";


	
	 
	 
	
	
	

	

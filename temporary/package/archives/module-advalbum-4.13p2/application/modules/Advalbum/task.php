<?php
/*
 * GETTING PARSED PHOTO IDS
 */

//wget -O- "http://<yoursite>/?m=lite&name=task&module=advalbum" > /dev/null 

$limit = Engine_Api::_()->getApi("settings", "core")->getSetting('album_max_photo_crontask', 100);
$photoColorTbl = Engine_Api::_()->getDbTable("photocolors", "advalbum");
$db = $photoColorTbl->getAdapter();
$photoIds = $db
	->select()
	->from('engine4_advalbum_photocolors')
	->query()
	->fetchAll(Zend_Db::FETCH_COLUMN, 1);
$str_photoIds = "";
if($photoIds)
	$str_photoIds = array_unique($photoIds);

/*
 * PROCESSING
 */
$photoTbl = Engine_Api::_()->getDbTable("photos", "advalbum");
$select = $photoTbl
	->select()
	->where("photo_id NOT IN (?)", $str_photoIds)
	->limit($limit);
$photos = $photoTbl->fetchAll($select);
if (count($photos))
{
	foreach ($photos as $photo)
	{
		$photo -> parseColor();
	}
	echo "PARSED PHOTO SUCCESSFULLY!";
}
else
{
	echo "NO PHOTOS TO PARSE!";
}

?>
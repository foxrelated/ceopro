<?php
$css = "";
if($this->no_title)
{
	$css .= " ".$this->no_title;
}
$album_listing_id = 'album_listing_featured';
$no_albums_message = $this->translate('There are no featured albums.');
echo $this->partial('_featuredalbum.tpl', 'advalbum', array(
	'arr_albums' => $this->arr_albums, 
	'album_listing_id'=> $album_listing_id, 
	'no_albums_message'=>$no_albums_message, 
	'short_title'=>1, 
	'css'=>$css,
	'class_mode' => $this->class_mode,
	'view_mode' => $this->view_mode,
	'mode_enabled' => $this->mode_enabled,
));
?>
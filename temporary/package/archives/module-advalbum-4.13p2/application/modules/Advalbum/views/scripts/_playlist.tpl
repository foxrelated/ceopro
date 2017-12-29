<?php echo '<?xml version="1.0" encoding="utf-8"?>' ?>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
	<trackList>
	<?php 
	$photo_count = count($this->photo_list);
	foreach ($this->photo_list as $photo_item) { 
		$photo_title = trim($photo_item->getShortTitle(62));
		if (!$photo_title) {
			$photo_title = $this->translate('[Untitled]');
		}
	
	?>
		<track>
			<title><?php echo htmlentities($photo_title)?></title>
			<creator></creator>
			<location><?php echo fixPhotoURL("http://" . $_SERVER['HTTP_HOST'] . $photo_item->getPhotoUrl("profile.normal"));?></location>
			<info></info>
		</track>
	<?php } ?>
	</trackList>
</playlist>
<?php function fixPhotoURL($photo_url) {
	$pos = strpos($photo_url, '?');
	if ($pos!==FALSE) {
		$photo_url = substr($photo_url, 0, $pos);
	}
	return $photo_url;
}
?>

<?php
$album_list = array();
$album_count = 0;
if (isset($this->arr_albums)) {
	$album_list = $this->arr_albums;
	$album_count = count($album_list);
} else if (isset($this->paginator)) {
	$album_list = $this->paginator;
	$album_count = $this->paginator->getTotalItemCount();
}
if ($album_count<=0) { // no photos
	if (isset($this->no_albums_message)) {
?>
<div class="tip">
      <span><?php echo $this->no_albums_message;?></span>
</div>
<?php
	}
	return;
}

$album_listing_id = "";
if (isset($this->album_listing_id)) {
	$album_listing_id = trim($this->album_listing_id);
}
if (!$album_listing_id)  $album_listing_id = 'album_listing_' . date("Ymdhis");

$css_main = "";
if ($this->css) {
	$css_main = "class='{$this->css}'";
}

$shortenLength = 15;
?>
<div <?php echo $css_main;?> id="<?php echo $album_listing_id; ?>">
<div class="ymb_thumb_slide">
  <ul class="thumbs thumbs_album_small swiper-wrapper">
     <?php
	foreach($album_list as $album ):
		$album_title_full = trim($album->getTitle());
		$album_title = Advalbum_Api_Core::shortenText($album_title_full, $shortenLength);
		//$album_title = str_replace("...", "<small>...</small>", $album_title);
		$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
		if ($album->count()>1) {
			$strPhotos = $this->translate('%1$d photos', $album->count());
		} else {
			$strPhotos = $this->translate('%1$d photo', $album->count());
		}
		$tooltip_text = $this->translate('%1$s (%2$s)', $album_title_tooltip, $strPhotos);
		$album_title_owner = $album->getOwner()->getTitle();
		$album_owner = $this->htmlLink($album->getHref(), Advalbum_Api_Core::shortenText($album_title_owner, $shortenLength), array('title' => $album_title_owner));

	 ?>
      <li id="thumbs-photo-album-<?php echo $album->album_id ?>" class="swiper-slide">
		<a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" title="<?php echo $tooltip_text; ?>"><span style="width:84px;height:63px;padding:0;margin:0;background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span></a>
		<p class="thumbs_info">
            <span class="thumbs_title" style="white-space:nowrap;"><a href="<?php echo $album->getHref(); ?>" title="<?php echo $tooltip_text; ?>"><?php echo $album_title; ?></a></span>
            <span class='advalbum_list_photos'><?php echo $this->translate('By %1s', $album_owner); ?></span>
            <?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album)); ?>

		</p>
	  </li>
    <?php endforeach;?>
  </ul>
</div>
</div>
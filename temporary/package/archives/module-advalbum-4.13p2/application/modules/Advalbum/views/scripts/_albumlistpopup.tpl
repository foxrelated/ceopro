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
	if (isset($this->no_albums_message) && $this->no_albums_message) {
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

if (isset($this->no_author_info) && $this->no_author_info) {
?>
<style>

</style>
<?php
}

$shortenLength = 20;
?>
<div <?php echo $css_main;?> id="<?php echo $album_listing_id; ?>">
  <ul class="thumbs thumbs_album">
     <?php 
	foreach($album_list as $album ):
		$album_title_full = trim($album->getTitle());
		$album_title_tooltip = "";
		if (isset($this->short_title) && $this->short_title) {
			$album_title = Advalbum_Api_Core::shortenText($album_title_full, $shortenLength);
			$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
		} else {
			$album_title = $album_title_full;
		}
	 ?>
      <li id="thumbs-photo-album-<?php echo $album->album_id ?>" style="width: auto;">
        <a class="thumbs_photo" style="margin-bottom: 10px; float: left" href="<?php echo $album->getHref();?>">
        <span style="width:120px;height:90px;padding:0;margin:0;background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);">
        </span></a>
        <p class="thumbs_info" style="float: left; width: 260px; padding-left: 20px;">
            <span class="thumbs_title" style="white-space:nowrap; width: 260px"><a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>">
            <?php echo $this->translate('%1$s\'s Album: %2$s', $album->getOwner()->__toString(), $this->htmlLink($album, $album->getTitle())); ?>
            </a></span>
            <?php echo $this->translate('Album added time: ');?> <?php echo $this->timestamp($album->creation_date) ?><br/>
            <?php 
				$photos_count = $album->count();
				if ($photos_count>1) {
					$str_photos = $this->translate('%1$s photos', $photos_count);
				} else {
				    $str_photos = $this->translate('%1$s photo', $photos_count);
				}
				$str_views = $this->translate('Views: %1$d', $album->view_count);
				$str_comments = $this->translate('Comments: %1$d', $album->comment_count);
				$likes_count = $album->like_count;
				if ($likes_count>1) {
					$str_likes = $this->translate('%1$d likes', $likes_count);
				} else {
				    $str_likes = $this->translate('%1$d like', $likes_count);
				}
				$album_info = $this->translate('%1$s<br/> %2$s<br/> %3$s', $str_photos, $str_views, $str_comments);
				echo $album_info;
			?>
		</p>
	  </li>
    <?php endforeach;?>
  </ul>
</div>
<?php if (isset($this->no_bottom_space) && $this->no_bottom_space) {
} else { ?>
<div style="margin-top:20px;"></div>
<?php } ?>
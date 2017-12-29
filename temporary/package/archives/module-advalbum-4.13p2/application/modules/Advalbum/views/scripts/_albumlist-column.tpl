<div id="<?php echo $this->album_listing_id; ?>" class="album-listing-view-mode">
	<ul class="ynadvalbum-grid-list ynadvalbum_listing_items ynadvalbum_grid_view">
	<?php
	$thumb_photo = '';
	foreach($this->arr_albums as $album ):
		$album_title_full = trim($album->getTitle());
		$album_title_tooltip = "";
		if (isset($this->short_title) && $this->short_title) {
		$album_title = Advalbum_Api_Core::shortenText($album_title_full, 20);
		$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
		} else {
		$album_title = $album_title_full;
		}
	?>
		<li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynadvalbum_listing_item advalbum_albums_grid_thumb'>
			<div class="ynadvalbum_album_listing thumbs_photo_grid">
				<span class="ynadvalbum_background" style="background-image: url(<?php echo $album->getPhotoUrl($thumb_photo); ?>);"></span>
				<div id="ynadvalbum_link_temp">
					<label><?php echo $this->locale()->toNumber($album->count()); ?><span class="ynicon yn-photo-o"></span></label>
					<div class="ynadvalbum_other_album_info">
						<?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album)); ?>
						<a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album->getTitle(); ?></a>
						<?php
						// end photo album
						$str_views = '<span class="yn_stats yn_stats_view">'.$this->translate(array('%s <span>view</span>', '%s <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)).'</span>';
						$str_comments = '<span class="yn_stats yn_stats_comment">'.$this->translate(array('%s <span>comment</span>', '%s <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)).'</span>';
						// $str_likes = $this->translate(array('%s advalbum_like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count));
						echo "<span class='advalbum_list_photos'>" . $str_views . '<span class="yn_dots">.</span>' . $str_comments . "</span>";
						?>
					</div>
					<a  href="<?php echo $album->getHref(); ?>"></a>
				</div>
			</div>
		</li>
	<?php endforeach;?>
	</ul>
</div>

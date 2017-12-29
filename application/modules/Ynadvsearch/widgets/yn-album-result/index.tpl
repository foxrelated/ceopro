<div class='count_results'>
    <span class="search_icon fa fa-search"></span>
    <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
    <span class="total_results">(<?php echo $this->total_content?>)</span>
    <span class="label_results"><?php echo $this->htmlLink(array('route' => 'album_general'), $this->label_content, array());?></span>
</div>

<?php
$session = new Zend_Session_Namespace('mobile');
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
	$css_main = "{$this->css}";
}

if (isset($this->no_author_info) && $this->no_author_info) {
	?>
	<?php
}
$shortenLength = 20;
?>
<div class="ynadvalbum_most_comments_photo">
	<div class ="<?php echo $css_main;?>" id="<?php echo $album_listing_id; ?>">
		<?php if(!$session -> mobile):?>
			<div class="ynsearch-listing-tab">
				<div title="<?php echo $this->translate('List view');?>" class="list-view" data-view="list"></div>
				<div title="<?php echo $this->translate('Grid view');?>" class="grid-view" data-view="grid"></div>
				<div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view" data-view="pinterest"></div>
			</div>
		<?php endif;?>
		<div id="<?php echo $album_listing_id; ?>" class="album-listing-view-mode <?php echo $css_main;?>">
			<div id="<?php echo $album_listing_id; ?>_view" class="<?php if ($this -> class_mode) echo $this -> class_mode; else echo 'ynalbum-list-view'; ?>">
				<ul class="ynalbum-list">
					<?php
					$thumb_photo = 'thumb.normal';
					if(defined('YNRESPONSIVE'))
					{
						$thumb_photo = 'thumb.profile';
					}
					foreach($album_list as $album ):
						$album_title_full = trim($album->getTitle());
						$album_title_tooltip = "";

						$album_title = $album_title_full;

						?>
						<li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynadvalbum_albums_thumb'>
							<a href="<?php echo $album->getHref(); ?>" class="ynalbum-thumb">
								<span class="span-image" style="background-image:url(<?php echo $album->getPhotoUrl(); ?>);"></span>
							</a>
							<div class="ynalbum-content">
								<?php
								$isOwner = $album -> getOwner() -> isSelf(Engine_Api::_() -> user() -> getViewer());
								$canEdit = $album -> authorization()->isAllowed(null, 'edit');
								?>
								<?php if ($isOwner && $canEdit): ?>
									<div class="ynadvalbum-options">
										<div class="ynadvalbum-options-button" >
											<span class="ynicon yn-arr-down"></span>
										</div>
										<?php echo $this->partial('_item-menu-album.tpl', 'advalbum', array('manage' => true, 'album' => $album)); ?>
									</div>
								<?php endif; ?>
								<span class="thumbs_title"><a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album_title; ?></a></span>
								<span class="ynalbum-rating"><?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album)); ?></span>
								<div class="thumbs_info">
									<?php
									// end photo album
									$photos_count = $album->count();
									$str_photos = $this->translate(array('%s photo', '%s photos', $photos_count), $this->locale()->toNumber($photos_count));
									if (isset($this->no_author_info) && $this->no_author_info) {
										$album_info_1 = $this->translate('%1$s', $str_photos);
									} else {
										$album_info_1 = $this->translate('%2$s by %1$s', $album->getOwner()->__toString(), $str_photos);
									}
									?>

									<div class='album-info'>
										<span class='advalbum_list_photos'><?php echo $album_info_1; ?></span>
										<span class="advalbum_album_statis">

										<?php if (isset($this->no_author_info) && $this->no_author_info) : ?>

											<span class="yn_stats yn_stats_view">
											<?php echo $this->translate(array('%s <span>view</span>', '%s <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
											</span>
											<span class="yn_dots">.</span>
											<span class="yn_stats yn_stats_comment">
											<?php echo $this->translate(array('%s <span>comment</span>', '%s <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
											</span>
											<span class="yn_dots">.</span>
											<span class="yn_stats yn_stats_like">
											<?php echo $this->translate(array('%s <span>like</span>', '%s <span>likes</span>', $album->like_count), $this->locale()->toNumber($album->like_count)); ?>
											</span>
										<?php else : ?>
											<span class="yn_stats yn_stats_view">
											<?php echo $this->translate(array('%s <span>view</span>', '%s <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
											</span>
											<span class="yn_dots">.</span>
											<span class="yn_stats yn_stats_comment">
											<?php echo $this->translate(array('%s <span>comment</span>', '%s <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
											</span>
										<?php endif; ?>
									</span>
									</div>

									<?php
									echo "<div class='album-photo-lists'>";
									//photo album
									if($album->virtual){
										$photo_list = $album->getVirtualPhotos();
									}
									else {
										$photo_list = $album->getAlbumPhotos();
									}
									foreach ($photo_list as $photo)
									{
										echo '<a class="album-photo-list" href="'.$photo->getHref().'"><span style="background-image: url('.$photo->getPhotoUrl().');"></span></a>';
									}
									echo "</div>";

									?>
								</div>
							</div>
						</li>
					<?php endforeach;?>
				</ul>

				<ul class="ynadvalbum-grid-list ynadvalbum_listing_items ynadvalbum_grid_view">
					<?php
					$thumb_photo = '';

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
						<li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynadvalbum_listing_item advalbum_albums_grid_thumb'>
							<div class="ynadvalbum_album_listing thumbs_photo_grid">
					        <span class="ynadvalbum_background" style="background-image: url(<?php echo $album->getPhotoUrl($thumb_photo); ?>);">
					        	<label><?php echo $this->locale()->toNumber($album->count()); ?></label>
					        </span>

								<a id="ynadvalbum_link_temp" href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"></a>
								<?php
								$isOwner = $album -> getOwner() -> isSelf(Engine_Api::_() -> user() -> getViewer());
								$canEdit = $album -> authorization()->isAllowed(null, 'edit');
								?>
								<?php if ($isOwner && $canEdit): ?>
									<div class="ynadvalbum-options">
										<div class="ynadvalbum-options-button" >
											<span class="ynicon yn-arr-down"></span>
										</div>
										<?php echo $this->partial('_item-menu-album.tpl', 'advalbum', array('manage' => true, 'album' => $album)); ?>
									</div>
								<?php endif; ?>

							</div>

							<div class="ynadvalbum_album_listing_title">
								<a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album->getTitle(); ?></a>
								<?php
								// end photo album
								$photos_count = $album->count();
								$str_photos = $this->translate(array('%s advalbum_photo', '%s photos', $photos_count), $this->locale()->toNumber($photos_count));


								if (isset($this->no_author_info) && $this->no_author_info) {
									$album_info_1 = $this->translate('%1$s', $str_photos);
								} else {
									$album_info_1 = $this->translate('<span>by</span> %1$s', $album->getOwner()->__toString(), $str_photos);
								}
								echo "<span class='advalbum_list_photos'>" . $album_info_1 . "</span>";
								?>

								<span class="advalbum_album_statis">
							<?php if (isset($this->no_author_info) && $this->no_author_info) : ?>
								<span class="yn_stats yn_stats_view">
								<?php echo $this->translate(array('%s <span>view</span>', '%s <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
								</span>
								<i class="yn_dots">.</i>
								<span class="yn_stats yn_stats_comment">
								<?php echo $this->translate(array('%s <span>comment</span>', '%s <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
								</span>
							<?php else : ?>
								<span class="yn_stats yn_stats_view">
								<?php echo $this->translate(array('%s <span>view</span>', '%s <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
								</span>
								<i class="yn_dots">.</i>
								<span class="yn_stats yn_stats_comment">
								<?php echo $this->translate(array('%s <span>comment</span>', '%s <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
								</span>
								<i class="yn_dots">.</i>
								<span class="yn_stats yn_stats_like">
								<?php echo $this->translate(array('%s <span>like</span>', '%s <span>likes</span>', $album->like_count), $this->locale()->toNumber($album->like_count)); ?>
								</span>
							<?php endif; ?>
						</span>

								<?php
								// rating
								echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album));
								?>
							</div>
						</li>

					<?php endforeach;?>
				</ul>

				<ul id="<?php echo $album_listing_id; ?>_tiles" class="ynadvalbum-pinterest-listing">
					<?php
					$thumb_photo = 'thumb.normal';
					if(defined('YNRESPONSIVE'))
					{
						$thumb_photo = 'thumb.profile';
					}
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
					<li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynadvalbum_listing_item ynsearch_pinterest'>
						<div class="ynadvalbum_thumbs_content">
							<div class="ynadvalbum_thumbs_image">
								<a id="ynadvalbum_link_temp" href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"></a>
								<img src="<?php echo $album->getPhotoUrl(); ?>" alt=""/>
								<label><?php echo $this->locale()->toNumber($album->count()); ?></label>
								<?php
								$isOwner = $album -> getOwner() -> isSelf(Engine_Api::_() -> user() -> getViewer());
								$canEdit = $album -> authorization()->isAllowed(null, 'edit');
								?>
								<?php if ($isOwner && $canEdit): ?>
									<div class="ynadvalbum-options">
										<div class="ynadvalbum-options-button" >
											<span class="ynicon yn-arr-down"></span>
										</div>
										<?php echo $this->partial('_item-menu-album.tpl', 'advalbum', array('manage' => true, 'album'=>$album)); ?>
									</div>
								<?php endif; ?>

							</div>
							<div class="ynadvalbum_thumbs_image-info clearfix">
								<div class="album-author-image">
									<?php
									$user = $album->getOwner();
									echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));
									$album_info_1 = $this->translate('<span>by</span> %1$s', $user->__toString(), $str_photos);
									?>
								</div>
								<a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album->getTitle(); ?></a>
								<div class="album-content">
									<?php
									echo "<span class='advalbum_list_photos'>" . $album_info_1 . "</span>";
									// rating
									echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album));

									// end photo album
									$str_views = $this->translate(array('advalbum_view', 'views', $album->view_count), $this->locale()->toNumber($album->view_count));
									$str_comments = $this->translate(array('advalbum_comment', 'comments', $album->comment_count), $this->locale()->toNumber($album->comment_count));
									$str_likes = $this->translate(array('advalbum_like', 'likes', $album->like_count), $this->locale()->toNumber($album->like_count));
									?>
								</div>
								<div class="ynadvalbum_thumbs_stats ynadvalbum_stats">
									<div class="ynadvalbum_thumbs_stats-info yn_stats yn_stats_view">
										<label><?php echo $this->locale()->toNumber($album->view_count); ?></label>
										<span><?php echo $str_views; ?></span>
									</div>
									<div class="ynadvalbum_thumbs_stats-info ynadvalbum_border yn_stats yn_stats_comment">
										<label><?php echo $this->locale()->toNumber($album->comment_count); ?></label>
										<span><?php echo $str_comments; ?></span>
									</div >
									<div class="ynadvalbum_thumbs_stats-info yn_stats yn_stats_like">
										<label><?php echo $this->locale()->toNumber($album->like_count); ?></label>
										<span><?php echo $str_likes; ?></span>
									</div>
								</div>
							</div>

						</div>

						<?php endforeach;?>
				</ul>
			</div>
		</div>
	</div>
</div>

<div id='paginator'>
	<?php if( $this->paginator->count() > 1 ): ?>
	     <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	          )); ?>
	<?php endif; ?>
</div>
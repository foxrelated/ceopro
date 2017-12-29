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
		<div class="ynadvalbum-listing-tab">
			<?php if(in_array('list', $this -> mode_enabled)):?>
			<div title="<?php echo $this->translate('List view');?>" class="list-view <?php if($this -> view_mode == 'list') echo 'active';?>" data-view="ynalbum-list-view"></div>
			<?php endif;?>
			<?php if(in_array('grid', $this -> mode_enabled)):?>
			<div title="<?php echo $this->translate('Grid view');?>" class="grid-view <?php if($this -> view_mode =='grid') echo 'active';?>" data-view="ynalbum-grid-view"></div>
			<?php endif;?>
			<?php if(in_array('pinterest', $this -> mode_enabled)):?>
			<div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view <?php if($this -> view_mode == 'pinterest') echo 'active';?>" data-view="ynalbum-pinterest-view"></div>
			<?php endif;?>
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
					<li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynadvalbum_albums_thumb clearfix'>
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
					<li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynadvalbum_listing_item ynadvalbum_pinterest'>
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
	<?php if (isset($this->no_bottom_space) && $this->no_bottom_space) {
	} else { ?>
	<div style="margin-top:20px;"></div>
	<?php } ?>
	<?php if(!$session -> mobile):?>
	<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/scripts/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
	<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/scripts/wookmark/jquery.wookmark.js"></script>
	<script type="text/javascript">
		jQuery.noConflict();
		(function ($){
			$('#<?php echo $album_listing_id; ?>_tiles').imagesLoaded(function() {

				var options = {
					itemWidth: 220,
					autoResize: true,
					container: $('#<?php echo $album_listing_id; ?>_tiles'),
					offset: 15,
					outerOffset: 0,
					flexibleWidth: '50%'
				};

				// Get a reference to your grid items.
				var handler = $('#<?php echo $album_listing_id; ?>_tiles li.ynadvalbum_pinterest');

				var $window = $(window);
				$window.resize(function() {
					var windowWidth = $window.width(),
							newOptions = { flexibleWidth: '50%' };
					// Right to Left layout
					if(en4.orientation == 'rtl')
						newOptions.direction = 'right';
					// Breakpoint
					if (windowWidth < 1024) {
						newOptions.flexibleWidth = '100%';
					}

					handler.wookmark(newOptions);
				});
				// Right to Left layout
				if(en4.orientation == 'rtl')
					options.direction = 'right';
				// Call the layout function.
				handler.wookmark(options);
			});

			$('#<?php echo $album_listing_id; ?> .ynadvalbum-listing-tab > div').click(function() {
				var handler = $('#<?php echo $album_listing_id; ?>_tiles li.ynadvalbum_pinterest');
				var options = {
					itemWidth: 220,
					autoResize: true,
					container: $('#<?php echo $album_listing_id; ?>_tiles'),
					offset: 25,
					outerOffset: 0,
					flexibleWidth: '50%'
				};

				// Breakpoint
				if ( $(window).width() < 1024) {
					options.flexibleWidth = '100%';
				}
				// Right to Left layout
				if(en4.orientation == 'rtl')
					options.direction = 'right';

				$('#<?php echo $album_listing_id; ?> .ynadvalbum-listing-tab > div').removeClass('active');
				$(this).addClass('active');

				$('#<?php echo $album_listing_id; ?>_view').attr('class', $(this).data('view') );

				if ( $(this).hasClass('list-view') ) {
					setCookie('<?php echo $album_listing_id; ?>view_mode','list');
				}
				if ( $(this).hasClass('grid-view') ) {
					setCookie('<?php echo $album_listing_id; ?>view_mode','grid');
				}
				if ( $(this).hasClass('pinterest-view') ) {
					handler.wookmark(options);
					setCookie('<?php echo $album_listing_id; ?>view_mode','pinterest');
				}
			});

		})(jQuery);
		window.addEvent('domready', function()
		{
			var view_mode = "";
			en4.core.runonce.add(function()
			{
				var view_mode  = getCookie('<?php echo $album_listing_id; ?>view_mode');
				$$('#main_tabs li.tab_layout_<?php echo $album_listing_id; ?>').addEvent('click', function(){
					if(view_mode == 'pinterest')
					{
						var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynadvalbum_pinterest');
						var options = {
							itemWidth: 220,
							autoResize: true,
							container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
							offset: 25,
							outerOffset: 0,
							flexibleWidth: '50%'
						};
						// Right to Left layout
						if(en4.orientation == 'rtl')
							options.direction = 'right';
						// Breakpoint
						if ( jQuery(window).width() < 1024) {
							options.flexibleWidth = '100%';
						}
						handler.wookmark(options);
					}
				});

				$$('li.tab_<?php echo $album_listing_id ?>')[0].getChildren('a')[0].addEvent('click', function(){
						var view_mode  = getCookie('<?php echo $album_listing_id; ?>view_mode') || '<?php echo $this->view_mode ?>';
					if(view_mode == 'pinterest')
					{
						var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynadvalbum_pinterest');
						var options = {
							itemWidth: 220,
							autoResize: true,
							container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
							offset: 25,
							outerOffset: 0,
							flexibleWidth: '50%'
						};
						// Right to Left layout
						if(en4.orientation == 'rtl')
							options.direction = 'right';
						// Breakpoint
						if ( jQuery(window).width() < 1024) {
							options.flexibleWidth = '100%';
						}
						handler.wookmark(options);
					}
				});
			});
			if(getCookie('<?php echo $album_listing_id; ?>view_mode')!= "")
			{
				view_mode = getCookie('<?php echo $album_listing_id; ?>view_mode');
				document.getElementById('<?php echo $album_listing_id; ?>_view').set('class',"ynalbum-"+getCookie('<?php echo $album_listing_id; ?>view_mode')+"-view");

				$$('#<?php echo $album_listing_id; ?> .ynadvalbum-listing-tab > div').removeClass('active');
				if(view_mode == "list" )
				{
					$$('#<?php echo $album_listing_id; ?> .ynadvalbum-listing-tab > div.list-view').addClass('active');
				}
				if(view_mode == "grid" )
				{
					$$('#<?php echo $album_listing_id; ?> .ynadvalbum-listing-tab > div.grid-view').addClass('active');
				}
				if(view_mode == "pinterest" )
				{
					$$('#<?php echo $album_listing_id; ?> .ynadvalbum-listing-tab > div.pinterest-view').addClass('active');
					var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynadvalbum_pinterest');
					var options = {
						itemWidth: 220,
						autoResize: true,
						container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
						offset: 25,
						outerOffset: 0,
						flexibleWidth: '50%'
					};
					// Right to Left layout
					if(en4.orientation == 'rtl')
						options.direction = 'right';
					// Breakpoint
					if ( jQuery(window).width() < 1024) {
						options.flexibleWidth = '100%';
					}
					handler.wookmark(options);
				}
			}
			else
			{
				document.getElementById('<?php echo $album_listing_id; ?>_view').set('class', "<?php echo $this -> class_mode;?>");
				$$('#<?php echo $album_listing_id; ?> .ynadvalbum-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
				if("<?php echo  $this->view_mode ?>" == "pinterest" )
				{
					var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynadvalbum_pinterest');
					var options = {
						itemWidth: 220,
						autoResize: true,
						container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
						offset: 25,
						outerOffset: 0,
						flexibleWidth: '50%'
					};
					// Right to Left layout
					if(en4.orientation == 'rtl')
						options.direction = 'right';
					// Breakpoint
					if ( jQuery(window).width() < 1024) {
						options.flexibleWidth = '100%';
					}
					handler.wookmark(options);
				}
			}
			if(view_mode == "pinterest" )
			{
				var tab_element = document.getElementsByClassName('tab_layout_<?php echo $album_listing_id; ?>');
				if(tab_element)
				{
					var class_name = '.tab_layout_<?php echo $album_listing_id; ?>';
					//console.log(class_name);
					$$(class_name).addEvent('click', function(event){
						var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynadvalbum_pinterest');
						var options = {
							itemWidth: 220,
							autoResize: true,
							container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
							offset: 25,
							outerOffset: 0,
							flexibleWidth: '50%'
						};
						// Right to Left layout
						if(en4.orientation == 'rtl')
							options.direction = 'right';
						// Breakpoint
						if ( jQuery(window).width() < 1024) {
							options.flexibleWidth = '100%';
						}
						handler.wookmark(options);
					});
				}
			}
		});

		function setCookie(cname,cvalue,exdays)
		{
			var d = new Date();
			d.setTime(d.getTime()+(exdays*24*60*60*1000));
			var expires = "expires="+d.toGMTString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		}

		function getCookie(cname)
		{
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i=0; i<ca.length; i++)
			{
				var c = ca[i].trim();
				if (c.indexOf(name)==0) return c.substring(name.length,c.length);
			}
			return "";
		}
	</script>
	<?php endif;?>
</div>

<script type="text/javascript">
	window.addEvent('domready', function() {
		ynadvalbumOptions();
	});

</script>
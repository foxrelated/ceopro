<script>
window.addEvent('domready', function()
{
	addEventAddTo();
	// add event for button Add to of Adv.Album photo
	function addEventAddTo() {
		$(document.body).addEvent('click', function(event){
			 var target = event.target;
             // if the user click outside the add to menu box, remove the add to menu box
			 if ($('ynadvalbum_addTo_menu_list')) {
                 if (!$('ynadvalbum_addTo_menu_list').contains(target)){
                	 if ($('ynadvalbum_addTo_menu_list')) {
                   		$('ynadvalbum_addTo_menu_list').destroy();
                	 }
                 }
                 else {
             	    if (target.get('id') == 'ynadvalbum_addTo_downloadresizephoto' || target.get('id') == 'ynadvalbum_addTo_downloadfullphoto') {
             	    	if ($('ynadvalbum_addTo_menu_list')) {
                       		$('ynadvalbum_addTo_menu_list').setStyle('display', 'none');
                    	 }
                 	}
                 }
			 }
		});
		$$('button.ynadvalbum_add_button').each(function(el){
			el.addEvent('click', function(e){
				e.stop();
				if ($('ynadvalbum_addTo_menu_list')) {
					$('ynadvalbum_addTo_menu_list').destroy();
				}

				var photo_id = el.get('photo-id');
				var album_id = el.get('album-id');

				var advalbum_addTo_menu_list  = new Element("div", {
					'id': "ynadvalbum_addTo_menu_list"
				});
				var advalbum_addTo_frame_loading  = new Element("div", {
					'id': "ynadvalbum_addTo_frame_loading",
					'class': 'ynadvalbum_addTo_frame'
				});
				var advalbum_addTo_loading  = new Element("div", {
					'id': "ynadvalbum_addTo_loading"
				});
				advalbum_addTo_frame_loading.adopt(advalbum_addTo_loading);
				advalbum_addTo_menu_list.adopt(advalbum_addTo_frame_loading);
				$(document.body).adopt(advalbum_addTo_menu_list);

				var position = el.getPosition();
				$('ynadvalbum_addTo_menu_list').setPosition({x: position.x, y: position.y + el.getHeight()});

			     var makeRequest = new Request({
			     	url: '<?php echo $this->url(array('action' => 'add-to'), 'album_extended', true) ?>',
			        data: { 'photo_id' : photo_id, 'album_id' : album_id },
			     	onComplete: function (respone){
			     		$('ynadvalbum_addTo_menu_list').innerHTML = respone;
			     		$$('#ynadvalbum_addTo_list .smoothbox').each(function(element){
			     			element.addEvent('click', function(event){
								event.stop();
								Smoothbox.open(this);
								$('ynadvalbum_addTo_menu_list').destroy();
							});
					    });
			     	}
			     }).send();

			});
		});
	}
});
</script>

<?php
$session = new Zend_Session_Namespace('mobile');
$params = array();
if($this -> album)
{
	$params = array('album_virtual' => $this -> album -> getIdentity());
}
$photo_list = array();
if (isset($this->arr_photos)) {
	$photo_list = $this->arr_photos;
} else if (isset($this->paginator)) {
	$photo_list = $this->paginator;
}
if (count($photo_list)<=0) { // no photos
	if (isset($this->no_photos_message) && $this->no_photos_message) {
?>
<div class="tip">
      <span><?php echo $this->no_photos_message;?></span>
</div>

<?php
	}
	return;
}

$photo_listing_id = "";
if (isset($this->photo_listing_id)) {
	$photo_listing_id = trim($this->photo_listing_id);
}
if (!$photo_listing_id)  $photo_listing_id = 'photo_listing_' . date("Ymdhis");

$userVirtualAlbums = Engine_Api::_()->getDbTable("albums", "advalbum")->getVirtualAlbumsAssoc(Engine_Api::_()->user()->getViewer());
$has_virtual_album = (count($userVirtualAlbums)) ? true : false;

$css_main = "";
if ($this->css) {
	$css_main = "class='{$this->css}'";
}

$sortable_css = "class = 'advalbum_albums_grid_thumb ynadvalbum_border_temp ynadvalbum_pinterest";
if ($this->sortable) {
	$sortable_css .= " sortable'";
}
else
{
	$sortable_css .= "'";
}

$shortenLength = Advalbum_Api_Core::SHORTEN_LENGTH_DEFAULT;

$bShowTitle = FALSE;
if (isset($this->show_title_info) && $this->show_title_info) {
	$bShowTitle = TRUE;
}
if (!$bShowTitle) { ?>
<style>

</style>
<?php
}
?>
<div class="adv-album-view-mode">
<div <?php echo $css_main;?> id="<?php echo $photo_listing_id; ?>">
	<?php if(!$session -> mobile):?>
		<div class="ynadvalbum-listing-tab">
			<?php if(in_array('grid', $this -> mode_enabled)):?>
		    <div title="<?php echo $this->translate('Grid view');?>" class="grid-view <?php if($this -> view_mode == 'list') echo 'active';?>" data-view="ynalbum-grid-view"></div>
		    <?php endif;?>
		    <?php if(in_array('pinterest', $this -> mode_enabled)):?>
		   	<div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view <?php if($this -> view_mode == 'pinterest') echo 'active';?>" data-view="ynalbum-pinterest-view"></div>
		   	<?php endif;?>
		</div>
	<?php endif;?>

<div class="photo-list-content ynalbum-grid-view" >

<div class="photo-grid-view <?php if($session->mobile) echo "ymb_thumb_slide"?>">
  <ul class="ynadvalbum-grid-list gallery<?php echo $this->rand; ?> clearfix swiper-wrapper">
 <?php $index = 0;
  $thumb_photo = '';
	foreach($photo_list as $photo ):
		$photo_title_full = trim($photo->getTitle());
		// photo title
		$photo_title_tooltip = Advalbum_Api_Core::defaultTooltipText($photo_title_full);
		if ($bShowTitle) {
			$photo_title = Advalbum_Api_Core::shortenText($photo_title_full, $shortenLength);
		}
		$strAuthor = "";
		if (isset($this->no_author_info) && $this->no_author_info) {
	  		$album = $photo->getParent();
		} else {
			$album = $photo->getParent();
			$album_title_full = $album->getTitle();
			$album_title = Advalbum_Api_Core::shortenText($album_title_full, 20);
			$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
			$album_owner = $album->getOwner();
			$album_owner_title_full = $album_owner->getTitle();
			$album_owner_title = Advalbum_Api_Core::shortenText($album_owner_title_full, 22);
			$album_owner_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_owner_title_full);
			$strAuthor = $this->translate('<span>By</span> %1$s <span>in</span> %2$s', $this->htmlLink($album_owner, $album_owner_title, array('title' => $album_owner_title_tooltip)), $this->htmlLink($album, $album_title, array('title' => $album_title_tooltip)));
		}
	 ?>
      <li <?php echo $sortable_css;?> id="thumbs-photo-<?php echo $photo->photo_id ?>">
		<div class="ynadvalbum_photos_listing thumbs_photo_grid">
			<span class="ynadvalbum_background" style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
			<div class="ynadvalbum_thumb_photo_info">
				<div class="ynadvalbum_thumb_photo_temp">
					<?php if ($bShowTitle && $photo_title) { ?>
						<?php } ?>
						<?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $photo)); ?>
			            <?php if ($strAuthor): ?>
							<div class="ynadvalbum_thumbs_info_owner">
								<?php echo "$strAuthor"; ?>
							</div>
						<?php endif; ?>
			            <span class="ynadvalbum_album_detail-title"><?php echo $this->htmlLink($photo, $photo_title, array('title' => $photo_title_tooltip, 'class' => 'thumbs_photo_link')); ?></span>

						<div class='ynadvalbum_stats'>
							<span class="yn_stats yn_stats_view">
								<?php
									echo $this->translate(array('%s <span>view</span>', '%s <span>views</span>', $photo->view_count), $this->locale()->toNumber($photo->view_count));
								?>
							</span>
							<i class="yn_dots">.</i>
							<span class="yn_stats yn_stats_comment">
								<?php
									echo $this->translate(array('%s <span>comment</span>', '%s <span>comments</span>', $photo->comment_count), $this->locale()->toNumber($photo->comment_count));
								?>
							</span>
							<i class="yn_dots">.</i>
							<span class="yn_stats yn_stats_like">
								<?php
									echo $this->translate(array('%s <span>like</span>', '%s <span>likes</span>', $photo->like_count), $this->locale()->toNumber($photo->like_count));
								?>
							</span>
						</div>
				</div>
			</div>
			<a  class="ynadvalbum_photo_listing_temp_link advalbum_smoothbox" href="<?php echo $photo->getHref($params) ?>"></a>
			<div class="ynadvalbum-options">
            	<div class="ynadvalbum-options-button" >
            		<span class="ynicon yn-arr-down"></span>
            	</div>
            	<?php echo $this->partial('_item-menu-photo.tpl', 'advalbum', array('view_mode' => isset($this->manage) ? 'manage' : 'list', 'has_virtual_album' => $has_virtual_album, 'album'=>$album, 'photo'=>$photo)); ?>
            </div>
	    </div>
	  </li>
    <?php $index ++; endforeach;?>
  </ul>
</div>
  <ul id="<?php echo $photo_listing_id; ?>_tiles" class="photo-pinterest-view gallery<?php echo $this->rand; ?> clearfix">
 <?php $index = 0;
  $thumb_photo = 'thumb.normal';
	if(defined('YNRESPONSIVE'))
	{
		$thumb_photo = 'thumb.profile';
	}
	foreach($photo_list as $photo ):
		$photo_title_full = trim($photo->getTitle());
		// photo title
		$photo_title_tooltip = Advalbum_Api_Core::defaultTooltipText($photo_title_full);
		if ($bShowTitle) {
			$photo_title = Advalbum_Api_Core::shortenText($photo_title_full, $shortenLength);
		}

		$strAuthor = "";
		if (isset($this->no_author_info) && $this->no_author_info) {

		} else {
			$album = $photo->getParent();
			$album_title_full = $album->getTitle();
			$album_title = Advalbum_Api_Core::shortenText($album_title_full, 20);
			$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);

			$album_owner = $album->getOwner();
			$album_owner_title_full = $album_owner->getTitle();
			$album_owner_title = Advalbum_Api_Core::shortenText($album_owner_title_full, 22);
			$album_owner_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_owner_title_full);

			$strAuthor = $this->translate('<span>By</span> %1$s <span>in</span> %2$s', $this->htmlLink($album_owner, $album_owner_title, array('title' => $album_owner_title_tooltip)), $this->htmlLink($album, $album_title, array('title' => $album_title_tooltip)));
		}
	 ?>
      <li <?php echo $sortable_css;?> id="thumbs-photo-<?php echo $photo->photo_id ?>" class="swiper-slide">
		<div>
			<a  class="ynadvalbum_photo_listing_temp_link advalbum_smoothbox" href="<?php echo $photo->getHref($params) ?>"></a>
			<img class="ynadvalbum_pinteres_thumbs-photo" src="<?php echo $photo->getPhotoUrl(); ?>" />
			<div class="ynadvalbum-options">
			<a  class="temp" href="<?php echo $photo->getHref($params) ?>"></a>
            	<div class="ynadvalbum-options-button" >
            		<span class="ynicon yn-arr-down"></span>
            	</div>
				<?php echo $this->partial('_item-menu-photo.tpl', 'advalbum', array('has_virtual_album' => $has_virtual_album, 'album'=>$album, 'photo'=>$photo)); ?>
            </div>
		</div>
		<div class="ynadvalbum_pinteres_photo_info">
			<div class="ynadvalbum_pinteres_photo_info_owner">
				<?php if ($bShowTitle && $photo_title) { ?>
	            <span class="thumbs_title"><?php echo $this->htmlLink($photo, $photo_title, array('title' => $photo_title_tooltip, 'class' => 'thumbs_photo_link')); ?></span>
				<?php } ?>
	            <?php if ($strAuthor) echo "$strAuthor"; ?>
            </div>
			<span class="ynadvalbum_thumb_stats">
				<span class="yn_stats yn_stats_view">
					<?php
						echo $this->translate(array('%s <span>view</span>', '%s <span>views</span>', $photo->view_count), $this->locale()->toNumber($photo->view_count));
					?>
				</span>
				<i class="yn_dots">.</i>
				<span class="yn_stats yn_stats_comment">
					<?php
						echo $this->translate(array('%s <span>comment</span>', '%s <span>comments</span>', $photo->comment_count), $this->locale()->toNumber($photo->comment_count));
					?>
				</span>
				<i class="yn_dots">.</i>
				<span class="yn_stats yn_stats_like">
					<?php
						echo $this->translate(array('%s <span>like</span>', '%s <span>likes</span>', $photo->like_count), $this->locale()->toNumber($photo->like_count));
					?>
				</span>
			</span>

			<?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $photo)); ?>
		</div>
	  </li>
    <?php $index ++; endforeach;?>
  </ul>
 </div>
	<div style="clear:both"></div>
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

          $('#<?php echo $photo_listing_id; ?>_tiles').imagesLoaded(function() {
            var options = {
              itemWidth: 215,
              autoResize: true,
              container: $('#<?php echo $photo_listing_id; ?>_tiles'),
              offset: 14,
              outerOffset: 0,
              flexibleWidth: '50%'
            };

            // Get a reference to your grid items.
            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li.ynadvalbum_pinterest');

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

         $('#<?php echo $photo_listing_id; ?> .ynadvalbum-listing-tab > div').click(function() {
            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li.ynadvalbum_pinterest');
            var options = {
                  itemWidth: 215,
                  autoResize: true,
                  container: $('#<?php echo $photo_listing_id; ?>_tiles'),
                  offset: 25,
                  outerOffset: 0,
                  flexibleWidth: '50%'
            };
			 // Right to Left layout
			 if(en4.orientation == 'rtl')
				 options.direction = 'right';
            // Breakpoint
            if ( $(window).width() < 1024) {
                options.flexibleWidth = '100%';
            }

            $('#<?php echo $photo_listing_id; ?> .ynadvalbum-listing-tab > div').removeClass('active');
            $(this).addClass('active');

            $('#<?php echo $photo_listing_id; ?> .photo-list-content').attr('class', 'photo-list-content '+$(this).data('view') );

            if ( $(this).hasClass('grid-view') ) {

                setCookie('<?php echo $photo_listing_id; ?>view_mode','grid');
            }

            if ( $(this).hasClass('pinterest-view') ) {
                handler.wookmark(options);
                setCookie('<?php echo $photo_listing_id; ?>view_mode','pinterest');
            }
         });

    })(jQuery);

	window.addEvent('domready', function()
	{
		en4.core.runonce.add(function()
		{
			var view_mode  = getCookie('<?php echo $photo_listing_id; ?>view_mode');
			$$('#main_tabs li.tab_layout_<?php echo $photo_listing_id; ?>').addEvent('click', function(){
				if(view_mode == 'pinterest')
				{
					var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynadvalbum_pinterest');
		            var options = {
	                  itemWidth: 215,
		                  autoResize: true,
		                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
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

			$$('li.tab_<?php echo $photo_listing_id ?>')[0].getChildren('a')[0].addEvent('click', function(){
				var view_mode  = getCookie('<?php echo $photo_listing_id; ?>view_mode') || '<?php echo $this->view_mode ?>';
				if(view_mode == 'pinterest')
				{
					var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynadvalbum_pinterest');
					var options = {
						itemWidth: 215,
						autoResize: true,
						container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
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
		if(getCookie('<?php echo $photo_listing_id; ?>view_mode') != "")
		{
			var view_mode  = getCookie('<?php echo $photo_listing_id; ?>view_mode');
			$$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content ynalbum-'+ view_mode +'-view');
			$$('#<?php echo $photo_listing_id; ?> .ynadvalbum-listing-tab > div').removeClass('active');

			if(view_mode == "grid" )
			{
				$$('#<?php echo $photo_listing_id; ?> .ynadvalbum-listing-tab > div.grid-view').addClass('active');
			}
			if(view_mode == "pinterest" )
			{
				$$('#<?php echo $photo_listing_id; ?> .ynadvalbum-listing-tab > div.pinterest-view').addClass('active');
				var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynadvalbum_pinterest');
		            var options = {
		                  itemWidth: 215,
		                  autoResize: true,
		                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
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
			$$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content '+'<?php echo $this->class_mode;?>');
			$$('#<?php echo $photo_listing_id; ?> .ynadvalbum-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
			if("<?php echo  $this->view_mode ?>" == "pinterest" )
			{
				$$('#<?php echo $photo_listing_id; ?> .ynadvalbum-listing-tab > div.pinterest-view').addClass('active');
				var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynadvalbum_pinterest');
		            var options = {
		                  itemWidth: 215,
		                  autoResize: true,
		                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
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

function showSmoothbox(url) {
		Smoothbox.open(url);
}
function closeSmoothbox() {
	var block = Smoothbox.instance;
	block.close();
}
</script>

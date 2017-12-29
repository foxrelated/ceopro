<script>
    window.addEvent('domready', function(){

        if($('filter_form'))
        {
            $('filter_form').set('action','');
        }

        addEventAddToAlbum();
        // add event for button Add to of Adv.Album photo
        function addEventAddToAlbum() {
            $(document.body).addEvent('click', function(event){
                var target = event.target;
                // if the user click outside the add to menu box, remove the add to menu box
                if (!target.contains($('ynadvalbum_addTo_menu_list'))){
                    if($('ynadvalbum_addTo_menu_list')) {
                        $('ynadvalbum_addTo_menu_list').destroy();
                    }
                }
            });
            $$('button.ynadvalbum_add_button').each(function(el){
                el.addEvent('click', function(e){
                    e.stop();
                    //e.preventDefault();
                    if ($('ynadvalbum_addTo_menu_list')) {
                        $('ynadvalbum_addTo_menu_list').destroy();
                    }

                    var album_id = el.get('album-id');
                    //var parent_offset = el.getOffsetParent().getCoordinates();

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
                        data: { 'album_id' : album_id },
                        onComplete: function (respone){
                            //el.innerHTML = respone;
                            $('ynadvalbum_addTo_menu_list').innerHTML = respone;
                            $$('#ynadvalbum_delete_album .smoothbox').each(function(element){
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
$shortenLength = 20;
?>
<div class="album-listing-view-mode">
    <div class="ynalbum-grid-view">
        <div>
            <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

            <ul class="ynadvalbum-grid-list ynadvalbum_listing_items ynadvalbum_grid_view">
                <?php
		        $thumb_photo = '';

		    	foreach($this->paginator as $album ):
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
                            <?php echo $this->partial('_item-menu-album.tpl', 'advalbum', array('manage' => 1, 'album'=>$album)); ?>
                        </div>
                        <?php endif; ?>

                    </div>

                    <div class="ynadvalbum_album_listing_title">
                        <a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album->getTitle(); ?></a>
                        <?php
					// end photo album
					$photos_count = $album->count();
                        $str_photos = $this->translate(array('%s  advalbum_photo', '%s  photos', $photos_count), $this->locale()->toNumber($photos_count));
                        if (isset($this->no_author_info) && $this->no_author_info) {
                            $album_info_1 = $this->translate('%1$s', $str_photos);
                        } 
                    ?>
                    <span class="advalbum_list_photos advalbum_album_statis">
                        <?php if (isset($this->no_author_info) && $this->no_author_info) : ?>
                            <span class="yn_stats yn_stats_view">
                            <?php echo $this->translate(array('%s  <span>view</span>', '%s  <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
                            </span>
                            <i class="yn_dots">.</i>
                            <span class="yn_stats yn_stats_comment">
                            <?php echo $this->translate(array('%s  <span>comment</span>', '%s  <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
                            </span>
                        <?php else : ?>
                            <span class="yn_stats yn_stats_view">
                            <?php echo $this->translate(array('%s  <span>view</span>', '%s  <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
                            </span>
                            <i class="yn_dots">.</i>
                            <span class="yn_stats yn_stats_comment">
                            <?php echo $this->translate(array('%s  <span>comment</span>', '%s  <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
                            </span>
                            <i class="yn_dots">.</i>
                            <span class="yn_stats yn_stats_like">
                            <?php echo $this->translate(array('%s  <span>like</span>', '%s  <span>likes</span>', $album->like_count), $this->locale()->toNumber($album->like_count)); ?>
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

            <?php if( $this->paginator->count() > 1 ): ?>
            <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
            array(
            'pageAsQuery' => false,
            'query' => $this->formValues
            )); ?>
            <?php endif; ?>
            <?php else: ?>
            <div class="tip">
		      <span>
		        <?php echo $this->translate('You do not have any albums yet.');?>
                  <?php if( $this->canCreate ): ?>
                  <?php echo $this->translate('Get started by %1$screating%2$s your first album!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
                  <?php endif; ?>
		      </span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        ynadvalbumOptions();
    });
</script>
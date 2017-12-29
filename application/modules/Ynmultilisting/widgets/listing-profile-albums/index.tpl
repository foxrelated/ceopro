<?php
  $this->headScript() -> appendScript('jQuery.noConflict();')
          ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/ynmultilisting.js');
  $album_listing_id = "ynmultilisting_profile_album";
?>


<div class="ynmultilisting_listing_action clearfix">
  <?php if( $this->canUpload ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'ynmultilisting_extended',
          'controller' => 'album',
          'action' => 'create',
          'subject' => $this->subject()->getGuid(),
        ), $this->translate('Create Album'), array(
          'class' => 'buttonlink icon_listings_add_photos'
      )) ?>
  <?php endif; ?>

  <?php if($this->viewer->getIdentity() > 0) :?>
  	  <?php echo $this->htmlLink(array(
          'route' => 'ynmultilisting_extended',
          'controller' => 'album',
          'action' => 'list',
          'subject' => $this->subject()->getGuid(),
        ), $this->translate('Browse Albums'), array(
          'class' => 'buttonlink icon_listings_browse_photos'
      )) ?>
  <?php endif; ?>
</div>

<div id='<?php echo $album_listing_id ?>'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ):
          $listing = $this->listing ?>
  <div class="ynmultilisting-listing-tab">
    <div title="<?php echo $this->translate('List view');?>" class="list-view" data-view="ynmultilisting-list-view"></div>
    <div title="<?php echo $this->translate('Grid view');?>" class="grid-view" data-view="ynmultilisting-grid-view"></div>
    <div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view" data-view="ynmultilisting-pinterest-view"></div>
  </div>

  <div id="<?php echo $album_listing_id ?>_view" class="photo-list-content">
    <!-- grid view -->
    <ul class="ynmultilisting_grid_view">
        <?php foreach( $this->paginator as $album ): ?>
            <?php
            $photoUrl = $album->getPhotoUrl('thumb.normal');
            if(!$photoUrl) {
                $photoUrl = $album->getAlbumPhotoUrl();
            }
            if(!$photoUrl)
                $photoUrl = $this->layout()->staticBaseUrl . '/application/modules/Ynmultilisting/externals/images/nophoto_album_thumb_profile.png';
            if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                $photoCount = $album->getPhotoCount();
            else
                $photoCount = $album->count();
            ?>
       <li>
          <div class="thumbs_photo">
              <a class="thumbs_photo_temp" href="<?php echo $album->getHref(); ?>"></a>
              <span style="background-image: url(<?php echo $photoUrl ?>)">
                <label class="ynmultilisting_photo_count"><?php echo $photoCount ?></label>
              </span>
              <div class="ynmultilisting-options">
                <div class="ynmultilisting-options-button"><span class="ynicon yn-arr-down"></span></div>
                <ul class="ynmultilisting_dropdown_items">
                    <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'album', 'action' => 'edit', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>" class="smoothbox"><span class="ynicon yn-text-edit"></span><?php echo $this->translate('Edit Album') ?></a></li>
                    <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'album', 'action' => 'delete', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>" class="smoothbox"><span class="ynicon yn-photo-del-o"></span><?php echo $this->translate('Delete Album') ?></a></li>
                    <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'photo', 'action' => 'upload', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>"><span class="ynicon yn-photo-del-o"></span><?php echo $this->translate('Add More Photo') ?></a></li>
                </ul>
              </div>
          </div>

          <div class="ynmultilisting_thumbs_info">
            <?php $title = Engine_Api::_()->ynmultilisting()->subPhrase($album->getTitle(),70);
                  if($title == '') $title = "Untitle Album";
                  echo $this->htmlLink($album->getHref(), $title, array('class' => 'ynmultilisting_thumbs_titles'));
            ?>
            <span class="ynmultilisting_thumbs_info_owner">
            <span><?php echo $this->translate('By');?></span>
            <?php if($album->user_id != 0 ){
                $name = $album->getMemberOwner()->getTitle();
                echo $this->htmlLink($album->getMemberOwner()->getHref(), $name, array('class' => 'ynmultilisting_thumbs_author'));
              }
               else{
                $name = $listing->getOwner()->getTitle();
                echo $this->htmlLink($listing->getOwner()->getHref(), $name, array('class' => 'ynmultilisting_thumbs_author'));
               }
            ?></span>
            <!-- <?php echo $this->timestamp($album->creation_date) ?> -->
            <span class="ynmultilisting_thumbs_statis">
              <span class="yn_stats yn_stats_view">
                <?php echo $this->translate(array('%d <span>view</span>', '%d <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
              </span>
              <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_comment">
              <?php echo $this->translate(array('%d <span>comment</span>', '%d <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
              </span>
              <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_like">
              <?php echo $this->translate(array('%d <span>like</span>', '%d <span>likes</span>', $album->likes()->getLikeCount()), $this->locale()->toNumber( $album->likes()->getLikeCount())); ?>
            </span>
            </span>
          </div>
        </li>
     <?php endforeach;?>
    </ul>
    <!-- pinterest view -->
    <ul id="<?php echo $album_listing_id; ?>_tiles" class="ynmultilisting_pinterest_view clearfix">
      <?php foreach( $this->paginator as $album ): ?>
          <?php
          $photoUrl = $album->getPhotoUrl('thumb.normal');

          if(!$photoUrl)
              $photoUrl = $album->getAlbumPhotoUrl();
          if(!$photoUrl)
              $photoUrl = $this->layout()->staticBaseUrl . '/application/modules/Ynmultilisting/externals/images/nophoto_album_thumb_profile.png';
          if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
              $photoCount = $album->getPhotoCount();
          else
              $photoCount = $album->count();
          ?>
        <li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynmultilisting_pinterest'>
            <div class="ynmultilisting_photo_items_thumbs">
              <a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"></a>
              <img src="<?php echo $photoUrl ?>" />
              <label class="ynmultilisting_photo_count"><?php echo $photoCount ?></label>
              <div class="ynmultilisting-options">
                <div class="ynmultilisting-options-button"><span class="ynicon yn-arr-down"></span></div>
                <ul class="ynmultilisting_dropdown_items">
                    <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'album', 'action' => 'edit', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>" class="smoothbox"><span class="ynicon yn-text-edit"></span><?php echo $this->translate('Edit Album') ?></a></li>
                    <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'album', 'action' => 'delete', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>" class="smoothbox"><span class="ynicon yn-photo-del-o"></span><?php echo $this->translate('Delete Album') ?></a></li>
                    <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'photo', 'action' => 'upload', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>"><span class="ynicon yn-photo-del-o"></span><?php echo $this->translate('Add More Photo') ?></a></li>
                </ul>
              </div>
            </div>
            <div class="ynmultilisting_photo_info clearfix">
                <?php $title = Engine_Api::_()->ynmultilisting()->subPhrase($album->getTitle(),70);
                  if($title == '') $title = "Untitle Album";
                  echo $this->htmlLink($album->getHref(), $title, array('class' => 'ynmultilisting_thumbs_titles'));
                ?>
                <span class="ynmultilisting_photo_info-owner">
                  <span><?php echo $this->translate('By');?></span>
                    <?php if($album->user_id != 0 ){
                        $name = $album->getMemberOwner()->getTitle();
                        echo $this->htmlLink($album->getMemberOwner()->getHref(), $name, array('class' => 'ynmultilisting_thumbs_author'));
                      }
                       else{
                        $name = $listing->getOwner()->getTitle();
                        echo $this->htmlLink($listing->getOwner()->getHref(), $name, array('class' => 'ynmultilisting_thumbs_author'));
                       }
                    ?>
                </span>
                <div class="ynmultilisting_thumbs_stats ynmultilisting_stats">
                  <div class="ynmultilisting_thumbs_stats-info yn_stats yn_stats_view">
                    <label><?php echo $album->view_count ?></label>
                    <span>views</span>
                  </div>
                  <div class="ynmultilisting_thumbs_stats-info ynmultilisting_border yn_stats yn_stats_comment">
                    <label><?php echo $album->comment_count ?></label>
                    <span>comments</span>
                  </div>
                  <div class="ynmultilisting_thumbs_stats-info yn_stats yn_stats_like">
                    <label><?php echo $album->likes()->getLikeCount() ?></label>
                    <span>likes</span>
                  </div>
                </div>
            </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <!-- list view -->
    <ul class="ynmultilisting_list_view clearfix">
      <?php foreach( $this->paginator as $album ): ?>
          <?php
          $photoUrl = $album->getPhotoUrl('thumb.normal');
          if(!$photoUrl)
              $photoUrl = $album->getAlbumPhotoUrl();
          if(!$photoUrl)
              $photoUrl = $this->layout()->staticBaseUrl . '/application/modules/Ynmultilisting/externals/images/nophoto_album_thumb_profile.png';
          if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
              $photoCount = $album->getPhotoCount();
          else
              $photoCount = $album->count();
          ?>
      <li class="clearfix">
          <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
              <span style="background-image: url(<?php echo $photoUrl ?>)">
              </span>
          </a>
          <div class="ynmultilisting_thumbs_info">
            <?php $title = Engine_Api::_()->ynmultilisting()->subPhrase($album->getTitle(),70);
                  if($title == '') $title = "Untitle Album";
                  echo $this->htmlLink($album->getHref(), $title, array('class' => 'ynmultilisting_thumbs_titles'));
            ?>
            <span class="ynmultilisting_thumbs_info_owner">
            <span><?php echo $this->translate(array('%d photo by', '%d photos by', $photoCount), $photoCount);?></span>
            <?php if($album->user_id != 0 ){
                $name = $album->getMemberOwner()->getTitle();
                echo $this->htmlLink($album->getMemberOwner()->getHref(), $name, array('class' => 'ynmultilisting_thumbs_author'));
              }
               else{
                $name = $listing->getOwner()->getTitle();
                echo $this->htmlLink($listing->getOwner()->getHref(), $name, array('class' => 'ynmultilisting_thumbs_author'));
               }
            ?></span>
            <span class="ynmultilisting_thumbs_statis">
              <span class="yn_stats yn_stats_view">
                <?php echo $this->translate(array('%d <span>view</span>', '%d <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
              </span>
              <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_comment">
              <?php echo $this->translate(array('%d <span>comment</span>', '%d <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
              </span>
              <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_like">
              <?php echo $this->translate(array('%d <span>like</span>', '%d <span>likes</span>', $album->likes()->getLikeCount()), $this->locale()->toNumber($album->likes()->getLikeCount())); ?>
            </span>
            </span>
            <?php
               echo "<div class='album-photo-lists'>";
               $photo_list = $album->getCollectiblesPaginator();
               foreach ($photo_list as $photo)
               {
                   echo '<a class="album-photo-list" href="'.$photo->getHref().'"><span style="background-image: url('.$photo->getPhotoUrl().');"></span></a>';
               }
               echo "</div>";
            ?>
          </div>
          <div class="ynmultilisting-options">
            <div class="ynmultilisting-options-button"><span class="ynicon yn-arr-down"></span></div>
            <ul class="ynmultilisting_dropdown_items">
                <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'album', 'action' => 'edit', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>" class="smoothbox"><span class="ynicon yn-text-edit"></span><?php echo $this->translate('Edit Album') ?></a></li>
                <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'album', 'action' => 'delete', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>" class="smoothbox"><span class="ynicon yn-photo-del-o"></span><?php echo $this->translate('Delete Album') ?></a></li>
                <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(array('controller' => 'photo', 'action' => 'upload', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$album->getIdentity()), 'ynmultilisting_extended') ?>"><span class="ynicon yn-photo-del-o"></span><?php echo $this->translate('Add More Photo') ?></a></li>
            </ul>
          </div>
      </li>
     <?php endforeach;?>
    </ul>

    <div class="clearfix">
      <div id="ynmultilisting_albums_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="ynmultilisting_albums_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No albums have been uploaded to this listing yet.');?>
    </span>
  </div>
  <style type="text/css">
  .layout_advgroup_profile_albums ul.global_form_box {
    padding: 15px 0 0!important;
  }
  </style>
  <?php endif; ?>
</div>

  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.wookmark.js"></script>
<script type="text/javascript">
    jQuery.noConflict();
    (function ($){
      if(!getCookie('<?php echo $album_listing_id; ?>view_mode'))
         setCookie('<?php echo $album_listing_id; ?>view_mode','list');

      // if current view mode is pinterest when windows load
      //re-run wookmark function when album tab is active
      $('.tab_layout_ynmultilisting_listing_profile_albums').on('click',function(){
        if(getCookie('<?php echo $album_listing_id; ?>view_mode') == 'pinterest'){
          var options = {
            itemWidth: 220,
            autoResize: true,
            container: $('#<?php echo $album_listing_id; ?>_tiles'),
            offset: 15,
            outerOffset: 0,
            flexibleWidth: '50%'
          };

          // Get a reference to your grid items.
          var handler = $('#<?php echo $album_listing_id; ?>_tiles li.ynmultilisting_pinterest');

          var $window = $(window);
          $window.resize(function() {
            var windowWidth = $window.width(),
                newOptions = { flexibleWidth: '50%' };

            // Breakpoint
            if (windowWidth < 1024) {
              newOptions.flexibleWidth = '100%';
            }
            handler.wookmark(newOptions);
          });

          // Call the layout function.
          handler.wookmark(options);
        }
      });
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
        var handler = $('#<?php echo $album_listing_id; ?>_tiles li.ynmultilisting_pinterest');

        var $window = $(window);
        $window.resize(function() {
          var windowWidth = $window.width(),
              newOptions = { flexibleWidth: '50%' };

          // Breakpoint
          if (windowWidth < 1024) {
            newOptions.flexibleWidth = '100%';
          }
          handler.wookmark(newOptions);
        });

        // Call the layout function.
        handler.wookmark(options);
      });

      $('#<?php echo $album_listing_id; ?> .ynmultilisting-listing-tab > div').click(function() {

        var handler = $('#<?php echo $album_listing_id; ?>_tiles li.ynmultilisting_pinterest');
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

        $('#<?php echo $album_listing_id; ?> .ynmultilisting-listing-tab > div').removeClass('active');
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
        $$('#main_tabs li.tab_layout_ynmultilisting_listing_albums').addEvent('click', function(){
          if(view_mode == 'pinterest')
          {
            var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynmultilisting_pinterest');
            var options = {
              itemWidth: 220,
              autoResize: true,
              container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
              offset: 25,
              outerOffset: 0,
              flexibleWidth: '50%'
            };

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
            var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynmultilisting_pinterest');
            var options = {
              itemWidth: 220,
              autoResize: true,
              container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
              offset: 25,
              outerOffset: 0,
              flexibleWidth: '50%'
            };

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
        document.getElementById('<?php echo $album_listing_id; ?>_view').set('class',"ynmultilisting-"+getCookie('<?php echo $album_listing_id; ?>view_mode')+"-view");

        $$('#<?php echo $album_listing_id; ?> .ynmultilisting-listing-tab > div').removeClass('active');
        if(view_mode == "list" )
        {
          $$('#<?php echo $album_listing_id; ?> .ynmultilisting-listing-tab > div.list-view').addClass('active');
        }
        if(view_mode == "grid" )
        {
          $$('#<?php echo $album_listing_id; ?> .ynmultilisting-listing-tab > div.grid-view').addClass('active');
        }
        if(view_mode == "pinterest" )
        {
          $$('#<?php echo $album_listing_id; ?> .ynmultilisting-listing-tab > div.pinterest-view').addClass('active');
          var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynmultilisting_pinterest');
          var options = {
            itemWidth: 220,
            autoResize: true,
            container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
            offset: 25,
            outerOffset: 0,
            flexibleWidth: '50%'
          };

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
        $$('#<?php echo $album_listing_id; ?> .ynmultilisting-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
        if("<?php echo  $this->view_mode ?>" == "pinterest" )
        {
          var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynmultilisting_pinterest');
          var options = {
            itemWidth: 220,
            autoResize: true,
            container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
            offset: 25,
            outerOffset: 0,
            flexibleWidth: '50%'
          };

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
          $$(class_name).addEvent('click', function(event){
            var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynmultilisting_pinterest');
            var options = {
              itemWidth: 220,
              autoResize: true,
              container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
              offset: 25,
              outerOffset: 0,
              flexibleWidth: '50%'
            };

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
  window.addEvent('domready', function() {
    ynmultilistingOptions();
  });
</script>

<script type="text/javascript">
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
            var anchor = $('ynmultilisting_listing_album').getParent();
            $('ynmultilisting_albums_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('ynmultilisting_albums_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('ynmultilisting_albums_previous').removeEvents('click').addEvent('click', function(){
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                        format : 'html',
                        subject : en4.core.subject.guid,
                        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    }
                }), {
                    'element' : anchor
                })
            });

            $('ynmultilisting_albums_next').removeEvents('click').addEvent('click', function(){
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                        format : 'html',
                        subject : en4.core.subject.guid,
                        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                    }
                }), {
                    'element' : anchor
                })
            });
        <?php endif; ?>
    });
</script>
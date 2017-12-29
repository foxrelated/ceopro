<?php
  $this->headScript() -> appendScript('jQuery.noConflict();')
          ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/ynmultilisting.js');
   $photo_listing_id = "ynmultilisting_profile_photos";
   $isAdvanced = Engine_Api::_()->hasModuleBootstrap('advalbum');
?>  
<h2>
    <?php echo $this->listing->__toString();
          echo ' &#187; ';
          if($this->album->getTitle()!='') echo $this->album->getTitle();
          else echo 'Untitle Album';
    ?>
</h2>

  <div class="ynmultilisting_listing_action clearfix">
      <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?>
        <!-- Go to www.addthis.com/dashboard to customize your tools --> 
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script>

      <div class="ynmultilisting_album_detail_option">
        <?php if($this->canEdit) echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'album', 'action' => 'edit', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Edit Album'), array(
          'class' => 'buttonlink icon_listings_edit smoothbox'
        )) ?>
        <?php if($this->canEdit  && $this->album->getTitle() !== 'Listing Profile') echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'album', 'action' => 'delete', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Delete Album'), array(
          'class' => 'buttonlink smoothbox icon_listings_delete smoothbox'
        )) ?>
        <?php if($this->canEdit)
            echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'photo', 'action' => 'upload', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Add More Photos'), array(
          'class' => 'buttonlink icon_listings_add_photos'
        )) ?>
        </div>

      <div class="ynmultilisting-options">
        <div class="ynmultilisting-options-button"><span class="ynicon yn-arr-down"></span></div>
        <ul class="ynmultilisting_dropdown_items">

            <li class="ynmultilisting_dropdown_item"><?php if($this->canEdit) echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'album', 'action' => 'edit', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Edit Album'), array(
             'class' => 'buttonlink icon_listings_edit smoothbox'
              )) ?>
            </li>

            <li class="ynmultilisting_dropdown_item"><?php if($this->canEdit  && $this->album->getTitle() !== 'Listing Profile') echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'album', 'action' => 'delete', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Delete  Album'), array(
              'class' => 'buttonlink smoothbox icon_listings_delete smoothbox'
              )) ?>
            </li>
          
            <li class="ynmultilisting_dropdown_item"><?php if($this->canEdit)
                echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'photo', 'action' => 'upload', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Add More Photos'), array(
              'class' => 'buttonlink icon_listings_add_photos'
              )) ?>
            </li>
        </ul>
      </div>
</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="ynmultilisting_listing_album-view">
     <div class="ynmultilisting_album_detail-desctiption">
        <span  class="ynmultilisting_album_detail_thumb-description-content">
          <?php echo $this->album->description?>
        </span>
        <span class="ynmultilisting_album_detail_thumb-more"><?php echo $this->translate('<span>Show more<span class="ynicon yn-arr-down"></span></span>'); ?>
        </span>
    </div>
    
    <div id="<?php echo $photo_listing_id; ?>">
        <div class="ynmultilisting_album_detail-stats clearfix">
            <div class="ynmultilisting_photo_detail-stats-info">
                <div class="ynmultilisting-photo"><?php echo $this->translate('Photos: %s', $this->album->getPhotoCount());?></div>
                <div class="ynmultilisting-views yn_stats_view"><i class="yn_dots">|</i><span><?php echo $this->translate('Views: %s', $this->album->view_count);?></span><i class="yn_dots">|</i></div>
                <div class="ynmultilisting-comments yn_stats_comment"><span><?php echo $this->translate('Comments: %s', $this->album->comment_count);?></span></div>
            </div>
        </div>
        <div class="ynmultilisting-listing-tab">
            <div title="<?php echo $this->translate('Grid view');?>" class="grid-view" data-view="ynmultilisting-grid-view"></div>
            <div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view" data-view="ynmultilisting-pinterest-view"></div>
        </div>
      <div class="photo-list-content">
        <!-- grid view -->
        <ul class="ynmultilisting_album_detail_grid_view clearfix">
          <?php foreach( $this->paginator as $photo ): ?>
           <li id='thumbs_nocaptions_<?php echo $photo->getIdentity()?>'>
              <div class="ynmultilisting_album_detail_grid_view_items">
                <a class="thumbs_photo clearfix" href="<?php echo $photo->getHref(); ?>">
                  <span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
                </a>
                <div class="ynmultilisting-options">
                    <div class="ynmultilisting-options-button"><span class="ynicon yn-arr-down"></span></div>
                    <ul class="ynmultilisting_dropdown_items">
                      <li class="ynmultilisting_dropdown_item"><?php if($this->canEdit) echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'photo', 'action' => 'edit', 'photo_id' => $photo->getIdentity()), $this->translate('Edit Photo'), array(
                       'class' => 'buttonlink icon_listings_edit smoothbox'
                        )) ?>
                      </li>

                      <li class="ynmultilisting_dropdown_item"><?php if($this->canEdit) echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'photo', 'action' => 'delete', 'photo_id' => $photo->getIdentity(), 'stay' => 1), $this->translate('Delete Photo'), array(
                       'class' => 'buttonlink icon_listings_delete smoothbox'
                        )) ?>
                      </li>

                      <li class="ynmultilisting_dropdown_item">
                         <?php echo $this->htmlLink(Array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>$photo->getType(), 'id'=>$photo->getIdentity(), 'format' => 'smoothbox'), '<span class="ynicon yn-share"></span>'.$this->translate('Share'), array('class' => 'smoothbox')); ?>
                      </li>

                      <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(Array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'subject'=>$photo->getGuid()), 'default') ?>" class="smoothbox"><span class="ynicon yn-warning-triangle"></span>Report photo</a></li>

                      <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(Array('module'=>'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $photo->getGuid()), 'user_extended') ?>" class="smoothbox"><span class="ynicon yn-photo"></span>Make profile photo</a></li>
                    </ul>
                </div>
                <div class="ynmultilisting_thumb_photo_info">
                    <a class="ynmultilisting_thumb_photo_info_title" href="<?php echo $photo->getHref(); ?>"><?php echo $photo->getTitle() ?></a>
                    <?php if( $isAdvanced): ?>
                    <div class="ynmultilisting_photo_info-stats">
                      <span class="ynmultilisting_photo_info-stats">
                        <span class="yn_stats yn_stats_view"><?php echo $this->translate(array('%d <span>view</span>', '%d <span>views</span>', $photo->view_count), $this->locale()->toNumber($photo->view_count)); ?></span>
                        <span class="yn_dots">.</span>
                        <span class="yn_stats yn_stats_comment"><?php echo $this->translate(array('%d <span>comment</span>', '%d <span>comments</span>', $photo->comment_count), $this->locale()->toNumber($photo->comment_count)); ?></span>
                        <span class="yn_dots">.</span>
                        <span class="yn_stats yn_stats_like"><?php echo $this->translate(array('%d <span>like</span>', '%d <span>likes</span>', $photo->like_count), $this->locale()->toNumber($photo->like_count)); ?></span>
                      </span>
                    </div>
                    <?php endif; ?>
                </div>
              </div>
              <?php if($this->listing->isOwner($this->viewer)):?>
              <?php endif;?>
            </li>
          <?php endforeach;?>
        </ul>
        <!-- printerest view -->
        <ul id="<?php echo $photo_listing_id; ?>_tiles" class="ynmultilisting_album_detail_pinterest_view">
        <?php foreach( $this->paginator as $photo ): ?>
          <li id="thumbs-photo-<?php echo $photo->photo_id ?>" class="ynmultilisting_pinterest">
            <div class="ynmultilisting_photo_items_thumbs">
                <a class="ynmultilisting_photo_items_temp" href="<?php echo $photo->getHref(); ?>"></a>
                <img class="ynmultilisting_pinteres_thumbs-photo" src="<?php echo $photo->getPhotoUrl($thumb_photo); ?>" />
                <div class="ynmultilisting-options">
                    <div class="ynmultilisting-options-button"><span class="ynicon yn-arr-down"></span></div>
                    <ul class="ynmultilisting_dropdown_items">
                      <li class="ynmultilisting_dropdown_item"><?php if($this->canEdit) echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'photo', 'action' => 'edit', 'photo_id' => $photo->getIdentity()), $this->translate('Edit Photo'), array(
                       'class' => 'buttonlink icon_listings_edit smoothbox'
                        )) ?>
                      </li>

                      <li class="ynmultilisting_dropdown_item"><?php if($this->canEdit) echo $this->htmlLink(array('route' => 'ynmultilisting_extended','controller' => 'photo', 'action' => 'delete', 'photo_id' => $photo->getIdentity(), 'stay' => 1), $this->translate('Delete Photo'), array(
                       'class' => 'buttonlink icon_listings_delete smoothbox'
                        )) ?>
                      </li>

                      <li class="ynmultilisting_dropdown_item">
                         <?php echo $this->htmlLink(Array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>$photo->getType(), 'id'=>$photo->getIdentity(), 'format' => 'smoothbox'), '<span class="ynicon yn-share"></span>'.$this->translate('Share'), array('class' => 'smoothbox')); ?>
                      </li>
                      
                      <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(Array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'subject'=>$photo->getGuid()), 'default') ?>" class="smoothbox"><span class="ynicon yn-warning-triangle"></span>Report photo</a></li>
                      <li class="ynmultilisting_dropdown_item"><a href="<?php echo $this->url(Array('module'=>'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $photo->getGuid()), 'user_extended') ?>" class="smoothbox"><span class="ynicon yn-photo"></span>Make profile photo</a></li>
                    </ul>
                  </div>
            </div>
            <div class="ynmultilisting_photo_info">
              <span class="ynmultilisting_photo_info-title">
                <a href="<?php echo $photo->getHref(); ?>"><?php echo $photo->getTitle() ?></a>
              </span>
            <?php if( $isAdvanced): ?>
              <span class="ynmultilisting_photo_info-stats">
                <span class="yn_stats yn_stats_view"><?php echo $this->translate(array('%d <span>view</span>', '%d <span>views</span>', $photo->view_count), $this->locale()->toNumber($photo->view_count)); ?></span>
                <span class="yn_dots">.</span>
                <span class="yn_stats yn_stats_comment"><?php echo $this->translate(array('%d <span>comment</span>', '%d <span>comments</span>', $photo->comment_count), $this->locale()->toNumber($photo->comment_count)); ?></span>
                <span class="yn_dots">.</span>
                <span class="yn_stats yn_stats_like"><?php echo $this->translate(array('%d <span>like</span>', '%d <span>likes</span>', $photo->like_count), $this->locale()->toNumber($photo->like_count)); ?></span>
              </span>
              </div>
                <?php endif; ?>
          </li>
           <?php endforeach;?>
        </ul>
      </div>
    </div>

    <?php if( $this->paginator->count() > 0 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
        )); ?>
    <?php endif; ?>
    <?php echo $this->action("list", "comment", "core", array("type"=>"ynmultilisting_album", "id"=>$this->album->getIdentity())); ?>
    <?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No photos have been uploaded in this album yet.');?>
        </span>
    </div>
</div>
<?php endif; ?>

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js"></script>
  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.wookmark.js"></script>

<script type="text/javascript">
  window.addEvent('domready', function() {
    ynmultilistingOptions();

    if ($$('.ynmultilisting_album_detail-desctiption').length) {
       $$('.ynmultilisting_album_detail_thumb-more').removeEvents('click').addEvent('click', function() {
          $$('.ynmultilisting_album_detail_thumb-description-content').toggleClass('ynmultilisting_album_detail-description_toggle');
          if ($$('.ynmultilisting_album_detail_thumb-description-content')[0].hasClass('ynmultilisting_album_detail-description_toggle'))
            $$('.ynmultilisting_album_detail_thumb-more').set('html','<span>Show more <span class="ynicon yn-arr-down"></span></span>');
          else
            $$('.ynmultilisting_album_detail_thumb-more').set('html','<span>Show less <span class="ynicon yn-arr-up"></span></span>');
        });
            var hide = $$('.ynmultilisting_album_detail_thumb-description-content').length;
            if(hide){
              var height = $$('.ynmultilisting_album_detail_thumb-description-content')[0].getHeight();
              if(height > 65){
           
                $$('.ynmultilisting_album_detail_thumb-description-content')[0].addClass('ynmultilisting_album_detail-description_toggle');
                $$('.ynmultilisting_album_detail_thumb-more')[0].setStyle('display', 'inline-block');
              }
              else{
                  $$('.ynmultilisting_album_detail-desctiption')[0].setStyle('height', '60px');
                  $$('.ynmultilisting_album_detail-desctiption')[0].toggleClass('hide_line');
              }
              if(height == 0){
                  $$('.ynmultilisting_album_detail-desctiption')[0].setStyle('height', '0px');
              }
            }
            else{
        $$('.ynmultilisting_album_detail-desctiption')[0].setStyle('display', 'none');
      }
            }
  });

</script>
<script type="text/javascript">
     jQuery.noConflict();
    (function ($){
        if(!getCookie('<?php echo $photo_listing_id; ?>view_mode'))
         setCookie('<?php echo $photo_listing_id; ?>view_mode','grid');
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
            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li.ynmultilisting_pinterest');

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

         $('#<?php echo $photo_listing_id; ?> .ynmultilisting-listing-tab > div').click(function() {
            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li.ynmultilisting_pinterest');
            var options = {
                  itemWidth: 215,
                  autoResize: true,
                  container: $('#<?php echo $photo_listing_id; ?>_tiles'),
                  offset: 25,
                  outerOffset: 0,
                  flexibleWidth: '50%'
            };

            // Breakpoint
            if ( $(window).width() < 1024) {
                options.flexibleWidth = '100%';
            }

            $('#<?php echo $photo_listing_id; ?> .ynmultilisting-listing-tab > div').removeClass('active');
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
      $$('#main_tabs li.tab_layout_ynmultilisting_business_profile_photos').addEvent('click', function(){
        if(view_mode == 'pinterest')
        {
            var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynmultilisting_pinterest');
            var options = {
                itemWidth: 215,
                  autoResize: true,
                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
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
    if(getCookie('<?php echo $photo_listing_id; ?>view_mode') != "")
    {
      var view_mode  = getCookie('<?php echo $photo_listing_id; ?>view_mode');
      $$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content ynmultilisting-'+ view_mode +'-view');
      $$('#<?php echo $photo_listing_id; ?> .ynmultilisting-listing-tab > div').removeClass('active');

      if(view_mode == "grid" )
      {
        $$('#<?php echo $photo_listing_id; ?> .ynmultilisting-listing-tab > div.grid-view').addClass('active');
      }
      if(view_mode == "pinterest" )
      {
        $$('#<?php echo $photo_listing_id; ?> .ynmultilisting-listing-tab > div.pinterest-view').addClass('active');
         var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynmultilisting_pinterest');
          var options = {
                itemWidth: 215,
                autoResize: true,
                container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
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
      $$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content '+'<?php echo $this->class_mode;?>');
      $$('#<?php echo $photo_listing_id; ?> .ynmultilisting-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
      if("<?php echo  $this->view_mode ?>" == "pinterest" )
      {
        $$('#<?php echo $photo_listing_id; ?> .ynmultilisting-listing-tab > div.pinterest-view').addClass('active');
        var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynmultilisting_pinterest');
        var options = {
              itemWidth: 215,
              autoResize: true,
              container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
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
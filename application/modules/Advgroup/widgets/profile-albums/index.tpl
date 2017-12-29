<?php if( $this->canUpload ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'group_extended',
        'controller' => 'album',
        'action' => 'create',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Create Album'), array(
        'class' => 'buttonlink icon_group_photo_new'
    )) ?>
  <?php endif; ?>
<?php
  $this->headScript()-> appendScript('jQuery.noConflict();');
  $album_listing_id = $this->identity;
  $this->view_mode = 'grid';
  $this->class_mode = 'ynadvgroup-grid-view';
?>
<div id='<?php echo $album_listing_id ?>'>
  <div class="ynadvgroup-listing-tab">
    <div title="<?php echo $this->translate('List view');?>" class="list-view" data-view="ynadvgroup-list-view"></div>
    <div title="<?php echo $this->translate('Grid view');?>" class="grid-view" data-view="ynadvgroup-grid-view"></div>
    <div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view" data-view="ynadvgroup-pinterest-view"></div>
  </div>
  <div id="<?php echo $album_listing_id ?>_view" class="photo-list-content ynadvgroup_listing_albums">
  <?php if( $this->paginator->getTotalItemCount() > 0 ):
          $group = $this->group?>
    <!-- gird view -->
    <ul class="ynadvgroup_grid_view_albums clearfix">
      <?php foreach( $this->paginator as $album ): ?>
       <li>
            <div class="ynadvgroup_grid_view_albums_thumb">
                <span class="ynadvgroup_grid_view_albums_temp"></span>
                <?php $photo = $album->getFirstCollectible();
                    if($photo):?>
                    <a href="<?php echo $album->getHref(); ?>" style="background-image: url('<?php echo $photo->getPhotoUrl('thumb.profile');?>')"></a>
                    <?php else:?>
                    <a href="<?php echo $album->getHref(); ?>" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advgroup/externals/images/nophoto-photoalbum.png')"></a>
                <?php endif;?>
                <label><?php echo $this->locale()->toNumber($album->count())?></label>
            </div>
          <div class="ynadvgroup_grid_view_albums_info">
            <?php $title = Engine_Api::_()->advgroup()->subPhrase($album->getTitle(),70);
                  if($title == '') $title = "Untitle Album";
                  echo $this->htmlLink($album->getHref(), $title, array('class' => 'ynadvgroup_grid_view_albums_info_title'));?>
            <span class="ynadvgroup_grid_view_albums_info_owner"><?php echo $this->translate('<span>By</span>');?>
            <?php if($album->user_id != 0 ){
                $name = Engine_Api::_()->advgroup()->subPhrase($album->getMemberOwner()->getTitle(),70);
                echo $this->htmlLink($album->getMemberOwner()->getHref(), $name, array('class' => 'thumbs_author'));
              }
               else{
                $name = Engine_Api::_()->advgroup()->subPhrase($group->getOwner()->getTitle(),70);
                echo $this->htmlLink($group->getOwner()->getHref(), $name, array('class' => 'thumbs_author'));
               }
            ?></span>
            <span class="ynadvgroup_grid_view_albums_info_statis">
              <span class="yn_stats yn_stats_view">
                  <?php echo $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
              </span>
                <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_comment">
                <?php echo $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
              </span>
                <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_like">
                <?php $like_count = $album->likes()->getLikeCount() ?>
                <?php echo $this->translate(array('%s like', '%s likes', $like_count), $this->locale()->toNumber($like_count)); ?>
              </span>
            </span>
          </div>
        </li>
     <?php endforeach;?>
    </ul>
    <!-- pinterest view -->
    <ul id="<?php echo $album_listing_id; ?>_tiles" class="ynadvgroup_pinterest_view_albums clearfix">
      <?php foreach( $this->paginator as $album ): ?>
       <li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynadvgroup_pinterest'>
          <div class="ynadvgroup_pinterest_view_albums_thumb">
            <a class="ynadvgroup_pinterest_view_albums_temp" href="<?php echo $album->getHref(); ?>"></a>
            <?php $photo = $album->getFirstCollectible();
            if($photo):?>
              <img src="<?php echo $photo->getPhotoUrl('thumb.profile');?>">
            <?php else:?>
              <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advgroup/externals/images/nophoto-photoalbum.png" />
            <?php endif;?>
            <label><?php echo $this->locale()->toNumber($album->count())?></label>
          </div>
          <div class="ynadvgroup_pinterest_view_albums_info clearfix">
            <?php $title = Engine_Api::_()->advgroup()->subPhrase($album->getTitle(),70);
              if($title == '') $title = "Untitle Album";
              echo $this->htmlLink($album->getHref(), $title, array('class' => 'ynadvgroup_pinterest_view_albums_info_title'));?>
            <span class="ynadvgroup_pinterest_view_albums_info_owner"><?php echo $this->translate('<span>By</span>');?>
            <?php if($album->user_id != 0 ){
                $name = Engine_Api::_()->advgroup()->subPhrase($album->getMemberOwner()->getTitle(),70);
                echo $this->htmlLink($album->getMemberOwner()->getHref(), $name, array('class' => 'thumbs_author'));
              }
               else{
                $name = Engine_Api::_()->advgroup()->subPhrase($group->getOwner()->getTitle(),70);
                echo $this->htmlLink($group->getOwner()->getHref(), $name, array('class' => 'thumbs_author'));
               }
            ?></span>
            <div class="ynadvgroup_thumbs_stats ynadvgroup_stats">
              <div class="ynadvgroup_thumbs_stats-info yn_stats yn_stats_view">
                <label><?php echo $this->locale()->toNumber($album->view_count) ?></label>
                <span><?php echo $this->translate(array('%s view', '%s views', $album->view_count), ''); ?></span>
              </div>
              <div class="ynadvgroup_thumbs_stats-info ynadvgroup_border yn_stats yn_stats_comment">
                <label><?php echo $this->locale()->toNumber($album->comment_count) ?></label>
                <span><?php echo $this->translate(array('%s comment', '%s comments', $album->comment_count), ''); ?></span>
              </div >
              <div class="ynadvgroup_thumbs_stats-info yn_stats yn_stats_like">
                <?php $like_count = $album->likes()->getLikeCount() ?>
                <label><?php echo $this->locale()->toNumber($like_count) ?></label>
                <span><?php echo $this->translate(array('%s like', '%s likes', $like_count), ''); ?></span>
              </div>
            </div>
            </div>
        </li>
     <?php endforeach;?>
    </ul>
    <!-- list view -->
    <ul class="ynadvgroup_list_view_albums">
      <?php foreach( $this->paginator as $album ): ?>
        <li class="clearfix">
          <div class="ynadvgroup_list_view_albums_thumb">
            <a class="ynadvgroup_list_view_albums_temp" href="<?php echo $album->getHref(); ?>"></a>
            <?php $photo = $album->getFirstCollectible();
                  if($photo):?>
              <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.profile');?>)">
            <?php else:?>
              <span style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advgroup/externals/images/nophoto-photoalbum.png)">
            <?php endif;?>
          </div>
          <div class="ynadvgroup_list_view_albums_info">
            <?php $title = Engine_Api::_()->advgroup()->subPhrase($album->getTitle(),70);
                  if($title == '') $title = "Untitle Album";
                  echo $this->htmlLink($album->getHref(), $title, array('class' => 'ynadvgroup_list_view_albums_info_title'));?>
            <span class="ynadvgroup_list_view_albums_info_owner"><?php echo $this->translate('<span>By</span>');?>
            <?php if($album->user_id != 0 ){
                $name = Engine_Api::_()->advgroup()->subPhrase($album->getMemberOwner()->getTitle(),70);
                echo $this->htmlLink($album->getMemberOwner()->getHref(), $name, array('class' => 'thumbs_author'));
              }
               else{
                $name = Engine_Api::_()->advgroup()->subPhrase($group->getOwner()->getTitle(),70);
                echo $this->htmlLink($group->getOwner()->getHref(), $name, array('class' => 'thumbs_author'));
               }
            ?></span>
            <span class="ynadvgroup_list_view_albums_info_statis">
              <span class="yn_stats yn_stats_view">
                  <?php echo $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
              </span>
                <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_comment">
                <?php echo $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
              </span>
                <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_like">
                <?php $like_count = $album->likes()->getLikeCount() ?>
                <?php echo $this->translate(array('%s like', '%s likes', $like_count), $this->locale()->toNumber($like_count)); ?>
              </span>
            </span>
            <?php
               echo "<div class='ynadvgroup_list_view_albums_info_photo_lists'>";
               $photo_list = $album->getCollectiblesPaginator();
               foreach ($photo_list as $photo)
               {
                   echo '<a class="album-photo-list" href="'.$photo->getHref().'"><span style="background-image: url('.$photo->getPhotoUrl().');"></span></a>';
               }
               echo "</div>";
            ?>
          </div>
        </li>
      <?php endforeach;?>
    </ul>
    <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No albums have been uploaded to this group yet.');?>
      </span>
    </div>
    <style type="text/css">
  	.layout_advgroup_profile_albums ul.global_form_box {
  		padding: 15px 0 0!important;
  	}
    </style>
    <?php endif; ?>
  </div>
</div>

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advgroup/externals/scripts/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advgroup/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advgroup/externals/scripts/wookmark/jquery.wookmark.js"></script>

<script type="text/javascript">
    jQuery.noConflict();
    (function ($){
      var options = {
        itemWidth: 220,
        autoResize: true,
        container: $('#<?php echo $album_listing_id; ?>_tiles'),
        offset: 15,
        outerOffset: 0,
        flexibleWidth: '50%'
      };

      // Get a reference to your grid items.
      var handler = $('#<?php echo $album_listing_id; ?>_tiles li.ynadvgroup_pinterest');
      $('#<?php echo $album_listing_id; ?>_tiles').imagesLoaded(function() {

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

      $('#<?php echo $album_listing_id; ?> .ynadvgroup-listing-tab > div').click(function() {

        // Breakpoint
        if ( $(window).width() < 1024) {
          options.flexibleWidth = '100%';
        }

        $('#<?php echo $album_listing_id; ?> .ynadvgroup-listing-tab > div').removeClass('active');
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
      var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynadvgroup_pinterest');
      var options = {
        itemWidth: 220,
        autoResize: true,
        container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
        offset: 25,
        outerOffset: 0,
        flexibleWidth: '50%'
      };

      en4.core.runonce.add(function()
      {
        var view_mode  = getCookie('<?php echo $album_listing_id; ?>view_mode');
        $$('#main_tabs li.tab_layout_ynadvgroup_listing_albums').addEvent('click', function(){
          if(view_mode == 'pinterest')
          {
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
            var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynadvgroup_pinterest');
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
        document.getElementById('<?php echo $album_listing_id; ?>_view').set('class',"ynadvgroup-"+getCookie('<?php echo $album_listing_id; ?>view_mode')+"-view");

        $$('#<?php echo $album_listing_id; ?> .ynadvgroup-listing-tab > div').removeClass('active');
        if(view_mode == "list" )
        {
          $$('#<?php echo $album_listing_id; ?> .ynadvgroup-listing-tab > div.list-view').addClass('active');
        }
        if(view_mode == "grid" )
        {
          $$('#<?php echo $album_listing_id; ?> .ynadvgroup-listing-tab > div.grid-view').addClass('active');
        }
        if(view_mode == "pinterest" )
        {
          $$('#<?php echo $album_listing_id; ?> .ynadvgroup-listing-tab > div.pinterest-view').addClass('active');
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
        $$('#<?php echo $album_listing_id; ?> .ynadvgroup-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
        if("<?php echo  $this->view_mode ?>" == "pinterest" )
        {
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

<script type="text/javascript">
  window.addEvent('domready', function() {
    ynadvgroupOptions();
  });
</script>
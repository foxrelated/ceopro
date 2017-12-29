<?php
  $photo_listing_id = "ynadvgroup_profile_photos";
    $album = $this->album;
    $this->view_mode = 'grid';
    $this->class_mode = 'ynadvgroup-grid-view';
?>
<h2>
    <?php echo $this->group->__toString();
          echo ' &#187; ';
          if($this->album->getTitle()!='') echo $this->album->getTitle();
          else echo 'Untitle Album';
    ?>
</h2>

<div class="ynadvgroup_listing_action clearfix">
  <div class="ynadvgroup_album_detail_addthis">
    <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<div class="addthis_sharing_toolbox"></div>'); ?> 
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
  </div>
  <div class="ynadvgroup_album_detail_option">
      <?php if($this->canEdit) echo $this->htmlLink(array('route' => 'group_extended','controller' => 'album', 'action' => 'edit', 'group_id' => $this->group->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Edit Album'), array(
    'class' => 'buttonlink icon_group_edit smoothbox'
      )) ?>
      <?php if($this->canEdit  && $this->album->getTitle() !== 'Group Profile') echo $this->htmlLink(array('route' => 'group_extended','controller' => 'album', 'action' => 'delete', 'group_id' => $this->group->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Delete Album'), array(
        'class' => 'buttonlink icon_group_delete smoothbox'
      )) ?>
      <?php if($this->canEdit)
          echo $this->htmlLink(array('route' => 'group_extended','controller' => 'photo', 'action' => 'upload', 'subject' => $this->group->getGuid(),'album_id'=>$this->album->getIdentity()), $this->translate('Add More Photos'), array(
        'class' => 'buttonlink icon_group_photo_new'
      )) ?>
  </div>
  <div class="ynadvgroup-options">
    <div class="ynadvgroup-options-button"><span class="ynicon yn-arr-down"></span></div>
    <ul class="ynadvgroup_dropdown_items">
        <li class="ynadvgroup_dropdown_item">
         <?php if($this->canEdit) echo $this->htmlLink(array('route' => 'group_extended','controller' => 'album', 'action' => 'edit', 'group_id' => $this->group->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Edit Album'), array(
    'class' => 'buttonlink icon_group_edit smoothbox'
  )) ?></li>
        <li class="ynadvgroup_dropdown_item">  <?php if($this->canEdit  && $this->album->getTitle() !== 'Group Profile') echo $this->htmlLink(array('route' => 'group_extended','controller' => 'album', 'action' => 'delete', 'group_id' => $this->group->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Delete Album'), array(
    'class' => 'buttonlink icon_group_delete smoothbox'
  )) ?></li>
        <li class="ynadvgroup_dropdown_item">  <?php if($this->canEdit)
      echo $this->htmlLink(array('route' => 'group_extended','controller' => 'photo', 'action' => 'upload', 'subject' => $this->group->getGuid(),'album_id'=>$this->album->getIdentity()), $this->translate('Add More Photos'), array(
    'class' => 'buttonlink icon_group_photo_new'
  )) ?></li>
    </ul>
  </div>
</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="ynadvgroup_album_detail-desctiption">
  <span  class="ynadvgroup_album_detail_thumb-description-content">
    <?php echo $this->album->description?>
  </span>
  <span class="ynadvgroup_album_detail_thumb-more"><?php echo $this->translate('<span>Show more<span class="ynicon yn-arr-down"></span></span>'); ?>
  </span>
</div>
  <div id="<?php echo $photo_listing_id; ?>">
  <div class="ynadvgroup_album_detail-stats clearfix">
    <div class="ynadvgroup_photo_detail-stats-info">
        <div class="ynadvgroup-photo"><?php echo $this->translate('Photos') . ': ' . $this->locale()->toNumber($album->count());?></div>
        <div class="ynadvgroup-views yn_stats_view"><i class="yn_dots">|</i><span><?php echo $this->translate('Views') . ': ' . $this->locale()->toNumber($album->view_count);?></span><i class="yn_dots">|</i></div>
        <div class="ynadvgroup-comments yn_stats_comment"><span><?php echo $this->translate('Comments') . ': ' . $this->locale()->toNumber($album->comment_count);?></span></div>
    </div>
  </div>
  <div class="ynadvgroup-listing-tab">
    <div title="<?php echo $this->translate('Grid view');?>" class="grid-view" data-view="ynadvgroup-grid-view"></div>
    <div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view" data-view="ynadvgroup-pinterest-view"></div>
  </div>
  <div class="photo-list-content">
     <!-- grid view -->
    <ul class="ynadvgroup_album_detail_grid_view clearfix">
      <?php foreach( $this->paginator as $photo ): ?>
       <li id='thumbs_nocaptions_<?php echo $photo->getIdentity()?>'>
          <div class="ynadvgroup_album_detail_grid_view_items">
            <a class="thumbs_photo clearfix" href="<?php echo $photo->getHref(); ?>">
              <span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
            </a>
            <div class="ynadvgroup_thumb_photo_info">
                  <a class="ynadvgroup_thumb_photo_info_title" href="<?php echo $photo->getHref(); ?>"><?php echo $photo->getTitle() ?></a>
                <div class="ynadvgroup_photo_info-stats">
                  <span class="yn_stats yn_stats_view"><?php echo $this->translate(array('%s view', '%s views', $photo->view_count), $this->locale()->toNumber($photo->view_count)); ?></span>
                  <span class="yn_dots">.</span>
                  <span class="yn_stats yn_stats_comment"><?php echo $this->translate(array('%s comment', '%s comments', $photo->comment_count), $this->locale()->toNumber($photo->comment_count)); ?></span>
                  <span class="yn_dots">.</span>
                  <?php $like_count = $photo->likes()->getLikeCount() ?>
                  <span class="yn_stats yn_stats_like"><?php echo $this->translate(array('%s like', '%s likes', $like_count), $this->locale()->toNumber($like_count)); ?></span>
                </div>
            </div>
          </div>
          
        </li>
      <?php endforeach;?>
    </ul>
    <!-- printerest view -->
    <ul id="<?php echo $photo_listing_id; ?>_tiles" class="ynadvgroup_album_detail_pinterest_view">
     <?php foreach( $this->paginator as $photo ): ?>
      <li id="thumbs-photo-<?php echo $photo->photo_id ?>" class="ynadvgroup_pinterest">
        <div class="ynadvgroup_photo_items_thumbs">
            <a class="ynadvgroup_photo_items_temp" href="<?php echo $photo->getHref(); ?>"></a>
            <img class="ynadvalbum_pinteres_thumbs-photo" src="<?php echo $photo->getPhotoUrl($thumb_photo); ?>" />
        </div>
        <div class="ynadvgroup_photo_info">
          <span class="ynadvgroup_photo_info-title">
            <a href="<?php echo $photo->getHref(); ?>"><?php echo $photo->getTitle() ?></a>
          </span>
          <span class="ynadvgroup_photo_info-stats">
            <span class="yn_stats yn_stats_view"><?php echo $this->translate(array('%s view', '%s views', $photo->view_count), $this->locale()->toNumber($photo->view_count)); ?></span>
              <span class="yn_dots">.</span>
              <span class="yn_stats yn_stats_comment"><?php echo $this->translate(array('%s comment', '%s comments', $photo->comment_count), $this->locale()->toNumber($photo->comment_count)); ?></span>
              <span class="yn_dots">.</span>
              <?php $like_count = $photo->likes()->getLikeCount() ?>
              <span class="yn_stats yn_stats_like"><?php echo $this->translate(array('%s like', '%s likes', $like_count), $this->locale()->toNumber($like_count)); ?></span>
          </span>
          </div>
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
  <br/>
  <?php 
	//echo $this->action("list", "comment", "core", array("type"=>"advgroup_album", "id"=>$this->album->getIdentity()));
	echo $this->content()->renderWidget('core.comments', array("type"=>"advgroup_album", "id"=>$this->album->getIdentity()));
  ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded in this album yet.');?>
    </span>
  </div>

<?php endif; ?>

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advgroup/externals/scripts/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advgroup/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advgroup/externals/scripts/wookmark/jquery.wookmark.js"></script>

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
            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li.ynadvgroup_pinterest');

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

         $('#<?php echo $photo_listing_id; ?> .ynadvgroup-listing-tab > div').click(function() {
            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li.ynadvgroup_pinterest');
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

            $('#<?php echo $photo_listing_id; ?> .ynadvgroup-listing-tab > div').removeClass('active');
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
      $$('#main_tabs li.tab_layout_ynadvgroup_business_profile_photos').addEvent('click', function(){
        if(view_mode == 'pinterest')
        {
            var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynadvgroup_pinterest');
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
      $$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content ynadvgroup-'+ view_mode +'-view');
      $$('#<?php echo $photo_listing_id; ?> .ynadvgroup-listing-tab > div').removeClass('active');

      if(view_mode == "grid" )
      {
        $$('#<?php echo $photo_listing_id; ?> .ynadvgroup-listing-tab > div.grid-view').addClass('active');
      }
      if(view_mode == "pinterest" )
      {
        $$('#<?php echo $photo_listing_id; ?> .ynadvgroup-listing-tab > div.pinterest-view').addClass('active');
         var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynadvgroup_pinterest');
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
      $$('#<?php echo $photo_listing_id; ?> .ynadvgroup-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
      if("<?php echo  $this->view_mode ?>" == "pinterest" )
      {
        $$('#<?php echo $photo_listing_id; ?> .ynadvgroup-listing-tab > div.pinterest-view').addClass('active');
        var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynadvgroup_pinterest');
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
});
</script>

<script type="text/javascript">
    if ($$('.ynadvgroup_album_detail-desctiption').length) {
       $$('.ynadvgroup_album_detail_thumb-more').removeEvents('click').addEvent('click', function() {
          $$('.ynadvgroup_album_detail_thumb-description-content').toggleClass('ynadvgroup_album_detail-description_toggle');
          if ($$('.ynadvgroup_album_detail_thumb-description-content')[0].hasClass('ynadvgroup_album_detail-description_toggle'))
            $$('.ynadvgroup_album_detail_thumb-more').set('html','<span>Show more <span class="ynicon yn-arr-down"></span></span>');
          else
            $$('.ynadvgroup_album_detail_thumb-more').set('html','<span>Show less <span class="ynicon yn-arr-up"></span></span>');
        });

            var hide = $$('.ynadvgroup_album_detail_thumb-description-content').length;
            if(hide){
              var height = $$('.ynadvgroup_album_detail_thumb-description-content')[0].getHeight();
              if(height > 65){
                $$('.ynadvgroup_album_detail_thumb-description-content')[0].addClass('ynadvgroup_album_detail-description_toggle');
                $$('.ynadvgroup_album_detail_thumb-more')[0].setStyle('display', 'inline-block');
              }
              else{
               $$('.ynadvgroup_album_detail-desctiption')[0].setStyle('height', '60px');
               $$('.ynadvgroup_album_detail-desctiption')[0].toggleClass('hide_line');
              }
              if(height == 0){
                $$('.ynadvgroup_album_detail-desctiption')[0].setStyle('height', '0px');
              }
            }
            else{
        $$('.ynadvgroup_album_detail-desctiption')[0].setStyle('display', 'none');
      }
            }
</script>

<script type="text/javascript">
	function slideshow(photo_id)
   	{
		request = new Request.JSON({
			'format' : 'json',
            'url' :  en4.core.baseUrl + 'groups/photo/set-slideshow',
            'data': {
            	'photo_id' : photo_id,
            },
            'onSuccess' : function(responseJSON) {
            	if(responseJSON.status)
            	{
            		obj = $('nocaptions_photo_slideshow_'+responseJSON.photo_id);
					obj.set('class','buttonlink icon_event_delete');
					obj.set('html',en4.core.language.translate('Remove from Slideshow'));
            	}
            	else
            	{
            		obj = $('nocaptions_photo_slideshow_'+responseJSON.photo_id);
            		obj.set('class','buttonlink icon_event_slideshow');
					obj.set('html',en4.core.language.translate('Add to Slideshow'));
            	}
            }
		});
        request.send();
        return false;
   	};
   	
   	function removeFile(photo_id)
   	{
   		var action = confirm(en4.core.language.translate('Are you sure you want to delete this photo?'));
   		
   		if(action)
   		{
   			request = new Request.JSON({
				'format' : 'json',
	            'url' :  en4.core.baseUrl + 'groups/photo/delete-photo',
	            'data': {
	            	'photo_id' : photo_id,
	            },
	            'onSuccess' : function(responseJSON) {
	            	
	            }
			});
	        request.send();
	        
	        $('thumbs_nocaptions_'+photo_id).dispose();
   		}
		
		return false;
   	}  	
</script>
<script type="text/javascript">
  window.addEvent('domready', function() {
    ynadvgroupOptions();
  });
</script>
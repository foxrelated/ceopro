<?php if( $this->paginator->getTotalItemCount() > 0 || $this->canUpload ):
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/js/businesspages.js');
$photo_listing_id = "ynbusinesspages_profile_photos";
    if (!isset($this->class_mode)) $this->class_mode = 'ynbusinesspages-grid-view';
    if (!isset($this->view_mode)) $this->view_mode = 'grid';
?>
<div class="ynbusinesspages-profile-module-header">
    <div class="ynbusinesspages-profile-header-right">
        <!-- Menu Bar -->
    <form action="<?php echo $this->url() ?>" method="get">
    <?php if($this->paginator->getTotalItemCount() > 5): ?>      
        <?php $i = 5; ?>
        <select name="num_items" onchange="this.form.submit()">
          <?php for($i; $i < $this->paginator->getTotalItemCount()+5; $i+=5): ?>
            <option value="<?php echo $i ?>" <?php if($i==$this->itemCountPerPage) echo 'selected' ?>><?php echo $this->translate('%s items per page', $i) ?></option>
          <?php endfor; ?>
        </select>
      
    <?php endif ?>
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'ynbusinesspages_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->subject()->getGuid(),
          'tab' => $this->identity,
        ), '<i class="ynicon yn-list-view"></i>'.$this->translate('View All Photos'), array(
          'class' => 'buttonlink'
      )) ?>
    <?php endif; ?>
   
      <?php if( $this->canUpload ): ?>
        <?php echo $this->htmlLink(array(
            'route' => 'ynbusinesspages_extended',
            'controller' => 'photo',
            'action' => 'upload',
            'subject' => $this->subject()->getGuid(),
            'tab' => $this->identity,
          ), '<i class="ynicon yn-plus-circle"></i>'.$this->translate('Upload Photos'), array(
            'class' => 'buttonlink'
        )) ?>
      <?php endif; ?>
      </form>
    </div> 
    <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
            <?php echo $this-> translate(array("ynbusiness_photo", "Photos", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>     
</div>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<script type="text/javascript">
en4.core.runonce.add(function()
{
    var anchor = $('ynbusinesspages_profile_photos').getParent();
    $('ynbusinesspages_profile_photos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynbusinesspages_profile_photos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynbusinesspages_profile_photos_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    },
                    onSuccess: function() {
                        setTimeout(ynbusinessPhotoInitViewMode, 200);
        }
      }), {
        'element' : anchor
      })
    });

    $('ynbusinesspages_profile_photos_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                    },
                    onSuccess: function() {
                        setTimeout(ynbusinessPhotoInitViewMode, 200);
        }
      }), {
        'element' : anchor
      })
    });
  });
</script>
<div id="<?php echo $photo_listing_id; ?>">
  <div class="ynbusinesspages-listing-tab">
            <div title="<?php echo $this->translate('Grid view');?>" class="grid-view" data-view="grid"></div>
            <div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view" data-view="pinterest"></div>
  </div>
  
  <div class="photo-list-content ynbusinesspages-grid-view" >
    <!-- grid view -->
    <ul class="thumbs" id="ynbusinesspages_profile_photos">
        <div id="ynbusiness_canloadmore" current_page="1">
            <div id="ynbusiness_loadmore" style="background-image: url('application/modules/Advalbum/externals/images/load_more.gif');"></div>
        </div>
    </ul>
    <!-- printer view -->
    <ul id="<?php echo $photo_listing_id; ?>_tiles" class="photo-pinterest-view gallery<?php echo $this->rand; ?> clearfix">
    </ul>
    <div id="ynbusiness_canloadmore_pinterest" current_page="1">
      <div id="ynbusiness_loadmore_pinterest" style="background-image: url('application/modules/Advalbum/externals/images/load_more.gif');"></div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/wookmark/jquery.wookmark.js"></script>

<script type="text/javascript">
    var isLoading = false;
    jQuery.noConflict();
    window.addEvent('domready', function () {
        ynbusinessPhotoInitViewMode();
        ynbusinesspagesOptions();
        ynbusinessLoadMorePhotos();
        // load more photos
        window.onscroll = ynbusinessOnLoadMorePhoto;
    });

    function ynbusinessGetLoadMoreEl(){
        var view_mode = getCookie('<?php echo $photo_listing_id; ?>view_mode') || '<?php echo $this->view_mode ?>' || 'grid';
        if(view_mode == 'grid'){
            return $('ynbusinesspages_profile_photos').getElement('#ynbusiness_canloadmore');
        }else{
            return $('ynbusiness_canloadmore_pinterest');
        }
    }

    function ynbusinessOnLoadMorePhoto(){
        var canLoadMore = ynbusinessGetLoadMoreEl();
        if (!canLoadMore)
            return;

        if (typeof (canLoadMore.offsetParent) != 'undefined') {
            var elementPostionY = canLoadMore.offsetTop;
        } else {
            var elementPostionY = canLoadMore.y;
        }
        if (elementPostionY <= window.getScrollTop() + window.getSize().y - 60) {
            (ynbusinessLoadMorePhotos).delay(1000);
        }
    }
    function ynbusinessLoadMorePhotos()
    {
        if (isLoading) return;
        isLoading = true;
        var view_mode = getCookie('<?php echo $photo_listing_id; ?>view_mode') || '<?php echo $this->view_mode ?>' || 'grid';
        var canLoadMore = ynbusinessGetLoadMoreEl();
        var nextPage = parseInt(canLoadMore.get('current_page'));
        new Request.HTML({
                method: 'post',
                url: '<?php echo $this->url(array('controller'=>'business', 'action'=>'load-more-photos', 'business_id'=>$this->subject()->getIdentity()), 'ynbusinesspages_specific') ?>',
                data: {
                    format: 'html',
                    page: nextPage,
                    view_mode: view_mode,
                    itemCountPerPage: <?php echo $this->itemCountPerPage ?>,
    },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            if(view_mode == 'grid')
                canLoadMore.outerHTML = responseHTML;
            else
                $('<?php echo $photo_listing_id; ?>_tiles').innerHTML +=  responseHTML;
            if(view_mode == 'pinterest'){
                canLoadMore.set('current_page', nextPage+1);
                if(responseHTML == "")
                    canLoadMore.destroy();
            }
            ynbusinessPhotoSetViewMode(view_mode);
            isLoading = false;
        }
    }).send();
    }

    en4.core.runonce.add(function()
    {
        $$('#main_tabs li.tab_layout_ynbusinesspages_business_profile_photos').addEvent('click', function(){
            ynbusinessPhotoInitViewMode();
        });

    });

    function ynbusinessPhotoInitViewMode() {

        var view_mode = getCookie('<?php echo $photo_listing_id; ?>view_mode') || '<?php echo $this->view_mode ?>' || 'grid';
        ynbusinessPhotoSetViewMode(view_mode);

        $$('#<?php echo $photo_listing_id; ?> .ynbusinesspages-listing-tab > div').removeEvents('click').addEvent('click', function(){
            var view_mode = this.get('data-view');
            ynbusinessPhotoSetViewMode(view_mode);
            ynbusinessOnLoadMorePhoto();
        });

    }

    function ynbusinessPhotoSetViewMode(view_mode) {

        $$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content ynbusinesspages-'+ view_mode +'-view');
        $$('#<?php echo $photo_listing_id; ?> .ynbusinesspages-listing-tab > div').removeClass('active');
        $$('#<?php echo $photo_listing_id; ?> .ynbusinesspages-listing-tab > div.' + view_mode + '-view').addClass('active');
        if(view_mode == "pinterest" )
        {
            var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li.ynbusinesspages_pinterest');
            var options = {
                itemWidth: 215,
                autoResize: true,
                container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
                offset: 25,
                outerOffset: 0,
                flexibleWidth: '50%'
            };

            if ( jQuery(window).width() < 1024) {
                options.flexibleWidth = '100%';
            }
            handler.wookmark(options);
            canLoadMore = $('ynbusiness_canloadmore_pinterest');
            if(canLoadMore != null){
                canLoadMore.show();
            }
        }else{
            canLoadMore = $('ynbusiness_canloadmore_pinterest');
            if(canLoadMore != null){
                canLoadMore.hide();
            }
        }

        setCookie('<?php echo $photo_listing_id; ?>view_mode', view_mode);
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
</script>
<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this business yet.');?>
    </span>
  </div>

<?php endif; ?>




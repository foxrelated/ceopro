
<?php
    $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/jquery-1.10.2.min.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
        ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/tagger.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/tabcontent.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/owl.carousel.min.js');
    $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/styles/owl.carousel.css');
?>
<?php $userVirtualAlbums = Engine_Api::_()->getDbTable("albums", "advalbum")->getVirtualAlbumsAssoc(Engine_Api::_()->user()->getViewer());
$has_virtual_album = (count($userVirtualAlbums)) ? true : false;
$session = new Zend_Session_Namespace('mobile');
?>
<script type="text/javascript">
    var taggerInstance;
    var movenext = 1;
    en4.core.runonce.add(function() {
        var taggerInstance = window.taggerInstance = new Tagger('ynadvalbum_photo_detail-next-main-photo', {
            'title' : '<?php echo $this->string()->escapeJavascript($this->translate('ADD TAG'));?>',
            'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
            'createRequestOptions' : {
                'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
                'data' : {
                    'subject' : '<?php echo $this->subject()->getGuid() ?>'
                }
            },
            'deleteRequestOptions' : {
                'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
                'data' : {
                    'subject' : '<?php echo $this->subject()->getGuid() ?>'
                }
            },
            'cropOptions' : {
                'container' : $('ynadvalbum_photo_detail-next-main-photo')
            },
            'tagListElement' : 'media_tags',
            'existingTags' : <?php echo Zend_Json::encode($this->tags) ?>,
        'suggestProto' : 'request.json',
                'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
                'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
        'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
            'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>,
            'isMobile': <?php echo ($session->mobile ? 'true' : 'false') ?>,
    });

        // Remove the href attrib while tagging
        var nextHref = $('ynadvalbum_photo_detail-next-main-photo').get('href');
        taggerInstance.addEvents({
            'onBegin' : function() {
                $('ynadvalbum_photo_detail-next-main-photo').erase('href');
            },
            'onEnd' : function() {
                $('ynadvalbum_photo_detail-next-main-photo').set('href', nextHref);
            }
        });
        var keyupEvent = function(e) {
            if( e.target.get('tag') == 'html' ||
                    e.target.get('tag') == 'body' ) {
                if( e.key == 'right' ) {
                    $('photo_next').fireEvent('click', e);
                } else if( e.key == 'left' ) {
                    $('photo_prev').fireEvent('click', e);
                }
            }
        }
        window.addEvent('keyup', keyupEvent);

        // Add shutdown handler
        en4.core.shutdown.add(function() {
            window.removeEvent('keyup', keyupEvent);
        });

    });

    // rating
    en4.core.runonce.add(function() {
        var pre_rate = <?php echo $this->photo->rating; ?>;
        var photo_id = <?php echo $this->photo->getIdentity(); ?>;
        var total_votes = <?php echo $this->rating_count; ?>;
        var viewer = <?php echo $this->viewer()->getIdentity(); ?>;

        var rating_over = window.rating_over = function(rating) {
            if( viewer == 0 ) {
                $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
            } else {
                $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
                for(var x=1; x<=5; x++) {
                    if(x <= rating) {
                        $('rate_'+x).set('class', 'advalbum_rating_star_big_generic ynicon yn-star ynadvalbum_ratings');
                    } else {
                        $('rate_'+x).set('class', 'advalbum_rating_star_big_generic ynicon yn-star');
                    }
                }
            }
        };

        var rating_out = window.rating_out = function() {
            if (total_votes > 0 && total_votes <= 1) {
                $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('rating')?>";
            }
            else {
                $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('ratings')?>";
            }
            if (pre_rate != 0){
                set_rating();
            }
            else {
                for(var x=1; x<=5; x++) {
                    $('rate_'+x).set('class', 'advalbum_rating_star_big_generic ynicon yn-star');
                }
            }
        };

        var set_rating = window.set_rating = function() {
            var rating = pre_rate;
            if (total_votes == 1) {
                $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('rating')?>";
            }
            else {
                $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('ratings')?>";
            }
            for(var x=1; x<=parseInt(rating); x++) {
                $('rate_'+x).set('class', 'advalbum_rating_star_big_generic ynicon yn-star ynadvalbum_ratings');
            }

            for(var x=parseInt(rating)+1; x<=5; x++) {
                $('rate_'+x).set('class', 'advalbum_rating_star_big_generic ynicon yn-star');
            }

            var remainder = Math.round(rating)-rating;
            if (remainder <= 0.5 && remainder !=0){
                var last = parseInt(rating)+1;
                $('rate_'+last).set('class', 'advalbum_rating_star_big_generic ynicon yn-star-half-o ynadvalbum_ratings');
            }
        };

        var rate = window.rate = function(rating) {
            $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
            (new Request.JSON({
                'format': 'json',
                'url' : '<?php echo $this->url(array('action' => 'rate', 'subject_id' => $this->photo->getIdentity()), 'album_extended', true) ?>',
                'data' : {
                    'format' : 'json',
                    'rating' : rating,
                },
                'onRequest' : function(){
                },
                'onSuccess' : function(responseJSON, responseText)
                {
                    total_votes = responseJSON.total;
                    pre_rate = responseJSON.rating;
                    set_rating();
                }
            })).send();
        };

        set_rating();
        window.addEvent('domready', function(){
            var currentItem = jQuery("#" + "<?php echo $this->photo->getIdentity(); ?>"),
                photoSlides = jQuery('#ynadvalbum_photo_detail_slide'),
                index = jQuery("#ynadvalbum_photo_detail_slide .ynadvalbum_photo_detail_thumb-items").index(currentItem);

             var item_amount = parseInt(photoSlides.find('.item').length); 
             var true_false = 0;
             if (item_amount > 1) {
                true_false = true;
             } else{
                true_false = false;
             }
            var rtl = false;
             if(jQuery("html").attr("dir") == "rtl") {
             rtl = true;
             }
            photoSlides.owlCarousel({
                rtl:rtl,
                nav: true,
                autoplay: false,
                navText:["",""],
                responsive:{
                    0:{
                        items:2
                    },
                    480:{
                        items:4
                    },
                    640:{
                        items:5
                    },
                    768:{
                        items:6
                    },
                    1000:{
                        items:8
                    }
                }
            });
            var owl = photoSlides.data('owlCarousel');
            // swipe to current photo
            owl.current(index);
            owl.update();
            photoSlides.show();
        });
    });
    en4.core.runonce.add(function(){
        $$('#ynadvalbum_photo_detail_btn-down').addEvent('click', function () {
            $$('#ynadvalbum_photo_detail_slide').toggle();
            this.toggleClass('ynadvalbum_button_photo_show');
              if (this.hasClass('ynadvalbum_button_photo_show')) {
                this.getChildren('span')[1].removeClass('yn-arr-up').addClass('yn-arr-down');
                this.getChildren('span')[0].removeClass('yn-photos-o').addClass('yn-photos-o');
              }
                
              else{
                this.getChildren('span')[1].removeClass('yn-arr-down').addClass('yn-arr-up');
                this.getChildren('span')[0].removeClass('yn-photos-o').addClass('yn-photos-o');
              }
            
        });
    });
</script>
<?php
$album = $this->album;
$album_owner = $album->getOwner();
$photo = $this->photo;
$photoCount = $this->paginatorp->getTotalItemCount();
?>
<div>
    <div>
        <div class="clearfix">
            <h2>
                <?php echo $this->translate('%1$s<span>\'s Album: </span>%2$s', $this->album->getOwner()->__toString(), $this->htmlLink($this->album, $this->album->getTitle())); ?>
                </h2>
                <div class="ynadvalbum_photo_detail-parent-btn">
                    <span id="ynadvalbum_photo_detail_btn-down">
                        <span class="ynicon yn-photos-o"></span><?php echo $this->translate(array('%s advalbum_photo', '%s photos', $photoCount), $this->locale()->toNumber($photoCount)) ?><span class="ynicon yn-arr-up"></span>
                    </span>
                </div>
        </div>
        <?php if ($photoCount): ?>
        <div>
            <div id="ynadvalbum_photo_detail_slide">
                <?php foreach($this->paginatorp as $photoThumb): ?>
                    <div id="<?php echo $photoThumb->getIdentity(); ?>" class="ynadvalbum_photo_detail_thumb-items item">
                        <?php $current = ($photoThumb->getIdentity() == $photo->getIdentity()) ? ' ynadvalbum_photo_current' : ''; ?>
                        <?php echo $this->htmlLink($photoThumb->getHref(), '<span class="ynadvalbum_photo_detail_thumb-item' . $current . '" style="background-image: url('.$photoThumb->getPhotoUrl().')"></span>', array(
                        )) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div>
        <div id='ynadvalbum_photo_detail-main-photo'>
            <div class="album_viewmedia_container">
            <?php
            $photoUrl = "javascript:void(0);";
            $photoUrl = $this->escape($this->nextPhoto->getHref(array('album_virtual' => $this -> album_virtual)));
            if(!$this->featured_view): ?>
                <a id='ynadvalbum_photo_detail-next-main-photo' href='<?php echo $photoUrl ?>'>
                    <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
                    'id' => 'media_photo',
                    )); ?>
                </a>
                <?php else: ?>
                <a id='ynadvalbum_photo_detail-next-main-photo' >
                    <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
                    'id' => 'media_photo',
                    )); ?>
                </a>
            <?php endif;?>
            </div>
        </div>

        <!-- Photo post date and actions -->
        <div class="ynadvalbum_photo_detail_thumb-option">
            <div class="ynadvalbum_photo_detail_post-time">
                <?php echo $this->translate("Posted on "), $this->timestamp($photo->creation_date) ?>
            </div>
            <div class="ynadvalbum_photo_detail_sub-menu">
                <?php if( $this->canTag ): ?>
                <?php echo $this->htmlLink('javascript:void(0);', '<span class="ynicon yn-user-plus"></span>' . $this->translate('Add tags'), array('onclick'=>'taggerInstance.begin();')) ?>
                <i class="yn_dots">.</i>
                <?php endif; ?>
                <?php if(Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                <?php echo $this->htmlLink(array(
                    'module'=> 'activity',
                    'controller' => 'index',
                    'action' => 'share',
                    'route' => 'default',
                    'type' => 'advalbum_photo',
                    'id' => $photo->getIdentity(),
                    'format' => 'smoothbox'),
                '<span class="ynicon yn-share"></span>' . $this->translate("Share"), array('class' => 'smoothbox')); ?>
                <i class="yn_dots">.</i>
                <?php echo $this->htmlLink(array('route' => 'user_extended',
                    'module' => 'user',
                    'controller' => 'edit',
                    'action' => 'external-photo',
                    'photo' => $photo->getGuid(),
                    'format' => 'smoothbox'),
                    '<span class="ynicon yn-photo-o"></span>' .$this->translate('Make profile picture'),
                    array('class' => 'smoothbox')
                ) ?>
                <?php endif; ?>
                    <div class="ynadvalbum-options">
                    <div class="ynadvalbum-options-button" >
                        <span class="ynicon yn-arr-down"></span>
                    </div>
                    <?php echo $this->partial('_item-menu-photo.tpl', 'advalbum', array(view_mode=> 'detail', 'has_virtual_album' => $has_virtual_album, 'album' => $album, 'photo' => $photo)); ?>
                </div>
                <!-- Photo actions here -->
            </div>
        <div class="ynadvalbum-options ynadvalbum-second">
                    <div class="ynadvalbum-options-button" >
                        <span class="ynicon yn-arr-down"></span>
                    </div>
                <?php echo $this->partial('_item-menu-photo.tpl', 'advalbum', array(view_mode=> 'detail-second', 'has_virtual_album' => $has_virtual_album, 'album' => $album, 'photo' => $photo)); ?>
                </div>
        </div>
        <!-- Photo taken date, location and rating -->
        <div class="ynadvalbum_photo_detail_thumb-info">
            <div class="ynadvalbum_photo_detail_thumb-info-local">
                <?php
        	    if (!empty($this->photo->taken_date) && Zend_Date::isDate($this->photo->taken_date, 'YYYY-MM-dd')) {
                    echo '<span class="ynicon yn-clock-o"></span>' . date('j F Y', strtotime($this->photo->taken_date));
                    if (!empty($this->photo->location)) {
                        echo ' - ';
                    }
                }
                if (!empty($this->photo->location)) {
                    $href = "https://maps.google.com/?q=" . urlencode($this->photo->location);
                    $location = '<span class="ynicon yn-map-marker"></span>' . sprintf("<a href='javascript:void(0)' onclick='openLocation(%s)'  title='%s'>%s</a>", "\"{$href}\"", Advalbum_Api_Core::shortenText($this->photo->location, 70), $this->photo->location);
                    echo $location;
                }
                ?>
            </div>
            <div id="advalbum_rating" onmouseout="rating_out();" class="ynadvalbum_photo_detail_thumb-rating">
                <span id="rate_1" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
                <span id="rate_2" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
                <span id="rate_3" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
                <span id="rate_4" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
                <span id="rate_5" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
                <span id="rating_text" class="rating_text advalbum_rating_text"><?php echo $this->translate('click to rate'); ?></span>  
            </div>
        </div>

        <div  class="ynadvalbum_photo_detail_thumb-description">
            <?php if ($this->photo->description): ?>
            <span  class="ynadvalbum_photo_detail_thumb-description-content ynadvalbum_photo_detail-description_toggle">
                <?php echo $this->photo->description ?>
            <?php endif; ?>
            </span>
            
              <span class="ynadvalbum_photo_detail_thumb-more"><?php echo $this->translate('<span>Show more<span class="ynicon yn-arr-down"></span></span>'); ?></span>
            
        </div>

        <div class="albums_viewmedia_info_tags" id="media_tags" style="display: none;">
            <?php echo $this->translate('Tagged:');?>
        </div>

        <div class="ynadvalbum_photo_detail_addthis">
            <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
            <!-- Go to www.addthis.com/dashboard to customize your tools -->  
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
        </div>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        ynadvalbumOptions();
        $$('.ynadvalbum_photo_detail_thumb-more').removeEvents('click').addEvent('click', function() {
          $$('.ynadvalbum_photo_detail_thumb-description-content').toggleClass('ynadvalbum_photo_detail-description_toggle');
          if ($$('.ynadvalbum_photo_detail_thumb-description-content')[0].hasClass('ynadvalbum_photo_detail-description_toggle'))
            $$('.ynadvalbum_photo_detail_thumb-more').set('html','<span>Show more <span class="ynicon yn-arr-down"></span></span>');
          else
            $$('.ynadvalbum_photo_detail_thumb-more').set('html','<span>Show less <span class="ynicon yn-arr-up"></span></span>');
        });

            var hide = Object.getLength($$('.ynadvalbum_photo_detail_thumb-description-content'));
            if(hide != '0'){
              var height = $$('.ynadvalbum_photo_detail_thumb-description-content')[0].getHeight();
              if(height > 43){
                $$('.ynadvalbum_photo_detail_thumb-description-content')[0].addClass('ynadvalbum_photo_detail-description_toggle');
                $$('.ynadvalbum_photo_detail_thumb-more').setStyle('display', 'inline-block');
              }
              else{
                $$('.ynadvalbum_photo_detail_thumb-description').setStyle('height','60px');
                $$('.ynadvalbum_photo_detail_thumb-description')[0].toggleClass('hide_line');
              }
            }
            else{
                 $$('.ynadvalbum_photo_detail_thumb-description').setStyle('display', 'none');
            }
        });

    function showSmoothbox(url) {
        Smoothbox.open(url);
    }
    function closeSmoothbox() {
        var block = Smoothbox.instance;
        block.close();
    }
</script>
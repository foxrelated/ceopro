<?php
$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
	->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
	->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
	->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
	->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
	->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/tagger.js')
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/tabcontent.js');
?>

<script type="text/javascript">

window.addEvent('domready', function()
{
      $(document.body).focus();
    	 if(window.parent.$('ynadvalbum_addTo_menu_list')) {

     		window.parent.$('ynadvalbum_addTo_menu_list').destroy();
    	 }
	  if (window.parent.Smoothbox.instance) {
		  window.parent.Smoothbox.instance.content.contentWindow.focus();
		  window.parent.Smoothbox.instance.doAutoResize = function(element){
			    element = $$('span#global_content_simple')[0];
			    var that = window.parent.Smoothbox.instance; 
			    if( !element || !that.options.autoResize )
			    {
			      return;
			    }

			    var size = Function.attempt(function(){
			      return element.getScrollSize();
			    }, function(){
			      return element.getSize();
			    }, function(){
			      return {
			        x : element.scrollWidth,
			        y : element.scrollHeight
			      }
			    });

			    var winSize = window.parent.getSize();
			    if( size.x - 70 > winSize.x ) size.x = winSize.x - 70;
			    if( size.y - 70 > winSize.y ) size.y = winSize.y - 70;

			    that.content.setStyles({
			      'width' : (size.x + 0) + 'px',
			      'height' : (size.y + 0) + 'px'
			    });

			    that.options.width = (size.x + 0);
			    that.options.height = (size.y + 0);

			    that.positionWindow();
		 }
	}
  });

  window.addEvent('domready', function(){
    	$('advalbum_arrow_next').addEvent('click', function(e) {
    		document.location.href = "<?php echo ($this->nextPhoto)?$this->nextPhoto->getHref(array('album_virtual' => $this -> album_virtual)) . '/format/smoothbox':'window.location.href' ?>";
    	});

    	$('advalbum_arrow_previous').addEvent('click', function(e){
    		window.location.href = "<?php echo ($this->previousPhoto)?$this->previousPhoto->getHref(array('album_virtual' => $this -> album_virtual)) . '/format/smoothbox':'window.location.href' ?>";
    	});
  });
  var taggerInstance;
  var movenext = 1;
  en4.core.runonce.add(function() {
        taggerInstance = new Tagger('advalbum_left_advalbum_viewLeft_photo', {
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
        'container' : $('advalbum_left_advalbum_viewLeft')
      },
      'tagListElement' : 'media_tags',
            'tagTextElement' : 'ynadvalbum_tag_text',
      'existingTags' : <?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>,
      'suggestParam' : <?php echo ( $this->viewer()->getIdentity() ? $this->action('suggest', 'friends', 'user', array('sendNow' => false, 'includeSelf' => true)) : 'null' ) ?>,
      'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
      'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
            'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>,
            'popupTagList': true
    });

	 // Remove the href attrib while tagging
    var nextHref = $('advalbum_left_advalbum_viewLeft_photo').get('href');
    taggerInstance.addEvents({
      'onBegin' : function() {
        $('advalbum_left_advalbum_viewLeft_photo').erase('href');
      },
      'onEnd' : function() {
        $('advalbum_left_advalbum_viewLeft_photo').set('href', nextHref);
      }
    });
  });

	window.addEvent('keyup', function(e) {
	    if( e.target.get('tag') == 'html' || e.target.get('tag') == 'body' ) {
	      	if( e.key == 'right' ) {
		        window.location.href = "<?php echo ( $this->nextPhoto ? $this->nextPhoto->getHref() : 'window.location.href' ) ?>" + '/format/smoothbox';
	        } else if( e.key == 'left' ) {
	      		window.location.href = "<?php echo ( $this->nextPhoto ? $this->nextPhoto->getHref() : 'window.location.href' ) ?>" + '/format/smoothbox';
	        }
	    }
    });

  // rating
  en4.core.runonce.add(function() {
      var pre_rate = <?php echo $this->photo->rating; ?>;
      var photo_id = <?php echo $this->photo->getIdentity(); ?>;
      var total_votes = <?php echo $this->rating_count; ?>;
      var viewer = <?php echo $this->viewer()->getIdentity(); ?>;

      var rating_over = window.rating_over = function(rating) {
          //set_rating();
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
  });
</script>
<?php
    $album_title_full = trim($this->album->getTitle());
    //$album_title = Advalbum_Api_Core::shortenText($album_title_full, $shortenLength);
    $album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
    if ($this->album->count()>1) {
        $strPhotos = $this->translate('%1$d photos', $this->album->count());
    } else {
        $strPhotos = $this->translate('%1$d photo', $this->album->count());
    }
    $tooltip_text = $this->translate('%1$s (%2$s)', $album_title_tooltip, $strPhotos);
    $album = $this->album;
$photo = $this->photo;
    $owner = $photo->getOwner();
 ?>
 <div onclick="parent.Smoothbox.close()" class='tclose advalbum_view_photo'>
          <span class="ynicon yn-del-circle"></span>
        </div>
<div class="ynadvalbum_view_photo_popup clearfix">
	<div class="ynadvalbum_left ynadvalbum_viewLeft" id ='advalbum_left_advalbum_viewLeft'>
        <div id="advalbum_view_photo">
		<div  class="album_viewmedia_container">
		    <a href="javascript:void(0);" class="advalbum_popup_viewPhoto" id="advalbum_left_advalbum_viewLeft_photo">
                <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
                    'id' => 'media_photo',
                )); ?>
			</a>
      <div class="ynadvalbum_popup_photo-info">
            <?php if ($this->photo->description || $this->photo->title): ?>
            <span class="ynadvalbum_popup_photo-description"><span class="ynadvalbum-title"><?php echo $this->photo->getTitle() ?></span><?php if ($this->photo->description) echo ' <span>-</span> ' . $this->photo->description ?></span>
            <?php endif; ?>
            <div class="ynadvalbum_photo_detail_popup-more">
              <span><?php echo $this->translate('+ View more'); ?></span>
            </div>
        </div>
		</div>
        </div>
		<div class="advalbum_nav">
			<a class="advalbum_arrow_previous" href="javascript:void(0)" id='advalbum_arrow_previous'>
        <span class="ynicon yn-arr-left"></span>   
      </a>
			<a class="advalbum_arrow_next" href="javascript:void(0)" id='advalbum_arrow_next'>
        <span class="ynicon yn-arr-right"></span>   
      </a>
		</div>
		<div class="advalbum_viewPhoto_container">
            
		    <div class='advalbum_viewPhoto_info'>
    		      
            	<div style=''>
            	<?php
        	    if (!empty($this->photo->taken_date) && Zend_Date::isDate($this->photo->taken_date, 'YYYY-MM-dd')) {
                    echo $this->photo->taken_date;
                    // echo $this->locale()->toDatetime($this->photo->taken_date);
                    if ($this->photo->location) {
                       echo ' - ';
                    }
                }
             	$href = "https://maps.google.com/?q=" . urlencode($this->photo->location);
             	$location = sprintf("<a href='javascript:void(0)' onclick='openLocation(%s)'  title='%s'>%s</a>", "\"{$href}\"", Advalbum_Api_Core::shortenText($this->photo->location, 70), $this->photo->location);

               if ($this->photo->location) {
              	 echo $location;
               }
           ?>
               </div>
           </div>
			<div class="albums_viewmedia_info_date clearfix">
			</div>
		</div>
	</div>
	<div class="advalbum_right">
		<div class="advalbum_right_content">
            
        <div class="thumbs thumbs_album_small">
          <div id="thumbs-photo-album-<?php echo $album->album_id ?>">
            <a class="thumbs_photo" href="<?php echo $album->getHref();?>">
              <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
            </a>
            <div class="thumbs_info">
                <div id="advalbum_rating" class="rating clearfix" onmouseout="rating_out();">
                    <span id="rate_1" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
                    <span id="rate_2" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
                    <span id="rate_3" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
                    <span id="rate_4" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
                    <span id="rate_5" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
                      <span id="rating_text" class="rating_text advalbum_rating_text"><?php echo $this->translate('click to rate'); ?></span>
                </div>

                        <div id="ynadvalbum_tag_text" style="display: none;">
                   <span class="ynadvalbum_tagged"><?php echo $this->translate('Tagged:');?></span>
                </div>
                        <div class="albums_viewmedia_info_tags" id="media_tags" style="display: none;  text-align: left; clear:both;">
                        </div>
              <span class="thumbs_title">
                <?php echo $this->translate('%1$s\'s Album: %2$s', '<a target="_top" href="'.$album->getOwner()->getHref().'">'.$this -> string() -> truncate($album->getOwner()-> getTitle(), 20).'</a>', $this->htmlLink($album, $album->getTitle()),array('target'=>'_top','title' => $album_title_tooltip)); ?>
              </span>
            </div>                                
          </div>
        </div>
          <?php
        echo $this->action("list", "comment", "core",
          array(
            "type"=>$this->photo->getType(),
            "id"=>$this->photo->getIdentity()
          )
        );
    ?>
    </div>
	</div>
    <div class="ynadvalbum_view_photo_popup-info-bottom">
      <div class="ynadvalbum_popup_owner">
            <?php
                $ownerPhoto = $owner->getPhotoUrl('thumb.profile');
                if (!$ownerPhoto)
                    $ownerPhoto = $this->getHelper('itemPhoto')->getNoPhoto($owner, 'thumb.profile');
            ?>
            <div class="ynadvalbum_popup_owner_info-photo" style="background-image:url(<?php echo $ownerPhoto ?>);">
                <a href="<?php echo $owner->getHref(); ?>"></a>
            </div>
            <div class="ynadvalbum_popup_owner_info-name">
                <?php echo $owner->getTitle(); ?>
            </div>
            <i class="yn_dots">.</i>
            <div class="ynadvalbum_popup_owner_info-title">
              <?php echo '<i class="yn_dots"></i>'.$album ?>
            </div>
          <?php if($photo->isOwner(Engine_Api::_() -> user() -> getViewer())): ?>
            <div class="ynadvalbum_popup_owner_info-title-manage">
                <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'editphotos', 'album_id' => $album->getIdentity()), '<span>(</span>' . $this->translate('manage photos') . '<span>)</span>', array(
                'target' => '_top'
                )) ?>
            </div>
          <?php endif; ?>
        
      </div>    
      <span class="ynadvalbum_popup_view_stats">
        <?php $photos_count = $album->count();
          $likes_count = $photo->like_count;
        $str_likes = '<span class="yn_stats yn_stats_view">'.$this->translate(array('%s <span>Like</span>', '%s <span>Likes</span>', $photo->like_count), $this->locale()->toNumber($photo->like_count)).'</span>';
          $str_comments = '<span class="yn_stats yn_stats_comment">'.$this->translate(array('%s <span>Comment</span>', '%s <span>Comments</span>', $photo->comment_count), $this->locale()->toNumber($photo->comment_count)).'</span>';
          echo '<span class="ynicon yn-thumb-up"></span>'.$str_likes.'<i class="yn_dots">.</i>';
          echo '<span class="ynicon yn-comment"></span>'.$str_comments;
        ?>
        <div class="popup-photo-view-rating">
            <?php if( $this->canTag ): ?>
                <div class="add-tag">
                    <span class="ynadvalbum_photo_popup_button">
                      <span class="ynicon yn-user-plus"></span>
                      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tags'), array('onclick'=>'taggerInstance.begin();')) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <div class="ynadvalbum-options-popup">
            <div class="ynadvalbum-options-button-popup"> <span class="ynicon yn-arr-up"></span> </div>
            <?php echo $this->partial('_item-menu-photo.tpl', 'advalbum', array('album' => $album, 'photo'=>$photo, 'view_mode'=>'detail-popup')); ?>
        </div>
      </span>
    </div>
</div>

<script type="text/javascript">
	function popupFixLinks() {
	    var popupArrLinks = document.links;
	    for (idxL=0; idxL<popupArrLinks.length;idxL++) {
	        var jsPos = popupArrLinks[idxL].href.indexOf('/profile/');
	        var jsView = popupArrLinks[idxL].href.indexOf('/view/');
	        if (jsPos>=0 || jsView >= 0) {
	            popupArrLinks[idxL].setAttribute("target","_top");
	        }
	    }
	}
	function cronFixLinks() {
	    popupFixLinks();
	    setTimeout("cronFixLinks()", 5000);
	}

	function do_onload() {
	    cronFixLinks();
	    if (parent.loading_complete) {
	        parent.loading_complete(<?php echo $this->photo->getIdentity(); ?>);
	    }
	}
	document.onload = do_onload();

window.addEvent('domready', function() {
    ynadvalbumOptions();
    $$('.ynadvalbum_photo_detail_popup-more').removeEvents('click').addEvent('click', function() {
      $$('.ynadvalbum_popup_photo-description').toggleClass('ynadvalbum_popup_photo_desc-toggle');
      if ($$('.ynadvalbum_popup_photo-description')[0].hasClass('ynadvalbum_popup_photo_desc-toggle'))
        $$('.ynadvalbum_photo_detail_popup-more').set('html','<span>+ View More</span>');
      else
        $$('.ynadvalbum_photo_detail_popup-more').set('html','<span>- View Less</span>');
    });

    var hide = ($$('.ynadvalbum_popup_photo-description').length);
    if(hide){
      var height = $$('.ynadvalbum_popup_photo-description')[0].getHeight();
            if(height > 84){
        $$('.ynadvalbum_photo_detail_popup-more').setStyle('display', 'block');

        $$('.ynadvalbum_popup_photo-description').toggleClass('ynadvalbum_popup_photo_desc-toggle');
      }
    }
    if($('comments'))
    {
        setTimeout(function(){
            $('comments').setStyle('overflow', 'scroll');
        }, 500);
    }
});


function showSmoothbox(url) {
    Smoothbox.open(url);
}
function closeSmoothbox() {
    var block = Smoothbox.instance;
    block.close();
}

    function ynadvalbumOpenTagBox() {
        $('media_tags').toggleClass('ynadvalbum_tag_show');
    }
    function ynadvalbumCloseTagBox() {
        $('media_tags').removeClass('ynadvalbum_tag_show');
    }
</script>
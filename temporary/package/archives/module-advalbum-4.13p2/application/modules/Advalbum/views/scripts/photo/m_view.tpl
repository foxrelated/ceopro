
<?php
$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/tagger.js');

function selfURL() {
	$server_array = explode ( "/", $_SERVER ['PHP_SELF'] );
	$server_array_mod = array_pop ( $server_array );
	if ($server_array [count ( $server_array ) - 1] == "admin") {
		$server_array_mod = array_pop ( $server_array );
	}
	$server_info = implode ( "/", $server_array );
	return "http://" . $_SERVER ['HTTP_HOST'] . $server_info . "/";
}
function serverURL() {
	return "http://" . $_SERVER ['HTTP_HOST'];
}
?>
<script type="text/javascript">
  var taggerInstance;
  var movenext = 1;
  en4.core.runonce.add(function() {
	    var taggerInstance = window.taggerInstance = new Tagger('media_photo_next', {
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
	          'container' : $('media_photo_next')
	        },
	        'tagListElement' : 'media_tags',
	        'existingTags' : <?php echo Zend_Json::encode($this->tags) ?>,
	        'suggestProto' : 'request.json',
	        'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
	        'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
	        'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
	        'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
	      });

		 // Remove the href attrib while tagging
	    var nextHref = $('media_photo_next').get('href');
	    taggerInstance.addEvents({
	      'onBegin' : function() {
	        $('media_photo_next').erase('href');
	      },
	      'onEnd' : function() {
	        $('media_photo_next').set('href', nextHref);
	      }
	    });
    var keyupEvent = function(e) {
        if( e.target.get('tag') == 'html' ||
            e.target.get('tag') == 'body' ) {
          if( e.key == 'right' ) {
            $('photo_next').fireEvent('click', e);
            //window.location.href = "<?php //echo ( $this->nextPhoto ? $this->nextPhoto->getHref() : 'window.location.href' ) ?>";
          } else if( e.key == 'left' ) {
            $('photo_prev').fireEvent('click', e);
            //window.location.href = "<?php //echo ( $this->previousPhoto ? $this->previousPhoto->getHref() : 'window.location.href' ) ?>";
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
      var rated = '<?php echo $this->is_rated; ?>';
      var photo_id = <?php echo $this->photo->getIdentity(); ?>;
      var total_votes = <?php echo $this->rating_count; ?>;
      var viewer = <?php echo $this->viewer()->getIdentity(); ?>;

      var rating_over = window.rating_over = function(rating) {
          if( rated == 1 ) {
              $('rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
              //set_rating();
          } else if( viewer == 0 ) {
              $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
          } else {
              $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
              for(var x=1; x<=5; x++) {
                  if(x <= rating) {
                      $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big');
                  } else {
                      $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
                  }
              }
          }
      }

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
                  $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
              }
          }
      }

      var set_rating = window.set_rating = function() {
          var rating = pre_rate;
          if (total_votes > 0 && total_votes <= 1) {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('rating')?>";
		  }
		  else {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('ratings')?>";
		  }
          for(var x=1; x<=parseInt(rating); x++) {
              $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big');
          }

          for(var x=parseInt(rating)+1; x<=5; x++) {
              $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
          }

          var remainder = Math.round(rating)-rating;
          if (remainder <= 0.5 && remainder !=0){
              var last = parseInt(rating)+1;
              $('rate_'+last).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_half');
          }
      }

      var rate = window.rate = function(rating) {
          $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
          for(var x=1; x<=5; x++) {
              $('rate_'+x).set('onclick', '');
          }
          (new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('action' => 'rate', 'subject_id' => $this->photo->getIdentity()), 'album_extended', true) ?>',
              'data' : {
                  'format' : 'json',
                  'rating' : rating,
              },
              'onRequest' : function(){
                  rated = 1;
                  total_votes = total_votes + 1;
                  pre_rate = (pre_rate+rating)/total_votes;
                  set_rating();
              },
              'onSuccess' : function(responseJSON, responseText)
              {
              }
          })).send();

      }

      set_rating();
  });
</script>
<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>
<?php $album_owner = $this->album->getOwner(); ?>
<div id="lightbox">
<div id="boxbox_1">
<div class="layout_middle">
<div class='albums_viewmedia'>
   <div class='albums_viewmedia_info'>
        <div class='album_viewmedia_container' id='media_photo_div' >
         <?php if (!$this->message_view):?>
			  <div class="albums_viewmedia_nav">
			     <?php if ($this->album->count() > 1): ?>
			    <div>
			      <?php echo $this->htmlLink(( $this->previousPhoto ? $this->previousPhoto->getHref() : null ), "<img src='./application/modules/Advalbum/externals/images/prev.png'/>", array('id' => 'photo_prev')) ?>
			     
			      <?php echo $this->htmlLink(( $this->nextPhoto ? $this->nextPhoto->getHref() : null ), "<img src='./application/modules/Advalbum/externals/images/next.png'/>", array('id' => 'photo_next')) ?>
			    </div>
			    <?php endif; ?>
			  </div>
		<?php endif;?>
        <?php
        $photoUrl = "javascript:;"; 
        $photoUrl = $this->escape($this->nextPhoto->getHref());
        if(!$this->featured_view):
        ?>
          <a id='media_photo_next' href='<?php echo $photoUrl ?>'>
            <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
              'id' => 'media_photo',
            )); ?>
          </a>
        <?php else: ?>
        <a style="cursor:default" id='media_photo_next' >
               <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
              'id' => 'media_photo',
            )); ?>
          </a>
        <?php endif;?>
        </div>
		<?php
         	$href = "https://maps.google.com/?q=" . urlencode($this->photo->location);         	
         	$location = sprintf("<a href='javascript:void(0)' onclick='openLocation(%s)'  title='%s'>%s</a>", "\"{$href}\"", Advalbum_Api_Core::shortenText($this->photo->location, 70), $this->photo->location);
        ?>
        <!-- Album name -->
        <h2>
		  <?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->htmlLink($this->album, $this->album->getTitle())); ?>
		</h2>
		 <?php if( $this->photo->getTitle() ): ?>
	      <div class="albums_viewmedia_info_title">
	      <?php echo $this->photo->getTitle(); ?>
	      </div>
	    <?php endif; ?>
    	<div class="albums_viewmedia_info_date_not_popup">
    		<div id="advalbum_rating" class="rating" onmouseout="rating_out();" style='float:left;'>
	    		<span id="rate_1" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
	    		<span id="rate_2" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
	    		<span id="rate_3" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
	    		<span id="rate_4" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
	    		<span id="rate_5" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
	    	    <span id="rating_text" class="rating_text advalbum_rating_text"><?php echo $this->translate('click to rate'); ?></span>
    		</div>
    	<div>
          <?php
          if (!empty($this->photo->taken_date) && Zend_Date::isDate($this->photo->taken_date, 'YYYY-MM-dd')) {
             echo $this->photo->taken_date;
             if ($this->photo->location)
                echo ' - ';
          }
          if ($this->photo->location) {
          	echo $location;
          }
          ?>
        </div>
    </div>
     <?php if ($this->photo->description): ?>
     	<div class="thumbs_album_slide_description"><?php echo $this->photo->description ?></div>
     <?php endif; ?>

    <div class="albums_viewmedia_info_tags" id="media_tags" style="display: none;">
      <?php echo $this->translate('Tagged:');?>
    </div>
	<div class="albums_viewmedia_info_date_not_popup" style="margin-top: 4px;  text-align: left; float: none;">
      <?php if( $this->canTag ): ?>
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tag'), array('onclick'=>'taggerInstance.begin();')) ?>
      -
      <?php endif; ?>
      <?php if (!$this->message_view):?>

      <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'advalbum_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
      - <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
      - <?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?>
      - <?php echo $this->htmlLink(array(
                            'route' => 'album_photo_specific',
							'action' => 'download-photo',
							'album_id' => $this->photo->album_id,
					        'photo_id' => $this->photo->getIdentity(),
	                        ),
							$this->translate('Download'), array(
					    )) ?>
      <?php endif;?>
     <?php if($this->can_edit): ?>
     <div class="adv_photo_edit">
	      <div><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'flip','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'vertical'),"<img src='./application/modules/Advalbum/externals/images/photo_flip_vertical.png'>",array('class' => 'smoothbox', 'title'=>$this->translate('flip vertical')));?></div>
	      <div><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'flip','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'horizontal'),"<img src='./application/modules/Advalbum/externals/images/photo_flip_horizontal.png'>",array('class' => 'smoothbox', 'title'=>$this->translate('flip horizontal')));?></div>
	      <div><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'rotate','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'left'),"<img src='./application/modules/Advalbum/externals/images/photo_rotate_left.png'>",array('class' => 'smoothbox', 'title'=>$this->translate('rotate left')));?></div>
	      <div><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'rotate','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'right'),"<img src='./application/modules/Advalbum/externals/images/photo_rotate_right.png'>",array('class' => 'smoothbox', 'title'=>$this->translate('rotate right')));?></div>
      </div>
      <?php endif;?>
	</div>
    </div>
  </div>
  <!-- like pretty photo -->
    <div id="div_photo_view_more">
	    <div id="div_minitoolbar" style="padding:8px 0px 8px 0px;">
	        <div style="float: right; margin-right: 4px; margin-top:1px;width:142px;">
	            <!-- AddThis Button BEGIN -->
	            <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
              <!-- Go to www.addthis.com/dashboard to customize your tools -->  
              <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
	            <!-- AddThis Button END -->
	        </div>
	        <style>
	        .minitoolbar {}
	        .minitoolbar div { font-size: 90%; }
	        .minitoolbar a {text-decoration: none;}
	        </style>
	    </div>
	    <div class="photo_view_bottom">
	    <div id="photo_view_form_comment" style="padding-left:4px;">
	   
	      <?php echo $this->action("list", "comment", "core", array("type"=>"advalbum_photo", "id"=>$this->photo->getIdentity())); ?>
	     </div>
	    </div>
	</div>
  </div>
  </div>
</div>
<style type="text/css">
.paginationControl {
    -moz-border-radius:3px 3px 3px 3px;
    border:0 solid #D0E2EC;
    clear:both;
    float:right;
    padding-right:10px;
    font-size: 8pt;
}
.paginationControl > li > a {
    padding:0.1em 0.2em;
}
div.albums_viewmedia_info {
    -moz-border-radius:3px 3px 3px 3px;
    border:3px solid #D0E2EC;
    padding:5px;
    text-align:center;
    width:auto;
    min-width: 650px;
}
.tabs_alt {
    margin:0 !important;
    padding-top:5px !important;
}
#global_page_advalbum-photo-view .tabs_alt > ul {
	border: 0;
}
#global_page_advalbum-photo-view .tabs_alt > ul > li > a {
	padding: 5px 6px;
}
#global_page_advalbum-photo-view .tabs_alt > ul > li > a.selected {
    -moz-border-radius:3px 3px 0 0;
    background :#FFFFFF;
    border-color:#CAD9A1 #CAD9A1 -moz-use-text-color;
    color:#000000;
    padding:5px 6px;

}

#global_page_advalbum-photo-view .tabs_alt > ul > li > a:hover {
    -moz-border-radius:3px 3px 0 0;
    background :#FFFFFF;
    border-color:#CAD9A1 #CAD9A1 -moz-use-text-color;
    color:#000000;
    padding:5px 6px;

}
ul.thumbps
{
    padding-top: 15px;
    padding-left: 25px;
    padding-bottom: 5px;
    overflow: hidden;
}
#global_page_advalbum-photo-view  .tabs_alt > ul {
    margin-bottom: 10px;
    height: 15px;
}

ul.thumbps > li {
float:left;
height:62px;
margin:0px 4px 0 0;
}
</style>
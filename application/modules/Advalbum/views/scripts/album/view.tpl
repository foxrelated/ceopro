<?php
	//Check mobile
	$session = new Zend_Session_Namespace('mobile');
	if(!$session -> mobile)
{
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/tinybox.js');
$this->headLink()
->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/styles/prettyPhoto.css');
}
?>

<?php if ($this->slideshow || $this->playlist)
{
echo $this->html_full;?>
<script type='text/javascript'>
	<?php if($this->body_class):?>
	document.body.addClass("<?php echo $this->body_class;?>");
	document.body.style.margin = "0";
	<?php endif;?>
</script>
<?php
	return;
}
?>

<script type='text/javascript'>
	window.addEvent('domready', function() {
		ynadvalbumOptions();
		if ($$('.ynadvalbum_album_detail-desctiption').length) {
		   $$('.ynadvalbum_album_detail_thumb-more').removeEvents('click').addEvent('click', function() {
          $$('.ynadvalbum_album_detail_thumb-description-content').toggleClass('ynadvalbum_album_detail-description_toggle');
          if ($$('.ynadvalbum_album_detail_thumb-description-content')[0].hasClass('ynadvalbum_album_detail-description_toggle'))
            $$('.ynadvalbum_album_detail_thumb-more').set('html','<span>Show more <span class="ynicon yn-arr-down"></span></span>');
          else
            $$('.ynadvalbum_album_detail_thumb-more').set('html','<span>Show less <span class="ynicon yn-arr-up"></span></span>');
        });

            var hide = $$('.ynadvalbum_album_detail_thumb-description-content').length;
            if(hide){
            
              var height = $$('.ynadvalbum_album_detail_thumb-description-content')[0].getHeight();
              if(height > 65){
           
              	$$('.ynadvalbum_album_detail_thumb-description-content')[0].addClass('ynadvalbum_album_detail-description_toggle');
                $$('.ynadvalbum_album_detail_thumb-more')[0].setStyle('display', 'inline-block');
              }
              else{
					$$('.ynadvalbum_album_detail-desctiption')[0].setStyle('height', '60px');
					$$('.ynadvalbum_album_detail-desctiption')[0].toggleClass('hide_line');
              }
            }
            else{
				$$('.ynadvalbum_album_detail-desctiption')[0].setStyle('display', 'none');
			}
            }
	});
	//rating
	en4.core.runonce.add(function()
	{
		var album_pre_rate = <?php echo $this->album->rating; ?>;
		var album_id = <?php echo $this->album->getIdentity(); ?>;
		var album_total_votes = <?php echo $this->rating_count; ?>;
		var viewer = <?php echo $this->viewer()->getIdentity(); ?>;

		var album_rating_over = window.album_rating_over = function(rating) {
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

		var album_rating_out = window.album_rating_out = function() {
			if (album_total_votes > 0 && album_total_votes <= 1) {
				$('rating_text').innerHTML = album_total_votes + " <?php echo $this->translate('rating')?>";
			}
			else {
				$('rating_text').innerHTML = album_total_votes + " <?php echo $this->translate('ratings')?>";
			}
			if (album_pre_rate != 0){
				album_set_rating();
			}
			else {
				for(var x=1; x<=5; x++) {
					$('rate_'+x).set('class', 'advalbum_rating_star_big_generic ynicon yn-star');
				}
			}
		};

		var album_set_rating = window.album_set_rating = function() {
			var rating = album_pre_rate;
			if (album_total_votes > 0 && album_total_votes <= 1) {
				$('rating_text').innerHTML = album_total_votes + " <?php echo $this->translate('rating')?>";
			}
			else {
				$('rating_text').innerHTML = album_total_votes + " <?php echo $this->translate('ratings')?>";
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

		var album_rate = window.album_rate = function(rating) {
			$('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
			(new Request.JSON({
				'format': 'json',
				'url' : '<?php echo $this->url(array('action' => 'rate', 'subject_id' => $this->album->getIdentity()), 'album_extended', true) ?>',
				'data' : {
					'format' : 'json',
					'rating' : rating,
					'is_album' : 1
				},
				'onRequest' : function(){
				},
				'onSuccess' : function(responseJSON, responseText)
				{
					album_total_votes = responseJSON.total;
					album_pre_rate = responseJSON.rating;
					album_set_rating();
				}
			})).send();
		};

		album_set_rating();
	});
</script>

<div>
	<div>
		<!-- MIDDLE CONTENT -->
		<div>
			<form name="gotoPage" id="gotoPage" method="post">
				<input type="hidden" name="nextpage" id="nextpage">

				<?php if ($this->album->count() > 0 && !$session -> mobile) : ?>
				<?php endif; ?>
				<div  class="ynadvalbum_albums_detail_title clearfix">
					<h2>
						<?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->album->getTitle()); ?>
					</h2>
					<div id="ynadvalbum_album_detail_rating" class="ynadvalbum_photo_detail_thumb-rating clearfix" onmouseout="album_rating_out();">
						<span id="rating_text" class="rating_text ynadvalbum_album_detail_rating-text"><?php echo $this->translate('click to rate'); ?></span>
						<span id="rate_1" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="album_rate(1);"<?php endif; ?> onmouseover="album_rating_over(1);"></span>
						<span id="rate_2" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="album_rate(2);"<?php endif; ?> onmouseover="album_rating_over(2);"></span>
						<span id="rate_3" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="album_rate(3);"<?php endif; ?> onmouseover="album_rating_over(3);"></span>
						<span id="rate_4" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="album_rate(4);"<?php endif; ?> onmouseover="album_rating_over(4);"></span>
						<span id="rate_5" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if ($this->viewer()->getIdentity()): ?>onclick="album_rate(5);"<?php endif; ?> onmouseover="album_rating_over(5);"></span>
					</div>
				</div>

				<div class="ynadvalbum_menu_parent">
					<div class="ynadvalbum_album_detail_addthis">
						<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
				        <!-- Go to www.addthis.com/dashboard to customize your tools -->  
					    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
					</div>
					<?php if ($this->paginator->getTotalItemCount()): ?>
					<span class="ynadvalbum_download-album-photo">
						<?php echo $this->htmlLink(array('route' => 'album_specific',
							'action' => 'download',
							'album_id' => $this->album->album_id),
							'<span class="ynicon yn-photo-download-o"></span> '.$this->translate('Download Album'),
							array(
						)); ?>
					</span>
					<?php endif; ?>
					<?php
						$isOwner = $this->album -> getOwner() -> isSelf(Engine_Api::_() -> user() -> getViewer());
					$canEdit = $this->album -> authorization()->isAllowed(null, 'edit');
					?>
					<div class="ynadvalbum-options">
						<div class="ynadvalbum-options-button" >
							<span class="ynicon yn-arr-down"></span>
						</div>
						<?php echo $this->partial('_item-menu-album.tpl', 'advalbum', array('manage' => ($isOwner && $canEdit), 'view_mode'=>'detail', 'album'=>$this->album, 'total_photos'=>$this->paginator->getTotalItemCount())); ?>
					</div>
				</div>

				<?php if (""!=$this->album->getDescription()): ?>
				<div class="ynadvalbum_album_detail-desctiption">
					<span  class="ynadvalbum_album_detail_thumb-description-content">
						<?php echo $this->album->getDescription() ?>
					</span>
					<span class="ynadvalbum_album_detail_thumb-more"><?php echo $this->translate('<span>Show more<span class="ynicon yn-arr-down"></span></span>'); ?>
					</span>
				</div>
				<?php endif ?>
			</form>
		</div>
		<!-- END MIDDLE CONTENT -->
	</div>
</div>
<?php
if($session -> mobile){
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/jquery-1.10.2.min.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/responsiveslides.min.js');
$this->headLink()
?>
<script type="text/javascript">
	jQuery.noConflict();
	jQuery(function () {
		jQuery("#ymb_home_featuredphoto").responsiveSlides({
			speed: 800
		});
		jQuery('.toggleSlideshowMobile').click(function(){
			if(jQuery(this).parent().hasClass('ym_view_list_photo')){
				jQuery(this).parent().removeClass('ym_view_list_photo');
				jQuery(this).parent().addClass('ym_view_slideshow_photo');
				jQuery(this).text('<?php echo $this->translate('List View');?>');
			}else{
				jQuery(this).parent().addClass('ym_view_list_photo');
				jQuery(this).parent().removeClass('ym_view_slideshow_photo');
				jQuery(this).text('<?php echo $this->translate('Slide Show');?>');
			}

		});
	});
</script>

<?php } ?>
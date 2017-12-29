<?php
$session = new Zend_Session_Namespace('mobile');
$album_list = array();
$album_count = 0;
if (isset($this->arr_albums)) {
	$album_list = $this->arr_albums;
	$album_count = count($album_list);
} else if (isset($this->paginator)) {
	$album_list = $this->paginator;
	$album_count = $this->paginator->getTotalItemCount();
}
if ($album_count<=0) { // no photos
	if (isset($this->no_albums_message) && $this->no_albums_message) {
?>
<div class="tip">
      <span><?php echo $this->no_albums_message;?></span>
</div>
<?php
	}
	return;
}

$album_listing_id = "";
if (isset($this->album_listing_id)) {
	$album_listing_id = trim($this->album_listing_id);
}
if (!$album_listing_id)  $album_listing_id = 'album_listing_' . date("Ymdhis");

$css_main = "";
if ($this->css) {
	$css_main = "{$this->css}";
}

if (isset($this->no_author_info) && $this->no_author_info) {
?>
<?php
}
$shortenLength = 20;
?>


<div class ="<?php echo $css_main;?>" id="<?php echo $album_listing_id; ?>">

	<div id="<?php echo $album_listing_id; ?>" class="album-listing-view-mode <?php echo $css_main;?>">
		<div id="<?php echo $album_listing_id; ?>_view" class="<?php if ($this -> class_mode) echo $this -> class_mode; else echo 'ynalbum-list-view'; ?>">
			<ul class="ynalbum-list" id="ynadvalbum_owl">
		    <?php
		        $thumb_photo = 'thumb.normal';
		    	if(defined('YNRESPONSIVE'))
		    	{
		    		$thumb_photo = 'thumb.profile';
		    	}
		    	foreach($album_list as $album ):

		    	$user = $album->getOwner();
				$noPhotoUrl = $this->layout()->staticBaseUrl.'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
				$album_title_full = trim($album->getTitle());
				$album_title_tooltip = "";
				$album_title = $album_title_full;
				
			 ?>
					<li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynadvalbum_albums_thumb item ynadvalbum_effect_shadow'>
		        <div href="<?php echo $album->getHref(); ?>" class="ynadvalbum_feature_photo_thumb">
		            <div class="ynadvalbum_feature_photo" style="background-image:url(<?php echo $album->getPhotoUrl(); ?>);">
			            <a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"></a>

			            <div>
			            	<div class="ynadvalbum_feature_photo_owner">
				            	 <div class="ynadvalbum_feature_photo_owner-bg" style="background-image:url(<?php echo $user->getPhotoUrl() ? $user->getPhotoUrl() : $noPhotoUrl; ?>);">
									<a href="<?php echo $user->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"></a>
				            	</div>
				            </div>
			            	<a class="ynadvalbum_feature_photo-title" href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album_title; ?></a>
			            	<?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album)); ?>
			            </div>
		            </div>
		            
		        </div>
		        <div class="ynadvalbum_feature_photo_content clearfix">			
		            
		            <div class="ynadvalbum_feature_photo_info">
		            <?php
		            	// end photo album
						$photos_count = $album->count();
						$str_photos = $this->translate(array('%s photo', '%s photos', $photos_count), $this->locale()->toNumber($photos_count));
						$str_views = $this->translate('%1$s views', $album->view_count);
						$str_likes = $this->translate('%1$s comments', $album->like_count);
						$str_comments = $this->translate('%1$s likes', $album->comment_count);

						if (isset($this->no_author_info) && $this->no_author_info) {
							$album_info_1 = $this->translate('%1$s', $str_photos);
						} else {
							$album_info_1 = $this->translate('<span>%2$s</span> <span>by</span> %1$s', $album->getOwner()->__toString(), $str_photos);
						}
				      ?>

						<div class='ynadvalbum_feature_photo_info-owner'>
							<span><?php echo $album_info_1 ?></span>
							<span class="advalbum_album_statis">
								<span class="yn_stats yn_stats_view">
								<?php echo $this->translate(array('%s <span>view</span>', '%s <span>views</span>', $album->view_count), $this->locale()->toNumber($album->view_count)); ?>
								</span>
								<i class="yn_dots">.</i>
								<span class="yn_stats yn_stats_comment">
								<?php echo $this->translate(array('%s <span>comment</span>', '%s <span>comments</span>', $album->comment_count), $this->locale()->toNumber($album->comment_count)); ?>
								</span>
								<i class="yn_dots">.</i>
								<span class="yn_stats yn_stats_like">
								<?php echo $this->translate(array('%s <span>like</span>', '%s <span>likes</span>', $album->like_count), $this->locale()->toNumber($album->like_count)); ?>
								</span>
							</span>
						</div>
		            </div>
		        </div>            
			  </li>
		    <?php endforeach;?>
		    </ul>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/scripts/jquery-1.10.2.min.js"></script>
<script src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/scripts/owl.carousel.min.js"></script>
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/styles/owl.carousel.css" />
<!-- <link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/styles/owl.theme.css" /> -->

<script type="text/javascript">
	jQuery(document).ready(function() {
	 	var owl_article = jQuery('#ynadvalbum_owl');
		var item_amount = parseInt(owl_article.find('.item').length); 
		var true_false = 0;
		if (item_amount > 1) {
			 true_false = true;
		}
		else{
	 		true_false = false;
	 	}
		var rtl = false;
	 	if(jQuery("html").attr("dir") == "rtl") {
	 		rtl = true;
	 	}
		jQuery("#ynadvalbum_owl").owlCarousel({
			rtl:rtl,
			nav:true_false,
			navText:["",""],
			loop: true_false,
			mouseDrag:true_false, 
			autoplay: true,
			dotsSpeed: 1000,
			dots: false,
			autoplayHoverPause: true,
			items: 3,
			responsiveClass:true,
		    responsive:{
		        0:{
		            items:1,
		            nav:true
		        },
		        600:{
		            items:2,
		            nav:false
		        },
		        991:{
		            items:3,
		            nav:true,
		            loop:false
		        }
		    }
		});
	});
</script>
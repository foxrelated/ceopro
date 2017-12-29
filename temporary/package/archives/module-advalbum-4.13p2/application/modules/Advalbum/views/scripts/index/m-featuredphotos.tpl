<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en_US">
<head>
<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<?php $tmp = date("YmdHis"); ?>
<?php
$photo_count = count($this->arr_photos);
$thumbnails_height = $photo_count * (90+2*2+1*2+9); // height, padding, border, margin bottom
?>

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/scripts/idangerous.swiper-2.0.min.js"></script>
<script type="text/javascript">
	$(function(){
		var gallery = $('.ym_large_featuredphoto').swiper({
			slidesPerView:'auto',
			watchActiveIndex: true,
			calculateHeight:true,
			centeredSlides: true,
			resizeReInit: true,
			keyboardControl: true,
			updateOnImagesReady:true,
			grabCursor: true,
			onImagesReady: function(){
				changeSize()
			}
		})
		function changeSize() {
			//Unset Width
			$('.swiper-slide').css('width','')
			//Get Size
			var imgWidth = $('.swiper-slide img').width();
			if (imgWidth+40>$(window).width()) imgWidth = $(window).width()-40;
			//Set Width
			$('.swiper-slide').css('width', imgWidth+40);
		}	
		changeSize();
		//Smart resize
		$(window).resize(function(){
			changeSize()
			gallery.resizeFix(true)	
		})
	})
</script>
</head>
<body>
<div class="swiper-container ym_large_featuredphoto">
	
	<div class="swiper-wrapper" style="width:2424px;">
		<?php
		foreach ($this->arr_photos as $photo_item) {
		?>
		<div class="swiper-slide">
			<div class="inner">
				<a target='_parent' href="<?php echo $photo_item->getHref() ?>">
					<img src ="<?php echo $photo_url = $photo_item->getPhotoUrl("profile.normal"); ?>"/>
				</a>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
</body>
</html>
<?php function fixPhotoURL($photo_url) {
	$pos = strpos($photo_url, '?');
	if ($pos!==FALSE) {
		$photo_url = substr($photo_url, 0, $pos);
	}
	return $photo_url;
}
?>
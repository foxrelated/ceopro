<?php 
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/jquery-1.3.2.js');
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/jcarousellite_1.0.1.min.js');
?>
<style>
#global_page_advalbum-photo-slideview {
	margin:0;	
	padding:0;
	background-color: #FFFFFF;
}
#global_page_advalbum-photo-slideview #global_content_simple {
	margin: 0;
	padding: 0;
	background-color: #FFFFFF;
}
iframe {
	border: solid 1px #FFFFFF;
}
</style>
<div class="slideview">
	<div class="carousel-box">
		<div class="inner">
			<a id="btn_prev" class="prev" onClick="slide_prev();"></a>
			<a id="btn_next" class="next" onClick="slide_next();"></a>

			<div class="carousel">
				<ul>
					<?php 
					$photo_count = 0;
					$photo_start = 0;
					$jsArray = $arrPhotosPreload = $arrPhotosViewCount = '';
					foreach ($this->photo_list as $photo_item) { 
					?>
					<li>
					<div class="viewframe">
					<div class="viewframe_loading" id="div_loading_<?php echo $photo_item->getIdentity()?>">
						<?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Advalbum/externals/images/loading.gif', ''); ?>
					</div>
						<div class="vf_iframe" id="slide_photo_<?php echo $photo_count;?>">
					<?php if ($this->photo->getIdentity()==$photo_item->getIdentity()) { ?>
						<iframe border="0" frameborder="0" framespacing="none" scrolling="no" width="562" height="640" marginwidth="0" marginheight="0" src="<?php echo $photo_item->getHref(array('action'=>'frameview')); ?>" id="photo_iframe_<?php echo $photo_count;?>"></iframe>
					<?php } ?>
						</div>
						<div style="width:1;height:1;overflow:hidden;" id="slide_photo_count_<?php echo $photo_count;?>"></div>
					</div>
					</li>
					<?php
						$jsArray .= "arrPhotos[$photo_count] = '" . $photo_item->getHref(array('action'=>'frameview')) . "';\r\n";
						$arrPhotosPreload .= "arrPhotosPreload[$photo_count] = 0;\r\n";
						$arrPhotosViewCount .= "arrPhotosViewCount[$photo_count] = " . $photo_item->getIdentity() . ";\r\n";
						if ($this->photo->getIdentity()==$photo_item->getIdentity()) {
							$photo_start = $photo_count;
							$arrPhotosPreload .= "arrPhotosPreload[$photo_count] = 1;\r\n";
						}
						$photo_count ++;
					} 
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
var photoCount = <?php echo $photo_count; ?>;
var photoCurrent = <?php echo $photo_start; ?>;
var arrPhotos = new Array();
var arrPhotosPreload = new Array();
var arrPhotosViewCount = new Array();
<?php echo $jsArray; ?>
<?php echo $arrPhotosPreload; ?>
<?php echo $arrPhotosViewCount; ?>

function load_photo(photoIdx) {
	if (photoIdx<0 || photoIdx>photoCount-1) return;
	if (arrPhotosPreload[photoIdx]>0) return;
	objPhoto = document.getElementById("slide_photo_" + photoIdx);
	if (objPhoto) {
		objPhoto.innerHTML = '<iframe border="0" frameborder="0" scrolling="no" width="562" height="625" marginwidth="0" marginheight="0" src="' + arrPhotos[photoIdx] + '" id="photo_iframe_' + photoIdx + '"></iframe>';
		arrPhotosPreload[photoIdx] = 1;
	}
}

function count_view() {
/*
	objPhoto_iFrame = document.getElementById("photo_iframe_" + photoCurrent);
	if (objPhoto_iFrame && objPhoto_iFrame.contentWindow && objPhoto_iFrame.contentWindow.do_count) {
		objPhoto_iFrame.contentWindow.do_count();
	}
*/	
}

function preload_data() {
	// prev
	idxPrev = photoCurrent - 1;
	if (idxPrev>=0) {
		load_photo(idxPrev);
	}
	// next
	idxNext = photoCurrent + 1;
	if (photoCurrent<=photoCount-1) {
		load_photo(idxNext);
	}
	// buttons
	objButtonNext = document.getElementById("btn_next");
	if (objButtonNext) {
		if (photoCurrent>=photoCount-1) {
			objButtonNext.style.visibility = 'hidden';
		} else {
			objButtonNext.style.visibility = 'visible';
		}
	}
	objButtonPrev = document.getElementById("btn_prev");
	if (objButtonPrev) {
		if (photoCurrent<=0) {
			objButtonPrev.style.visibility = 'hidden';
		} else {
			objButtonPrev.style.visibility = 'visible';
		}
	}
	// count
	count_view();
}
function slide_prev() {
	if (photoCurrent>0) {
		photoCurrent --;
		preload_data();
	}
}
function slide_next() {
	if (photoCurrent<photoCount-1) {
		photoCurrent ++;
		preload_data();
	}
}
document.onload = preload_data();

function loading_complete(photoID) {
	objLoading = document.getElementById("div_loading_" + photoID);
	if (objLoading) {
		objLoading.innerHTML = "";
		objLoading.style.display = 'none';
	}
	// count the current
	count_view();
}
</script>
<script type="text/javascript">
	$(function() {
		 $(".carousel").jCarouselLite({
			  btnNext: ".next",
			  btnPrev: ".prev",
			  auto: 0,
			  speed: 1000,
			  visible: 1,
			  scroll: 1,
			  circular: false,
			  vertical: false,
			  start: <?php echo $photo_start;?>
		 });
	});
</script>

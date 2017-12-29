<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>

<?php $tmp = date("YmdHis"); ?>
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/slideshow/slideshow.css"></link>
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/styles/slideshow.css?r=<?php echo $tmp; ?>"></link>

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/slideshow/mootools-1.3.2-core.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/slideshow/mootools-1.3.2.1-more.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/slideshow/slideshow.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/slideshow/slideshow.kenburns.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/slideshow/slideshow.flash.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/slideshow/slideshow.fold.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advalbum/externals/slideshow/slideshow.push.js"></script>
<script type="text/javascript">
//<![CDATA[
  window.addEvent('domready', function()
  {
	var effect = '<?php echo $this->effect?>'
	var ssdata = {
	<?php
	$first_photo = "";
	foreach ($this->photo_list as $photo_item) {
		$file = Engine_Api::_()->getApi('storage', 'storage')->get($photo_item->file_id, "profile.normal");
		if ($file->service_id == 1) {
		    $photo_url = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $file->getHref();
		}
		else {
            $photo_url = $file->getHref();
        }

		if (!$first_photo) {
			$first_photo = $photo_url;
		} else {
			echo ",\r\n";
		}
		$photo_caption = trim($photo_item->getTitle());
		$photo_caption = Advalbum_Api_Core::defaultTooltipText($photo_caption);
		echo "'$photo_url': {caption: '$photo_caption'}";

	} ?>
	};

	switch(effect) {
		case 'flash':
			new Slideshow.Flash('ss_show', ssdata, { color: ['tomato', 'palegreen', 'orangered', 'aquamarine'], height: 510, hu: '', width: 680 });
			break;
		case 'fold':
			new Slideshow.Fold('ss_show', ssdata, { height: 510, hu: '', width: 680, captions: true, controller: true, thumbnails: false });
			break;
		case 'push':
			new Slideshow.Push('ss_show', ssdata, { captions: true, controller: true, thumbnails: false, height: 510, hu: '', transition: 'back:in:out', width: 680 });
			break;
		case 'kenburns':
			new Slideshow.KenBurns('ss_show', ssdata, { captions: true, controller: true, thumbnails: false, titles: false, delay: 3000, duration: 2000, zoom: [10, 10], width: 680, height: 510, hu: '' });
		default:
			break;
	}
	});
//]]>
</script>
</head>
<body>
<div class="ss_slideshow">
<?php
	$album_owner = $this->album->getOwner();
?>
	<div class="ss_album_title">
  <?php echo $this->translate('Album %1$s by %2$s', $this->htmlLink($this->album, $this->album->getTitle(), array('target'=>'_top')), $this->htmlLink($album_owner->getHref(), $album_owner->getTitle(), array('target'=>'_top')));  ?>
	</div>
	<div class="ss_photo">
		<div id="ss_show" class="slideshow">
		</div>
	</div>
	<div class="ss_more_info">

	<?php echo $this->translate("Hover the mouse on the photo to see navigation buttons");?>
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

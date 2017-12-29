<?php
$this->headScript()
	->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/carousel/jquery-1.9.1.min.js')
	->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/jquery.flexslider.js');

$this->headLink()
	->appendStylesheet($this->baseUrl() . '/application/modules/Advgroup/externals/styles/flexslider.css');
?>

<!-- Place somewhere in the <body> of your page -->
<div class="flexslider">
  <ul class="slides">
  	<?php foreach ($this->photos as $photo_item) :?>
    <li>	
	    <span class="flexslider-image" style="background-image: url(<?php echo $photo_item->getPhotoUrl('thumb.main'); ?>);"></span>
    </li>
    <?php endforeach;?>
  </ul>
</div>

<style type="text/css">
	.flexslider-image {
		width: 100%;
		height: 200px;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: center;
		display: block;
	}
</style>

<script type="text/javascript" charset="utf-8">
	jQuery.noConflict();
	jQuery(window).load(function() {
		jQuery('.flexslider').flexslider({
                prevText: "",
                nextText: "", 
				animation: '<?php echo $this->effect; ?>',
				animationLoop: <?php echo ($this->allowLoop) ? 'true' : 'false'; ?>
		});
  	});
</script>

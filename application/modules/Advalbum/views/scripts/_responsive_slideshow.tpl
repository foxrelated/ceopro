<div id="parallax_<?php echo $this->slider_id; ?>" class="advalbum_responsive_slideshow_parallax pxs_container" style="height:<?php echo $this -> height;?>px">
	
			

			<div class="ms-vertical-template  ms-tabs-vertical-template">
			    <!-- masterslider -->
			    <div class="master-slider ms-skin-default" id="masterslider">
					<?php foreach ($this->items as $photo): ?>
			         <div class="ms-slide">
				                          <img src="<?php echo $this->escape($photo->getPhotoUrl('thumb.icon'));?>" alt="<?php echo $this->escape($photo->getTitle());?>" />
				         
				        <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.icon');?>" alt="thumb" />
				        <a href="<?php echo $this -> image_embed; ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title=""></a>
				     </div>
				     <?php endforeach; ?>
			    </div>
			</div>

</div>
<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/scripts/jquery-1.10.2.min.js"></script>
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/styles/masterslider.css" />
<link href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/styles/style.css" rel='stylesheet' type='text/css'>
<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/scripts/jquery.easing.min.js"></script>
<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/scripts/masterslider.min.js"></script>

	<script type="text/javascript">		
	
		var slider = new MasterSlider();
		slider.setup('masterslider' , {
			width:660,
			height:430,
			space:5,
			view:'basic',
			dir:'v'
		});
		slider.control('arrows');	
		slider.control('scrollbar' , {dir:'v'});	
		slider.control('circletimer' , {color:"#FFFFFF" , stroke:9});
		slider.control('thumblist' , {autohide:false ,dir:'v'});

	</script>
</html>

<!-- <script type="text/javascript">
	jQuery('#parallax_<?php echo $this->slider_id; ?>').parallaxSlider({
    auto: <?php echo $this -> speed?>,
    speed: 1000
  });
</script>
<?php if(defined('YNRESPONSIVE')):?>
<div class="advalbum_responsive_slideshow_flex flexslider" id="flexslider_<?php echo $this->slider_id; ?>">
  <ul class="slides">
    <?php foreach( $this->items as $item): ?>    
    <?php $title  =  $item->getTitle(); ?>
    <li class="<?php echo ++$index==1?'active':''; ?>">
      <div class="overflow-hidden" style="height:350px">
		  <span style="background-image: url(<?php echo $item -> getPhotoUrl()?>);"></span>
            <?php if($title && $this->show_title): ?>
            <div class="carousel-caption">
              <p><?php echo $this->htmlLink($item->getHref(), $title) ?></p>
            </div>
            <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
    
  </ul>
</div>
<script type="text/javascript">
jQuery(window).load(function() {
  jQuery('#flexslider_<?php echo $this -> slider_id; ?>').flexslider({
    animation: "slide",
	auto: <?php echo $this -> speed?>,
    speed: 1000
  });
});
</script>
<?php endif;?> -->
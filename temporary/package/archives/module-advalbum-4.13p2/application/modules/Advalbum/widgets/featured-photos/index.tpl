<div class="ms-vertical-template  ms-tabs-vertical-template">
	<div class="master-slider ms-skin-default" id="ynadvalbum_masterslider">
		<div class="ynadvalbum_photo_feature_button">
			<span class="ynicon yn-photo-o" id="ynadvalbum_feature_photo_btn"></span>
		</div>
		<?php foreach ($this->items as $photo): ?>
	     <div class="ms-slide" data-fill-mode="center">
			 <div class="ynadvalbum_photo_feature_bottom">
				 <div class="ynadvalbum_photo_feature_description">
					<span><?php echo $photo->getDescription(); ?></span>
				</div>
			 </div>
            <img src="application/modules/Advalbum/externals/images/blank.gif" data-src="<?php echo $this->escape($photo->getPhotoUrl());?>" alt="<?php echo $this->escape($photo->getTitle());?>" />
	        <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl();?>" alt="thumb" />
	        <a href="<?php echo $photo->getHref(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title=""></a>
	     </div>
	     <?php endforeach; ?>
	</div>
</div>

<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/scripts/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/scripts/jquery.easing.min.js"></script>
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/styles/masterslider.css" />
<link href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Advalbum/externals/styles/style.css" rel='stylesheet' type='text/css'>
<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/scripts/masterslider.js"></script>

<script type="text/javascript">		
	var slider = new MasterSlider();
	slider.setup('ynadvalbum_masterslider' , {
		width:1100,
		height:620,
		space:5,
		view:'basic',
		dir:'h',
		autoplay: true,
		loop: true
	});
	slider.control('arrows',{autohide:false});	
	slider.control('bullets' , {autohide:false  , dir:"h", align:"bottom"});
	slider.control('circletimer' , {color:"#FFFFFF" , stroke:9});
	slider.control('thumblist' , {autohide:false ,dir:'v'});
</script>

<script type="text/javascript">
  window.addEvent('domready', function() {
    $('ynadvalbum_feature_photo_btn').removeEvents('click').addEvent('click', function() {
       this.getParent('.ynadvalbum_photo_feature_button').getSiblings('.ms-thumb-list.ms-dir-v').toggleClass('show');
       $$('.ms-nav-next').toggleClass('ynadvalbum_btn_hide');
       $$('.ms-nav-prev').toggleClass('ynadvalbum_btn_hide');

    });
});
</script>






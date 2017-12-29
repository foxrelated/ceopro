<?php
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery-1.10.2.min.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/owl.carousel.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/prettify.js');
	$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/owl.carousel.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/prettify.css');
?>

<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-content">
		<?php echo $this -> business -> short_description?>
	</div>
</div>

<?php if($this -> business -> theme == "theme3") :?>
	<?php if (count($this->covers)):?>	
		<div class="ynbusinesspages-profile-fields">	
			<div id="ynbusinesspages-profile-slider" class="owl-carousel">
				<?php foreach ($this->covers as $photo):?>
					<div class="item">
						<span class="ynbusinesspages-slider-flex-thumb" style="background-image: url(<?php echo $photo->getPhotoUrl();?>);"></span>
					</div>
				<?php endforeach;?>
			</div>
		</div>
	<?php endif;?>
<?php endif;?>

<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="ynicon yn-arr-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="ynicon yn-folder-open"></i><?php echo $this->translate('Category');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<ul>
			<?php foreach($this -> categoryMaps  as $categoryMap) :?>
				<?php 
					$table = Engine_Api::_() -> getDbTable('categories', 'ynbusinesspages');
					$category = $table -> getNode($categoryMap -> category_id);
					$i = 0;
				?>
				<?php if($category) :?>
					<li>
						<?php foreach($category->getBreadCrumNode() as $node): ?>
							<?php if($node -> category_id != 1) :?>
							<?php if($i != 0) :?>
								&raquo;	
							<?php endif;?>
			        			<?php $i++; echo $this->translate($node->shortTitle()) ?>
			        		<?php endif; ?>
				     	 <?php endforeach; ?>
				     	 <?php if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
									&raquo;	
						 <?php endif;?>
				     	 <?php echo $category->getTitle(); ?>
			     	 </li>
		     	 <?php endif;?>
			<?php endforeach;?>
		</ul>
	</div>
</div>

<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="ynicon yn-arr-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="ynicon yn-users"></i><?php echo $this->translate('Business Size');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<?php echo $this -> business -> size?>
	</div>
</div>

<?php if(count($this -> founders)):?>
<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="ynicon yn-arr-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="ynicon yn-rocket"></i> <?php echo $this->translate('Founders');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<ul class="ynbusinesspages-overview-founder">
			<?php foreach($this -> founders  as $founder) :?>
			<li>
				<?php if(!empty($founder -> user_id)) :?>
					<?php $user = Engine_Api::_() -> getItem('user', $founder -> user_id);?>
					<?php if($user -> getIdentity() > 0):?>
						<?php echo $this -> htmlLink($user -> getHref(), $user -> getTitle(), array('target' => '_blank'));?></span>
					<?php endif;?> 
				<?php else :?>
					<span><?php echo $founder -> name ?></span>
				<?php endif;?>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
<?php endif;?>

<?php if($this->locations->count()): ?>
<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="ynicon yn-arr-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="ynicon yn-building"></i> <?php echo $this->translate('Locations');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<ul class="ynbusinesspages-overview-listmaps">
			<?php $locationIndex = 1; foreach($this -> locations  as $location) :?>
			<li>
				<div class="ynbusinesspages-overview-maps-title"><?php echo $location -> title ?></div>
				<div class="ynbusinesspages-overview-maps-location"><i class="ynicon yn-map-marker"></i> <?php echo $location -> location ?></div>
				<?php if ($location -> latitude != '0' && $location -> longitude != '0'):?>
					<?php
						echo $this -> partial('_location_map.tpl', 'ynbusinesspages', array(
							'item' => $location,
							'map_canvas_id' => 'map-canvas-'.$locationIndex,
						));
						$locationIndex++;
					?>
	            <?php endif;?>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
<?php endif; ?>
<?php if($this -> package -> getIdentity() && $this -> package -> allow_owner_add_customfield && count($this -> businessInfos)) :?>
<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="ynicon yn-arr-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="ynicon yn-book"></i><?php echo $this->translate('Additional Information');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
	<ul>
		<?php foreach($this -> businessInfos  as $businessInfo) :?>
		<li>
			<span class="ynbusinesspages-overview-custom-header"><?php echo $businessInfo -> header ;?></span>
			<span><?php echo $businessInfo -> content ;?></span>
		</li>
		<?php endforeach;?>
	</ul>
	</div>
</div>
<?php endif;?>

<?php if($this -> business -> user_id != 0) :?>
<?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> business); ?>
<?php if($this -> fieldValueLoop($this -> business, $fieldStructure)):?>
<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="ynicon yn-arr-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="ynicon yn-building"></i> <?php echo $this->translate('Business Specifications');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
	   <?php echo $this -> fieldValueLoop($this -> business, $fieldStructure); ?>
	</div>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="ynicon yn-arr-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="ynicon yn-text-align-justify"></i><?php echo $this->translate('Description');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<div class="ynbusinesspages-description rich_content_body">
			<?php echo $this -> business -> description?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$$('.ynbusinesspages-overview-toggle-button').addEvent('click', function(){
		this.toggleClass('ynbusinesspages-overview-content-closed');
		this.getParent('.ynbusinesspages-profile-fields').getElement('.ynbusinesspages-overview-content').toggle();
	});

	<?php if($this -> business -> theme == "theme3") :?>
		// Can also be used with $(document).ready()
		jQuery.noConflict();
        	jQuery(window).load(function() {
		var owl_article = jQuery('#ynbusinesspages-profile-slider');
	 	var item_amount = parseInt(owl_article.find('.item').length); 
	 	var true_false = 0;
	 	if (item_amount > 1) {
	 		true_false = true;
	 	}else{
	 		true_false = false;
	 	}
		var rtl = false;
		if(jQuery("html").attr("dir") == "rtl") {
			rtl = true;
		}
		jQuery("#ynbusinesspages-profile-slider").owlCarousel({
		rtl:rtl,
		nav:true_false,
		navText:["<span class='ynicon yn-arr-left'></span>","<span class='ynicon yn-arr-right'></span>"],
		loop: true_false,
		mouseDrag:true_false, 
		autoplay: false,
		dotsSpeed:1000,
		autoplayHoverPause:true,
		items:1
	});
	});

	<?php endif;?>

</script>

<script>
	
	window.addEvent('domready', function(){
		$$('.tab_layout_ynbusinesspages_business_profile_overview').addEvent('click', function(){		

			<?php if($this -> business -> theme == "theme3") :?>
				jQuery.noConflict();
				if ( jQuery('#ynbusinesspages-profile-slider').length > 0 ) {
					jQuery('#ynbusinesspages-profile-slider').flexslider().resize();
				}
			<?php endif;?>			
			
			<?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?key=". Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc')."&v=3.exp&sensor=true&libraries=places"); ?>
			
			<?php $locationIndex = 1; foreach($this -> locations  as $location) :?>
				<?php if ($location -> latitude != '0' && $location -> longitude != '0'):?>
					var fromPoint;
					var endPoint = new  google.maps.LatLng(<?php echo $location->latitude;?>, <?php echo $location->longitude;?>);
					var directionsService = new google.maps.DirectionsService();
					var directionsDisplay;
					var map;
					var marker;
					
					  var center =  new google.maps.LatLng(<?php echo $location->latitude;?>, <?php echo $location->longitude;?>);
					  var mapOptions = {
					    center: center,
					    zoom: 13
					  };
					
					  directionsDisplay = new google.maps.DirectionsRenderer();
					  map = new google.maps.Map(document.getElementById('map-canvas-<?php echo $locationIndex;?>'), mapOptions);
					  directionsDisplay.setMap(map);
					  
					
					  var infowindow = new google.maps.InfoWindow();
					
					  marker = new google.maps.Marker({
					  	map:map,
					  	draggable:true,
					  	animation: google.maps.Animation.DROP,
					  	position: center
					  });
						
					  google.maps.event.addListener(marker, 'dragend', toggleBounce);
					
					  function toggleBounce() {
							if (marker.getAnimation() != null) 
							{
					  			marker.setAnimation(null);
							} 
							else 
							{
					  			marker.setAnimation(google.maps.Animation.BOUNCE);
							}
							var point = marker.getPosition();
							fromPoint = new google.maps.LatLng(point.lat(), point.lng());
					  }
					
				    <?php $locationIndex++;?>
				<?php endif;?>
		    <?php endforeach;?>
		});	
	});
</script>
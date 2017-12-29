<div id="location-wrapper" class="form-wrapper">
	<div id="location-label" class="form-label">
		<label for="location">
			<?php echo $this->translate('Location');?>
		</label>
	</div>
	<div id="location-element" class="form-element">
		<input type="hidden" name="location_title" id="location_title">
		<input type="text" name="location" id="location" value="<?php if(isset($this->location)) echo $this->location;?>">
		<a style="display: inline-block; vertical-align: middle;" href="javascript:void()" onclick="return getCurrentLocation(this,'location', 'location_title', 'latitude', 'longitude');" >
			<i class="ynicon yn-location"></i>
		</a>			
	</div>
</div>


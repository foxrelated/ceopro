<div id="location-wrapper" class="form-wrapper">
	<div id="location-label" class="form-label">
		<label for="location"><?php echo $this -> translate("Location") ?></label>
	</div>
	<div id="location-element" class="form-element">
		<input type="text" name="location" id="location" value="<?php echo $this -> location_address?>">
		<a class='advgroup_location_icon' href="javascript:void()" onclick="return getCurrentLocation(this);" >
			<i class="ynicon yn-map-marker"></i>
		</a>			
	</div>
</div>


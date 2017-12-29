<div id="location-wrapper" class="form-wrapper">
	<div id="location-label" class="form-label">
		&nbsp;
	</div>
	<div id="location-element" class="form-element">
		<input type="text" name="location" id="location" value="<?php if($this->location) echo $this->location;?>">
		<a class='ynmember_location_icon' href="javascript:void()" onclick="return getCurrentLocation(this);" >
			<span class="ynicon yn-location" id="search_localtion"></span>
		</a>			
	</div>
</div>


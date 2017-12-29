<div id="location_subscribe-wrapper" class="form-wrapper">
	<div id="location_subscribe-label" class="form-label">
		<label for="location_subscribe" class="optional"><?php echo $this->translate('Location');?></label>
	</div>
	<div id="location_subscribe-element" class="form-element">
		<input style="width: 80%;" type="text" name="location_subscribe" id="location_subscribe" value="<?php if($this->location) echo $this->location;?>">
		<a class='ynjobposting_location_icon' href="javascript:void()" onclick="return getSubscribeCurrentLocation(this);" >
			<i class="ynicon yn-location"></i>
		</a>			
	</div>
</div>


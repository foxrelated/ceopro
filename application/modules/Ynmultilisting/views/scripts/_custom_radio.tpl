<?php
	$checked1 = (is_null($this -> listingtype)) ||  ($this -> listingtype && ($this -> listingtype -> {$this->name} == '1') );
?>
<div class="form-wrapper form-ynbusinesspages-choose-theme">
	<div class="form-label">
		<?php echo $this->translate($this->label)?>
	</div>
	<div style="display:inline-block" class="form-element">
		<div style="display:inline" class="view-choose">
			<input id='<?php echo $this->name?>1' type='radio' name='<?php echo $this->name;?>' value ='1' <?php echo ($checked1) ? 'checked="checked"' : '';?>>
			<img style="width: 120px;" src="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/type_<?php echo $this->name;?>1.png" />
		</div>
		<div style="display:inline" class="view-choose">
			<input id='<?php echo $this->name?>2' type='radio' name='<?php echo $this->name;?>' value ='2' <?php echo (!$checked1) ? 'checked="checked"' : '';?>>
			<img style="width: 120px;" src="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/type_<?php echo $this->name;?>2.png" />
		</div>
	</div>
</div>

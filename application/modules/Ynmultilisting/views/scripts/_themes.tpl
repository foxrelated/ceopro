<div class="form-wrapper form-ynmultilisting-choose-theme">
	<div class="form-label">
		<?php echo $this->translate('Select Themes')?>
	</div>
	<div class="form-element">
		<div class="item-form-theme-choose">
			<input id='package_theme1' type='checkbox' name='themes[]' value ='theme1'>
			<img src="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/theme1.png" />
		</div>
		<div class="item-form-theme-choose">
			<input id='package_theme2' type='checkbox' name='themes[]' value ='theme2'>
			<img src="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/theme2.png" />
		</div>
		<div class="item-form-theme-choose">
			<input id='package_theme3' type='checkbox' name='themes[]' value ='theme3'>
			<img src="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/theme3.png" />
		</div>
		<div class="item-form-theme-choose">
			<input id='package_theme4' type='checkbox' name='themes[]' value ='theme4'>
			<img src="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/theme4.png" />
		</div>
		<div class="item-form-theme-choose">
			<input id='package_theme5' type='checkbox' name='themes[]' value ='theme5'>
			<img src="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/theme5.png" />
		</div>
	</div>
</div>
<script type='text/javascript'>
	<?php if($this->package):?>
		<?php foreach($this->package->themes as $item) :?>
		    var id = 'package_' + '<?php echo $item;?>';
			$(id).setProperty('checked', 'true');
		<?php endforeach ;?>
	<?php endif;?>
</script>
